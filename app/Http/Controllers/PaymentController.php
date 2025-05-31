<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Permohonan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentController extends Controller
{
    //
    public function __construct()
    {
      
    }

    public function processPayment(Request $request){

      Config::$serverKey = config('services.midtrans.server_key');
      Config::$clientKey = config('services.midtrans.client_key'); 
      Config::$isProduction = config('services.midtrans.is_production');
      Config::$isSanitized = config('services.midtrans.is_sanitized');
      Config::$is3ds = config('services.midtrans.is_3ds');


      $user_id = Auth::id();
      $nim = $request->nim;
      $biaya = [];
      $data = $request->dokumen;
      foreach($data as $item){
        foreach($item as $dokumen => $detail){
          $biaya[] = $detail['jumlah_cetak'] * $detail['harga'];
        }
      }
      $biayakirim = $request->biayakirim;
      $metode_pembayaran = $request->metodebayar;
      $metode_pengiriman = $request->metodekirim;
      $alamat = $request->alamat;
      $kurir = $request->kurir;
      $tipe_kurir = $request->tipe_kurir;
      $biaya_total = array_sum($biaya) + $biayakirim;

      DB::beginTransaction();

      try {

        $pembayaran = Pembayaran::create([
          'metode_pembayaran' => $metode_pembayaran,
          'jumlah_bayar'      => $biaya_total,
          'status_pembayaran' => 'berhasil',
          'bukti_pembayaran'  => '',
          'tanggal_bayar'     => now()
        ]);

        foreach($data as $item){
          foreach($item as $dokumen => $detail){
            Permohonan::create([
              'user_id'              => $user_id,
              'nim'                  => $nim,
              'nomor_permohonan'     => $detail['nomer'],
              'jumlah_cetak'         => $detail['jumlah_cetak'],
              'metode_pengiriman'    => $metode_pengiriman,
              'dokumen_id'           => $dokumen,
              'status_permohonan_id' => 1,
              'pembayaran_id'        => $pembayaran->id,
              'kurir'                => $kurir,
              'tipe_kurir'           => $tipe_kurir,
            ]);
          }
        }

        DB::commit();

        $transaction_details = array(
          'order_id'     => time(),
          'gross_amount' => $biaya_total
        );

        $items = [];

        foreach($data as $item){
          foreach($item as $dokumen => $detail){
            $items[] = [
              'id'            => time(),
              'price'         => 5000,
              'quantity'      => $detail['jumlah_cetak'],
              'name'  => $dokumen,
            ];
          }
        }

        $customer_details = array(
          'first_name'      => Auth::user()->name,
          'email'     => Auth::user()->email,
          'phone' => "08763192318237",
        );

        $transaction_data = array(
          'payment_type'        => 'payment_gateway',
          'transaction_details' => $transaction_details,
          'customer_details'    => $customer_details
        );
  
        $snapToken = \Midtrans\Snap::getSnapToken($transaction_data);
        return response()->json(['snapToken' => $snapToken]);
      } catch(\Exception $e) {
        DB::rollBack();

        return response()->json(['message' => 'Terjadi Kesalahan', 'error' => $e->getMessage()], 500);
      }
    }
}
