<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KendaraanModel;
use App\Helpers\ApiFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\MerekModel;
use App\Models\KategoriModel;


class KendaraanController extends Controller
{
    public function index(Request $request)
    {
        $kendaraan = KendaraanModel::orderby('id', 'ASC')->get();

        $response = ApiFormatter::createJson(200, 'Get Data Kendaraan Success', $kendaraan);
        return response()->json($response);
    }

    public function create(Request $request)
    {
        try {
            $params = $request->all();

            $validator = Validator::make($params, [
                 'nama_kendaraan' => 'required|max:150',
                 'harga' => 'required|numeric',
                 'stok' => 'required|integer',
                 'merek_id' => 'required|exists:merek,id',
                 'kategori_id' => 'required|exists:kategori,id',
                 'deskripsi' => 'nullable'
            ], [
                'nama_kendaraan.required' => 'Nama Kendaraan is required',
                'harga.required' => 'Harga is required',
                'harga.numeric' => 'Harga must be numeric',
                'stok.required' => 'Stok is required',
                'stok.integer' => 'Stok must be integer',
            ]);

            if ($validator->fails()) {
                $response = ApiFormatter::createJson(400, 'Bad Request', $validator->errors()->all());
                return response()->json($response);
            }

            $kendaraan = [
                'nama_kendaraan' => $params['nama_kendaraan'],
                'harga' => $params['harga'],
                'stok' => $params['stok'],
                'merek_id' => $params['merek_id'], 
                'kategori_id' => $params['kategori_id'],
                'deskripsi' => $params['deskripsi'] ?? null,
];


            $data = KendaraanModel::create($kendaraan);
            $createdKendaraan = KendaraanModel::find($data->id);

            $response = ApiFormatter::createJson(200, 'Create Kendaraan Success', $createdKendaraan);
            return response()->json($response);

        } catch (\Exception $e) {
            $response = ApiFormatter::createJson(500, 'Internal Server Error', $e->getMessage());
            return response()->json($response);
        }
    }

    public function detail($id)
    {
        try {
            $kendaraan = KendaraanModel::find($id);

            if (is_null($kendaraan)) {
                return ApiFormatter::createJson(404, 'Kendaraan Not Found');
            }

            $response = ApiFormatter::createJson(200, 'Get Detail Kendaraan Success', $kendaraan);
            return response()->json($response);

        } catch (\Exception $e) {
            $response = ApiFormatter::createJson(400, $e->getMessage());
            return response()->json($response);
        }
    }

   public function update(Request $request, $id)
{
    try {
        $params = $request->request->all();
        if (empty($params)) {
            parse_str($request->getContent(), $params);
        }

        $preKendaraan = KendaraanModel::find($id);
        if (is_null($preKendaraan)) {
            return response()->json(
                ApiFormatter::createJson(404, 'Data Not Found')
            );
        }

        $validator = Validator::make($params, [
            'nama_kendaraan' => 'required|max:150',
            'harga' => 'required|numeric',
            'stok' => 'required|integer',
            'merek_id' => 'required|exists:merek,id',
            'kategori_id' => 'required|exists:kategori,id',
            'deskripsi' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json(
                ApiFormatter::createJson(400, 'Bad Request', $validator->errors()->all())
            );
        }

        $preKendaraan->update([
            'nama_kendaraan' => $params['nama_kendaraan'],
            'harga'          => $params['harga'],
            'stok'           => $params['stok'],
            'merek_id'       => $params['merek_id'],
            'deskripsi'      => $params['deskripsi'] ?? null,
        ]);

        return response()->json(
            ApiFormatter::createJson(200, 'Update Kendaraan Success', $preKendaraan->fresh())
        );

    } catch (\Exception $e) {
        return response()->json(
            ApiFormatter::createJson(500, 'Internal Server Error', $e->getMessage())
        );
    }
}

    public function delete($id)
    {
        try {
            $kendaraan = KendaraanModel::find($id);

            if (is_null($kendaraan)) {
                return ApiFormatter::createJson(404, 'Data Not Found');
            }

            $kendaraan->delete();

            $response = ApiFormatter::createJson(200, 'Delete Kendaraan Success');
            return response()->json($response);

        } catch (\Exception $e) {
            $response = ApiFormatter::createJson(500, 'Internal Server Error', $e->getMessage());
            return response()->json($response);
        }
    }

    public function getByMerek($merek_id)
{
    try {

        // cek merek
        $merek = MerekModel::find($merek_id);

        if (is_null($merek)) {
            return response()->json(
                ApiFormatter::createJson(404, 'Merek Not Found')
            );
        }

        // ambil kendaraan berdasarkan merek
        $kendaraan = KendaraanModel::where('merek_id', $merek_id)
            ->orderBy('nama_kendaraan', 'ASC')
            ->get();

        $result = [
            "merek" => [
                "id" => $merek->id,
                "nama_merek" => $merek->nama_merek
            ],
            "kendaraan" => $kendaraan
        ];

        return response()->json(
            ApiFormatter::createJson(
                200,
                "Get Kendaraan by Merek Success",
                $result
            )
        );

    } catch (\Exception $e) {
        return response()->json(
            ApiFormatter::createJson(
                500,
                "Internal Server Error",
                $e->getMessage()
            )
        );
    }
}

public function getByKategori($kategori_id)
{
    try {

        // cek kategori
        $kategori = KategoriModel::find($kategori_id);

        if (is_null($kategori)) {
            return response()->json(
                ApiFormatter::createJson(404, 'Kategori Not Found')
            );
        }

        // ambil kendaraan berdasarkan kategori
        $kendaraan = KendaraanModel::where('kategori_id', $kategori_id)
            ->orderBy('nama_kendaraan', 'ASC')
            ->get();

        $result = [
            "kategori" => [
                "id" => $kategori->id,
                "kode_kategori" => $kategori->kode_kategori,
                "nama_kategori" => $kategori->nama_kategori
            ],
            "kendaraan" => $kendaraan
        ];

        return response()->json(
            ApiFormatter::createJson(
                200,
                "Get Kendaraan by Kategori Success",
                $result
            )
        );

    } catch (\Exception $e) {
        return response()->json(
            ApiFormatter::createJson(
                500,
                "Internal Server Error",
                $e->getMessage()
            )
        );
    }
}


}