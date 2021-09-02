<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use App\Helpers\jwtAuth;
use Illuminate\Support\Facades\Auth;
use vendor\autoload;
use App\Http\Controllers\Auth\GoogleSocialiteController;
use Google\Client;

class Captcha
{
    public function getCaptcha($SecretKey)
    {
        $answer = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6Le-tuAaAAAAAHWMoqaqA8ctpuLukA79UICBi3l8={$SecretKey}");
        $return = json_decode($answer);
        return $return;
    }
}


class UserController extends Controller
{

    public function register(Request $request)
    {
        // RECOGER DATOS DE USUARIO POR POST
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if (!empty($params) && !empty($params_array)) {
            //LIMPIAR DATOS
            $params_array = array_map('trim', $params_array);

            // VALIDAR DATOS
            $validate = \Validator::make($params_array, [
                'name' => 'required|alpha',
                'email' => 'required|email|unique:users',
                'password' => 'required',
            ]);

            if ($validate->fails()) {
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'El usuario no se ha creado',
                    'errors' => $validate->errors()
                );
            } else {

                // CIFRAR LA CONTRASEÑA
                $pwd = hash('sha256', $params->password);

                // CREAR USUARIO
                $user = new User();
                $user->name = $params_array['name'];
                $user->email = $params_array['email'];
                $user->password = $pwd;
                $user->image = 'default-image.png';

                //GUARDAR EL USUARIO
                $user->save();

                $data = array(
                    'status' => 'succes',
                    'code' => 200,
                    'message' => 'El usuario se ha creado correctamente',
                    'user' => $user
                );
            }
        } else {
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'Los datos enviados no son correctos',
            );
        }
        return response()->json($data, $data['code']);
    }

    public function login(Request $request)
    {
        $jwtAuth = new \JwtAuth();
        //RECIBIR DATOS POR POST
        $json = $request->input('json', null);
        $captcha = $request->input('captcha', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);
        //VALIDAR ESOS DATOS
        $validate = \Validator::make($params_array, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validate->fails()) {
            $signup = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'El usuario no se ha podido identificar',
                'errors' => $validate->errors()
            );
        } else {

            if ($captcha) {
                $ObjCaptcha = new Captcha();
                $return = $ObjCaptcha->getCaptcha($captcha);
                $return->success == true && $return->score > 0.5;

                //CIFRAR PASSWORD
                $pwd = hash('sha256', $params->password);

                //DEVOLVER TOKEN O DATOS
                $signup = $jwtAuth->signup($params->email, $pwd);

                if (!empty($params->gettoken)) {
                    $signup = $jwtAuth->signup($params->email, $pwd, true);
                }

            } else {
                $data = array(
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Eres un Robot'
                );
            }
        }


        return response()->json($signup, 200);
    }

    public function signup($email, $password, $decode = false) {
        //BUSCAR SI EXISTE EL USUARIO CON SUS CREDENCIALES
        $user = User::where([
            'email'=> $email,
            'password'=> $password
        ])->first();
        
        //GENERAR EL TOKEN CON DATOS DE USUARIO IDENTIFICADO
        if (is_object($user)) {
            $jwtAuth = new \JwtAuth();
            $jwt = $jwtAuth->create($user, $decode);
            $data = [
                'status'    => 'success',
                'token'     => $jwt,
                'user'      => $user,
                'avatar'    => $user->image
            ];
        } else {
            $data = array(
                'status'    => 'error',
                'message'   => 'El email o la contraseña no son correctos.'
            );
        }
        return $data;
    }

    public function update(Request $request)
    {
        //COMPROBAR SI EL USUARIO ESTÁ IDENTIFICADO
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checToken = $jwtAuth->checkToken($token);

        //RECOGER DATOS POR POST
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if ($checToken && !empty($params_array)) {

            //SACAR USUARIO IDENTIFICADO
            $user = $jwtAuth->checkToken($token, true);


            //VALIDAR LOS DATOS
            $validate = \Validator::make($params_array, [
                'name' => 'required|alpha',
                'email' => 'required|email|unique:users' . $user->sub
            ]);

            //QUITAR LOS CAMPOS QUE NO QUIERO ACTUALIZAR
            unset($params_array['id']);
            unset($params_array['password']);
            unset($params_array['image']);
            unset($params_array['create_at']);

            //ACTUALIZAR USUARIO EN BBD
            $user_update = User::where('id', $user->sub)->update($params_array);

            //DEVOLVER ARRAY CON RESULTADO
            $data = array(
                'code' => 200,
                'status' => 'success',
                'user' => $user,
                'change' => $params_array
            );
        } else {
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'El usuario no está identificado.'
            );
        }
        return response()->json($data, $data['code']);
    }

    public function upload(Request $request)
    {
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $token = $jwtAuth->checkToken($token, true);

        if ($token) { // Comprobamos que está autenticado
            //Recoger datos de la petición
            $image = $request->file('file0');
            $json = $request->input('json', null);
            if ($json != null) {
                $params_array = json_decode($json, true);
            };
            $user = [ // Montamos los datos de la tabla usuario actualizada
                'id'    => $token->sub
            ];
            if (!empty($params_array)) { // Hay datos en la variable json
                //VALIDAR LOS DATOS
                $validate = \Validator::make($params_array, [
                    'name' => 'required|alpha',
                    'email' => 'required|email|unique:users' . $token->sub,
                ]);
                if ($validate->fails()) { // Si los datos no son validos devolvemos un error
                    $data = array(
                        'code' => 400,
                        'status' => 'error',
                        'message' => 'Los datos de usuario no son correctos.'
                    );
                    return response()->json($data, $data['code']);
                }
                $user['name']   = $params_array['name'];
                $user['email']  = $params_array['email'];
                $user['address']  = $params_array['address'];
                $user['phone']  = $params_array['phone'];
                $user['web']  = $params_array['web'];
                $user['taxes']  = $params_array['taxes'];
                $user['measure']  = $params_array['measure'];
                $user['coin']  = $params_array['coin'];
                //QUITAR LOS CAMPOS QUE NO QUIERO ACTUALIZAR
                unset($params_array['id']);
                unset($params_array['password']);
                unset($params_array['create_at']);
                unset($params_array['remember_token']);
            } else {
                $row = User::find($token->sub);
                $user['name']   = $row['name'];
                $user['email']  = $row['email'];
                $user['address']  = $row['address'];
                $user['phone']  = $row['phone'];
                $user['web']  = $row['web'];
                $user['taxes']  = $row['taxes'];
                $user['measure']  = $row['measure'];
                $user['coin']  = $row['coin'];
                $user['image']  = "http://localhost/users/" . $row['email'];
                $params_array   = [];
            }
            if (!empty($image)) { // Se ha subido una imagen
                // Validamos la imagen
                $validate = \Validator::make($request->all(), [
                    'file0' => 'required|image|mimes:jpg,jpeg,png,gif'
                ]);
                if ($validate->fails()) { // Si los datos no son validos devolvemos un error
                    $data = array(
                        'code'  => 400,
                        'status' => 'error',
                        'message' => 'Los datos de la imagen no son correctos.'
                    );
                    return response()->json($data, $data['code']);
                }
                $params_array['image'] = time() . $image->getClientOriginalName();
                // Guardamos la imagen en disco
                \Storage::disk('users')->put($params_array['image'], \File::get($image));
                $user['image'] = "http://localhost/users/" . $params_array['image'];
            }
            //ACTUALIZAR USUARIO EN BBD
            $user_update = User::where('id', $token->sub)->update($params_array);
            if ($user_update) {
                $data = [
                    'code'  => 200,
                    'status' => 'success',
                    'image' => $user['image'],
                    'user'  => $user
                ];
            } else {
                $data = [
                    'code'  => 400,
                    'status' => 'error',
                    'message' => 'No se han podido guardar los datos.'
                ];
            }
        } else {
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'El usuario no está identificado imagen.'
            );
        }
        return response()->json($data, $data['code']);
    }

    public function detail($id)
    {
        $user = User::find($id);

        if (is_object($user)) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'user' => $user,
                'avatar' => "http://localhost/users/" . $user->image
            );
        } else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'El usuario no existe.'
            );
        }

        return response()->json($data, $data['code']);
    }

    public function password(Request $request)
    {
        //RECIBIR DATOS POR POST
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        //VALIDAR ESOS DATOS
        $validate = \Validator::make($params_array, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validate->fails()) {
            $signup = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'El usuario no se ha podido identificar',
                'errors' => $validate->errors()
            );
        } else {
            //CIFRAR PASSWORD
            $pwd = hash('sha256', $params->password);

            //DEVOLVER TOKEN O DATOS
            $signup = $this->signup($params->email, $pwd, !empty($params->gettoken));

            if ($signup) {

                $email = $params_array['email'];
                $newpassword = $params_array['newpassword'];
                $newpasswordcode = hash('sha256', $newpassword);
                //QUITAR LOS CAMPOS QUE NO QUIERO ACTUALIZAR
                unset($params_array['id']);
                unset($params_array['name']);
                unset($params_array['email']);
                unset($params_array['image']);
                unset($params_array['create_at']);
                unset($params_array['address']);
                unset($params_array['phone']);
                unset($params_array['web']);
                unset($params_array['taxes']);
                unset($params_array['measure']);
                unset($params_array['coin']);

                //ACTUALIZAR USUARIO EN BBD
                $user_update = User::where('email', $email)->update(['password' => $newpasswordcode]);

                //DEVOLVER ARRAY CON RESULTADO
                $data = array(
                    'code' => 200,
                    'status' => 'success',
                    'user' => $params_array,
                );
            } else {
                $data = array(
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'No se ha enviado el nuevo password'
                );
            }
        }
        return response()->json($signup, 200);
    }

    public function googlelogin(Request $request)
    {
        $json_idtoken = $request->input('idtoken', null);
        $params_idtoken = json_decode($json_idtoken);
        $params_array_idtoken = json_decode($json_idtoken, true);
        $id_token = $params_array_idtoken;

        $CLIENT_ID = '396756697427-ha04mugspccpvt1epe2ov76g4okfnbao.apps.googleusercontent.com';

        $client = new Client(['client_id' => $CLIENT_ID]);
        $payload = $client->verifyIdToken($id_token);

        if ($payload) {
            $userid = $payload['sub'];
            $email=$payload['email'];
            //$password=$payload["at_hash"];
            //$pwd = hash('sha256', $password);

            $user = User::where([
                'email'=> $email
            ])->first();

            if(!is_object($user)){
                $user = new User();
                $user->name = $payload['name'];
                $user->email = $payload['email'];
                //$user->password = $pwd;
                $user->image = $payload['picture'];
                

                $user->save();

            } else {
                $user->image = $payload['picture'];
            }
            $jwtAuth = new \JwtAuth();
            $data = $jwtAuth->create($user, !empty($payload->gettoken));

        } else {
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'No se ha iniciado la sesión con Google'
            );
        }
        return response()->json($data, $data['code']);
    }
}
