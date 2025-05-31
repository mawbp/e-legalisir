<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class DokumenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        try {
          $dokumen = Dokumen::all();
          return response()->json($dokumen);
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
        //
      $validator = Validator::make($request->all(), [
        'nama' => 'required|string',
        'des' => 'nullable|string',
        'pem' => 'required|string',
        'harga' => 'required|integer',
        'file' => 'required_if:pem,Admin|nullable|file|mimes:jpg,png,jpeg,pdf'
      ], [
        'nama.required' => 'Lengkapi nama dokumen',
      ]);

      if($validator->fails()){
        return response()->json([
          'validation' => $validator->errors()->first()
        ]);
      }

      $nama_dokumen = $request->nama;
      $deskripsi = $request->des;
      $pemilik = $request->pem;
      $harga = $request->harga;
      
       if ($request->hasFile('file')) {
            $file = $request->file('file');
    
            // Atau bikin nama baru dengan format tertentu
            $path = '/home/fstunipd/public_html/uploads/';
            $nama = Str::slug($nama_dokumen, '_') .'.'. $file->getClientOriginalExtension();
            $full_path = $path . $nama;
    
            // Hapus file lama kalau ada
            if (file_exists($full_path)) {
                unlink($full_path);
            }
    
            // Pindahkan file
            $file->move($path, $nama);
        }
      try {
        $cek = Dokumen::where('nama_dokumen', $nama_dokumen)->exists();
        if($cek){
          return response()->json(['validation' => "Dokumen sudah ada"]);
        }
        Dokumen::create([
          'nama_dokumen'   => $nama_dokumen,
          'deskripsi'  => $deskripsi,
          'pemilik'     => $pemilik,
          'harga_per_lembar' => $harga
        ]);
        return response()->json(['success' => 'Dokumen berhasil ditambahkan']);
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
          $cek = Dokumen::where('id', $id)->exists();
          $data = Dokumen::where('id', $id)->first();
          $slug = Str::slug($data->nama_dokumen, '_');
          $possibleExtensions = ['jpg', 'png', 'jpeg', 'pdf'];
          $extension;
          
            foreach ($possibleExtensions as $ext) {
                $filename = $slug .'.'. $ext;
                $filePath = '/home/fstunipd/public_html/uploads/' . $filename;
        
                if (file_exists($filePath)) {
                    $extension = $ext;
                    break;
                } else {
                    $extension = 'jpg';
                }
            }
          if($cek){
            return response()->json(['data' => $data, 'slug' => $slug, 'ext' => $extension]);
          } else {
            return response()->json(['failed' => 'Dokumen tidak ditemukan']);
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
        //
      $validator = Validator::make($request->all(), [
        'nama' => 'required|string',
        'des' => 'nullable|string',
        'pem' => 'required|string',
        'file' => 'nullable|file|mimes:jpg,png,jpeg,pdf'
      ], [
        'nama.required' => 'Lengkapi nama dokumen',
      ]);

      if($validator->fails()){
        return response()->json([
          'validation' => $validator->errors()->first()
        ]);
      }
      
      try {
          $nama_dokumen = $request->nama;
          $deskripsi = $request->des;
          $pemilik = $request->pem;
          if($pemilik == "Admin"){
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                
                $path = '/home/fstunipd/public_html/uploads/';
                $baseName = Str::slug($nama_dokumen, '_');
                
                foreach (glob($path . $baseName . '.*') as $oldFile) {
                    if (is_file($oldFile)) {
                        unlink($oldFile);
                    }
                }
                
                $nama = $baseName . '.' . $file->getClientOriginalExtension();
                $file->move($path, $nama);
            } else {
                $path = '/home/fstunipd/public_html/uploads/';
                $baseName = Str::slug($nama_dokumen, '_');
                $found = false;
                
                foreach (glob($path . $baseName . '.*') as $oldFile) {
                    if (is_file($oldFile)) {
                        $found = true;
                        break;
                    }
                }
                
                if(!$found){
                    return response()->json(['validation' => "Silahkan upload file dulu"]);
                }
            }
          } else if($pemilik == "Alumni"){
              $path = '/home/fstunipd/public_html/uploads/';
              $baseName = Str::slug($nama_dokumen, '_');
            
              foreach (glob($path . $baseName . '.*') as $oldFile) {
                  if (is_file($oldFile)) {
                      unlink($oldFile);
                  }
              }
          }
            
            $data = Dokumen::where('id', $id)->value('nama_dokumen');
            $dupe = Dokumen::where('nama_dokumen', $nama_dokumen)->exists();
            if($dupe && $nama_dokumen != $data){
              return response()->json(['failed' => "Dokumen sudah ada"]);
            }
            $dokumen = Dokumen::findOrFail($id);
            $dokumen->update([
              'nama_dokumen' => $nama_dokumen,
              'deskripsi' => $deskripsi,
              'pemilik' => $pemilik
            ]);
            return response()->json(['success' => 'Dokumen berhasil diperbarui']);
      }catch(\Exception $e){
          return response()->json(['error' => 'ok']);
      }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        try {
          $dokumen = Dokumen::find($id);
          $getdok = Dokumen::where('id', $id)->first();
          $path = '/home/fstunipd/public_html/uploads/';
          $baseName = Str::slug($getdok->nama_dokumen, '_');
        
          foreach (glob($path . $baseName . '.*') as $oldFile) {
              if (is_file($oldFile)) {
                  unlink($oldFile);
              }
          }
            
          if($dokumen){
            $dokumen->delete();
            return response()->json(['success' => 'Dokumen berhasil dihapus']);
          } else {
            return response()->json(['failed' => 'Dokumen tidak ditemukan']);
          }
        } catch (\Exception $e){
          return response()->json(['error' => $e->getMessage()]);
        }
    }
}
