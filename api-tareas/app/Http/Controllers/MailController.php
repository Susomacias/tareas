<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Mail;
use App\Helpers\jwtAuth;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class MailController extends Controller
{

    public function contact(Request $request){
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        $mail = new Mail();
                $mail->name = $params_array['name'];
                $mail->email = $params_array['email'];
                $mail->message = $params_array['subject'];
        
     if($mail){
        $to = "susomacias@hotmail.com";
        $subject = "Nueva consulta de". " ". $mail->name;
        $message = $mail->message;
        $headers = "From: $mail->email" . "\r\n" . "CC: susomacias1983@gmail.com";

        mail($to, $subject, $message, $headers);

        $data = array(
            'status' => 'succes',
            'code' => 200,
            'message' => 'El mail se ha enviado correctamente',
        );
     } else {
        $data = array(
            'status' => 'error',
            'code' => 404,
            'message' => 'Error: El mail no se ha enviado',
        );
    }
    return response()->json($data, $data['code']);
    }

    public function passwordrecoveri(Request $request){

        //GENERAR CONTRASEÑA ALEATORIA
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $password= substr(str_shuffle($permitted_chars), 0, 10);
        $pwd = hash('sha256', $password);

        //RECOGER DATOS POR POST
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        $email= $params_array['email'];

        if (!empty($params_array)) {

            //VALIDAR LOS DATOS            
            $validate = \Validator::make($params_array, [
                'email' => 'required|email',
            ]);

            //QUITAR LOS CAMPOS QUE NO QUIERO ACTUALIZAR
            unset($params_array['id']);
            unset($params_array['name']);
            unset($params_array['image']);
            unset($params_array['create_at']);

            //ACTUALIZAR USUARIO EN BBD
            $user_update = User::where('email',$email)->update(['password'=>$pwd]);

            if($user_update){
                $to = $email;
                $subject = "Nueva Contraseña";
                $message = $password;
                $headers = "From: susomacias@hotmail.com";
        
                mail($to, $subject, $message, $headers);
            }

            //DEVOLVER ARRAY CON RESULTADO
            $data = array(
                'code' => 200,
                'status' => 'success',
                'user'=>$params_array,
            );
        } else {
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'No se ha enviado el nuevo password'
            );
        }
        return response()->json($data, $data['code']);
    }
}