<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BeritaResouce;
use Illuminate\Http\Request;
use App\Models\Berita;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BeritaController extends Controller
{
    public function index()
    {
        $beritas = Berita::all();

        return new BeritaResouce(true, 'Daftar Berita', $beritas);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'gambar'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'judul'     => 'required',
            'konten'   => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $gambar = $request->file('gambar');
        $gambar->storeAs('public/beritas', $gambar->hashName());

        $berita = Berita::create([
            'gambar'   => $gambar->hashName(),
            'judul'    => $request->judul,
            'konten'   => $request->konten,
        ]);

        return new BeritaResouce(true, 'Data Berita Berhasil Ditambahkan!', $berita);
    }


    public function show($id)
    {
        $berita = Berita::find($id);
        return new BeritaResouce(true, 'Data Berita Ditemukan!', $berita);
    }

    public function update(Request $request, $id)
    {

        $berita = Berita::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'judul'     => 'required',
            'konten'   => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->hasFile('gambar')) {

            $gambar = $request->file('gabar');
            $gambar->storeAs('public/beritas', $gambar->hashName());

            Storage::delete('public/beritas/' . $berita->gambar);

            $berita->update([
                'gambar'     => $gambar->hashName(),
                'judul'     => $request->judul,
                'konten'   => $request->konten,
            ]);
        } else {

            $berita->update([
                'judul'     => $request->judul,
                'konten'   => $request->konten,
            ]);
        }


        return new BeritaResouce(true, 'Data Berita Berhasil Diubah!', $berita);
    }

    public function destroy($id)
    {

        $berita = Berita::findOrFail($id);
        Storage::delete('public/beritas/' . $berita->gambar);

        $berita->delete();

        return new BeritaResouce(true, 'Data Berita Berhasil Dihapus!', null);
    }
}
