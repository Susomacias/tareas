<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Budguet;
use App\Models\Row;
use App\Models\Client;
use App\Helpers\jwtAuth;
use Illuminate\Support\Facades\Auth;


class BudguetController extends Controller{
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
                //'name' => 'required',
        ]);

        //GUARDAR EL ARTICULO
        if ($validate->fails()) {
            $data = [
              'code' => 400,
               'status' => 'error',
               'message' => 'No se ha creado el articulo, faltan datos'
            ];
        } else {
            $user_id = $user->sub;
            $last_number = Budguet::where('user_id', $user_id)->latest('id')->first();
           // var_dump($last_number);

            if($last_number==NULL){
            $number_budguet = 1;
            }else{
            $number_last_budguet = $last_number->number_budguet;
            $number_budguet = $number_last_budguet + 1;
            }
            

            $budguet = new Budguet();
            $budguet->user_id = $user->sub;
            $budguet->number_budguet = $number_budguet;
            $budguet->client_id = $params->client_id;
            $budguet->name = $params->name;
            $budguet->description = $params->description;
            $budguet->price = $params->price;
            $budguet->tax = $params->tax;
            $budguet->total = $params->total;
            $budguet->save();

            $data = array(
                'code' => 200,
                'status' => 'success',
                'budguet' => $budguet
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

    public function getBudguetByUser(Request $request){
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checToken = $jwtAuth->checkToken($token);

        //RECOGER DATOS POR POST
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);


        if ($checToken && !empty($params_array)) {
            
            $id = $params_array['id'];
            $budguets = Budguet::where('user_id', $id)->orderBy('name')->get();
            $hash = md5(serialize($budguets));

            return response()->json([
            'status'=>'success',
            'list'=>$budguets,
            'hash' => $hash
            ], 200);  
        }
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
            
            $budguets = Budguet::where('client_id', $id)->orderBy('updated_at')->get();
            $hash = md5(serialize($budguets));

            return response()->json([
            'status'=>'success',
            'list'=>$budguets,
            'hash'=> $hash
            ], 200);  
        }
     }

     public function detail($id){
        $budguet = Budguet::find($id);

        if (is_object($budguet)) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'budguet' => $budguet,
            );
        } else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'El presupuesto no existe.'
            );
        }

        return response()->json($data, $data['code']);
    }

     public function updateBudguet(Request $request){
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
            'client_id'=>$params_array['client_id'],
            'name'=>$params_array['name'],
            'description'=>$params_array['description'],
            'price'=>$params_array['price'],
            'tax'=>$params_array['tax'],
            'total'=>$params_array['total'],
        );
    
        if ($checToken && !empty($update_data)) {
        
            $budguet = Budguet::where('id', $id)
                        ->update($update_data);

            $budguets = Budguet::where('user_id', $userId)->orderBy('name')->get();
            $hash = md5(serialize($budguets));
        
                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'changes' => $update_data,
                    'list' => $budguets,
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

    public function deleteBudguet(Request $request) {

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
        
            $budguet = Budguet::where('id', $id)
                        ->where('user_id', $user->sub)
                        ->first();
        
            if (!empty($budguet)) {
                $budguet->delete();

            $budguets = Budguet::where('user_id', $userId)->orderBy('name')->get();
            $hash = md5(serialize($budguets));
        
                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'budguet' => $budguet,
                    'list'      => $budguets,
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