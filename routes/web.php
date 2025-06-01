<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AlumniController;
use App\Http\Controllers\Api\WilayahController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\DokumenController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\ProdiController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UserController;
use App\Models\Alumni;
use App\Models\User;
use App\Models\Pengaturan;
use App\Models\Alamat;
use App\Models\Dokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

Route::post('/doku/notify', [ApiController::class, 'notifDoku']);

Route::get('/', function() {
    $pengaturan = Pengaturan::all()->keyBy('nama');
    return view('auth.user', ['pengaturan' => $pengaturan]);
})->name('start');

// ADMIN ROUTE
Route::prefix('admin')->middleware('role:admin')->group(function() {
  Route::get('/', [AdminController::class, 'showLoginForm'])->withoutMiddleware('role:admin')->name('admin.loginform');
  Route::post('/login', function (Request $request){
    $request->validate([
      'username' =>  'required|string',
      'password' => 'required|string',
    ]);
    $admin = User::where('name', $request->username)->first();
    if($admin && Hash::check($request->password, $admin->password)){
      Auth::login($admin);

      return redirect()->route('admin.dashboard');
    }

    $hash = Hash::make($request->password);

    return redirect()->back()->with('error', 'Username atau password salah');
  })->withoutMiddleware('role:admin')->name('admin.login');
  Route::post('/logout', function(){
    $cek = Auth::check();
    $role  = Auth::user()->role;
    if($cek && $role == 'admin'){
      Auth::logout();
      return redirect()->route('admin.loginform')->with('logout', 'Kamu berhasil logout');
    }
    return response()->json(['message' => 'Gagal logout', 'check' => $cek]);
  })->name('admin.logout');
  Route::get('/dashboard', [AdminController::class, 'tampilDashboard'])->name('admin.dashboard');
  Route::get('/registrasi', function(){ return view('admin.registrasi'); })->name('admin.registrasi');
  Route::get('/dtregis', [AdminController::class, 'regisIndex'])->name('admin.dtregis');
  Route::get('/lihatberkas/{id}', [AdminController::class, 'lihatBerkas'])->name('admin.lihatberkas');
  Route::get('/setujuregis/{id}', [AdminController::class, 'setujuRegis'])->name('admin.setujuregis');
  Route::get('/tolakregis/{id}', [AdminController::class, 'tolakRegis'])->name('admin.tolakregis');
  Route::get('/pengumuman',  function(){ return view('admin.pengumuman'); })->name('admin.pengumuman');
  Route::get('/dtpengumuman', [AdminController::class, 'umumIndex'])->name('admin.dtpengumuman');
  Route::get('/buatpengumuman', function(){ return view('admin.editor'); })->name('admin.buatpengumuman');
  Route::post('/tambahpengumuman', [AdminController::class, 'tambahUmum'])->name('admin.tambahpengumuman');
  Route::get('/pengajuan', function(){ return view('admin.pengajuan'); })->name('admin.pengajuan');
  Route::get('/dtpermohonan', [AdminController::class, 'getPermohonan'])->name('admin.dtpermohonan');
  Route::get('/detail/{id}', [AdminController::class, 'getMohon'])->name('admin.detail');
  Route::get('/biaya/{id}', [AdminController::class, 'getBiaya'])->name('admin.biaya');
  Route::get('/kirim/{id}', [AdminController::class, 'getKirim'])->name('admin.kirim');
  Route::post('/bayarkurir', [AdminController::class, 'bayarKurir'])->name('admin.bayarkurir');
  Route::post('/update', [AdminController::class, 'updateStatus'])->name('admin.update');
  Route::post('/tolak', [AdminController::class, 'tolakStatus'])->name('admin.tolak');
  Route::get('/dokumen', function(){ return view('admin.dokumen'); })->name('admin.dokumen');
  Route::get('/dtdokumen', [AdminController::class, 'getDokumen'])->name('admin.dtdokumen');
  Route::get('/laporan', [AdminController::class, 'tampilLaporan'])->name('admin.laporan');
  Route::get('/dtlaporan', [AdminController::class, 'dataLaporan'])->name('admin.dtlaporan');
  Route::get('/unduhinvoice/{id}/{status}', [AdminController::class, 'unduhInvoice'])->name('admin.unduhinvoice');
  Route::get('/alumni', [AdminController::class, 'tampilAlumni'])->name('admin.alumni');
  Route::get('/unduh', function(){
    $path = storage_path('app/public/template_data_alumni.xlsx');
    if(!file_exists($path)){
      abort(404);
    }
    return response()->download($path);
  })->name('admin.unduh');
  Route::post('/impor', [AdminController::class, 'imporAlumni'])->name('admin.impor');
  Route::post('/uploadscan', [AdminController::class, 'uploadScan'])->name('admin.uploadscan');
  Route::get('/prodi', function(){ return view('admin.prodi'); })->name('admin.prodi');
  Route::get('/akun', function(){ return view('admin.akun'); })->name('admin.akun');
  Route::post('/editprofil', [AdminController::class, 'editProfil'])->name('admin.editprofil');
  Route::post('/editlegal', [AdminController::class, 'editLegal'])->name('admin.editlegal');
  Route::post('/editbiaya', [AdminController::class, 'editBiaya'])->name('admin.editbiaya');
  Route::post('/editkampus', [AdminController::class, 'editKampus'])->name('admin.editkampus');
  Route::post('/simpanalamat', [AdminController::class, 'simpanAlamat'])->name('admin.simpanalamat');
  Route::post('/editpembayaran', [AdminController::class, 'editPembayaran'])->name('admin.editpembayaran');
  Route::get('/settingakun', function(){ $pengaturan = Pengaturan::all()->keyBy('nama'); return view('admin.settingakun', compact('pengaturan')); })->name('admin.setting.akun');
  Route::get('/settinglegal', function(){ $pengaturan = Pengaturan::all()->keyBy('nama'); return view('admin.settinglegal', compact('pengaturan')); })->name('admin.setting.legal');
  Route::get('/settingbiaya', function(){
      $pengaturan = Pengaturan::all()->keyBy('nama'); 
      $dokumen = Dokumen::all();
      return view('admin.settingbiaya', compact('pengaturan', 'dokumen')); 
  })->name('admin.setting.biaya');
  Route::get('/settingpembayaran', function(){ $pengaturan = Pengaturan::all()->keyBy('nama'); return view('admin.settingpembayaran', compact('pengaturan')); })->name('admin.setting.pembayaran');
});


