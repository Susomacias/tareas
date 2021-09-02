<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Row;
use App\Models\Budguet;
use App\Models\Client;
use App\Helpers\jwtAuth;
use Illuminate\Support\Facades\Auth;


class RowController extends Controller{
    private function getIdentity($request) {
        $jwtAuth = new JwtAuth();
        $token = $request->header('Authorization', null);
        $user = $jwtAuth->checkToken($token, true);

        return $user;
    }

    public function create(Request $request){
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checToken = $jwtAuth->checkToken($token);

        //RECOGER DATOS POR POST
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);


        if ($checToken && !empty($params_array)) {

        //CONSEGUIR USUARIO IDENTIFICADO (llamada a funcion privada)
        $user = $this->getIdentity($request);

        //VALIDAR LOS DATOS
        $validate = \Validator::make($params_array, [
               // 'name' => 'required',
        ]);

        //GUARDAR EL ARTICULO
        if ($validate->fails()) {
            $data = [
              'code' => 400,
               'status' => 'error',
               'message' => 'No se ha creado el articulo, faltan datos'
            ];
        } else {
            $row = new Row();
            $row->user_id = $user->sub;
            $row->budguet_id = $params->budguet_id;
            $row->amount = $params->amount;
            $row->name = $params->name;
            $row->description = $params->description;
            $row->price = $params->price;
            $row->tax = $params->tax;
            $row->total = $params->total;
            $row->save();

            $data = array(
                'code' => 200,
                'status' => 'success',
                'row' => $row
            );
        }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Envía los datos correctamente'
             ];
        }
        return response()->json($data, $data['code']);
    }

    public function getRowByUser(Request $request){
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checToken = $jwtAuth->checkToken($token);

        //RECOGER DATOS POR POST
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);


        if ($checToken && !empty($params_array)) {
            
            $id = $params_array['id'];
            $rows = Row::where('user_id', $id)->orderBy('name')->get();
            $hash = md5(serialize($rows));

            return response()->json([
            'status'=>'success',
            'list'=>$rows,
            'hash' => $hash
            ], 200);  
        }
     }

     public function getRowByBudguet(Request $request){
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checToken = $jwtAuth->checkToken($token);

        //RECOGER DATOS POR POST
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);
        $id=$params_array['id'];

        if ($checToken && !empty($id)) {
            
            $rows = Row::where('budguet_id', $id)->orderBy('name')->get();
            $hash = md5(serialize($rows));

            return response()->json([
            'status'=>'success',
            'list'=>$rows,
            'hash'=> $hash
            ], 200);  
        }
     }

     public function getRowByBudguetpdf(Request $request){
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checToken = $jwtAuth->checkToken($token);

        //RECOGER DATOS POR POST
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);
        $id=$params_array['id'];

        if ($checToken && !empty($id)) {
            
            $rows = Row::where('budguet_id', $id)
                    ->orderBy('name')
                    ->get(['amount','name','description','total']);

            $hash = md5(serialize($rows));

            return response()->json([
            'status'=>'success',
            'list'=>$rows,
            'hash'=> $hash
            ], 200);  
        }
     }

     public function updateRow(Request $request){
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checToken = $jwtAuth->checkToken($token);
    
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);
        $id=$params_array['id'];
        $userId=$params_array['user_id'];
        $name=$params_array['name'];

        $update_data = array(
            'amount'=>$params_array['amount'],
            'name'=>$params_array['name'],
            'description'=>$params_array['description'],
            'price'=>$params_array['price'],
            'tax'=>$params_array['tax'],
            'total'=>$params_array['total'],
        );
    
        if ($checToken && !empty($update_data)) {
        
            $row = Row::where('id', $id)
                        ->update($update_data);

            $rows = Row::where('user_id', $userId)->orderBy('name')->get();
            $hash = md5(serialize($rows));
        
                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'changes' => $update_data,
                    'list' => $rows,
                    'hash' => $hash
                    
                ];

            } else {
                $data = [
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'El post no existe'
                ];
            }

            return response()->json($data, $data['code']);
     }

    public function deleteRow(Request $request) {

        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checToken = $jwtAuth->checkToken($token);
    
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);
        $userId=$params_array['user_id'];
        $id=$params_array['id'];
    
    
        if ($checToken && !empty($params_array)) {

            $user = $this->getIdentity($request);
        
            $row = Row::where('id', $id)
                        ->where('user_id', $user->sub)
                        ->first();
        
            if (!empty($row)) {
                $row->delete();

            $rows = Row::where('user_id', $userId)->orderBy('name')->get();

            $hash = md5(serialize($rows));
        
                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'row' => $row,
                    'list'      => $rows,
                    'hash'      => $hash
                ];

            } else {
                $data = [
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'El post no existe'
                ];
            }

        } else {
            $data = [
            'code' => 400,
            'status' => 'error',
            'message' => 'Envía los datos correctamente'
            ];
        }
            return response()->json($data, $data['code']);
     }

}