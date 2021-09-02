<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Category;
use App\Models\Article;
use App\Models\Element;
use App\Helpers\jwtAuth;
use Illuminate\Support\Facades\Auth;


class ElementController extends Controller{
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
            $element = new Element();
            $element->user_id = $user->sub;
            $element->name = $params->name;
            $element->description = $params->description;
            $element->unit_amount=$params->unit_amount;
            $element->unit_measurement=$params->unit_measurement;
            $element->unit_costprice=$params->unit_costprice;
            $element->unit_profitpercentage=$params->unit_profitpercentage;
            $element->unit_price=$params->unit_price;

            $element->surface_multiple=$params->surface_multiple;
            $element->surface_multiple_amount=$params->surface_multiple_amount;
            $element->surface_height=$params->surface_height;
            $element->surface_width=$params->surface_width;
            $element->surface_layout=$params->surface_layout;
            $element->surface_article_height=$params->surface_article_height;
            $element->surface_article_width=$params->surface_article_width;
            $element->surface_article_layout=$params->surface_article_layout;
            $element->surface_itens=$params->surface_itens;
            $element->surface_total_surfaces=$params->surface_total_surfaces;
            $element->surface_whole=$params->surface_whole;
            $element->surface_surcharge_item=$params->surface_surcharge_item;
            $element->surface_individual_cost=$params->surface_individual_cost;
            $element->surface_costprice=$params->surface_costprice;
            $element->surface_profitpercentage=$params->surface_profitpercentage;
            $element->surface_price=$params->surface_price;

            $element->coil_width=$params->coil_width;
            $element->coil_article_height=$params->coil_article_height;
            $element->coil_article_width=$params->coil_article_width;
            $element->coil_article_layout=$params->coil_article_layout;
            $element->coil_articles_amount=$params->coil_articles_amount;
            $element->coil_total_measure=$params->coil_total_measure;
            $element->coil_surcharge_item=$params->coil_surcharge_item;
            $element->coil_measured_cost=$params->coil_measured_cost;
            $element->coil_costprice=$params->coil_costprice;
            $element->coil_profitpercentage=$params->coil_profitpercentage;
            $element->coil_price=$params->coil_price;
            $element->save();

            $data = array(
                'code' => 200,
                'status' => 'success',
                'element' => $element
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

    public function getElementsByUser(Request $request){
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checToken = $jwtAuth->checkToken($token);

        //RECOGER DATOS POR POST
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);


        if ($checToken && !empty($params_array)) {
            
            $id = $params_array['id'];
            $elements = Element::where('user_id', $id)->orderBy('name')->get();
            $hash = md5(serialize($elements));

            return response()->json([
            'status'=>'success',
            'list'=>$elements,
            'hash' => $hash
            ], 200);  
        }
     }

     public function updateElement(Request $request){
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
        
            $element = Element::where('id', $id)
                        ->update($update_data);

            $elements = Element::where('user_id', $userId)->orderBy('name')->get();
            $hash = md5(serialize($elements));
        
                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'changes' => $update_data,
                    'list' => $elements,
                    'hash' => $hash
                    
                ];

            } else {
                $data = [
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'El elemento no existe'
                ];
            }

            return response()->json($data, $data['code']);
     }

    public function deleteElement(Request $request) {

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
        
            $element = Element::where('id', $id)
                        ->where('user_id', $user->sub)
                        ->first();
        
            if (!empty($element)) {
                $element->delete();

            $elements = Element::where('user_id', $userId)->orderBy('name')->get();
            $hash = md5(serialize($elements));
        
                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'element' => $element,
                    'list'      => $elements,
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
        $element = Element::find($id);

        if (is_object($element)) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'element' => $element,
            );
        } else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'El elemento no existe.'
            );
        }

        return response()->json($data, $data['code']);
    }
}