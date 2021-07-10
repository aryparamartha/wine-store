<?php

namespace App\Http\Controllers;

use App\Pengerjaan;
use App\Konsumen;
use App\Layanan;
use Illuminate\Http\Request;
use Carbon;
use DB;
use PDF;
use App;

class PengerjaanController extends Controller
{
    public function index()
    {
        $pengerjaans = Pengerjaan::get();
        $konsumens = Konsumen::get();
        $layanans = Layanan::get();
        return view('pengerjaan.index', compact('pengerjaans','konsumens','layanans'));
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $input['tanggal'] = Carbon::now();
        $pengerjaan = Pengerjaan::create($input);
        return redirect('pengerjaan')->with('success', 'Berhasil menambah data pengerjaan');
    }

    public function update(Request $request, $id)
    {
        $pengerjaan = Pengerjaan::find($id);
        $input = $request->all();
        $pengerjaan->update($input);
        return redirect('pengerjaan')->with('success', 'Berhasil memperbaharui data pengerjaan');
    }

    
    public function getPengerjaanAktif($id)
    {
        $pengerjaan = DB::table('tb_pengerjaan')
        ->select(['jenis_layanan','jumlah','total_harga','tanggal','status'])
        ->join('tb_layanan', 'tb_pengerjaan.id_layanan', '=', 'tb_layanan.id_layanan')
        ->where('tb_pengerjaan.id_konsumen', $id)
        ->where('tb_pengerjaan.status', '!=','selesai')
        ->get();
        $response['status'] = 200;
        $response['message'] = "Data Tersedia";
        if($pengerjaan->count()==0){
            $pengerjaan = [];
            $response['message'] = "Data Tidak Tersedia";
        }
        $response['data'] = $pengerjaan;

        return response()->json($response, 200);
    }
    public function getPengerjaanSelesai($id)
    {
        $pengerjaan = DB::table('tb_pengerjaan')
        ->select(['jenis_layanan','jumlah','total_harga','tanggal','status'])
        ->join('tb_layanan', 'tb_pengerjaan.id_layanan', '=', 'tb_layanan.id_layanan')
        ->where('tb_pengerjaan.id_konsumen', $id)
        ->where('tb_pengerjaan.status', 'selesai')
        ->get();

        $response['status'] = 200;
        $response['message'] = "Data Tersedia";
        if($pengerjaan->count()==0){
            $pengerjaan = [];
            $response['message'] = "Data Tidak Tersedia";
        }
        $response['data'] = $pengerjaan;

        return response()->json($response, 200);
    }

    public function print(Pengerjaan $pengerjaan){
        return view('pengerjaan.print', compact('pengerjaan'));
    }
}
