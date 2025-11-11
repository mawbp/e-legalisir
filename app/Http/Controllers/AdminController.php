<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\MyEmail;
use App\Models\Registrasi;
use App\Models\Pengumuman;
use App\Models\Pengaturan;
use App\Models\Alumni;
use App\Models\Prodi;
use App\Models\Dokumen;
use App\Models\Pembayaran;
use App\Models\Permohonan;
use App\Models\Alamat;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use Midtrans\Config;
use App\Imports\AlumniImport;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;

class AdminController extends Controller
{
    //
    public function showLoginForm()
    {
      $pengaturan = Pengaturan::all()->keyBy('nama');
      return view('auth.admin', ['pengaturan' => $pengaturan]);
    }

    public function tampilDashboard(Request $request)
    {
      $start_date;
      $end_date;
      $days;
      $total;
      $format_uang;
      $baru;
      $diproses;
      $selesai;
      $si;
      $mat;
      $total_si;
      $total_mat;
      $rentang;
      $rentang_prodi;
      $data_bayar;
      $label_prodi;
      $labels_bayar;
      $totals_bayar;
      $persen;
      $unit;
      $result_mat;
      $data_prodi;

      if($request->start_date && $request->end_date){
        $start = $request->start_date;
        $end = $request->end_date;
        $start_date = Carbon::parse($start)->startOfDay();
        $end_date = Carbon::parse($end)->endOfDay();
        $days = $start_date->diffInDays($end_date);
        $pembayaran = Pembayaran::whereBetween('created_at', [$start_date, $end_date]);
        $total = Permohonan::whereBetween('created_at', [$start_date, $end_date])->distinct('permohonan_id')->count();
        $total_uang = Pembayaran::whereBetween('created_at', [$start_date, $end_date])->where('status_pembayaran', 'success')->sum('jumlah_bayar');
        $format_uang = number_format($total_uang, 2, ',', '.');
        $rentang = $start_date->format('M d') . ' - ' . $end_date->format('M d');
        
        $baru = Permohonan::whereBetween('created_at', [$start_date, $end_date])->where('status_permohonan', 'Validasi Dokumen')->distinct('permohonan_id')->count();
        $selesai = Permohonan::whereBetween('created_at', [$start_date, $end_date])->where('status_permohonan', 'Selesai')->distinct('permohonan_id')->count();
        $diproses = Permohonan::whereBetween('created_at', [$start_date, $end_date])->whereNotIn('status_permohonan', ['Validasi Dokumen', 'Permohonan Gagal', 'Selesai'])->distinct('permohonan_id')->count();
        $persen = ($total > 0) ? round(($selesai / $total) * 100) : 0;
      } else {
        $tgl_awal = Permohonan::orderBy('created_at', 'asc')->first();
        $tgl_akhir = Permohonan::orderBy('created_at', 'desc')->first();
        $start_date = Carbon::parse($tgl_awal->created_at)->startOfDay();
        $end_date = Carbon::parse($tgl_akhir->created_at)->endOfDay();
        $days = $start_date->diffInDays($end_date);
        if($tgl_awal && $tgl_akhir){
          $rentang = Carbon::parse($tgl_awal->created_at)->format('M d')  . ' - ' . Carbon::parse($tgl_akhir->created_at)->format('M d');;
        } else {
          $rentang = Carbon::now()->format('M d');
        }

        $total = Permohonan::distinct('permohonan_id')->count('permohonan_id');
        $total_uang = Pembayaran::where('status_pembayaran', 'success')->sum('jumlah_bayar');
        $format_uang = number_format($total_uang, 2, ',', '.');

        $baru = Permohonan::where('status_permohonan', 'Validasi Dokumen')->distinct('permohonan_id')->count('permohonan_id');
        $selesai = Permohonan::where('status_permohonan', 'Selesai')->distinct('permohonan_id')->count();
        $dikirim = Permohonan::where('status_permohonan', 'Proses Pengiriman / Pengambilan Dokumen')->count();
        $diproses = Permohonan::whereNotIn('status_permohonan', ['Validasi Dokumen', 'Selesai'])->distinct('permohonan_id')->count();
        $persen = ($total > 0) ? round(($selesai / $total) * 100) : 0;
      }
        
        $label_prodi = [];
        $ranges = [];
        $dates = collect();
        $current = $start_date->copy();
        
        if($days <= 7){
            while($current->lte($end_date)){
                $label_prodi[] = $current->format('Y-m-d');
                $ranges[] = [
                    'start' => $current->copy()->startOfDay(),
                    'end' => $current->copy()->endOfDay(),
                ];
                $current->addDay();
            }
            
            $unit = "(Per hari)";
        } else if($days <= 31){
            while($current->lte($end_date)){
                $start_minggu = $current->copy()->startOfWeek();
                $end_minggu = $current->copy()->endOfWeek();
                $label_prodi[] = $start_minggu->format('d-m-Y') . ' s.d ' . $end_minggu->format('d-m-Y');
                $ranges[] = [
                    'start' => $start_minggu,
                    'end' => $end_minggu,
                ];
                $current->addWeek();
            }
            
            $unit = "(Per Minggu)";
        } else if($days <= 365){
            while($current->lte($end_date)){
                $label_prodi[] = $current->format('F Y');
                $ranges[] = [
                    'start' => $current->copy()->startOfMonth(),
                    'end' => $current->copy()->endOfMonth(),
                ];
                $current->addMonth();
            }
            
            $unit = "(Per bulan)";
        } else {
            while($current->lte($end_date)){
                $label_prodi[] = $current->format('Y');
                $ranges[] = [
                    'start' => $current->copy()->startOfYear(),
                    'end' => $current->copy()->endOfYear(),
                ];
                $current->addYear();
            }
        }
        
        $prodi = Prodi::all();
        $data_prodi = [];
        foreach($prodi as $p){
            $counts = [];
            foreach($ranges as $range){
                $count = Permohonan::with('user.alumni')
                    ->whereBetween('created_at', [$range['start'], $range['end']])
                    ->whereHas('user.alumni', function($q) use ($p){
                        $q->where('prodi', $p->nama_prodi);
                    })
                    ->distinct('permohonan_id')
                    ->count('permohonan_id');
                    
                $counts[] = $count; 
            }
            
            $data_prodi[$p->nama_prodi] = $counts;
        }
    
         $data_bayar = Pembayaran::whereBetween('created_at', [$start_date, $end_date])
                ->where('status_pembayaran', 'success')
                ->selectRaw('DATE(created_at) as label, sum(jumlah_bayar) as total')
                ->groupBy('label')
                ->orderBy('label')
                ->get();


        return view('admin.dashboard', [
          'total' => $total,
          'total_uang' => $format_uang,
          'baru' => $baru,
          'diproses' => $diproses,
          'selesai' => $selesai,
          'label_prodi' => $label_prodi,
          'data_prodi' => $data_prodi,
          'rentang' => $rentang,
          'labels_bayar' => $data_bayar->pluck('label'),
          'totals_bayar' => $data_bayar->pluck('total'),
          'persen'  => $persen,
          'unit'    => $unit,
        ]);
    }

