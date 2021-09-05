<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\TaskModel;
use App\Helpers\jwtAuth;
use Illuminate\Support\Facades\Auth;


class TaskController extends Controller{
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
               'message' => 'No se ha creado la tarea, faltan datos'
            ];
        } else {
            $task = new TaskModel();
            $task->user_id = $user->sub;
            $task->list_id = $params->list_id;
            $task->name = $params->name;
            $task->description = $params->description;
            $task->color = $params->color;
            $task->date = $params->date;
            $task->save();

            $data = array(
                'code' => 200,
                'status' => 'success',
                'task' => $task
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
        $id=$params_array;

        if ($checToken && !empty($id)) {
            
            $tasks = TaskModel::where('user_id', $id)->orderBy('created_at')->get();
            $hash = md5(serialize($tasks));

            return response()->json([
            'status'=>'success',
            'list'=>$tasks,
            'hash' => $hash
            ], 200);  
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
            'list_id'=>$params_array['list_id'],
            'name'=>$params_array['name'],
            'description'=>$params_array['description'],
            'color'=>$params_array['color'],
            'date'=>$params_array['date']
        );
    
        if ($checToken && !empty($update_data)) {
        
            $task = TaskModel::where('id', $id)
                        ->update($update_data);

            $tasks = TaskModel::where('user_id', $userId)->orderBy('created_at')->get();
            $hash = md5(serialize($tasks));
        
                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'changes' => $update_data,
                    'list'      => $tasks,
                    'hash'      => $hash
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
        
            $task = TaskModel::where('id', $id)
                        ->where('user_id', $user->sub)
                        ->first();
        
            if (!empty($task)) {
                $task->delete();

            $tasks = TaskModel::where('user_id', $userId)->orderBy('created_at')->get();
            $hash = md5(serialize($tasks));
        
                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'task' => $task,
                    'list'      => $tasks,
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