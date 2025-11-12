<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class PengumumanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        try {
          $pengumuman = Pengumuman::all();
          return response()->json($pengumuman);
        } catch (\Exception $e){
          return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string',
            'durasi' => 'required|integer|min:1',
            'editor' => 'required',
        ]);
        
        $dur = now()->addDays((int)$request->durasi);
    
        Pengumuman::create([
            'judul' => $request->judul,
            'expired_at' => $dur,
            'isi' => $request->editor,
        ]);
    
        return redirect()->route('admin.pengumuman')->with('success', 'Pengumuman berhasil disimpan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pengumuman = Pengumuman::where('id', $id)->first();
        $durasi = $pengumuman->created_at->diffInDays($pengumuman->expired_at);
        
        return view('admin.editoredit', ['pengumuman' => $pengumuman, 'durasi' => $durasi]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'judul' => 'required|string',
            'durasi' => 'required|integer|min:1',
            'editor' => 'required',
        ]);
        
        $pengumuman = Pengumuman::where('id', $id)->first();
          
        $durasi = $pengumuman->created_at->copy()->addDays((int) $request->durasi);
    
        Pengumuman::where('id', $id)->update([
            'judul' => $request->judul,
            'expired_at' => $durasi,
            'isi' => $request->editor,
        ]);
    
        return redirect()->route('admin.pengumuman')->with('success', 'Pengumuman berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        try {
          $pengumuman = Pengumuman::find($id);
          if($pengumuman){
            $pengumuman->delete();
            return response()->json(['success' => true, 'message' => 'Pengumuman berhasil dihapus']);
          } else {
            return response()->json(['success' => false, 'message' => 'Pengumuman tidak ditemukan']);
          }
        } catch (\Exception $e){
            dd($e);
          return response()->json(['success' => false, 'message' => "Terjadi Kesalahan"]);
        }
    }
}
