<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Category;
use App\Models\Article;
use App\Models\Element;
use App\Models\Feature;
use App\Helpers\jwtAuth;
use Illuminate\Support\Facades\Auth;


class FeatureController extends Controller{
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
            $feature = new Feature();
            $feature->user_id = $user->sub;
            $feature->article_id = $params->article_id;
            $feature->name = $params->name;
            $feature->description = $params->description;
            $feature->unit_amount=$params->unit_amount;
            $feature->unit_measurement=$params->unit_measurement;
            $feature->unit_costprice=$params->unit_costprice;
            $feature->unit_profitpercentage=$params->unit_profitpercentage;
            $feature->unit_price=$params->unit_price;

            $feature->surface_multiple=$params->surface_multiple;
            $feature->surface_multiple_amount=$params->surface_multiple_amount;
            $feature->surface_height=$params->surface_height;
            $feature->surface_width=$params->surface_width;
            $feature->surface_layout=$params->surface_layout;
            $feature->surface_article_height=$params->surface_article_height;
            $feature->surface_article_width=$params->surface_article_width;
            $feature->surface_article_layout=$params->surface_article_layout;
            $feature->surface_itens=$params->surface_itens;
            $feature->surface_total_surfaces=$params->surface_total_surfaces;
            $feature->surface_whole=$params->surface_whole;
            $feature->surface_surcharge_item=$params->surface_surcharge_item;
            $feature->surface_individual_cost=$params->surface_individual_cost;
            $feature->surface_costprice=$params->surface_costprice;
            $feature->surface_profitpercentage=$params->surface_profitpercentage;
            $feature->surface_price=$params->surface_price;

            $feature->coil_width=$params->coil_width;
            $feature->coil_article_height=$params->coil_article_height;
            $feature->coil_article_width=$params->coil_article_width;
            $feature->coil_article_layout=$params->coil_article_layout;
            $feature->coil_articles_amount=$params->coil_articles_amount;
            $feature->coil_total_measure=$params->coil_total_measure;
            $feature->coil_surcharge_item=$params->coil_surcharge_item;
            $feature->coil_measured_cost=$params->coil_measured_cost;
            $feature->coil_costprice=$params->coil_costprice;
            $feature->coil_profitpercentage=$params->coil_profitpercentage;
            $feature->coil_price=$params->coil_price;
            $feature->save();

