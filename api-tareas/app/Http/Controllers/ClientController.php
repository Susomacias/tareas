<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Client;
use App\Models\Budguet;
use App\Helpers\jwtAuth;
use Illuminate\Support\Facades\Auth;


class ClientController extends Controller{
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
               'message' => 'No se ha creado el articulo, faltan datos'
            ];
        } else {
            $client = new Client();
            $client->user_id = $user->sub;
            $client->name = $params->name;
            $client->name = $params->name;
            $client->email = $params->email;
            $client->phone = $params->phone;
            $client->address = $params->address;
            $client->data = $params->data;
            $client->observations = $params->observations;
            $client->save();

            $data = array(
                'code' => 200,
                'status' => 'success',
                'client' => $client
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

    public function getClientByUser(Request $request){
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checToken = $jwtAuth->checkToken($token);

        //RECOGER DATOS POR POST
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);


        if ($checToken && !empty($params_array)) {
            
            $id = $params_array['id'];
            $clients = Client::where('user_id', $id)->orderBy('name')->get();
            $hash = md5(serialize($clients));

            return response()->json([
            'status'=>'success',
            'list'=>$clients,
            'hash' => $hash
            ], 200);  
        }
     }


     public function updateClient(Request $request){
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
            'email'=>$params_array['email'],
            'phone'=>$params_array['phone'],
            'address'=>$params_array['address'],
            'data'=>$params_array['data'],
            'observations'=>$params_array['observations'],
            'favorite'=>$params_array['favorite'],        
        );
    
        if ($checToken && !empty($update_data)) {
        
            $client = Client::where('id', $id)
                        ->update($update_data);

            $clients = Client::where('user_id', $userId)->orderBy('name')->get();
            $hash = md5(serialize($clients));
        
                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'changes' => $update_data,
                    'list' => $clients,
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

    public function deleteClient(Request $request) {

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
        
            $client = Client::where('id', $id)
                        ->where('user_id', $user->sub)
                        ->first();
        
            if (!empty($client)) {
                $client->delete();

            $clients = Client::where('user_id', $userId)->orderBy('name')->get();
            $hash = md5(serialize($clients));
        
                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'client' => $client,
                    'list'      => $clients,
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

     public function detail($id){
        $client = Client::find($id);

        if (is_object($client)) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'client' => $client,
            );
        } else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'El cliente no existe.'
            );
        }

        return response()->json($data, $data['code']);
    }

    public function getBudguetByClient(Request $request){
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checToken = $jwtAuth->checkToken($token);

        //RECOGER DATOS POR POST
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);
        $id=$params_array['id'];

        if ($checToken && !empty($id)) {
            
            $budguets = Budguet::where('client_id', $id)->orderBy('name')->get();
            $hash = md5(serialize($budguets));

            return response()->json([
            'status'=>'success',
            'list'=>$budguets,
            'hash'      => $hash
            ], 200);  
        }
     }
}