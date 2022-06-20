<?php

namespace App\Http\Controllers;

use App\Models\App_usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Config;

use Illuminate\Support\Facades\Hash;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{

    public function __construct() {
        Config::set('jwt.ttl', 60*24*7); //Expira en 7 días
        $this->middleware('jwt.verify', ['except' => ['login', 'register']]);
    }

    public function login(Request $request){

        $HttpStatus = null;
        $xData = array();

        $credentials = $request->only('email', 'password');
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                //return response()->json(['error' => 'invalid_credentials'], 400);
                $xData['status_id'] = 6;
                $xData['status_name'] = 'ERROR';
                $xData['status_description'] = 'Credenciales no válidas';
                $xData['token_type'] = 'bearer';
                $xData['expires_in'] = null;
                $xData['token'] = null;
                $HttpStatus = Response::HTTP_BAD_REQUEST;
                return response()->json($xData, $HttpStatus);
            }
        } catch (JWTException $e) {
            //return response()->json(['error' => 'could_not_create_token'], 500);
            $xData['status_id'] = 7;
            $xData['status_name'] = 'ERROR';
            $xData['status_description'] = 'No se pudo crear el token';
            $xData['token_type'] = 'bearer';
            $xData['expires_in'] = null;
            $xData['token'] = null;
            $HttpStatus = Response::HTTP_INTERNAL_SERVER_ERROR;
            return response()->json($xData, $HttpStatus);
        }

        $xData['status_id'] = 1;
        $xData['status_name'] = 'OK';
        $xData['status_description'] = 'Token generado';
        $xData['token_type'] = 'bearer';
        $xData['expires_in'] = auth()->factory()->getTTL();
        $xData['token'] = $token;
        $HttpStatus = Response::HTTP_OK;
        return response()->json($xData, $HttpStatus);
        //return response()->json(compact('token'));


    }


    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:app_usuarios',
            'password' => 'required|string|min:6',
            'id_estado' => 'required',
        ]);

        if($validator->fails()){
             return response()->json($validator->errors(), 400);
        }

        $app_usuario = App_usuario::create(array_merge(
                    $validator->validated(),
                    ['password' => bcrypt($request->password)]
                ));

        return response()->json([
            'message' => 'Usuario registrado exitosamente',
            'app_usuario' => $app_usuario
        ], 201);
    }


    public function logout() {
        auth()->logout();
        return response()->json(['message' => 'El usuario cerró la sesión correctamente']);
    }


    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }


    public function userProfile() {
        return response()->json(auth()->user());
    }


    protected function createNewToken($token){

        $xData = array();
        $xData['estado'] = 'OK';
        $xData['status'] = 'Token generado';
        $xData['token_type'] = 'bearer';
        $xData['expires_in'] = auth()->factory()->getTTL() * 60;
        $xData['token'] = $token;
        $HttpStatus = Response::HTTP_OK;
        return response()->json($xData, $HttpStatus);
    }


    public function API_ResetPasswordCliente($Encriptado)
    {
        $xData = array("sVal" => $Encriptado);
        return view('resetFormClaveCliente', ['wData' => $xData]);
    }

}
