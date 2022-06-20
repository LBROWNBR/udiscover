<?php

namespace App\Http\Controllers;

use App\Models\GeneralesModel;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\Hash;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class GeneralesController extends Controller
{

    public function __construct()
    {
        $this->middleware('jwt.verify');
    }

    public function API_Ubigeos(Request $request)
    {

        $xData = array();

        $header = $request->header();
        $headerAuthorization = $request->header('Authorization');
        $tokenBearer = $request->bearerToken();

        $ObjDepa = GeneralesModel::AppModel_ListarDepartamentos();
        foreach ($ObjDepa as $key1 => $DataDepa) {

            $xCodDepa = $DataDepa->coddep;
            $ObjProv = GeneralesModel::AppModel_ListarProvincias(['coddep' => $xCodDepa]);

            $xData['dpto'][$key1]['id'] = $DataDepa->coddep;
            $xData['dpto'][$key1]['name'] = $DataDepa->departamento;

            foreach ($ObjProv as $key2 => $DataProv) {

                $xData['dpto'][$key1]['prov'][$key2]['id'] = $DataProv->codprov;
                $xData['dpto'][$key1]['prov'][$key2]['name'] = $DataProv->provincia;

                $xCodProv = $DataProv->codprov;
                $ObjDist = GeneralesModel::AppModel_ListarDistritos(['coddep' => $xCodDepa, 'codprov' => $xCodProv]);

                foreach ($ObjDist as $key3 => $DataDist) {
                    $xData['dpto'][$key1]['prov'][$key2]['dist'][$key3]['id'] = $DataDist->coddist;
                    $xData['dpto'][$key1]['prov'][$key2]['dist'][$key3]['name'] = $DataDist->distrito;
                }
            }
        }

        //$xData['token'] = $tokenBearer;
        return response()->json($xData);
    }


    public function API_Departamentos(Request $request)
    {
        $xData = array();
        $xData['Departamentos'] = GeneralesModel::AppModel_ListarDepartamentos();

        return response()->json($xData);
    }


    public function API_Provincias(Request $request, $xCodDepa)
    {
        $xData = array();

        if($xCodDepa != ''){
            $xData['Provincias'] = GeneralesModel::AppModel_ListarProvincias(['coddep' => $xCodDepa]);
        }else{
            $xData['Provincias'] = null;
        }

        return response()->json($xData);
    }


    public function API_Distritos(Request $request, $xCodDepa, $xCodProv)
    {
        $xData = array();

        if($xCodDepa != ''){
            $xData['Distritos']  = GeneralesModel::AppModel_ListarDistritos(['coddep' => $xCodDepa, 'codprov' => $xCodProv]);
        }else{
            $xData['Distritos'] = null;
        }

        return response()->json($xData);
    }

    //============================================================================================================
    //============================================================================================================
    //============================================================================================================


    public function API_TipoDocumentos(Request $request)
    {
        $xResponse = array();
        $ObjTipoDoc = GeneralesModel::AppModel_ListarTipoDocumentos();

        foreach ($ObjTipoDoc as $key1 => $DataTipDoc) {
            $xResponse[$key1]['cod_tipdoc'] = $DataTipDoc->cod_tipdoc;
            $xResponse[$key1]['desc_tipdoc'] = $DataTipDoc->desc_tipdoc;
            $xResponse[$key1]['abrv_tipdoc'] = $DataTipDoc->abrv_tipdoc;
            $xResponse[$key1]['id_estado'] = $DataTipDoc->id_estado;
            $xResponse[$key1]['long_caracter'] = ($DataTipDoc->long_caracter)*1;
        }

        return response()->json($xResponse);
    }

    public function API_TextoWhatsApp(Request $request)
    {
        $xResponse = array();
        $xResponse = GeneralesModel::AppModel_TextoWhatsApp();

        return response()->json($xResponse);
    }


    public function API_TextoEmail(Request $request)
    {
        $xResponse = array();
        $xResponse = GeneralesModel::AppModel_TextoEmail();

        return response()->json($xResponse);
    }


    //============================================================================================================
    //============================================================================================================
    //============================================================================================================


    public function API_TipoPrestamos(Request $request)
    {
        $xResponse = array();
        $xResponse = GeneralesModel::AppModel_ListarTipoPrestamos();

        return response()->json($xResponse);
    }


    public function API_TipoPagos(Request $request)
    {
        $xResponse = array();
        $xResponse = GeneralesModel::AppModel_ListarTipoPagoApp();

        return response()->json($xResponse);
    }

    public function API_NroCuotas(Request $request)
    {
        $xData = array();

        for ( $var = 1; $var <= 60; $var++){
            $xData[] = $var;
        }

        return response()->json($xData);
    }


    public function API_CalculoSimulador(Request $request)
    {
        $HttpStatus = null;
        $xData = array();

        $xSimulador = array();
        $xCronograma = array();

        /*
            ------------------------------
            C = (P*i)/(1- (1+i)^-N)
            ------------------------------
            C = cuota
            P = capital
            I = tasa de interes por periodo
            N = numero de periodos

        */

        /*
        $intMensual = 9;
        $nroMeses = 6;
        $montoCapital = 1000;
        $fechaPrestamo = '19-07-2021';
        */

        $tipoPrestamo = $request->input('tipoprestamo');
        $montoCapital = $request->input('montoprestamo');
        $tipoPago = $request->input('tipopago');
        $interes = $request->input('interes');
        $fechaPrestamo = $request->input('fechaprestamo');
        $nroCuotas = $request->input('cuotas');


        $tipoPrestamo = ($tipoPrestamo) ? $tipoPrestamo : '0';
        $tipoPago = ($tipoPago) ? $tipoPago : '0';

        $objTipoPrestamo = GeneralesModel::AppModel_VerTipoPrestamos_ById(['cod_prod' => $tipoPrestamo]);
        if($objTipoPrestamo){


            $objTipoPago = GeneralesModel::AppModel_VerTipoPago_ById(['cod_tippago' => $tipoPago]);
            if($objTipoPago){

                $intMensual = $interes;

                $I = round($intMensual / 100,2);
                $I2 = $I + 1 ;
                $I2 = pow($I2,-$nroCuotas) ;

                $pagoCuotaMensual = round(($montoCapital * $I) / (1 - $I2),2);

                $xSimulador['TipoPrestamoCodigo'] = $tipoPrestamo;
                $xSimulador['TipoPrestamoDescrip'] = $objTipoPrestamo->nom_prod;
                $xSimulador['PrestamoSolicitado'] = $montoCapital;
                $xSimulador['TipoPagoCodigo'] = $tipoPago;
                $xSimulador['TipoPagoDescrip'] = $objTipoPago->nom_tippago;
                $xSimulador['InteresTexto'] = $intMensual.'% Mensual';
                $xSimulador['InteresNumero'] = $intMensual;
                $xSimulador['FechaPrestamo'] = $fechaPrestamo;
                $xSimulador['Cuotas'] = $nroCuotas;
                $xSimulador['pagoCuota'] = $pagoCuotaMensual;

                $saldoPrincipal = $montoCapital;

                for ($c=0;$c<$nroCuotas;$c++) {

                    $fechaPrestamo = date("d-m-Y",strtotime($fechaPrestamo."+ 30 days"));
                    $interesCuota = round($saldoPrincipal * $I,2);
                    $amortizacionCuota = round($pagoCuotaMensual-$interesCuota,2);
                    $saldoPrincipal = round($saldoPrincipal - $amortizacionCuota,2);

                    $saldoPrincipal = ($saldoPrincipal*1>0) ? $saldoPrincipal : 0;

                    $xCuotas = [
                        'NroCuota' => $c+1,
                        'Fecha' => $fechaPrestamo,
                        'Cuota' => $pagoCuotaMensual,
                        'Interes' => $interesCuota,
                        'Amortizacion' => $amortizacionCuota,
                        'Saldo' => $saldoPrincipal
                    ];

                    $xCronograma[] = $xCuotas;
                }

                $xSimulador['Cronograma'] = $xCronograma;



                $xData['Estado'] = 'OK';
                $xData['Mensaje'] = 'Prestamo Generado';
                $xData['Simulador'] = $xSimulador;
                $HttpStatus = Response::HTTP_OK;

            }else{

                $xData['Estado'] = 'ERROR';
                $xData['Mensaje'] = 'Tipo pago no existe en la base de datos.';
                $xData['Simulador'] = null;
                $HttpStatus = Response::HTTP_NOT_FOUND;
            }

        }else{

            $xData['Estado'] = 'ERROR';
            $xData['Mensaje'] = 'Tipo prestamo no existe en la base de datos.';
            $xData['Simulador'] = null;
            $HttpStatus = Response::HTTP_NOT_FOUND;
        }

        return response()->json($xData, $HttpStatus);
    }

}
