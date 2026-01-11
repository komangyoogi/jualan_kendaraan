<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KategoriModel;
use App\Helpers\ApiFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KategoriController extends Controller
{
    public function index(Request $request)
    {
        $kategori = KategoriModel::orderBy('id', 'ASC')->get();

        $response = ApiFormatter::createJson(
            200,
            'Get Data Kategori Success',
            $kategori
        );
        return response()->json($response);
    }

    public function create(Request $request)
    {
        try {
            $params = $request->all();

            $validator = Validator::make($params, [
                'kode_kategori' => 'required|max:20|unique:kategori,kode_kategori',
                'nama_kategori' => 'required|max:100',
                'deskripsi'     => 'nullable',
            ], [
                'kode_kategori.required' => 'Kode Kategori is required',
                'kode_kategori.unique'  => 'Kode Kategori already exists',
                'nama_kategori.required' => 'Nama Kategori is required',
            ]);

            if ($validator->fails()) {
                $response = ApiFormatter::createJson(
                    400,
                    'Bad Request',
                    $validator->errors()->all()
                );
                return response()->json($response);
            }

            $kategori = [
                'kode_kategori' => $params['kode_kategori'],
                'nama_kategori' => $params['nama_kategori'],
                'deskripsi'     => $params['deskripsi'] ?? null,
            ];

            $data = KategoriModel::create($kategori);
            $createdKategori = KategoriModel::find($data->id);

            $response = ApiFormatter::createJson(
                200,
                'Create Kategori Success',
                $createdKategori
            );
            return response()->json($response);

        } catch (\Exception $e) {
            $response = ApiFormatter::createJson(
                500,
                'Internal Server Error',
                $e->getMessage()
            );
            return response()->json($response);
        }
    }

    public function detail($id)
    {
        try {
            $kategori = KategoriModel::find($id);

            if (is_null($kategori)) {
                return ApiFormatter::createJson(
                    404,
                    'Kategori Not Found'
                );
            }

            return ApiFormatter::createJson(
                200,
                'Get Detail Kategori Success',
                $kategori
            );

        } catch (\Exception $e) {
            return ApiFormatter::createJson(
                400,
                $e->getMessage()
            );
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $params = $request->all();

            $preKategori = KategoriModel::find($id);
            if (is_null($preKategori)) {
                return ApiFormatter::createJson(
                    404,
                    'Data Not Found'
                );
            }

            $validator = Validator::make($params, [
                'kode_kategori' => 'required|max:20|unique:kategori,kode_kategori,' . $id,
                'nama_kategori' => 'required|max:100',
                'deskripsi'     => 'nullable',
            ]);

            if ($validator->fails()) {
                return ApiFormatter::createJson(
                    400,
                    'Bad Request',
                    $validator->errors()->all()
                );
            }

            $kategori = [
                'kode_kategori' => $params['kode_kategori'],
                'nama_kategori' => $params['nama_kategori'],
                'deskripsi'     => $params['deskripsi'] ?? null,
            ];

            $preKategori->update($kategori);
            $updatedKategori = $preKategori->fresh();

            return ApiFormatter::createJson(
                200,
                'Update Kategori Success',
                $updatedKategori
            );

        } catch (\Exception $e) {
            return ApiFormatter::createJson(
                500,
                'Internal Server Error',
                $e->getMessage()
            );
        }
    }

    public function delete($id)
    {
        try {
            $kategori = KategoriModel::find($id);

            if (is_null($kategori)) {
                return ApiFormatter::createJson(
                    404,
                    'Data Not Found'
                );
            }

            $kategori->delete();

            return ApiFormatter::createJson(
                200,
                'Delete Kategori Success'
            );

        } catch (\Exception $e) {
            return ApiFormatter::createJson(
                500,
                'Internal Server Error',
                $e->getMessage()
            );
        }
    }
}
