<?php
namespace App\Helpers;
        
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class JwtAuth {
    
    public $key;
    
    public function __construct() {
        $this->key = 'mi clave secreta';
    }
    
    public function create($user, $decode = false) {
        $token = array (
            'sub'=> $user->id,
            'iat'=> time(),
            'exp'=> time() + (7*24*60*60),
            'origin' => 'googlelogin'
        );
        $jwt = JWT::encode($token, $this->key, 'HS256');
        $user->image =  $user->image;

        $user= array (
            'id'=> $user->id,
            'name'=> $user->name,
            'email'=> $user->email,
            'image'=> $user->image,
            'tasks_list'=> $user->tasks_list,

        );
    //DEVOLVER LOS DATOS DECODIFICADOS O EL TOKEN EN FUNCIÓN DE UN PARAMETRO
        if($decode){
            $data = [
                'status'=>'success',
                'code'  => 200,
                'token' => JWT::decode($jwt, $this->key, ['HS256']),
                'user'  => $user,
                'image' => $user['image'],
                'origin' => 'googlelogin'
            ];
        }else{
            $data = [
                'status'=>'success',
                'code'  => 200,
                'token' => $jwt,
                'user'  => $user,
                'image' => $user['image'],
                'origin' => 'googlelogin'
            ];
        }
        return $data;
    }

    public function signup($email, $password, $decode = false){
        //BUSCAR SI EXISTE EL USUARIO CON SUS CREDENCIALES
        $user = User::where([
                'email'=> $email,
                'password'=> $password
        ])->first();
        
        //COMPROBAR SI SON CORRECTAS
        $signup = false;
        if(is_object($user)){
            $signup = true;
        }
        
        //GENERAR EL TOKEN CON DATOS DE USUARIO IDENTIFICADO
        if ($signup){
            
            $token = array (
                'sub'=> $user->id,
                'iat'=> time(),
                'exp'=> time() + (7*24*60*60),
                'origin' => 'userlogin'
            );
            $jwt = JWT::encode($token, $this->key, 'HS256');

            $user->image = "http://localhost/users/" . $user->image;

            $userarray= array (
                'id'=> $user->id,
                'name'=> $user->name,
                'email'=> $user->email,
                'image'=> $user->image,
                'tasks_list'=> $user->tasks_list,
            );


        //DEVOLVER LOS DATOS DECODIFICADOS O EL TOKEN EN FUNCIÓN DE UN PARAMETRO
            if($decode){
                $data = [
                    'status'=>'success',
                    'token' => JWT::decode($jwt, $this->key, ['HS256']),
                    'user'  => $userarray,
                    //'avatar' => $user->image,
                    'origin' => 'userlogin'
                ];
            }else{
                $data = [
                    'status'=>'success',
                    'token' => $jwt,
                    'user'  => $userarray,
                    //'avatar' => $user->image,
                    'origin' => 'userlogin'
                ];
            }
        }else{
            $data = array(
                'status'=>'error',
                'message'=> 'El email o la contraseña son incorrectos'
            );
        }
        
        return $data;
    }
    
    public function checkToken($jwt, $decode = false){
        $auth = false;
        
        try{
            $jwt = str_replace('"','',$jwt);
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
        } catch (\Exception $e) {
            return false;
        }
        
        if($decode){
            return $decoded;
        } elseif(!empty($decoded) && is_object($decoded) && isset($decoded->sub)) {
            $auth = true;
        } else {
            $auth = false;
        }
        
        return $auth;
    }
}
