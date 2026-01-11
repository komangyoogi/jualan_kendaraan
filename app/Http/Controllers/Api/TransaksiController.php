<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TransaksiModel;
use App\Models\KendaraanModel;
use App\Helpers\ApiFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransaksiController extends Controller
{
    // Menampilkan semua transaksi
    public function index(Request $request)
    {
        $transaksi = TransaksiModel::orderBy('id', 'ASC')->get();


        $response = ApiFormatter::createJson(200, 'Get Data Transaksi Success', $transaksi);
        return response()->json($response);
    }

    // Membuat transaksi baru
    public function create(Request $request)
    {
        try {
            $params = $request->only(['kendaraan_id','tanggal_transaksi','total_harga','status']);

            $validator = Validator::make($params, [
                'kendaraan_id'      => 'required|exists:kendaraan,id',
                'tanggal_transaksi' => 'required|date',
                'total_harga'       => 'required|integer|min:0',
                'status'            => 'required|string|max:50',
            ]);

            if ($validator->fails()) {
                return response()->json(
                    ApiFormatter::createJson(400, 'Bad Request', $validator->errors()->all())
                );
            }

            $transaksi = TransaksiModel::create($params);

            return response()->json(
                ApiFormatter::createJson(200, 'Create Transaksi Success', $transaksi)
            );

        } catch (\Exception $e) {
            return response()->json(
                ApiFormatter::createJson(500, 'Internal Server Error', $e->getMessage())
            );
        }
    }

    // Menampilkan detail transaksi
    public function detail($id)
    {
        try {
            $transaksi = TransaksiModel::with(['kendaraan', 'kendaraan.merek', 'kendaraan.kategori'])
                ->find($id);

            if (is_null($transaksi)) {
                return response()->json(
                    ApiFormatter::createJson(404, 'Transaksi Not Found')
                );
            }

            return response()->json(
                ApiFormatter::createJson(200, 'Get Transaksi Detail Success', $transaksi)
            );

        } catch (\Exception $e) {
            return response()->json(
                ApiFormatter::createJson(500, 'Internal Server Error', $e->getMessage())
            );
        }
    }

    // Update transaksi
    public function update(Request $request, $id)
    {
        try {
            $params = $request->all();

            $transaksi = TransaksiModel::find($id);
            if (is_null($transaksi)) {
                return response()->json(
                    ApiFormatter::createJson(404, 'Data Not Found'), 404
                );
            }

            $validator = Validator::make($params, [
                'kendaraan_id'      => 'required|exists:kendaraan,id',
                'tanggal_transaksi' => 'required|date',
                'total_harga'       => 'required|integer|min:0',
                'status'            => 'required|string|max:50',
            ]);

            if ($validator->fails()) {
                return response()->json(
                    ApiFormatter::createJson(400, 'Bad Request', $validator->errors()->all()), 400
                );
            }

            $transaksi->update($params);

            return response()->json(
                ApiFormatter::createJson(200, 'Update Transaksi Success', $transaksi->fresh())
            );

        } catch (\Exception $e) {
            return response()->json(
                ApiFormatter::createJson(500, 'Internal Server Error', $e->getMessage())
            );
        }
    }
    
    // Hapus transaksi
    public function delete($id)
    {
        try {
            $transaksi = TransaksiModel::find($id);
            if (is_null($transaksi)) {
                return response()->json(
                    ApiFormatter::createJson(404, 'Data Not Found')
                );
            }

            $transaksi->delete();

            return response()->json(
                ApiFormatter::createJson(200, 'Delete Transaksi Success')
            );

        } catch (\Exception $e) {
            return response()->json(
                ApiFormatter::createJson(500, 'Internal Server Error', $e->getMessage())
            );
        }
    }

    // Dapatkan transaksi berdasarkan kendaraan
    public function getByKendaraan($kendaraan_id)
    {
        try {
            $kendaraan = KendaraanModel::find($kendaraan_id);
            if (is_null($kendaraan)) {
                return response()->json(
                    ApiFormatter::createJson(404, 'Kendaraan Not Found')
                );
            }

            $transaksi = TransaksiModel::where('kendaraan_id', $kendaraan_id)
                ->with(['kendaraan', 'kendaraan.merek', 'kendaraan.kategori'])
                ->get();

            return response()->json(
                ApiFormatter::createJson(200, 'Get Transaksi by Kendaraan Success', $transaksi)
            );

        } catch (\Exception $e) {
            return response()->json(
                ApiFormatter::createJson(500, 'Internal Server Error', $e->getMessage())
            );
        }
    }
}
