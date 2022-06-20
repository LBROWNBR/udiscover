<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Symfony\Component\HttpFoundation\Response;

class JwtMiddleware extends BaseMiddleware
{

    public function handle($request, Closure $next)
    {
        $HttpStatus = null;
        $xData = array();

        try {

            if (!$user = JWTAuth::parseToken()->authenticate()) {

                $xData['status_id'] = 2;
                $xData['status_name'] = 'ERROR';
                $xData['status_description'] = 'Usuario no encontrado';
                $xData['token_type'] = 'bearer';
                $xData['expires_in'] = null;
                $xData['token'] = null;
                $HttpStatus = Response::HTTP_NOT_FOUND;
                return response()->json($xData, $HttpStatus);
            }
        } catch (Exception $e) {

            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                $xCodigo = 3;
                $Mensaje = 'El token no es válido';

            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                $xCodigo = 4;
                $Mensaje = 'Token caducado';

            }else{
                $xCodigo = 5;
                $Mensaje = 'Token de autorización no encontrado';
            }

            $xData['status_id'] = $xCodigo;
            $xData['status_name'] = 'ERROR';
            $xData['status_description'] = $Mensaje;
            $xData['token_type'] = 'bearer';
            $xData['expires_in'] = null;
            $xData['token'] = null;
            $HttpStatus = Response::HTTP_NOT_FOUND;
            return response()->json($xData, $HttpStatus);
        }

        return $next($request);

        /*
        return $next($request)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            //->header('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With, Application');
            ->header('Access-Control-Allow-Headers', 'DNT,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type,Range')
            ->header('Access-Control-Expose-Headers', 'Content-Length,Content-Range');
        */

        /*
        //permitir otros encabezados a sus rutas
        return $next($request)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With, Application');
        */

    }
}