    public function getPermohonan(Request $request){
      $query = Permohonan::query()
        ->select([
          'permohonan.permohonan_id',
          'alumni.nim',
          'alumni.nama',
          'permohonan.status_permohonan',
          'permohonan.created_at',
          DB::raw('MAX(permohonan.created_at) as tanggal_terakhir'),
          DB::raw('COUNT(permohonan.id) as jumlah_permohonan')
        ])
        ->join('users', 'permohonan.user_id', '=', 'users.id')
        ->join('alumni', 'users.alumni_id', '=', 'alumni.id')
        ->groupBy('permohonan.permohonan_id', 'alumni.nim', 'alumni.nama', 'permohonan.status_permohonan', 'permohonan.created_at')
        ->orderBy('permohonan.created_at', 'desc');

        if($request->filled('status')){
          $query->where('status_permohonan.nama_status', $request->status);
        }

        if($request->filled('search.value')){
          $search = $request->search['value'];
          $query->where(function($q) use ($search){
            $q->where('permohonan.permohonan_id', 'like', "%$search%")
              ->orWhere('alumni.nim', 'like', "%$search%")
              ->orWhere('alumni.nama', 'like', "%$search%");
          });
        }

        $totalRecords = Permohonan::count();
        $filteredQuery = clone $query;
        $filteredCount = DB::table(DB::raw("({$filteredQuery->toSql()}) as sub"))
                          ->mergeBindings($filteredQuery->getQuery())
                          ->count();

        $data = $query->skip($request->start)
                  ->take($request->length)
                  ->get();

        return response()->json([
          'draw' => $request->draw,
          'recordsTotal' => $totalRecords,
          'recordsFiltered' => $filteredCount,
          'data' => $data->map(function($item){
            return [
              'permohonan_id' => $item->permohonan_id,
              'nim' => $item->nim,
              'nama' => $item->nama,
              'tanggal' => $item->created_at,
              'status' => $item->status_permohonan
            ];
          })
        ]);
    }

