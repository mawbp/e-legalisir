<?php

namespace App\Http\Controllers;

use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProdiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        try {
          $prodi = Prodi::all();
          return response()->json($prodi);
        } catch (\Exception $e){
          return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
      $validator = Validator::make($request->all(), [
        'nama' => 'required|string',
      ], [
        'nama.required' => 'Lengkapi nama prodi',
      ]);

      if($validator->fails()){
        return response()->json([
          'validation' => $validator->errors()->first()
        ]);
      }

      $nama_prodi = $request->nama;
      try {
        $cek = Prodi::where('nama_prodi', $nama_prodi)->exists();
        if($cek){
          return response()->json(['validation' => "Prodi sudah ada"]);
        }
        Prodi::create([
          'nama_prodi'   => $nama_prodi,
        ]);
        return response()->json(['success' => 'Prodi berhasil ditambahkan']);
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
          $cek = Prodi::where('id', $id)->exists();
          $data = Prodi::where('id', $id)->first();
          if($cek){
            return response()->json($data);
          } else {
            return response()->json(['failed' => 'Prodi tidak ditemukan']);
          }
        } catch (\Exception $e){
          return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
      $validator = Validator::make($request->all(), [
        'nama' => 'required|string',
      ], [
        'nama.required' => 'Lengkapi nama prodi',
      ]);

      if($validator->fails()){
        return response()->json([
          'validation' => $validator->errors()->first()
        ]);
      }

      $nama_prodi = $request->nama;
      try {
        $data = Prodi::where('id', $id)->value('nama_prodi');
        $dupe = Prodi::where('nama_prodi', $nama_prodi)->exists();
        if($dupe && $nama_prodi != $data){
          return response()->json(['failed' => "Prodi sudah ada"]);
        }
        $prodi = Prodi::findOrFail($id);
        $prodi->update([
          'nama_prodi' => $nama_prodi,
        ]);
        return response()->json(['success' => 'Prodi berhasil diperbarui']);
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
          $prodi = Prodi::find($id);
          if($prodi){
            $prodi->delete();
            return response()->json(['success' => 'Prodi berhasil dihapus']);
          } else {
            return response()->json(['failed' => 'Prodi tidak ditemukan']);
          }
        } catch (\Exception $e){
          return response()->json(['error' => $e->getMessage()]);
        }
    }
}
