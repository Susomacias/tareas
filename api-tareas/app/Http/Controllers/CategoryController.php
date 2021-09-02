<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Category;
use App\Models\Article;
use App\Helpers\jwtAuth;
use Illuminate\Support\Facades\Auth;


class CategoryController extends Controller{
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
               'message' => 'No se ha creado la categoría, faltan datos'
            ];
        } else {
            $category = new Category();
            $category->user_id = $user->sub;
            $category->department_id = $params->department_id;
            $category->name = $params->name;
            $category->description = $params->description;
            $category->save();

            $data = array(
                'code' => 200,
                'status' => 'success',
                'category' => $category
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

    public function getCategoryByUser(Request $request){
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checToken = $jwtAuth->checkToken($token);

        //RECOGER DATOS POR POST
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);


        if ($checToken && !empty($params_array)) {
            
            $id = $params_array['id'];
            $categories = Category::where('user_id', $id)->orderBy('name')->get();
            $hash = md5(serialize($categories));

            return response()->json([
            'status'    =>'success',
            'list'      => $categories,
            'hash'      => $hash
            ], 200);  
        }
     }

     public function getCategoryByDepartment(Request $request){
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checToken = $jwtAuth->checkToken($token);

        //RECOGER DATOS POR POST
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);
        $id=$params_array['id'];

        if ($checToken && !empty($id)) {
            
            $categories = Category::where('department_id', $id)->orderBy('name')->get();
            $hash = md5(serialize($categories));

            return response()->json([
            'status'=>'success',
            'list'=>$categories,
            'hash'      => $hash
            ], 200);  
        }
     }

     public function updateCategory(Request $request){
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
            'department_id'=>$params_array['department_id'],
            'name'=>$params_array['name'],
            'description'=>$params_array['description'],
            'favorite'=>$params_array['favorite']
        );
    
        if ($checToken && !empty($update_data)) {
        
            $category = Category::where('id', $id)
                        ->update($update_data);

            $categories = Category::where('user_id', $userId)->orderBy('name')->get();
            $hash = md5(serialize($categories));
        
                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'changes' => $update_data,
                    'list'      => $categories,
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

    public function deleteCategory(Request $request) {

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
        
            $category = Category::where('id', $id)
                        ->where('user_id', $user->sub)
                        ->first();

            $update_data = array('category_id'=>null,);

            $departmentinarticles = Article::where('category_id', $id)
                                    ->update($update_data); 
        
            if (!empty($category)) {
                $category->delete();

            $categories = Category::where('user_id', $userId)->orderBy('name')->get();
            $hash = md5(serialize($categories));
        
                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'category' => $category,
                    'list'      => $categories,
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
        $category = Category::find($id);

        if (is_object($category)) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'category' => $category,
            );
        } else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'La Categoría no existe.'
            );
        }

        return response()->json($data, $data['code']);
    }


}