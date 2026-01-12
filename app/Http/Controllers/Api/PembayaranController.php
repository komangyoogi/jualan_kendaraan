<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PembayaranModel;
use Illuminate\Http\Request;
use App\Helpers\ApiFormatter;

class PembayaranController extends Controller
{
    /**
     * GET /api/pembayaran
     */
    public function index()
    {
        try {
            $pembayaran = PembayaranModel::orderBy('id', 'ASC')->get();

            return response()->json(
                ApiFormatter::createJson(
                    200,
                    'Get Data Pembayaran Success',
                    $pembayaran
                ),
                200
            );
        } catch (\Exception $e) {
            return response()->json(
                ApiFormatter::createJson(
                    500,
                    'Terjadi kesalahan',
                    $e->getMessage()
                ),
                500
            );
        }
    }

    /**
     * POST /api/pembayaran
     */
    public function create(Request $request)
    {
        $validated = $request->validate([
            'transaksi_id'       => 'required|integer',
            'metode_pembayaran'  => 'required|string',
            'jumlah_pembayaran'  => 'required|integer',
            'status'             => 'required|string',
        ]);

        $pembayaran = PembayaranModel::create($validated);

        return response()->json(
            ApiFormatter::createJson(
                201,
                'Pembayaran berhasil ditambahkan',
                $pembayaran
            ),
            201
        );
    }

    /**
     * GET /api/pembayaran/{id}
     */
    public function detail($id)
    {
        $pembayaran = PembayaranModel::find($id);

        if (!$pembayaran) {
            return response()->json(
                ApiFormatter::createJson(
                    404,
                    'Data pembayaran tidak ditemukan'
                ),
                404
            );
        }

        return response()->json(
            ApiFormatter::createJson(
                200,
                'Detail pembayaran',
                $pembayaran
            ),
            200
        );
    }

    /**
     * PUT /api/pembayaran/{id}
     */
    public function update(Request $request, $id)
    {
        $pembayaran = PembayaranModel::find($id);

        if (!$pembayaran) {
            return response()->json(
                ApiFormatter::createJson(
                    404,
                    'Data pembayaran tidak ditemukan'
                ),
                404
            );
        }

        $validated = $request->validate([
            'transaksi_id'       => 'required|integer',
            'metode_pembayaran'  => 'required|string',
            'jumlah_pembayaran'  => 'required|integer',
            'status'             => 'required|string',
        ]);

        $pembayaran->update($validated);

        return response()->json(
            ApiFormatter::createJson(
                200,
                'Pembayaran berhasil diupdate',
                $pembayaran
            ),
            200
        );
    }

    /**
     * PATCH /api/pembayaran/{id}
     */
    public function patch(Request $request, $id)
    {
        $pembayaran = PembayaranModel::find($id);
        if (!$pembayaran) {
            return response()->json(
                ApiFormatter::createJson(404, 'Data pembayaran tidak ditemukan'), 404
            );
        }

        $data = $request->only(['transaksi_id', 'metode_pembayaran', 'jumlah_pembayaran', 'status']);
        $rules = [];
        if (array_key_exists('transaksi_id', $data)) {
            $rules['transaksi_id'] = 'integer';
        }
        if (array_key_exists('metode_pembayaran', $data)) {
            $rules['metode_pembayaran'] = 'string';
        }
        if (array_key_exists('jumlah_pembayaran', $data)) {
            $rules['jumlah_pembayaran'] = 'integer';
        }
        if (array_key_exists('status', $data)) {
            $rules['status'] = 'string';
        }

        if (!empty($rules)) {
            $validated = $request->validate($rules);
            $pembayaran->update($validated);
        }

        return response()->json(
            ApiFormatter::createJson(200, 'Pembayaran berhasil diperbarui sebagian', $pembayaran),
            200
        );
    }

    /**
     * DELETE /api/pembayaran/{id}
     */
    public function delete($id)
    {
        $pembayaran = PembayaranModel::find($id);

        if (!$pembayaran) {
            return response()->json(
                ApiFormatter::createJson(
                    404,
                    'Data pembayaran tidak ditemukan'
                ),
                404
            );
        }

        $pembayaran->delete();

        return response()->json(
            ApiFormatter::createJson(
                200,
                'Pembayaran berhasil dihapus'
            ),
            200
        );
    }
}
