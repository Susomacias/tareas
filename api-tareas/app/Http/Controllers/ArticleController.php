<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Category;
use App\Models\Article;
use App\Helpers\jwtAuth;
use Illuminate\Support\Facades\Auth;


class ArticleController extends Controller{
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
            $article = new Article();
            $article->user_id = $user->sub;
            $article->department_id = $params->department_id;
            $article->category_id = $params->category_id;
            $article->name = $params->name;
            $article->description = $params->description;
            $article->save();

            $data = array(
                'code' => 200,
                'status' => 'success',
                'article' => $article
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

    public function getArticleByUser(Request $request){
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checToken = $jwtAuth->checkToken($token);

        //RECOGER DATOS POR POST
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);


        if ($checToken && !empty($params_array)) {
            
            $id = $params_array['id'];
            $articles = Article::where('user_id', $id)->orderBy('name')->get();
            $hash = md5(serialize($articles));

            return response()->json([
            'status'=>'success',
            'list'=>$articles,
            'hash' => $hash
            ], 200);  
        }
     }

     public function getArticleByDepartment(Request $request){
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checToken = $jwtAuth->checkToken($token);

        //RECOGER DATOS POR POST
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);
        $id=$params_array['id'];

        if ($checToken && !empty($id)) {
            
            $articles = Article::where('department_id', $id)->orderBy('name')->get();
            $hash = md5(serialize($articles));

            return response()->json([
            'status'=>'success',
            'list'=>$articles,
            'hash'=> $hash
            ], 200);  
        }
     }

     public function getArticleByCategory(Request $request){
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checToken = $jwtAuth->checkToken($token);

        //RECOGER DATOS POR POST
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);
        $id=$params_array['id'];
        //var_dump($id);
        
        //$id = array('id'=>$json);

        if ($checToken && !empty($id)) {
            
            $articles = Article::where('category_id', $id)->orderBy('name')->get();
            //var_dump($articles);
            $hash = md5(serialize($articles));

            return response()->json([
            'status'=>'success',
            'list'=>$articles,
            'hash' => $hash
            ], 200);  
        }
     }

     public function updateArticle(Request $request){
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
            'category_id'=>$params_array['category_id'],
            'name'=>$params_array['name'],
            'description'=>$params_array['description'],
            'favorite'=>$params_array['favorite']
        );
    
        if ($checToken && !empty($update_data)) {
        
            $article = Article::where('id', $id)
                        ->update($update_data);

            $articles = Article::where('user_id', $userId)->orderBy('name')->get();
            $hash = md5(serialize($articles));
        
                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'changes' => $update_data,
                    'list' => $articles,
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

    public function deleteArticle(Request $request) {

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
        
            $article = Article::where('id', $id)
                        ->where('user_id', $user->sub)
                        ->first();
        
            if (!empty($article)) {
                $article->delete();

            $articles = Article::where('user_id', $userId)->orderBy('name')->get();
            $hash = md5(serialize($articles));
        
                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'article' => $article,
                    'list'      => $articles,
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
        $article = Article::find($id);

        if (is_object($article)) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'article' => $article,
            );
        } else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'El articulo no existe.'
            );
        }

        return response()->json($data, $data['code']);
    }


}