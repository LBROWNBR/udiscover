<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Funciones;
use App\Models\ClienteModel;
use App\Models\Companias;
use App\Models\GeneralesModel;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Crypt;
use Mail;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Hash;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class CompaniasController extends Controller
{

    public function __construct()
    {
        $this->middleware('jwt.verify');
    }

    public function index()
    {
        return Companias::all();
    }

    public function show(Companias $compania)
    {
        return $compania;
    }

    public function store(Request $request)
    {
        $compania = Companias::create($request->all());

        return response()->json($compania, 201);
    }

    public function update(Request $request, $id)
    {
        $Companias = Companias::find($id);

        /*
        $compania->Nombre =  $request->get('Nombre');
        $compania->Correo = $request->get('Correo');
        $compania->LogoPath = $request->get('LogoPath');
        $compania->WebPage = $request->get('WebPage');
        $compania->save();*/

        $Companias->update($request->all());

        return response()->json($Companias, 200);
    }

    public function delete($id)
    {
        $compania = Companias::find($id);
        $compania->delete();

        return response()->json(null, 204);
    }


}
