<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MerekModel;
use App\Helpers\ApiFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MerekController extends Controller
{
    public function index(Request $request)
    {
        $merek = MerekModel::orderBy('id', 'ASC')->get();

        $response = ApiFormatter::createJson(200, 'Get Data Merek Success', $merek);
        return response()->json($response);
    }

   public function create(Request $request)
{
    try {
        $params = $request->all();

        $validator = Validator::make($params, [
            'kode_merek' => 'required|max:20|unique:merek,kode_merek',
            'nama_merek' => 'required|max:100',
        ], [
            'kode_merek.required' => 'Kode Merek is required',
            'kode_merek.unique'  => 'Kode Merek already exists',
            'nama_merek.required' => 'Nama Merek is required',
        ]);

        if ($validator->fails()) {
            $response = ApiFormatter::createJson(
                400,
                'Bad Request',
                $validator->errors()->all()
            );
            return response()->json($response);
        }

        $merek = [
            'kode_merek' => $params['kode_merek'],
            'nama_merek' => $params['nama_merek'],
        ];

        $data = MerekModel::create($merek);
        $createdMerek = MerekModel::find($data->id);

        $response = ApiFormatter::createJson(
            200,
            'Create Merek Success',
            $createdMerek
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
            $merek = MerekModel::find($id);

            if (is_null($merek)) {
                return ApiFormatter::createJson(404, 'Merek Not Found');
            }

            return ApiFormatter::createJson(200, 'Get Detail Merek Success', $merek);

        } catch (\Exception $e) {
            return ApiFormatter::createJson(400, $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $params = $request->all();

            $preMerek = MerekModel::find($id);
            if (is_null($preMerek)) {
                return ApiFormatter::createJson(404, 'Data Not Found');
            }

            $validator = Validator::make($params, [
            'kode_merek' => 'required|max:20|unique:merek,kode_merek,' . $id,
            'nama_merek' => 'required|max:100',
            ]);

            if ($validator->fails()) {
                return ApiFormatter::createJson(400, 'Bad Request', $validator->errors()->all());
            }

            $merek = [
            'kode_merek' => $params['kode_merek'],
            'nama_merek' => $params['nama_merek'],
            ];

            $preMerek->update($merek);
            $updatedMerek = $preMerek->fresh();

            return ApiFormatter::createJson(200, 'Update Merek Success', $updatedMerek);

        } catch (\Exception $e) {
            return ApiFormatter::createJson(500, 'Internal Server Error', $e->getMessage());
        }
    }


    public function delete($id)
    {
        try {
            $merek = MerekModel::find($id);

            if (is_null($merek)) {
                return ApiFormatter::createJson(404, 'Data Not Found');
            }

            $merek->delete();

            return ApiFormatter::createJson(200, 'Delete Merek Success');

        } catch (\Exception $e) {
            return ApiFormatter::createJson(500, 'Internal Server Error', $e->getMessage());
        }
    }
}