    public function getMohon($id){
      try {
        $permohonan = Permohonan::with(['dokumen', 'pembayaran', 'user.alumni'])
        ->where('permohonan_id', $id)
        ->get();
        $permohonan_only = Permohonan::where('permohonan_id', $id)->first();
        $metode_ambil = json_decode($permohonan[0]->metode_pengambilan);
        $valid = true;
        $dokumenArray = [];
        
        foreach ($permohonan as $p) {
            $slug = Str::slug($p->dokumen->nama_dokumen, '_');
            if($p->dokumen->pemilik == "Alumni"){
                $found = false;
            
                // Coba beberapa ekstensi
                $possibleExtensions = ['jpg', 'png', 'jpeg', 'pdf'];
            
                foreach ($possibleExtensions as $ext) {
                    $filename = $p->user->alumni->kode_file .'_'. $p->user->alumni->nim .'_'. $slug .'.'. $ext;
                    $filePath = '/home/fstunipd/public_html/uploads/' . $filename;
            
                    if (file_exists($filePath)) {
                        $dokumenArray[$slug] = '/uploads/' . $filename;
                        $found = true;
                        break;
                    }
                }
            
                if (!$found) {
                    $dokumenArray[$slug] = '/uploads/' . $p->user->alumni->kode_file .'_'. $p->user->alumni->nim .'_'. $slug .'.jpg';
                    $valid = false;
                }    
            } else if($p->dokumen->pemilik == "Admin"){
                $possibleExtensions = ['jpg', 'png', 'jpeg', 'pdf'];
            
                foreach ($possibleExtensions as $ext) {
                    $filename = $slug .'.'. $ext;
                    $filePath = '/home/fstunipd/public_html/uploads/' . $filename;
            
                    if (file_exists($filePath)) {
                        $dokumenArray[$slug] = '/uploads/' . $filename;
                        break;
                    }
                }
            }
        }   
        
        return view('admin.detail', ["permohonan" => $permohonan, "ambil" => $metode_ambil, "valid" => $valid, "dokumen" => $dokumenArray]);
      } catch (\Exception $e){
        dd($e);
      }
    }

    public function getBiaya($id){
      try {
        $permohonan = Permohonan::with(['dokumen', 'pembayaran', 'user.alumni'])
        ->where('permohonan_id', $id)
        ->get();
        $pengaturan = Pengaturan::all()->keyBy('nama');
        return view('admin.biaya', compact('permohonan', 'pengaturan'));
      } catch (\Exception $e){
        abort(404);
      }
    }

    public function getKirim($id){
      try {
        $permohonan = Permohonan::with(['dokumen', 'pembayaran', 'user.alumni'])
        ->where('permohonan_id', $id)
        ->get();
        $metode_ambil = json_decode($permohonan[0]->metode_pengambilan);
        $user = User::where('id', $permohonan[0]->user_id)->first();
        $alamat = Alamat::where('id', $user->alamat_id)->first();
        $alamat_user = "{$alamat->kelurahan}, {$alamat->kecamatan}, {$alamat->kabupaten}, {$alamat->provinsi}, {$alamat->kode_pos}";
        $catatan_user = $alamat->catatan;
        $pengaturan = Pengaturan::all()->keyBy('nama');
        return view('admin.kirim', [
          'permohonan' => $permohonan,
          'pengaturan' => $pengaturan,
          'alamat_user' => $alamat_user,
          'catatan_user' => $catatan_user,
          'ambil' => $metode_ambil,
        ]);
      } catch (\Exception $e){
        abort(404);
      }
    }

    public function bayarKurir(Request $request)
    {
      $permohonan_id = $request->mohonId;
      $permohonan = Permohonan::with('pembayaran')->where('permohonan_id', $permohonan_id)->first();
      Config::$serverKey = config('services.midtrans.server_key');
      Config::$clientKey = config('services.midtrans.client_key'); 
      Config::$isProduction = config('services.midtrans.is_production');
      Config::$isSanitized = config('services.midtrans.is_sanitized');
      Config::$is3ds = config('services.midtrans.is_3ds');

      $transaction_details = array(
        'order_id'     => rand(),
        'gross_amount' => $permohonan->pembayaran->biaya_kurir
      );

      $items[] = [
        'id'  => $permohonan_id,
        'price' => $permohonan->pembayaran->biaya_kurir,
        'quantity' => 1,
        'name'  => 'Biaya Kurir',
      ];

      $customer_details = array(
        'first_name' => Auth::user()->name,
        'email'      => Auth::user()->email,
        'phone'      => Auth::user()->phone,
      );

      $transaction_data = array(
        'payment_type'        => 'payment_gateway',
        'transaction_details' => $transaction_details,
        'customer_details'    => $customer_details,
      );

      $snapToken = \Midtrans\Snap::getSnapToken($transaction_data);

      return response()->json(['snapToken' => $snapToken]);
    }

