<?php

namespace App\Http\Controllers;

use App\Models\Alumni;
use App\Models\Dokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AlumniController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        try {
          $alumni = Alumni::all();
          return response()->json($alumni);
        } catch (\Exception $e){
          return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $rules = [
            'nim' => 'required|string',
            'nama' => 'required|string',
            'prodi' => 'required|string',
            'angkatan' => 'required|string'
        ];
        
        $dokumen = Dokumen::all();
        foreach ($dokumen as $d) {
            if($d->pemilik == "Alumni"){
                $slug = Str::slug($d->nama_dokumen, '_');
                $rules["scan_$slug"] = 'required|file|mimes:jpg,jpeg,png|max:2048';    
            }
        }
        
        $validator = Validator::make($request->all(), $rules, [
            'nim.required' => 'Lengkapi NIM alumni',
            'nama.required' => 'Lengkapi nama alumni',
            'prodi.required' => 'Lengkapi prodi alumni',
            'angkatan.required' => 'Lengkapi tahun angkatan alumni',
        ]);

        if($validator->fails()){
            return response()->json([
                'validation' => $validator->errors()->first()
            ]);
        }
        
        $nim = $request->nim;
        $nama = $request->nama;
        $formatted_nama = Str::title($nama);
        $prodi = $request->prodi;
        $angkatan = $request->angkatan;
        $kode = Str::random(32);

        foreach ($dokumen as $d) {
            if($d->pemilik == "Alumni"){
                $slug = Str::slug($d->nama_dokumen, '_');
                $inputName = 'scan_' . $slug;
            
                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    $nama = $kode .'_'. $nim .'_'. $slug .'.'. $file->getClientOriginalExtension();

                    $file->storeAs('berkas', $nama, 'public');
                }    
            }
        }
    
      try {
        $cek = Alumni::where('nim', $nim)->exists();
        if($cek){
          return response()->json(['validation' => "Alumni dengan NIM $nim sudah ada"]);
        }
        Alumni::create([
          'nim'   => $nim,
          'nama'  => $formatted_nama,
          'prodi' => $prodi,
          'tahun_angkatan' => $angkatan,
          'kode_file'   => $kode,
        ]);
        return response()->json(['success' => 'Alumni berhasil ditambahkan']);
      } catch (\Exception $e){
        return response()->json(['error' => $e->getMessage()]);
      }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        try {
            $cek = Alumni::where('id', $id)->exists();
            $data = Alumni::where('id', $id)->first();
            $dokumen = Dokumen::all();
            $dokumenArray = [];
            
            foreach ($dokumen as $d) {
                $slug = Str::slug($d->nama_dokumen, '_');
                $found = false;
            
                // Coba beberapa ekstensi
                $possibleExtensions = ['jpg', 'png', 'jpeg', 'pdf'];
            
                foreach ($possibleExtensions as $ext) {
                    $filename = $data->kode_file .'_'. $data->nim .'_'. $slug .'.'. $ext;
                    $filePath = 'uploads/' . $filename;
            
                    if (file_exists($filePath)) {
                        $dokumenArray[$slug] = 'uploads/' . $filename;
                        $found = true;
                        break;
                    }
                }
            
                if (!$found) {
                    $dokumenArray[$slug] = null;
                }
            }
            if($cek){
                return response()->json([ 'data' => $data, 'dokumen' => $dokumenArray]);
            } else {
                return response()->json(['failed' => 'Alumni tidak ditemukan']);
            }
        } catch (\Exception $e){
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
      $rules = [
          'nim' => 'required|string',
          'nama' => 'required|string',
          'prodi' => 'required|string',
          'angkatan' => 'required|string'
      ];
      
      $dokumen = Dokumen::all();
      foreach ($dokumen as $d) {
          if($d->pemilik == "Alumni"){
              $slug = Str::slug($d->nama_dokumen, '_');
              $rules["scane_$slug"] = 'nullable|file|mimes:jpg,jpeg,png|max:2048';    
          }
      }
      $validator = Validator::make($request->all(), $rules, [
          'nim.required' => 'Lengkapi NIM alumni',
          'nama.required' => 'Lengkapi nama alumni',
          'prodi.required' => 'Lengkapi prodi alumni',
          'prodi.required' => 'Lengkapi prodi alumni',
          'angkatan.required' => 'Lengkapi tahun angkatan alumni',
      ]);

      if($validator->fails()){
          return response()->json([
          'validation' => $validator->errors()->first()
          ]);
      }

      $nim = $request->nim;
      $nama_alumni = $request->nama;
      $formatted_nama = Str::title($nama_alumni);
      $prodi = $request->prodi;
      $angkatan = $request->angkatan;
      $kode = Alumni::where('nim', $nim)->value('kode_file');

      foreach ($dokumen as $d) {
        $slug = Str::slug($d->nama_dokumen, '_'); 
        $inputName = 'scane_' . $slug;
    
        if ($request->hasFile($inputName)) {
          $file = $request->file($inputName);
  
          $nama = $kode .'_'. $nim .'_'. $slug .'.'. $file->getClientOriginalExtension();
  
          // Hapus file lama kalau ada
          $files = Storage::disk('public')->files('berkas');
          foreach ($files as $dupeFile) {
            if (preg_match("/berkas\/" . $kode .'_'. $nim .'_'. $slug . "\.[a-zA-Z0-9]+$/", $dupeFile)) {
              Storage::disk('public')->delete($dupeFile);
            };
          }
          
          $file->storeAs('berkas', $nama, 'public');
        }
      }

      try {
        $data = Alumni::where('id', $id)->value('nim');
        $dupe = Alumni::where('nim', $nim)->exists();
        if($dupe && $nim != $data){
          return response()->json(['validation' => "Alumni dengan nim $nim sudah terdaftar"]);
        }
        $alumni = Alumni::findOrFail($id);
        $alumni->update([
          'nim' => $nim,
          'nama' => $formatted_nama,
          'prodi' => $prodi,
          'tahun_angkatan' => $angkatan
        ]);
        return response()->json(['success' => 'Alumni berhasil diperbarui']);
      } catch (\Exception $e){
        return response()->json(['error' => $e->getMessage()]);
      }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        try {
          $alumni = Alumni::find($id);
          $kode = Alumni::where('id', $id)->value('kode_file');
          $nim = Alumni::where('id', $id)->value('nim');
          $dokumen = Dokumen::where('pemilik', "Alumni")->get();
          
          foreach ($dokumen as $d) {
              if($d->pemilik == "Alumni"){
                $path = '/home/fstunipd/public_html/uploads/';
                $slug = Str::slug($d->nama_dokumen, '_');
                $baseName = $kode .'_'. $nim .'_'. $slug;
                
                foreach (glob($path . $baseName . '.*') as $oldFile) {
                    if (is_file($oldFile)) {
                          unlink($oldFile);
                    }
                }
              }
        }
          
          if($alumni){
            $alumni->delete();
            return response()->json(['success' => 'Alumni berhasil dihapus']);
          } else {
            return response()->json(['validation' => 'Alumni tidak ditemukan']);
          }
        } catch (\Exception $e){
          return response()->json(['error' => $e->getMessage()]);
        }
    }
}
