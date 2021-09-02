<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Department;
use App\Models\Category;
use App\Models\Article;
use App\Helpers\jwtAuth;
use DB;
use Illuminate\Support\Facades\Auth;


class DepartmentController extends Controller{

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
               'message' => 'No se ha creado el departamento, faltan datos'
            ];
        } else {
            $department = new Department();
            $department->user_id = $user->sub;
            $department->name = $params->name;
            $department->description = $params->description;
            $department->save();

            $data = array(
                'code' => 200,
                'status' => 'success',
                'department' => $department
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

    public function getDepartmentByUser(Request $request){
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checToken = $jwtAuth->checkToken($token);

        //RECOGER DATOS POR POST
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);


        if ($checToken && !empty($params_array)) {
            
            $id = $params_array['id'];
            $departments = Department::where('user_id', $id)->orderBy('name')->get();
            $hash = md5(serialize($departments));

            return response()->json(array(
                'status'    => 'success',
                'list'      => $departments,
                'hash'      => $hash,
            ), 200);  
        }
     }

     public function updateDepartment(Request $request){
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
            'description'=>$params_array['description'],
            'favorite'=>$params_array['favorite']
        );
    
        if ($checToken && !empty($update_data)) {
        
            $department = Department::where('id', $id)
                        ->update($update_data);
        
            $departments = Department::where('user_id', $userId)->orderBy('name')->get();
            $hash = md5(serialize($departments));

            $data = array(
                'code' => 200,
                'status'    => 'success',
                'list'      => $departments,
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

    public function deleteDepartment(Request $request) {

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
        
            $department = Department::where('id', $id)
                        ->where('user_id', $user->sub)
                        ->first();

         $update_data = array('department_id'=>null,);

         $departmentinarticles = Article::where('department_id', $id)
                                 ->update($update_data);   

         $departmentincategories = Category::where('department_id', $id)
                                   ->update($update_data);

        
            if (!empty($department)) {
                $department->delete();

            $departments = Department::where('user_id', $userId)->orderBy('name')->get();
            $hash = md5(serialize($departments));
        
                $data = [
                    'code' => 200,
                    'status'    => 'success',
                    'list'      => $departments,
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

     public function detail($id){
        $department = Department::find($id);

        if (is_object($department)) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'department' => $department,
            );
        } else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'El departamento no existe.'
            );
        }

        return response()->json($data, $data['code']);
    }

}