// USER ROUTE
Route::prefix('user')->middleware('role:user')->group(function() {
  Route::post('/register', [UserController::class, 'register'])->withoutMiddleware('role:user')->name('user.register');
  Route::get('/register', function(){ return view('auth.userreg'); })->withoutMiddleware('role:user')->name('user.regispage');
  Route::get('/login', function(){ return view('auth.user'); })->withoutMiddleware('role:user');
  Route::post('/reset', [UserController::class, 'sendReset'])->withoutMiddleware('role:user')->name('user.reset');
  Route::get('/lupa', function(){ return view('auth.userlupa'); })->withoutMiddleware('role:user')->name('user.lupa');
  Route::get('/reset-password', [UserController::class, 'showResetForm'])->withoutMiddleware('role:user');
  Route::post('/ganti', [UserController::class, 'resetPassword'])->withoutMiddleware('role:user')->name('user.ganti');
  Route::post('/login', function(Request $request){
    $alumni_id = Alumni::where('nim', $request->nim)->value('id');
    $user = User::where('alumni_id', $alumni_id)->first();
    if($user && Hash::check($request->password, $user->password)){
      Auth::login($user);
      return redirect()->route('user.dashboard');
    }
    return redirect()->back()->with('error', 'NIM atau password salah');
  })->withoutMiddleware('role:user')->name('user.login');
  Route::post('/logout', function(){
    $cek = Auth::check();
    $role  = Auth::user()->role;
    if($cek && $role == 'user'){
      Auth::logout();
      return redirect()->route('start')->with('logout', 'Kamu berhasil logout');
    }
    return response()->json(['message' => 'Gagal logout', 'check' => $cek]);
  })->name('user.logout');
  Route::get('/dashboard', [UserController::class, 'showDashboard'])->name('user.dashboard');
  Route::get('/getumum/{id}', [UserController::class, 'getUmum'])->name('user.getumum');
  Route::get('/detail/{id}', [UserController::class, 'getMohon'])->name('user.detail');
  Route::get('/biaya/{id}', [UserController::class, 'getBiaya'])->name('user.biaya');
  Route::get('/cekharga', [UserController::class, 'cekHarga'])->name('user.cekharga');
  Route::get('/pengajuan', [UserController::class, 'pengajuan'])->name('user.pengajuan')->middleware('complete');
  Route::post('/mohon', [UserController::class, 'permohonan'])->name('user.mohon');
  Route::post('/ajukanulang', [UserController::class, 'mohonUlang'])->name('user.ajukanulang');
  Route::get('/mohonsukses', function(){
    if(!session()->has('permohonan')){
      return redirect()->route('user.dashboard');
    }
    return view('user.suksesmohon');
  })->name('user.mohon.success');
  Route::post('/batalajuan', [UserController::class, 'batalAjuan'])->name('user.batal');
  Route::post('/simpanalamat', [UserController::class, 'simpanAlamat'])->name('user.alamat');
  Route::post('/ubahalamat', [UserController::class, 'ubahAlamat'])->name('user.ubahAlamat');
  Route::post('/alamatutama', [UserController::class, 'alamatUtama'])->name('user.alamatutama');
  Route::post('/hapusalamat', [UserController::class, 'hapusAlamat'])->name('user.hapusAlamat');
  Route::get('/riwayat', function(){ return view('user.riwayat'); })->name('user.riwayat');
  Route::get('/dtriwayat', [UserController::class, 'dataRiwayat'])->name('user.dtriwayat');
  Route::get('/dtcekstatus', [UserController::class, 'dataCekstatus'])->name('user.dtcekstatus');
  Route::get('/cekstatus', function(){ return view('user.cekstatus'); })->name('user.cekstatus');
  Route::get('/akun', function(){ return view('user.akun'); })->name('user.akun');
  Route::post('/updateprofil', [UserController::class, 'updateProfil'])->name('user.updateprofil');
  Route::post('/ceknim', [UserController::class, 'ceknim'])->name('user.ceknim')->withoutMiddleware('role:user');
  Route::post('/cekbayar', [UserController::class, 'cekbayar'])->name('user.cekbayar');
});

