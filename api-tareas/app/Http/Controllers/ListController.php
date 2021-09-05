<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\ListModel;
use App\Models\TaskModel;
use App\Helpers\jwtAuth;
use Illuminate\Support\Facades\Auth;


class ListController extends Controller{

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
                'name' => 'required',
        ]);

        //GUARDAR EL ARTICULO
        if ($validate->fails()) {
            $data = [
              'code' => 400,
               'status' => 'error',
               'message' => 'No se ha creado el listado, faltan datos'
            ];
        } else {
            $listing = new ListModel();
            $listing->user_id = $user->sub;
            $listing->name = $params->name;
            $listing->save();

            $data = array(
                'code' => 200,
                'status' => 'success',
                'listing' => $listing
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

    public function reader(Request $request){
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checToken = $jwtAuth->checkToken($token);

        //RECOGER DATOS POR POST
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);
        $id = $params_array['id'];

        if ($checToken && !empty($id)) {
                        
            $listings = ListModel::where('user_id', $id)->orderBy('created_at')->get();
            $hash = md5(serialize($listings));

            return response()->json(array(
                'status'    => 'success',
                'list'      => $listings,
                'hash'      => $hash,
            ), 200);  
        }
     }

     public function update(Request $request){
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
            'name'=>$params_array['name'],
        );
    
        if ($checToken && !empty($update_data)) {
        
            $listing = ListModel::where('id', $id)
                        ->update($update_data);
        
            $listings = ListModel::where('user_id', $userId)->orderBy('created_at')->get();
            $hash = md5(serialize($listings));

            $data = array(
                'code' => 200,
                'status'    => 'success',
                'list'      => $listings,
                'hash'      => $hash
            );  
            
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'El post no existe'
            ];
        }

        return response()->json($data, $data['code']);
     }

    public function delete(Request $request) {

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
        
            $listing = ListModel::where('id', $id)
                        ->where('user_id', $user->sub)
                        ->first();

         $update_data = array('list_id'=>null,);   

         $departmentincategories = TaskModel::where('list_id', $id)
                                   ->update($update_data);

        
            if (!empty($listing)) {
                $listing->delete();

            $listings = ListModel::where('user_id', $userId)->orderBy('created_at')->get();
            $hash = md5(serialize($listings));
        
                $data = [
                    'code' => 200,
                    'status'    => 'success',
                    'list'      => $listings,
                    'hash'      => $hash
                ];

            } else {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Envía los datos correctamente'
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