            $data = array(
                'code' => 200,
                'status' => 'success',
                'feature' => $feature
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

    public function createfromelement(Request $request){
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checToken = $jwtAuth->checkToken($token);

        //RECOGER DATOS POR POST
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);
        $id=$params_array['id'];
        $article_id=$params_array['article_id'];

        if ($checToken && !empty($params_array)) {

        //CONSEGUIR USUARIO IDENTIFICADO (llamada a funcion privada)
        $user = $this->getIdentity($request);

        //VALIDAR LOS DATOS
        $validate = \Validator::make($params_array, [
                'id' => 'required',
        ]);

        //GUARDAR EL ARTICULO
        if ($validate->fails()) {
            $data = [
              'code' => 400,
               'status' => 'error',
               'message' => 'No se ha creado el articulo, faltan datos'
            ];
        } else {
            $element = Element::where('id', $id)->get();

            $feature = new Feature(); 
            $feature->user_id = $user->sub;
            $feature->article_id = $article_id;
            $feature->name = $element[0]->name;
            $feature->description = $element[0]->description;
            $feature->unit_amount=$element[0]->unit_amount;
            $feature->unit_measurement=$element[0]->unit_measurement;
            $feature->unit_costprice=$element[0]->unit_costprice;
            $feature->unit_profitpercentage=$element[0]->unit_profitpercentage;
            $feature->unit_price=$element[0]->unit_price;

            $feature->surface_multiple=$element[0]->surface_multiple;
            $feature->surface_multiple_amount=$element[0]->surface_multiple_amount;
            $feature->surface_height=$element[0]->surface_height;
            $feature->surface_width=$element[0]->surface_width;
            $feature->surface_layout=$element[0]->surface_layout;
            $feature->surface_article_height=$element[0]->surface_article_height;
            $feature->surface_article_width=$element[0]->surface_article_width;
            $feature->surface_article_layout=$element[0]->surface_article_layout;
            $feature->surface_itens=$element[0]->surface_itens;
            $feature->surface_total_surfaces=$element[0]->surface_total_surfaces;
            $feature->surface_whole=$element[0]->surface_whole;
            $feature->surface_surcharge_item=$element[0]->surface_surcharge_item;
            $feature->surface_individual_cost=$element[0]->surface_individual_cost;
            $feature->surface_costprice=$element[0]->surface_costprice;
            $feature->surface_profitpercentage=$element[0]->surface_profitpercentage;
            $feature->surface_price=$element[0]->surface_price;

            $feature->coil_width=$element[0]->coil_width;
            $feature->coil_article_height=$element[0]->coil_article_height;
            $feature->coil_article_width=$element[0]->coil_article_width;
            $feature->coil_article_layout=$element[0]->coil_article_layout;
            $feature->coil_articles_amount=$element[0]->coil_articles_amount;
            $feature->coil_total_measure=$element[0]->coil_total_measure;
            $feature->coil_surcharge_item=$element[0]->coil_surcharge_item;
            $feature->coil_measured_cost=$element[0]->coil_measured_cost;
            $feature->coil_costprice=$element[0]->coil_costprice;
            $feature->coil_profitpercentage=$element[0]->coil_profitpercentage;
            $feature->coil_price=$element[0]->coil_price;
            $feature->save();

            $data = array(
                'code' => 200,
                'status' => 'success',
                'feature' => $feature
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

    public function getFeaturesByUser(Request $request){
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checToken = $jwtAuth->checkToken($token);

        //RECOGER DATOS POR POST
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);


        if ($checToken && !empty($params_array)) {
            
            $id = $params_array['id'];
            $features = Feature::where('user_id', $id)->orderBy('name')->get();
            $hash = md5(serialize($features));

            return response()->json([
            'status'=>'success',
            'list'=>$features,
            'hash' => $hash
            ], 200);  
        }
     }

     public function getFeaturesByArticle(Request $request){
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checToken = $jwtAuth->checkToken($token);

        //RECOGER DATOS POR POST
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);


        if ($checToken && !empty($params_array)) {
            
            $id = $params_array['id'];
            $features = Feature::where('article_id', $id)->orderBy('id')->get();
            $hash = md5(serialize($features));

            return response()->json([
            'status'=>'success',
            'list'=>$features,
            'hash' => $hash
            ], 200);  
        }
     }

     public function updateFeature(Request $request){
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
            'unit_amount'=>$params_array['unit_amount'],
            'unit_measurement'=>$params_array['unit_measurement'],
            'unit_costprice'=>$params_array['unit_costprice'],
            'unit_profitpercentage'=>$params_array['unit_profitpercentage'],
            'unit_price'=>$params_array['unit_price'],

            'surface_multiple'=>$params_array['surface_multiple'],
            'surface_multiple_amount'=>$params_array['surface_multiple_amount'],
            'surface_height'=>$params_array['surface_height'],
            'surface_width'=>$params_array['surface_width'],
            'surface_layout'=>$params_array['surface_layout'],
            'surface_article_height'=>$params_array['surface_article_height'],
            'surface_article_width'=>$params_array['surface_article_width'],
            'surface_article_layout'=>$params_array['surface_article_layout'],
            'surface_itens'=>$params_array['surface_itens'],
            'surface_total_surfaces'=>$params_array['surface_total_surfaces'],
            'surface_whole'=>$params_array['surface_whole'],
            'surface_surcharge_item'=>$params_array['surface_surcharge_item'],
            'surface_individual_cost'=>$params_array['surface_individual_cost'],
            'surface_costprice'=>$params_array['surface_costprice'],
            'surface_profitpercentage'=>$params_array['surface_profitpercentage'],
            'surface_price'=>$params_array['surface_price'],

            'coil_width'=>$params_array['coil_width'],
            'coil_article_height'=>$params_array['coil_article_height'],
            'coil_article_width'=>$params_array['coil_article_width'],
            'coil_article_layout'=>$params_array['coil_article_layout'],
            'coil_articles_amount'=>$params_array['coil_articles_amount'],
            'coil_total_measure'=>$params_array['coil_total_measure'],
            'coil_surcharge_item'=>$params_array['coil_surcharge_item'],
            'coil_measured_cost'=>$params_array['coil_measured_cost'],
            'coil_costprice'=>$params_array['coil_costprice'],
            'coil_profitpercentage'=>$params_array['coil_profitpercentage'],
            'coil_price'=>$params_array['coil_price'],  
        );
    
        if ($checToken && !empty($update_data)) {
        
            $feature = Feature::where('id', $id)
                        ->update($update_data);

            $features = Feature::where('user_id', $userId)->orderBy('name')->get();
            $hash = md5(serialize($features));
        
                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'changes' => $update_data,
                    'list' => $features,
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

    public function deleteFeature(Request $request) {

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
        
            $feature = Feature::where('id', $id)
                        ->where('user_id', $user->sub)
                        ->first();
        
            if (!empty($feature)) {
                $feature->delete();

            $features = Feature::where('user_id', $userId)->orderBy('name')->get();
            $hash = md5(serialize($features));
        
                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'feature' => $feature,
                    'list'      => $features,
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