// API ROUTE
Route::prefix('api')->group(function(){
    Route::get('/cobanot', function(){ return view('user.cobanot'); })->name('api.cobanot');
  Route::post('/paymentpg', [ApiController::class, 'paymentPg'])->name('api.paymentpg');
  Route::post('/bayarulang', [ApiController::class, 'bayarUlang'])->name('api.bayarulang');
  Route::post('/updatepaymentpg', [ApiController::class, 'updatePaymentPg'])->name('api.updatePaymentPg');
  Route::post('/paymenttf', [ApiController::class, 'paymentTf'])->name('api.paymenttf');
  Route::post('/midtrans/notification', [ApiController::class, 'handleNotification'])->name('api.midtrans.not');
  Route::post('/updatepayment', [ApiController::class, 'updatePayment'])->name('api.update.bayar');
  Route::get('/provinsi', function(){
    $response = Http::get('https://wilayah.id/api/provinces.json');
    return response()->json($response->json());
  })->name('api.provinsi');
  Route::get('/kabupaten/{provinsiId}', function($provinsiId){
    $response = Http::get("https://wilayah.id/api/regencies/{$provinsiId}.json");
    return response()->json($response->json());
  })->name('api.kabupaten');
  Route::get('/kecamatan/{kabupatenId}', function($kabupatenId){
    $response = Http::get("https://wilayah.id/api/districts/{$kabupatenId}.json");
    return response()->json($response->json());
  })->name('api.kecamatan');
  Route::get('/kelurahan/{kecamatanId}', function($kecamatanId){
    $response = Http::get("https://wilayah.id/api/villages/{$kecamatanId}.json");
    return response()->json($response->json());
  })->name('api.kelurahan');
  Route::post('/kurir', [ApiController::class, 'getRates'])->name('api.kurir');
  Route::post('/order', [ApiController::class, 'createOrder'])->name('api.order');
  Route::post('/simpantracking', [ApiController::class, 'storeTracking'])->name('api.store.tracking');
  Route::post('/lacakorder', [ApiController::class, 'trackOrder'])->name('api.track.order');
  Route::post('/selesai', [ApiController::class, 'selesai'])->name('api.selesai');  
});

// RESOURCE ROUTE
Route::resource('alumni', AlumniController::class);
Route::resource('dokumen', DokumenController::class);
Route::resource('prodi', ProdiController::class);
Route::resource('pengumuman', PengumumanController::class);

