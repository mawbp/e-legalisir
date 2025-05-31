<?php

namespace App\Http\Controllers;

use App\Models\Alamat;
use App\Models\Pengaturan;
use App\Models\Pembayaran;
use App\Models\Permohonan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Midtrans\Config;
use Midtrans\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use App\Mail\MyEmail;

class ApiController extends Controller
{
    //
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
      $this->apiKey = config('services.biteship.api_key');
      $this->baseUrl = config('services.biteship.base_url');
    }
    
    public function bayarUlang(Request $request){
        try {
            $permohonan_id = $request->id;
            $permohonan = Permohonan::where('permohonan_id', $permohonan_id)->first();
            $pembayaran_id = $permohonan->pembayaran_id;
            $metodekirim = $request->metodeKirim;
            $biayakurir = $request->biayaKurir;
            $jumlahbayar = $request->jumlahBayar;
            $alamat_id = $request->alamatId;
            $pembayaran = Pembayaran::create([
              'metode_pengiriman'   => $metodekirim,
              'biaya_kurir'         => $biayakurir,
              'jumlah_bayar'        => $jumlahbayar,
              'alamat_id'           => $alamat_id,
              'status_pembayaran'   => 'pending',
              'bukti_pembayaran'    => "",
              'expired_at'          => now()->addMinutes(5),
            ]);
            
            Permohonan::where('permohonan_id', $permohonan_id)
                ->update([
                   'pembayaran_id' => $pembayaran->id 
                ]);
                
            Pembayaran::where('id', $pembayaran_id)->update(['status_pembayaran' => 'expired', 'snap_token' => '']);
            return response()->json(['success' => true]);    
        } catch(\Exception $e){
            return response()->json(['success' => false]);
        }
    }

    public function paymentPg(Request $request){

      $permohonan_id = $request->id;
      $dokumen = Permohonan::with('dokumen')->where('permohonan_id', $permohonan_id)->get();
      $pembayaran_id = Permohonan::where('permohonan_id', $permohonan_id)->value('pembayaran_id');
      $pembayaran = Pembayaran::where('id', $pembayaran_id)->first();
      $pengaturan = Pengaturan::all()->keyBy('nama');
      $biaya_kurir = $pembayaran->biaya_kurir;
      $biaya_admin = $pengaturan['biaya_admin']->nilai;
      $biaya_total = Pembayaran::where('id', $pembayaran_id)->value('jumlah_bayar');
      $snapToken;

      if($pengaturan['payment_gateway']->nilai == 'midtrans'){
        if (empty($pengaturan['skey_midtrans']->nilai) || empty($pengaturan['ckey_midtrans']->nilai)) {
            return response()->json(['error' => true]);
        }
        
        try {
            Config::$serverKey = $pengaturan['skey_midtrans']->nilai;
            Config::$clientKey =  $pengaturan['ckey_midtrans']->nilai;
            Config::$isProduction = config('services.midtrans.is_production');
            Config::$isSanitized = config('services.midtrans.is_sanitized');
            Config::$is3ds = config('services.midtrans.is_3ds');
    
            $transaction_details = array(
              'order_id'     => 'INV-' . time(),
              'gross_amount' => $biaya_total
            );
    
            $items = [];
            foreach($dokumen as $d){
              $items[] = [
                'id'            => $d->dokumen_id,
                'price'         => $d->harga_per_lembar,
                'quantity'      => $d->jumlah_cetak,
                'name'          => $d->dokumen->nama_dokumen,
              ];
            }
    
            $items[] = [
              'id'  => 'ITEM-' . time(),
              'price' => $biaya_kurir,
              'quantity' => 1,
              'name'  => 'Ongkos Kirim',
            ];
    
            $items[] = [
              'id'  => $permohonan_id,
              'price' => $biaya_admin,
              'quantity' => 1,
              'name'  => 'Biaya Admin',
            ];
    
            $customer_details = array(
              'first_name' => Auth::user()->name,
              'email'      => Auth::user()->email,
              'phone'      => Auth::user()->phone,
            );
    
            $expiry = array(
              'start_time' => Carbon::parse($pembayaran->expired_at)->subMinutes(5)->format('Y-m-d H:i:s O'),
              'unit' => 'minutes',
              'duration' => 5
            );
    
            $transaction_data = array(
              'payment_type'        => 'payment_gateway',
              'transaction_details' => $transaction_details,
              'customer_details'    => $customer_details,
              'items'               => $items,
              'expiry'              => $expiry
            );
    
            $snapToken = \Midtrans\Snap::getSnapToken($transaction_data);
            Pembayaran::where('id', $pembayaran_id)->update([
              'metode_pembayaran' => 'Payment Gateway',
              'snap_token' => $snapToken
            ]);
    
            return response()->json(['snapToken' => $snapToken, 'metode' => $pengaturan['payment_gateway']->nilai]);    
        } catch(\Exception $e) {
            return response()->json(['error' => true]);
        }
      } else if ($pengaturan['payment_gateway']->nilai == 'doku'){
        if(empty($pengaturan['skey_doku']->nilai) || empty($pengaturan['ckey_doku']->nilai)){
            return response()->json(['error' => true]);
        }
        
        try {
            $client_id = $pengaturan['ckey_doku']->nilai;
            $secret_key = $pengaturan['skey_doku']->nilai;
            $request_id = uniqid();
            $request_timestamp = now()->subHour(7)->format('Y-m-d\TH:i:s\Z');
            $request_target = '/checkout/v1/payment';
            $items = [];
            foreach($dokumen as $d){
              $items[] = [
                'id'            => $d->dokumen_id,
                'price'         => $d->harga_per_lembar,
                'quantity'      => $d->jumlah_cetak,
                'name'          => $d->dokumen->nama_dokumen,
              ];
            }
    
            $items[] = [
              'id'  => 'ITEM-' . time(),
              'price' => $biaya_kurir,
              'quantity' => 1,
              'name'  => 'Ongkos Kirim',
            ];
    
            $items[] = [
              'id'  => $permohonan_id,
              'price' => $biaya_admin,
              'quantity' => 1,
              'name'  => 'Biaya Admin',
            ];
            
            $customer = array(
              'id'         => "CUST-" . time(),
              'name'       => Auth::user()->name,
              'email'      => Auth::user()->email,
              'phone'      => Auth::user()->phone,
            );
            
            $url = 'https://api-sandbox.doku.com/checkout/v1/payment';
            $body = [
              "order" => [
                "invoice_number" => 'INV-' . time(),
                "amount" => $biaya_total,
                "callback_url" => route('user.dashboard'),
                "callback_url_cancel" => route('user.dashboard'),
                "line_items" => $items,
              ],
              "payment" => [
                "payment_due_date" => 60
              ],
              "customer" => $customer,
            ];
    
            $body_json = json_encode($body);
            $digest = base64_encode(hash('sha256', $body_json, true));
            $component = "Client-Id:{$client_id}\nRequest-Id:{$request_id}\nRequest-Timestamp:{$request_timestamp}\nRequest-Target:{$request_target}\nDigest:{$digest}";
            $signature = base64_encode(hash_hmac('sha256', $component, $secret_key, true));
    
            $response = Http::withHeaders([
              'Client-ID' => $client_id,
              'Request-ID' => $request_id,
              'Request-Timestamp' => $request_timestamp,
              'Signature' => 'HMACSHA256=' . $signature,
              'Content-Type' => 'application/json',
            ])->post($url, $body);
    
            $result = $response->json();
            $url = $result['response']['payment']['url'];
            if(isset($url)){
              Pembayaran::where('id', $pembayaran_id)->update([
                'metode_pembayaran' => 'Payment Gateway',
                'snap_token' => $url,
              ]);
    
              return response()->json(['url' => $url, 'metode' => $pengaturan['payment_gateway']->nilai]);
            } else {
              return response()->json(['error' => 'not ok']);
            }   
        } catch (\Exception $e){
            return response()->json(['error' => true]);
        }
      }
    }

    public function updatePaymentPg(Request $request){
      set_time_limit(0);
      $pembayaran_id = $request->pembayaran_id;
      $permohonan_id = $request->permohonan_id;
      $pengaturan = Pengaturan::all()->keyBy('nama');
      $user_id = Permohonan::where('permohonan_id', $permohonan_id)->first()->value('user_id');
      $email = User::where('id', $user_id)->value('email');

      $data = [
        'subject' => 'Permohonan Legalisir',
        'title' => 'Status Pengajuan Permohonan Legalisir',
        'body' => 'Pembayaran anda telah diterima, dokumen anda sedang dalam proses legalisir.',
      ];

       $cek_biaya_kurir = Pembayaran::where('id', $pembayaran_id)->value('biaya_kurir');
       $biaya_kurir;
       if(!$cek_biaya_kurir){
         $biaya_kurir = 0;
       } else {
         $biaya_kurir = $cek_biaya_kurir;
       }

       $permohonan = Permohonan::with('dokumen')->where('permohonan_id', $permohonan_id)->get();
       $items = [];
       foreach($permohonan as $p){
         $item = InvoiceItem::make($p->dokumen->nama_dokumen)
           ->description($p->dokumen->deskripsi)
           ->pricePerUnit($p->harga_per_lembar)
           ->quantity($p->jumlah_cetak);
         $items[] = $item;
       }
       $items[] = InvoiceItem::make("Biaya Pengiriman")
           ->description("")
           ->pricePerUnit($biaya_kurir)
           ->quantity(1);

       $client = new Party([
         'name'   => $pengaturan['nama_kampus']->nilai,
         'phone'  => $pengaturan['phone_kampus']->nilai,
         'custom_fields' => [
           'email' => $pengaturan['email_kampus']->nilai
         ],
       ]);

       $customer = new Party([
         'name'  => Auth::user()->alumni->nama,
         'phone' => Auth::user()->phone,
         'custom_fields' => [
           'email' => Auth::user()->email
         ],
       ]);

       $notes = [
         'Terima kasih telah menggunakan Layanan Legalisir Online',
         'Fakultas SAINTEK UNIPDU Jombang',
       ];
       $notes = implode("<br>", $notes);

       $invoice = Invoice::make('receipt')
         ->series('BIG')
         ->status(__('invoices::invoice.paid'))
         ->sequence(667)
         ->serialNumberFormat('{SEQUENCE}/{SERIES}')
         ->seller($client)
         ->buyer($customer)
         ->date(now())
         ->dateFormat('Y/m/d')
         ->currencySymbol('Rp')
         ->currencyCode('IDR')
         ->currencyFormat('{SYMBOL}{VALUE}')
         ->currencyThousandsSeparator('.')
         ->currencyDecimalPoint(',')
         ->filename('invoices/' . $permohonan_id . '_paid')
         ->addItems($items)
         ->notes($notes)
         ->save('public');

         $filePath_update = storage_path('app/public/invoices/' . $permohonan_id . '_paid.pdf');
        Mail::to($email)->send(new MyEmail($data, $filePath_update));

        Pembayaran::where('id', $pembayaran_id)->update([
          'status_pembayaran' => 'success',
          'snap_token' => '',
        ]);

        Permohonan::where('permohonan_id', $permohonan_id)->update([
          'catatan' => '',
          'status_permohonan' => 'Proses Legalisir Dokumen'
        ]);

        return response()->json(['success' => 'ok']);
    }

    public function paymentTf(Request $request){
      $permohonan_id = $request->id;
      $pembayaran_id = Permohonan::where('permohonan_id', $permohonan_id)->value('pembayaran_id');
      $pembayaran = Pembayaran::where('id', $pembayaran_id)->first();
      $expiry = $pembayaran->expired_at;
      if($expiry < Carbon::now()){
        return response()->json(["failed" => "Waktu pembayaran sudah habis, silahkan ulangi pembayaran"]);
      } else {
        $validator = Validator::make($request->all(), [
          'id'   => 'required|string',
          'file' => 'required|file|mimes:jpg,jpeg,png|max:2048'
        ]);

        if($validator->fails()){
          return response()->json([
            'validation' => $validator->errors()->first()
          ]);
        }

        if($request->hasFile('file')){
          $file = $request->file('file');
          $nama = $permohonan_id . '_bukti' . '.' . $file->getClientOriginalExtension();
          $path = '/home/fstunipd/public_html/uploads/';
          $full_path = '/home/fstunipd/public_html/uploads/' . $nama;

          if(file_exists($full_path)){
            unlink($full_path);
          }

          $file->move($path, $nama);

          Permohonan::where('permohonan_id', $permohonan_id)->update([
            'catatan' => '',
            'status_permohonan' => 'Validasi Pembayaran'
          ]);

          $pembayaran->update([
            'metode_pembayaran' => 'Transfer Bank',
            'status_pembayaran' => 'pending',
            'bukti_pembayaran' => $nama,
          ]);
          return response()->json(['success' => 'Upload bukti pembayaran berhasil']);
        }
      }

      return response()->json(["failed" => "File tidak ditemukan"]);
    }

    // public function handleNotification(Request $request)
    // {
    //   Config::$serverKey = $pengaturan['skey_midtrans']->nilai;
    //   Config::$isProduction = config('services.midtrans.is_production');

    //   $notif = new Notification();
    //   $transaction_status = $notif->transaction_status;
    //   $payment_type = $notif->payment_type;
    //   $order_id = $notif->order_id;
    //   $fraud_status = $notif->fraud_status;

    //   $permohonan = Permohonan::where('order_id', $order_id)->first();
    //   $pembayaran = Pembayaran::where('id', $permohonan->pembayaran_id)->first();
    //   if(!$permohonan){
    //     return response()->json(['message' => 'transaksi tidak ditemukan'], 404);
    //   }

    //   switch ($transaction_status) {
    //     case 'capture':
    //       if($payment_type == 'credit_card'){
    //         if($fraud_status == 'challenge'){
    //           $pembayaran->status_pembayaran = 'challenge';
    //         } else {
    //           $pembayaran->status_pembayaran = 'success';
    //         }
    //       }
    //       break;
    //     case 'settlement':
    //       $pembayaran->status_pembayaran = 'success';
    //       break;
    //     case 'pending':
    //       $pembayaran->status_pembayaran = 'pending';
    //       break;
    //     case 'deny':
    //     case 'expire':
    //     case 'cancel':
    //       $pembayaran->status_pembayaran = $transaction_status;
    //       break;
    //   }

    //   $pembayaran->save();
    //   return response()->json(['success' => 'OK'], 200);
    // }

    public function getRates(Request $request){
      $kodepos = $request->kodepos;

      $data = [
        "origin_postal_code"      => 61361,
        "destination_postal_code" => $kodepos,
        "couriers"                => "paxel,jne,sicepat,jnt,anteraja,tiki,ninja,sap,lion,pos,rpx,gojek,grab",
        "items"                   => [
          [
            "name"   => "Dokumen",
            "description" => "Dokumen Legalisir",
            "value"       => 20000,
            "length"      => 10,
            "width"       => 10, 
            "height"      => 10,
            "weight"      => 100,
            "quantity"    => 1
          ]
        ]
      ];

      try {
        $response = Http::withHeaders([
          'Authorization' => $this->apiKey,
          'Content-Type' => 'application/json',
        ])->post($this->baseUrl . "/v1/rates/couriers", $data);
  
        return response()->json($response->json());
      } catch (\Exception $e){
        return response()->json(["error" => $e->getMessage()]);
      }
    }

    public function createOrder(Request $request){
      $permohonan_id = $request->permohonanId;
      $metode_ambil = $request->metodeAmbil;
      $catatan = $request->catatan;
      $permohonan = Permohonan::with('user', 'dokumen', 'pembayaran')->where('permohonan_id', $permohonan_id)->first();
      $kampus = Pengaturan::all()->keyBy('nama');

      $pemohonId = $permohonan->user->id;
      $pemohonEmail = $permohonan->user->email;
      $adminId = Auth::id();
      $admin = User::find($adminId);
      $alamatPemohon = Alamat::find($permohonan->pembayaran->alamat_id);
      $alamatLengkapPemohon = "{$alamatPemohon->kelurahan}, {$alamatPemohon->kecamatan}, {$alamatPemohon->kabupaten}, {$alamatPemohon->provinsi}, Indonesia";

      $data = [
        "origin_contact_name"       => $admin->name,
        "origin_contact_phone"      => $admin->phone,
        "origin_address"            => $kampus['alamat_kampus']->nilai,
        "origin_note"               => $kampus['catatan_alamat_kampus']->nilai,
        "origin_postal_code"        => $kampus['kodepos_kampus']->nilai,
        "origin_collection_method"  => $metode_ambil,
        "destination_contact_name"  => $permohonan->user->name,
        "destination_contact_phone" => $permohonan->user->phone,
        "destination_contact_email" => $permohonan->user->email,
        "destination_address"       => $alamatLengkapPemohon,
        "destination_note"          => $alamatPemohon->catatan,
        "destination_postal_code"   => $alamatPemohon->kode_pos,
        "courier_company"           => $permohonan->kurir,
        "courier_type"              => $permohonan->tipe_kurir,
        "delivery_type"             => "now",
        "delivery_date"             => now()->format('Y-m-d'),
        "delivery_time"             => now()->format('H:i'),
        "order_note"                => $catatan,
        "items"                     => [
          [
            "name" => "Dokumen Legalisir",
            "value" => $permohonan->pembayaran->jumlah_bayar,
            "quantity" => "1",
            "weight" => "100",
          ]
        ]
      ];

      try {
        $response = Http::withHeaders([
          'Authorization' => $this->apiKey,
          'Content-Type' => 'application/json',
        ])->post($this->baseUrl . "/v1/orders", $data);

        $metode_kirim = $permohonan->pembayaran->metode_pengiriman;
        $body = '';
        if($metode_kirim == "Dikirim ke Rumah"){
          $body = 'Legalisir telah selesai, dokumen anda dalam proses pengiriman.';
        } else if($metode_kirim == "Diambil di Kampus"){
          $body = 'Legalisir telah selesai, dokumen anda siap diambil.';
        }

        $data = [
          'subject' => 'Permohonan Legalisir',
          'title' => 'Status Pengajuan Permohonan Legalisir',
          'body' => $body,
        ];

        Mail::to($pemohonEmail)->send(new MyEmail($data));

        Permohonan::where('permohonan_id', $permohonan_id)
            ->update([
                'status_permohonan' => 'Pengiriman / Pengambilan Dokumen'
            ]);
  
        return response()->json(['permohonan_id' => $permohonan_id, 'response' => $response->json(), 'metode_ambil' => $metode_ambil]);
      } catch (\Exception $e){
        return response()->json(["error" => $e->getMessage()]);
      }
    }

    public function storeTracking(Request $request){
      $tracking_id = $request->trackingId;
      $order_id = $request->orderId;
      $waybill_id = $request->waybillId;
      $catatan_pengiriman = $request->catatan;
      $permohonan_id = $request->permohonanId;
      $metode_ambil = $request->metodeAmbil;
      try {
        DB::table('permohonan')->where('permohonan_id', $permohonan_id)->update([
          'tracking_id' => $tracking_id, 
          'order_id' => $order_id,
          'waybill_id' => $waybill_id,
          'catatan_pengiriman' => $catatan_pengiriman,
          'metode_pengambilan_terpilih' => $metode_ambil,
        ]);
        if($metode_ambil == "pickup"){
          return response()->json(['success' => 'Pengiriman berhasil diproses, silahkan tunggu kurir menjemput paket.']);
        } else if($metode_ambil == "drop_off"){
          return response()->json(['success' => 'Pengiriman berhasil diproses, silahkan serahkan paket anda ke kantor terdekat.']);
        }
      } catch (\Exception $e){
        return response()->json(["error" => $e->getMessage()]);
      }
    }

    public function trackOrder(Request $request){
      try {
        $permohonanId = $request->id;
        $permohonan = Permohonan::where('permohonan_id', $permohonanId)->first();
        $trackingId = $permohonan->tracking_id;
        return response()->json(['success' => 'OK', 'url' => "https://track.biteship.com/{$trackingId}?environment=development"]);
      } catch (\Exception $e){
        return response()->json(["error" => $e->getMessage()]);
      }
    }

    public function selesai(Request $request){
      $user_id = Auth::id();
      $permohonan_id = $request->permohonan_id;

      try {
        Permohonan::where('permohonan_id', $permohonan_id)
            ->update([
                'status_permohonan' => 'Selesai'
            ]);
        return response()->json(['success' => "Proses legalisir berhasil diselesaikan"]);
      } catch(\Exception $e){
        return response()->json(["error" => $e->getMessage()]);
      }
    }
    
    public function notifDoku(Request $request){
        // Ambil data penting
        $invoiceNumber = $request->input('order.invoice_number');
        $status = $request->input('transaction.status');

        // Contoh pencarian order berdasarkan invoice_number
        $order = Pembayaran::where('id', $invoiceNumber)->first();

        if (!$order) {
            Log::error('DOKU Order not found', ['invoice' => $invoiceNumber]);
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Update status jika transaksi berhasil
        if ($status === 'SUCCESS') {
            $order->status = 'success';
            $order->snap_token = '';
            $order->save();

            Log::info('Order marked as paid', ['invoice' => $invoiceNumber]);
        }

        // Kirim response OK
        return response()->json(['message' => 'Notification processed'], 200);
    }
}
