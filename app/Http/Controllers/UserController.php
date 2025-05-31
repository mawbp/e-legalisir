<?php

namespace App\Http\Controllers;

use App\Models\Registrasi;
use App\Models\Pengumuman;
use App\Models\Pengaturan;
use App\Models\Alamat;
use App\Models\Alumni;
use App\Models\Dokumen;
use App\Models\Permohonan;
use App\Models\Pembayaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use function Pest\Laravel\json;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class UserController extends Controller
{
    //
    public function showLoginForm()
    {
      return view('auth.user');
    }

    public function showDashboard()
    {
      $user_id = Auth::id();
      $data = Permohonan::select('permohonan_id', DB::raw('count(*) as total'))
        ->where('user_id', $user_id)
        ->whereNotIn('status_permohonan', ['Permohonan Gagal', 'Selesai'])
        ->groupBy('permohonan_id')
        ->get();
      $dokumen = Dokumen::all();

      $permohonanku = [];

      foreach($data as $d){
        $permohonan_id = Permohonan::with(['dokumen', 'pembayaran'])->where('permohonan_id', $d->permohonan_id)->get();
        
        $permohonan = [
          'permohonan_id' => $d->permohonan_id,
          'pembayaran_id' => $permohonan_id[0]->pembayaran_id,
          'tanggal'       => $permohonan_id[0]->created_at,
          'catatan'       => $permohonan_id[0]->catatan,
          'status'        => $permohonan_id[0]->status_permohonan,
          'pengiriman'    => $permohonan_id[0]->pembayaran->metode_pengiriman,
          'pembayaran'    => $permohonan_id[0]->pembayaran->metode_pembayaran,
          'status_bayar'  => $permohonan_id[0]->pembayaran->status_pembayaran,
          'total_biaya'   => $permohonan_id[0]->pembayaran->jumlah_bayar,
          'expired_at'    => $permohonan_id[0]->pembayaran->expired_at,
          'dokumen'       => []
        ];

        // if($permohonan_id[0]->pembayaran->expired_at > Carbon::now()){
        //   $permohonan['token'] = $permohonan_id[0]->pembayaran->snap_token;
        // } else {
        //   $permohonan['token'] = '';
        // }

        foreach($permohonan_id as $p){
          $permohonan['dokumen'][] = [
            'nama_dokumen' => $p->dokumen->nama_dokumen,
            'jumlah'       => $p->jumlah_cetak
          ];
        }

        $permohonanku[] = $permohonan;
      }

      $pengaturan = Pengaturan::all()->keyBy('nama');
      
      $pengumuman = Pengumuman::where('expired_at', '>', now())
        ->get()
        ->map(function ($item) {
            $item->created_at_formatted = Carbon::parse($item->created_at)
                ->locale('id')
                ->isoFormat('D MMMM YYYY');
            return $item;
        });

      return view('user.dashboard', compact('permohonanku', 'dokumen', 'pengaturan', 'pengumuman'));
    }
    
    public function getUmum($id){
        try {
            $pengumuman = Pengumuman::where('id', $id)->first();
            $pengumuman->created_at_formatted = Carbon::parse($pengumuman->created_at)
                ->locale('id')
                ->isoFormat('D MMMM YYYY');
            return response()->json(['success' => $pengumuman]);
        } catch(\Exception $e){
            return response()->json(['error' => 'Terjadi kesalahan']);
        }
    }

    public function pengajuan(){
      $dokumen = Dokumen::all();
      $user_id = Auth::id();
      $alamat_id = User::where('id', $user_id)->value('alamat_id');
      $alamat = ALamat::where('id', $alamat_id)->first();
      $alamatAll = Alamat::where('user_id', $user_id)->get();
      $pengaturan = Pengaturan::all()->keyBy('nama');
      return view('user.pengajuan', compact('dokumen', 'alamat', 'alamatAll', 'pengaturan'), ["alamat_id" => $alamat_id]);
    }

    public function cekHarga(Request $request){
      $dokumen = Dokumen::all();
      $data = [];
      foreach($dokumen as $d){
        $data[] = [
          'nama' => $d->nama_dokumen,
          'harga' => $d->harga_per_lembar,
        ];
      }
      return response()->json($data);
    }

    public function ubahAlamat(Request $request)
    {
      try {
        $alamat_id = $request->alamat_id;
        User::where('id', Auth::id())->update([
          'alamat_id' => $alamat_id
        ]);
        $alamat = Alamat::where('id', $alamat_id)->first();
        return response()->json(['success' => "Alamat berhasil diubah", "alamat" => $alamat]);
      } catch(\Exception $e) {
        return response()->json(['error' => "Alamat gagal diubah"]);
      }

    }

    public function simpanAlamat(Request $request){
      $validator = Validator::make($request->all(), [
        'kelurahan' => 'required',
        'kecamatan' => 'required',
        'kabupaten' => 'required',
        'provinsi' => 'required',
        'kodepos' => 'required',
        'catatan' => 'nullable|string',
        'label' => 'nullable|string',
      ]);

      if($validator->fails()){
        return response()->json([
          'validation' => $validator->errors()->first()
        ], 422);
      }

      $user_id = Auth::id();
      $kelurahan = $request->kelurahan;
      $kecamatan = $request->kecamatan;
      $kabupaten = $request->kabupaten;
      $provinsi = $request->provinsi;
      $kodepos = $request->kodepos;
      $catatan = $request->catatan;
      $label = $request->label;

      try {
        $alamat = Alamat::create([
          'user_id'   => $user_id,
          'kelurahan' => $kelurahan,
          'kecamatan' => $kecamatan,
          'kabupaten' => $kabupaten,
          'provinsi'  => $provinsi,
          'kode_pos'  => $kodepos,
          'catatan' => $catatan,
          'label' => $label,
        ]);
        User::where('id', $user_id)->update([
          'alamat_id' => $alamat->id
        ]);
        return response()->json(['success' => 'Alamat Berhasil Disimpan', 'alamat' => $alamat]);
      } catch(\Exception $e){
        return response()->json(['error' => $e->getMessage()]);
      }
    }

    public function alamatUtama(Request $request)
    {
      $alamat_id = $request->alamat_id;
      $user_id = Auth::id();
      User::where('id', $user_id)->update([
        'alamat_id' => $alamat_id
      ]);
      return redirect()->route('user.akun');
    }

    public function hapusAlamat(Request $request)
    {
      $validator = Validator::make($request->all(), [
        'id'        => 'required',
      ]);

      if($validator->fails()){
        return response()->json([
          'validation' => $validator->errors()->first()
        ], 422);
      }

      $alamat_id = $request->id;
      $user = User::find(Auth::id());

      try {
        $alamat = $user->alamat()->where('id', $alamat_id)->first();
        if($alamat){
          $alamat->delete();
        }
        Alamat::where('id', $alamat_id)->delete();
        return response()->json(['success' => 'Alamat Berhasil Dihapus']);
      } catch(\Exception $e){
        return response()->json(['error' => $e->getMessage()]);
      }
    }

    public function dataRiwayat(){
      $user_id = Auth::id();
      $permohonan = Permohonan::with(['dokumen', 'pembayaran'])->where('user_id', $user_id)->get();
      return response()->json($permohonan);
    }

    public function dataCekstatus(){
      $user_id = Auth::id();
      try {

        $permohonan = Permohonan::with(['user', 'dokumen'])
        ->where('permohonan.user_id', $user_id)
        ->where('status_permohonan', '!=', 'Selesai')
        ->join('users', 'permohonan.user_id', '=', 'users.id')
        ->join('alumni', 'users.alumni_id', '=', 'alumni.id')
        ->select('permohonan.permohonan_id',  'alumni.nama', 'permohonan.status_permohonan', DB::raw('DATE(permohonan.created_at) as tanggal'))
        ->groupBy('permohonan.permohonan_id', 'alumni.nama')
        ->orderBy('permohonan.created_at', 'desc')
        ->get();
        return response()->json($permohonan);
      } catch (\Exception $e){
        return response()->json(['error' => $e->getMessage()]);
      }
    }

    public function getMohon($id){
      try {
        $permohonan = Permohonan::with(['user.alumni', 'pembayaran', 'dokumen'])
        ->where('permohonan_id', $id)
        ->get();
        return view('user.detail', compact('permohonan'));
      } catch (\Exception $e){
        return response()->json(['error' => $e->getMessage()]);
      }
    }

    public function getBiaya($id)  
    {
      $permohonan_id = $id;
      $pembayaran_id = Permohonan::where('permohonan_id', $permohonan_id)->value('pembayaran_id');
      $metode = Pembayaran::where('id', $pembayaran_id)->value('metode_pembayaran');

      try {
        $permohonan = Permohonan::with(['pembayaran', 'dokumen'])
          ->where('permohonan_id', $permohonan_id)
          ->get();
        $pembayaran = Pembayaran::where('id', $pembayaran_id)->first();
        
        // if(!$pembayaran->expired_at){
        //   $pembayaran->update([
        //     'expired_at' => now()->addDays(1)
        //   ]);
        // }

        // if($pembayaran->expired_at < Carbon::now()){
        //   $pembayaran->update([
        //     'snap_token' => '',
        //     'expired_at' => now()->addDays(1)
        //   ]);
        // }

        $token = Pembayaran::where('id', $pembayaran_id)->value('snap_token');
        
        $pengaturan = Pengaturan::all()->keyBy('nama');
          return view('user.biaya', [
            'permohonan' => $permohonan,
            'pengaturan' => $pengaturan,
            'pembayaran' => $pembayaran,
            'token' => $token,
            'metode' => $metode,
          ]);
      } catch(\Exception $e){
        dd($e);
      }
    }

    public function ceknim(Request $request){
      $nim = $request->nim;
      $alumni = Alumni::where('nim', $nim)->first();
      if($alumni){
        $user = User::where('alumni_id', $alumni->id)->first();
        if($user){
          return response()->json(['error' => 'Maaf, NIM sudah terdaftar']);
        }
        return response()->json(['success' => 'ok', 'nama' => $alumni->nama, 'prodi' => $alumni->prodi]);
      } else {
        return response()->json(['error' => "Maaf, data anda tidak ditemukan"]);
      }
    }

    public function register(Request $request){
      $nim = $request->nim;
      $nama = $request->nama;
      $email = $request->email;
      $nomor_ijazah = $request->ijazah;

      $alumni_id = Alumni::where('nim', $nim)->value('id');
      $cekdata = User::where('alumni_id', $alumni_id)->exists();
      if($cekdata){
        return redirect()->back()->with('error', 'Maaf, pengguna sudah terdaftar');
      } else {
        Registrasi::create([
          "nim" => $nim,
          "nama" => $nama,
          "email" => $email,
          "nomor_ijazah" => $nomor_ijazah,
          "status" => 'Menunggu',
        ]);
        
        return redirect()->route('start')->with('register', 'Pendaftaran berhasil, silahkan tunggu persetujuan admin, dan password akan dikirim ke email anda.');
      }
    }

    public function sendReset(Request $request)
    {
      $validator = Validator::make($request->all(), [
        'nim' => 'required|string|min:7'
      ]);

      if($validator->fails()){
        return back()->with('error', $validator->errors()->first());
      }

      $nim = $request->nim;
      $alumni = Alumni::where('nim', $nim)->first();
      if(!$alumni){ return back()->with('error', 'NIM tidak ditemukan'); }
      $user = User::where('alumni_id', $alumni->id)->first();
      if(!$user){ return back()->with('error', 'Akun anda tidak ditemukan, silahkan mendaftar terlebih dahulu'); }

      $email = $user->email;
      $token = Str::random(10);

      User::where('alumni_id', $alumni->id)->update([
        'reset_password_token' => $token
      ]);

      $resetLink = url("/user/reset-password?token=$token&nim=$nim");
      Mail::raw("Klik link ini untuk reset password: $resetLink", function($message) use ($email){
        $message->to($email)->subject('Reset Password');
      });

      return back()->with('success', 'Link reset password sudah dikirim');
    }

    public function showResetForm(Request $request)
    {
      return view('auth.reset-pass-user', [
        'token' => $request->token,
        'nim' => $request->nim,
      ]);
    }

    public function resetPassword(Request $request)
    {
      $validator = Validator::make($request->all(), [
        'password' => 'required|string|min:7'
      ]);

      if($validator->fails()){
        return back()->with('error', $validator->errors()->first());
      }
      
      $alumni_id = Alumni::where('nim', $request->nim)->value('id');

      $tokenData = User::where('alumni_id', $alumni_id)
        ->where('reset_password_token', $request->token)
        ->first();

      if(!$tokenData){
        return back()->with('error', 'Token tidak valid atau sudah kedaluwarsa');
      }

      $user = User::where('alumni_id', $alumni_id)->first();
      $user->password = Hash::make($request->password);
      $user->save();

      User::where('alumni_id', $alumni_id)->update([
        'reset_password_token' => null
      ]);

      return redirect()->route('start')->with('register', 'Password berhasil diubah, silahkan login');
    }

    public function permohonan(Request $request){

      $pengaturan = Pengaturan::all()->keyBy('nama');

      $validator = Validator::make($request->all(), [
        'dokumen' => 'required|array',
        'dokumen.*.namadok' => 'required|string',
        'dokumen.*.cetak' => 'required|integer|min:5|max:' . $pengaturan['maksimal_cetak']->nilai,
        'metodekirim' => 'required|string',
      ], [
        'metodekirim.required' => 'Pilih metode pengiriman',
        'dokumen.required' => 'Data dokumen wajib diisi',
        'dokumen.*.namadok.required' => 'Nama dokumen wajib diisi',
        'dokumen.*.cetak.required' => 'Jumlah cetak wajib diisi',
        'dokumen.*.cetak.integer' => 'Jumlah cetak harus berupa angka',
        'dokumen.*.cetak.min' => 'Minimal cetak adalah 5 kali',
        'dokumen.*.cetak.max' => 'Maksimal cetak adalah ' . $pengaturan['maksimal_cetak']->nilai . ' kali',
      ]);

      if($validator->fails()){
        return response()->json([
          'validation' => $validator->errors()->first()
        ]);
      }

      $jumlahCetakPerDokumen = [];
      foreach($request->dokumen as $d){
        $nama = strtolower($d['namadok']);
        $dok = $d['namadok'];
        $cetak = (int) $d['cetak'];
        if(!isset($jumlahCetakPerDokumen[$nama])){
          $jumlahCetakPerDokumen[$nama] = 0;
        }
        $jumlahCetakPerDokumen[$nama] += $cetak;
        if($jumlahCetakPerDokumen[$nama] > $pengaturan['maksimal_cetak']->nilai){
          return response()->json(['validation' => "Jumlah cetak untuk dokumen $dok melebihi batas maksimum " . $pengaturan['maksimal_cetak']->nilai . " lembar"]);
        }
      }



      $user = Auth::user();
      $user_id = $user->id;
      $alamat_id = $request->alamat;
      $nim = Alumni::where('id', $user->alumni_id)->value('nim');
      $dokumen = $request->dokumen;
      $metodekirim = $request->metodekirim;
      $metodebayar = $request->metodebayar;
      $biayakurir = $request->biayakirim;
      $jumlahbayar = $request->jumlahbayar;
      $kurir = $request->kurir;
      $tipe_kurir = $request->tipe_kurir;
      $metodeambil = $request->metodeambil;
      $metodeambil_fix = json_encode($metodeambil);
      $tanggal = Carbon::now()->format('Ymd_His');
      $randomstr = Str::upper(Str::random(6));
      $permohonan_id = "{$tanggal}_{$user_id}_{$randomstr}";

      try {
        $pembayaran = Pembayaran::create([
          'metode_pengiriman'   => $metodekirim,
          'biaya_kurir'         => $biayakurir,
          'jumlah_bayar'        => $jumlahbayar,
          'alamat_id'           => $alamat_id,
          'bukti_pembayaran'    => "",
        ]);

        foreach($dokumen as $data){
          $dokumen_id = Dokumen::where('nama_dokumen', $data['namadok'])->value('id');

          Permohonan::create([
            'user_id'              => $user_id,
            'permohonan_id'        => $permohonan_id,
            'jumlah_cetak'         => $data['cetak'],
            'harga_per_lembar'     => $data['harga'],
            'dokumen_id'           => $dokumen_id,
            'status_permohonan'    => 'Validasi Dokumen',
            'pembayaran_id'        => $pembayaran->id,
            'kurir'                => $kurir,
            'tipe_kurir'           => $tipe_kurir,
            'metode_pengambilan'   => $metodeambil_fix,
          ]);
        }

        session()->flash('permohonan', true);
        return response()->json(['success' => 'ok']);
      } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
      }
    }

    public function mohonUlang(Request $request)
    {
       $validator = Validator::make($request->all(), [
        'dokumen' => 'required|array',
        'dokumen.*.id' => 'required|string',
        'dokumen.*.nomor' => 'required|string',
      ]);

      if($validator->fails()){
        return response()->json([
          'validation' => "Harap lengkapi data dokumen"
        ]);
      }

      $permohonan_id = $request->mohonId;
      $dokumen = $request->dokumen;

      try {
        foreach($dokumen as $d){
          Permohonan::where('id', $d['id'])->update([
            'nomor_dokumen' => $d['nomor']
          ]);
        }

        Permohonan::where('permohonan_id', $permohonan_id)->update([
          'status_permohonan' => 'Validasi Dokumen',
          'catatan' => ''
        ]);

        return response()->json(['success' => 'Permohonanmu berhasil diajukan ulang.']);
      } catch(\Exception $e){
        return response()->json(['error', $e->getMessage()]);
      }
    }

    public function batalAjuan(Request $request)
    {
      $pembayaran_id = $request->pembayaran_id;
      $response = Pembayaran::where('id', $pembayaran_id)->delete();
      if($response){
        return response()->json(['success' => 'Permohonan anda telah dibatalkan']);
      } else {
        return response()->json(['error' => 'Data tidak ditemukan']);
      }
    }

    public function cekbayar(Request $request){
      $permohonan_id = $request->id;
      $id = Permohonan::where('permohonan_id', $permohonan_id)->value('id');
      $permohonan = Permohonan::with('pembayaran')->where('permohonan_id', $permohonan_id)->first();
      $metode_pembayaran = $permohonan->pembayaran->metode_pembayaran;
      if($metode_pembayaran == "Transfer Bank"){
        return response()->json(['metode' => 'tf']);
      } elseif ($metode_pembayaran == "Payment Gateway") {
        return response()->json(['metode' => 'pg']);
      } else {
        return response()->json(['error' => 'Ada yang bermasalah']);
      }
    }

    public function updateProfil(Request $request){
      $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'phone' => 'required|numeric|digits_between:10,15',
        'password' => 'nullable|min:7'
      ]);

      if($validator->fails()){
        return response()->json([
          'errors' => $validator->errors()->first()
        ], 422);
      }

      $id = $request->id;
      $email = $request->email;
      $phone = $request->phone;
      $password = $request->password;
      $password_hash;
      if($password){
        $password_hash = Hash::make($request->password);
      }
      try {
        if($password){
          User::where('id', $id)->update([
            'email' => $email,
            'phone' => $phone,
            'password' => $password_hash
          ]);
        } else {
          User::where('id', $id)->update([
            'email' => $email,
            'phone' => $phone,
          ]);
        }

        return response()->json(['success' => "Profil berhasil diperbarui"]);
      } catch(\Exception $e){
        return response()->json(['error' => $e->getMessage()]);
      }
    }
}