    public function getDokumen(){
      try {
        $dokumen = Dokumen::all();
        return response()->json($dokumen);
      } catch (\Exception $e){
        return response()->json(['error' => $e->getMessage()]);
      }
    }

    public function updateStatus(Request $request){
      set_time_limit(0);
      $nim = $request->nim;
      $permohonan_id = $request->permohonanId;
      $pesan_admin = $request->pesanAdmin;
      $pembayaran_id = Permohonan::where('permohonan_id', $permohonan_id)->value('pembayaran_id');
      $user_id = Alumni::where('nim', $nim)->value('user_id');
      $user = User::with('alumni')->where('id', $user_id)->first();
      $pengaturan = Pengaturan::all()->keyBy('nama');
      if(!$pesan_admin){ $pesan_admin = 'Tidak ada.'; }

      try {

        $status = Permohonan::where('permohonan_id', $permohonan_id)->first();
        $nama_status = $status->status_permohonan;
        $email = User::where('id', $user_id)->value('email');
        $data = '';

        if($nama_status == 'Validasi Dokumen') {
          Permohonan::where('permohonan_id', $permohonan_id)->update([
            'catatan' => $pesan_admin,
            'status_permohonan' => 'Menunggu Pembayaran'
          ]);
          $data = [
            'subject' => 'Permohonan Legalisir',
            'title' => 'Status Pengajuan Permohonan Legalisir',
            'body' => "Dokumen anda telah tervalidasi, silahkan menyelesaikan pembayaran melalui dashboard.\nCatatan admin: $pesan_admin",
          ];

          $metodebayar = Pembayaran::where('id', $pembayaran_id)->value('metode_pembayaran');
          $cek_biaya_kurir = Pembayaran::where('id', $pembayaran_id)->value('biaya_kurir');
          $biaya_kurir;
          if(!$cek_biaya_kurir){
            $biaya_kurir = 0;
          } else {
            $biaya_kurir = $cek_biaya_kurir;
          }
          
          Pembayaran::where('id', $pembayaran_id)->update([
            'status_pembayaran' => 'pending',
            'expired_at' => now()->addMinutes(5),
          ]);

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
             'name'  => $user->alumni->nama,
             'phone' => $user->phone,
             'custom_fields' => [
               'email' => $user->email
             ],
           ]);

           $notes = [
             'Terima kasih telah menggunakan Layanan Legalisir Online',
             'Fakultas SAINTEK UNIPDU Jombang',
           ];
           $notes = implode("<br>", $notes);

           $invoice = Invoice::make('receipt')
             ->series('BIG')
             ->status(__('invoices::invoice.due'))
             ->sequence(667)
             ->serialNumberFormat('{SEQUENCE}/{SERIES}')
             ->seller($client)
             ->buyer($customer)
             ->date(now())
             ->dateFormat('Y/m/d')
             ->payUntilDays(1)
             ->currencySymbol('Rp')
             ->currencyCode('IDR')
             ->currencyFormat('{SYMBOL}{VALUE}')
             ->currencyThousandsSeparator('.')
             ->currencyDecimalPoint(',')
             ->filename('invoices/' . $permohonan_id)
             ->addItems($items)
             ->notes($notes)
             ->save('public');

           $filePath = storage_path('app/public/invoices/' . $permohonan_id . '.pdf');

          Mail::to($email)->send(new MyEmail($data, $filePath));
        } else if($nama_status == 'Validasi Pembayaran'){
            Permohonan::where('id', $permohonan_id)->update([
                'catatan' => $pesan_admin,
                'status_permohonan' => 'Proses Legalisir Dokumen'
            ]);
            $data = [
                'subject' => 'Permohonan Legalisir',
                'title' => 'Status Pengajuan Permohonan Legalisir',
                'body' => "Pembayaran anda telah diterima, dokumen anda sedang dalam proses legalisir.\nCatatan admin: $pesan_admin",
            ];

            $filePath = 'public/invoice' . $permohonan_id . '.pdf';
            if(Storage::exists($filePath)){
                Storage::delete($filePath);
            }

            Pembayaran::where('id', $pembayaran_id)->update([
                'status_pembayaran' => 'success',
            ]);
          
            $filePath = 'public/invoice' . $permohonan_id . '.pdf';
            if(Storage::exists($filePath)){
                Storage::delete($filePath);
            }

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
                'name'  => $user->alumni->nama,
                'phone' => $user->phone,
                'custom_fields' => [
                    'email' => $user->email
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
        } else if($nama_status == 'Proses Legalisir Dokumen') {
            Permohonan::where('id', $permohonan_id)->update([
                'catatan' => $pesan_admin,
                'status_permohonan' => 'Pengiriman / Pengambilan Dokumen'
            ]);
            $data = [
                'subject' => 'Permohonan Legalisir',
                'title' => 'Status Pengajuan Permohonan Legalisir',
                'body' => "Proses legalisir selesai, dokumen anda siap dikirim / diambil.\nCatatan admin: $pesan_admin",
            ];

            Pembayaran::where('id', $pembayaran_id)->update([
                'status_pembayaran' => 'success',
            ]);
        }
        return response()->json(['success' => 'Status Permohonan Berhasil Diperbarui']);
      } catch(\Exception $e){
        // return response()->json(['error' => $e->getMessage()]);
        dd($e);
      }
    }

    public function tolakStatus(Request $request)
    {
      $permohonan_id = $request->permohonanId;
      $permohonan = Permohonan::where('permohonan_id', $permohonan_id)->first();
      $nama_status = $permohonan->status_permohonan;
      $email = User::where('id', $permohonan->user_id)->value('email');
      $pesan_admin = $request->pesanAdmin;
      if(!$pesan_admin){ $pesan_admin = 'Tidak ada.'; }
      $data;

      if($nama_status == "Validasi Dokumen"){

        Permohonan::where('permohonan_id', $permohonan_id)->update([
          'status_permohonan' => 'Permohonan Gagal',
          'catatan' => $pesan_admin,
        ]);

        $data = [
          'subject' => 'Permohonan Legalisir',
          'title' => 'Status Pengajuan Permohonan Legalisir',
          'body' => "Permohonan anda gagal diproses, silahkan ajukan ulang permohonan.\nCatatan admin: $pesan_admin",
        ];

      } else if($nama_status == 'Validasi Pembayaran'){
        
        Permohonan::where('id', $permohonan_id)->update([
          'catatan' => $pesan_admin,
          'status_permohonan' => 'Menunggu Pembayaran'
        ]);
        
        Pembayaran::where('id', $permohonan->pembayaran_id)->update(['expired_at' => now()->addMinute()]);

        $data = [
          'subject' => 'Permohonan Legalisir',
          'title' => 'Status Pengajuan Permohonan Legalisir',
          'body' => "Validasi pembayaran gagal, silahkan upload ulang bukti pembayaran.\nCatatan admin: $pesan_admin",
        ];
      } else {
        return response()->json(['error' => 'Terjadi kesalahan']);
      }
      
      Mail::to($email)->send(new MyEmail($data));
      return response()->json(['success' => 'Status Permohonan Berhasil Diperbarui']);
    }

    public function cekNim(Request $request){
      $nim = $request->nim;
      try {
        $cek = Alumni::where('nim', $nim)->exists();
        if($cek){
          return response()->json(['success' => 'Valid']);
        }
        return response()->json(['failed' => 'Tidak Valid']);
      } catch(\Exception $e){
        return response()->json(['error' => $e->getMessage()]);
      }
    }
    
    public function tampilAlumni(){
        $dokumen = Dokumen::all();
        $prodi = Prodi::all();
        
        return view('admin.alumni', compact('dokumen', 'prodi'));
    }

    public function imporAlumni(Request $request)
    {
      set_time_limit(0);
      $validator = Validator::make($request->all(), [
        'file' => 'required|file|mimes:xlsx,xls'
      ]);

      if($validator->fails()){
        return response()->json([
          'validation' => $validator->errors()->first()
        ]);
      }

      if($request->hasFile('file')){
        $file = $request->file('file');
        
        $import = new AlumniImport;
        try {
          Excel::import($import, $file);
          $msg = "Berhasil mengimpor seluruh data";

          if(!empty($import->errors)){
            $msg = "Hanya {$import->successCount} data berhasil diimpor";
            return response()->json(['warning' => $msg, 'errors' => $import->errors]);
          }

          return response()->json(['success' => $msg]);
        } catch(\Exception $e) {
          return response()->json(['error' => $e->getMessage]);
        }
      } else {
        return response()->json(["validation" => "File tidak ditemukan"]);
      }
    }
    
    public function uploadScan(Request $request){
        set_time_limit(0);
        $request->validate([
            'dokumen' => 'required',
            'file' => 'required|mimes:zip'
        ]);
        
        $zipFile = $request->file('file');
        $originalName = $zipFile->getClientOriginalName();
        $destinationPath = storage_path('app/temp/');
        
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }
        
        $zipFile->move($destinationPath, $originalName);
        
        // Path lengkap ke file ZIP
        $zipFullPath = $destinationPath . '/' . $originalName;
        
        $tempExtractPath = storage_path('app/temp_scan');
        if(!File::exists($tempExtractPath)){
            File::makeDirectory($tempExtractPath, 0755, true);
        } else {
            File::cleanDirectory($tempExtractPath);
        }
        
        $zip = new ZipArchive;
        if($zip->open($zipFullPath) === TRUE){
            $zip->extractTo($tempExtractPath);
            $zip->close();
        } else {
            return response()->json(['validation' => 'Gagal membuka ZIP', 'error' => $zip->open($zipFullPath)]);
        }
        
        $files = File::files($tempExtractPath);
        foreach($files as $file){
            $originalName = $file->getFilenameWithoutExtension();
            $extension = $file->getExtension();
            
            $nim = preg_replace('/\D/', '', $originalName);
            $kode = Alumni::where('nim', $nim)->value('kode_file');
            
            if($kode){
                $namaBaru = $kode .'_'. $nim .'_'. $request->dokumen . '.' . $extension;
                $full_path = '/home/fstunipd/public_html/uploads/' . $namaBaru;
                
                if(file_exists($full_path)){
                    unlink($full_path);
                }
        
                File::move($file->getPathname(), $full_path);
            } else {
                return response()->json(['validation' => 'Data alumni tidak valid']);
            }
        }
        
        return response()->json(['success' => 'Scan dokumen berhasil diupload']);
    }

    public function editProfil(Request $request)    
    {
      $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'phone' => 'required|numeric|digits_between:10,15',
        'password' => 'nullable|min:8'
      ]);
    }

    public function editKampus(Request $request)
    {
      $request->validate([
        'kampus' => 'required|string|max:255',
        'fak' => 'required|string|max:255',
        'email' => 'required|email',
        'phone' => 'required|numeric|digits_between:10,15',
        'address' => 'string'
      ]);

      $kampus = $request->kampus;
      $fak = $request->fak;
      $email = $request->email;
      $phone = $request->phone;
      $address = $request->address;

      try {
        Pengaturan::where('nama', 'nama_kampus')->update(['nilai' => $kampus]);
        Pengaturan::where('nama', 'nama_fakultas')->update(['nilai' => $fak]);
        Pengaturan::where('nama', 'email_kampus')->update(['nilai' => $email]);
        Pengaturan::where('nama', 'phone_kampus')->update(['nilai' => $phone]);
        return redirect()->back()->with('success', 'Data berhasil disimpan');
      } catch(\Exception $e){
        return redirect()->back()->with('error', 'Data gagal disimpan');
      }
    }

    public function simpanAlamat(Request $request)
    {
      $request->validate([
        'provinsi' => 'required|string',
        'kabupaten' => 'required|string',
        'kecamatan' => 'required|string',
        'kelurahan' => 'string|required',
        'kodepos' => 'string|required',
        'catatan' => 'string|nullable',
      ]);

      $provinsi = $request->provinsi;
      $kabupaten = $request->kabupaten;
      $kecamatan = $request->kecamatan;
      $kelurahan = $request->kelurahan;
      $kodepos = $request->kodepos;
      $catatan = $request->catatan;

      try {
        Pengaturan::where('nama', 'alamat_kampus')->update(['nilai' => "{$kelurahan}, {$kecamatan}, {$kabupaten}, {$provinsi}, {$kodepos}"]);
        Pengaturan::where('nama', 'catatan_alamat_kampus')->update(['nilai' => $catatan]);
        Pengaturan::where('nama', 'kodepos_kampus')->update(['nilai' => $kodepos]);
        return redirect()->back()->with('success', 'Data berhasil disimpan');
      } catch(\Exception $e){
        return redirect()->back()->with('error', 'Data gagal disimpan');
      }
    }

    public function editBiaya(Request $request)
    {
        $request->validate([
            'biaya' => 'required|array',
            'biaya.*' => 'required',
            'admin' => 'required|string|min:0'
        ]);
      
        foreach ($request->biaya as $id => $nilai) {
            Dokumen::where('id', $id)->update([
                'harga_per_lembar' => $nilai
            ]);
        }
        
      $admin = $request->admin;
      
      try {
        Pengaturan::where('nama', 'biaya_admin')->update(['nilai' => $admin]);
        return redirect()->back()->with('success', 'Data berhasil disimpan');
      } catch(\Exception $e){
        return redirect()->back()->with('error', 'Data gagal disimpan');
      }
    }

    public function editLegal(Request $request)
    {
      $request->validate([
        'cetak' => 'required|numeric',
      ]);

      $cetak = $request->cetak;
      
      try {
        Pengaturan::where('nama', 'maksimal_cetak')->update(['nilai' => $cetak]);
        return redirect()->back()->with('success', 'Data berhasil disimpan');
      } catch(\Exception $e){
        return redirect()->back()->with('error', 'Data gagal disimpan');
      }
    }

     public function editPembayaran(Request $request)
    {
      $request->validate([
        'bank' => 'required|string',
        'norek' => 'required|numeric',
        'pg'  => $request->switch ? 'required' : 'nullable',
        'switch' => 'boolean',
        'skey_midtrans' => 'nullable|string',
        'ckey_midtrans' => 'required|string',
        'skey_doku' => 'nullable|string',
        'ckey_doku' => 'nullable|string',
      ]);
      
        if ($request->pg == 'midtrans') {
            $request->validate([
                'skey_midtrans' => 'required|string',
                'ckey_midtrans' => 'required|string',
            ]);
        }
        
        if ($request->pg == 'doku') {
            $request->validate([
                'skey_doku' => 'required|string',
                'ckey_doku' => 'required|string',
            ]);
        }

      $bank = $request->bank;
      $norek = $request->norek;
      $skey_midtrans = $request->skey_midtrans;
      $ckey_midtrans = $request->ckey_midtrans;
      $skey_doku = $request->skey_doku;
      $ckey_doku = $request->ckey_doku;
      $opsi_pg;
      $pg;
      if($request->switch){
        $opsi_pg = 'on';
        $pg = $request->pg;
      } else {
        $opsi_pg = 'off';
        $pg = '';
      }
      
      try {
        Pengaturan::where('nama', 'nama_bank')->update(['nilai' => $bank]);
        Pengaturan::where('nama', 'no_rekening')->update(['nilai' => $norek]);
        Pengaturan::where('nama', 'payment_gateway')->update(['nilai' => $pg]);
        Pengaturan::where('nama', 'opsi_pg')->update(['nilai' => $opsi_pg]);
        Pengaturan::where('nama', 'skey_midtrans')->update(['nilai' => $skey_midtrans]);
        Pengaturan::where('nama', 'ckey_midtrans')->update(['nilai' => $ckey_midtrans]);
        Pengaturan::where('nama', 'skey_doku')->update(['nilai' => $skey_doku]);
        Pengaturan::where('nama', 'ckey_doku')->update(['nilai' => $ckey_doku]);
        return redirect()->back()->with('success', 'Data berhasil disimpan');
      } catch(\Exception $e){
        return redirect()->back()->with('error', 'Data gagal disimpan');
      }
    }

    public function dataLaporan(Request $request)
    {
      $start_date = $request->start_date;
      $end_date = $request->end_date;
      $status = $request->status;
      $metode = $request->metode;
      $query = DB::table('view_master_transaksi');

      if($start_date){
        $query->whereDate('tanggal_permohonan', '>=', $start_date);
      }

      if($end_date){
        $query->whereDate('tanggal_permohonan', '<=', $end_date);
      }

      if($status){
        $query->where('status_pembayaran', $status);
      }

      if($metode){
        if($metode == "tf_ambil"){
          $query->where('metode_pembayaran', 'Transfer Bank')->where('metode_pengiriman', 'Diambil di Kampus');
        } else if($metode == "tf_kirim"){
          $query->where('metode_pembayaran', 'Transfer Bank')->where('metode_pengiriman', 'Dikirim ke Rumah');
        } else if($metode == "pg_ambil"){
          $query->where('metode_pembayaran', 'Payment Gateway')->where('metode_pengiriman', 'Diambil di Kampus');
        } else if($metode == "pg_kirim"){
          $query->where('metode_pembayaran', 'Payment Gateway')->where('metode_pengiriman', 'Dikirim ke Rumah');
        }
      }

      $data = $query->get();
      return response()->json($data);
    }
    
    public function unduhInvoice($id, $status){
        if($status == 'success'){
            $filepath = storage_path("app/public/invoices/{$id}_paid.pdf");
            if (!file_exists($filepath)) {
                abort(404, 'Invoice tidak ditemukan');
            }
        
            return response()->download($filepath);
        } else if($status == 'pending'){
            $filepath = storage_path("app/public/invoices/{$id}.pdf");
            if (!file_exists($filepath)) {
                abort(404, 'Invoice tidak ditemukan');
            }
        
            return response()->download($filepath);
        } else {
            abort(404, 'Tidak Ditemukan');
        }
    }

    public function tampilLaporan(Request $request)
    {
      return view('admin.laporan');
    }

    public function regisIndex()
    {
      $regis = Registrasi::orderBy('created_at', 'desc')->get();
      return response()->json($regis);
    }

    public function umumIndex()
    {
      $umum = Pengumuman::all();
      return response()->json($umum);
    }
    
    public function lihatBerkas($id){
        $data = Registrasi::where('id', $id)->first();
        $kode_file = Alumni::where('nim', $data->nim)->value('kode_file');
        $url;
        $possibleExtensions = ['jpg', 'png', 'jpeg'];
        try {
            foreach ($possibleExtensions as $ext) {
                $filename = $kode_file .'_'. $data->nim .'_ijazah' .'.'. $ext;
                $filePath = 'uploads/' . $filename;
        
                if (file_exists($filePath)) {
                    $url = asset('uploads/' . $filename);
                }
                
            }
            
            return response()->json(['success' => 'ok', 'url' => $url]);
        } catch (\Exception $e){
            return response()->json(['error' => 'ok']);
        }
    }

    public function setujuRegis($id)
    {
      $data = Registrasi::where('id', $id)->first();
      $alumni_id = Alumni::where('nim', $data->nim)->value('id');
      $password = mt_rand(1000000, 9999999);

      $user = User::create([
        "name" => $data->nama,
        "email" => $data->email,
        "alumni_id" => $alumni_id,
        "role" => "user",
        "password" => Hash::make($password),
      ]);

      Alumni::where('nim', $data->nim)->update([
        'user_id' => $user->id
      ]);

      Registrasi::where('id', $id)->update([
        'status' => "Disetujui"
      ]);

     $content = [
        'subject' => 'Registrasi Akun Legalisir Online UNIPDU',
        'title' => 'Registrasi Akun Anda Berhasil',
        'body' => 'Silahkan login ke akun anda dengan password berikut: ' . $password,
      ];

      Mail::to($data->email)->send(new MyEmail($content));
      return response()->json(['success' => 'ok']);
    }

    public function tolakRegis($id)
    {
      Registrasi::where('id', $id)->update([
        'status' => "Ditolak"
      ]);

     $content = [
        'subject' => 'Registrasi Akun Legalisir Online UNIPDU',
        'title' => 'Registrasi Akun Anda gagal',
        'body' => 'Maaf, akun anda gagal terdaftar karena nomor ijazah tidak valid',
      ];

      Mail::to($data->email)->send(new MyEmail($content));
      return response()->json(['success' => 'ok']);
    }

    public function tambahUmum(Request $request)
    {
      $request->validate([
        'judul' => 'required|string',
        'durasi' => 'required|integer',
        'editor' => 'required',
      ]);
      
      $durasi = now()->addDays($request->durasi);

      Pengumuman::create([
        'judul' => $request->judul,
        'expired_at' => $durasi,
        'isi' => $request->editor,
      ]);

      return redirect()->route('admin.pengumuman')->with('success', 'Pengumuman berhasil disimpan');
    }
    
     public function tampilUmum($id)
     {
         $pengumuman = Pengumuman::where('id', $id)->first();
         return response()->json($pengumuman);
     }
     
     public function editUmum(Request $request)
     {
          $request->validate([
            'judul' => 'required|string',
            'durasi' => 'required|integer',
            'editor' => 'required',
          ]);
          
          $durasi = now()->addDays($request->durasi);
    
          Pengumuman::where('id', $request->id)->update([
            'judul' => $request->judul,
            'expired_at' => $durasi,
            'isi' => $request->editor,
          ]);
    
          return redirect()->route('admin.pengumuman')->with('success', 'Pengumuman berhasil diperbarui');
     }
     
     
}
