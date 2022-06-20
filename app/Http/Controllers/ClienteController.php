<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Funciones;
use App\Models\ClienteModel;
use App\Models\GeneralesModel;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Crypt;
use Mail;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Hash;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class ClienteController extends Controller
{

    public function __construct()
    {
        $this->middleware('jwt.verify');
    }



    public function API_RegistrarClientePotencial(Request $request)
    {
        $HttpStatus = null;
        $xData = array();

        $tipoPrestamo = $request->input('tipoprestamo');
        $montoCapital = $request->input('montoprestamo');
        $tipoPago = $request->input('tipopago');
        $interes = $request->input('interes');
        $fechaPrestamo = $request->input('fechaprestamo');
        $nroCuotas = $request->input('cuotas');

        $tipodoc = $request->input('tipodoc');
        $nrodoc = $request->input('nrodoc');
        $nombres = $request->input('nombres');
        $apepat = $request->input('apepat');
        $apemat = $request->input('apemat');
        $celular = $request->input('celular');
        $correo = $request->input('correo');
        $coddepa = $request->input('coddepa');
        $codprov = $request->input('codprov');
        $coddist = $request->input('coddist');
        $nomDistrito = $request->input('distrito');
        $direccion = $request->input('direccion');

        $nomTipoPrestamo    = GeneralesModel::AppModel_VerTipoPrestamos_ById(['cod_prod' => $tipoPrestamo])->nom_prod;
        //$nomDistrito        = GeneralesModel::AppModel_VerDistrito_ByID(['coddep' => $coddepa, 'codprov' => $codprov, 'coddist' => $coddist])->distrito;

        $ObjCliPotencial = ClienteModel::Verificar_Existe_TipoDoc_NumDoc_ClientePotencial(['tipo_doc' => $tipodoc, 'nro_doc' => $nrodoc]);
        if($ObjCliPotencial){

                $id_potencial = $ObjCliPotencial->id_potencial;

                $xParametrosReg = [
                    'id_potencial' => $id_potencial,
                    'nombres_cli' => $nombres,
                    'apepat_cli' => $apepat,
                    'apemat_cli' => $apemat,
                    'tipo_doc' => $tipodoc,
                    'nro_doc' => $nrodoc,
                    'cod_depa' => $coddepa,
                    'cod_prov' => $codprov,
                    'cod_dist' => $coddist,
                    'distrito' => $nomDistrito,
                    'direc' => $direccion,
                    'tele_fijo' => NULL,
                    'tele_cel' => $celular,
                    'correo' => $correo,
                    'cod_prod' => $tipoPrestamo,
                    'tipo_prestamo' => $nomTipoPrestamo,
                    'observacion' => 'Solicita Prestamo de S/ '.$montoCapital.' a través del APP',
                    'pasado' => 'no',
                    'id_estado' => '1',
                    'form_ingreso' => 'APP'
                ];

                ClienteModel::UpdateData_ClientePotencial($xParametrosReg);

                $Estado = 'OK';
                $Mensaje = 'Cliente Actualizado con Exito!';
                $CodCliPotencial = $id_potencial;
                $HttpStatus = Response::HTTP_OK;


        }else{

            $id_potencial = ClienteModel::Generar_Codigo_ClientePotencial()->codigo;

            $xParametrosReg = [
                'id_potencial' => $id_potencial,
                'nombres_cli' => $nombres,
                'apepat_cli' => $apepat,
                'apemat_cli' => $apemat,
                'tipo_doc' => $tipodoc,
                'nro_doc' => $nrodoc,
                'cod_depa' => $coddepa,
                'cod_prov' => $codprov,
                'cod_dist' => $coddist,
                'distrito' => $nomDistrito,
                'direc' => $direccion,
                'tele_fijo' => NULL,
                'tele_cel' => $celular,
                'correo' => $correo,
                'cod_prod' => $tipoPrestamo,
                'tipo_prestamo' => $nomTipoPrestamo,
                'observacion' => 'Solicita Prestamo de S/ '.$montoCapital.' a través del APP',
                'pasado' => 'no',
                'id_estado' => '1',
                'form_ingreso' => 'APP'
            ];

            ClienteModel::InsertData_ClientePotencial($xParametrosReg);

            $Estado = 'OK';
            $Mensaje = 'Cliente Registrado con Exito!';
            $CodCliPotencial = $id_potencial;
            $HttpStatus = Response::HTTP_OK;

        }


        $id_prest_potencial = ClienteModel::Generar_Codigo_PrestamoClientePotencial()->codigo;
        $xParametrosPrest = [
            'id_prest_potencial' => $id_prest_potencial,
            'id_potencial' => $id_potencial,
            'form_ingreso' => 'APP',
            'tipoprestamo' => $tipoPrestamo,
            'montoprestamo' => $montoCapital,
            'cod_pago' => $tipoPago,
            'interes' => $interes,
            'fechaprestamo'=> Funciones::cambiar_Fecha_a_SqlServer($fechaPrestamo),
            'cuotas' => $nroCuotas,
            'id_estado' => '1'
        ];

        ClienteModel::InsertData_PrestamoClientePotencial($xParametrosPrest);

        $xData['estado'] = $Estado;
        $xData['mensaje'] = $Mensaje;
        $xData['id_tipo_prestamo'] = $tipoPrestamo;
        $xData['id_cli_potencial'] = $CodCliPotencial;
        $xData['id_prest_potencial'] = $id_prest_potencial;

        return response()->json($xData, $HttpStatus);
    }



    public function API_RegistrarGarantiaClientePotencial(Request $request)
    {
        $HttpStatus = null;
        $xData = array();

        $varGeneral = $request->all();

        //$varArchivos  = $varGeneral['archivos'];
/*
        echo "<pre>";
        print_r($varArchivos);
        echo "</pre>";
        */

       // $xData['archivos'] = $varArchivos;

        $jsonDatos = $request->input('jsonDatos');
        $jsonDatos = json_decode($jsonDatos);

        $id_tipo_prestamo   = $jsonDatos->id_tipo_prestamo;
        $id_potencial   = $jsonDatos->id_potencial;
        $id_prest_potencial   = $jsonDatos->id_prest_potencial;

        $descrip_garantia   = $jsonDatos->descrip_garantia;
        $marca   = $jsonDatos->marca;
        $modelo   = $jsonDatos->modelo;
        $ano   = $jsonDatos->ano;
        $color   = $jsonDatos->color;
        $estado   = $jsonDatos->estado;
        $valor   = $jsonDatos->valor;
        $linea = 0;
        $ubi   = $jsonDatos->ubi;
        $obser   = $jsonDatos->obser;

        $kilate   = $jsonDatos->kilate;
        $peso   = $jsonDatos->peso;
        $descripjoya   = $jsonDatos->descripjoya;

        $xParametrosGarantia = [
            'id_prest_potencial' => $id_prest_potencial,
            'id_potencial' => $id_potencial,
            'descrip_garantia' => $descrip_garantia,
            'marca' => $marca,
            'modelo' => $modelo,
            'ano' => $ano,
            'color' => $color,
            'estado' => $estado,
            'valor' => $valor,
            'linea' => $linea,
            'ubi' => $ubi,
            'obser' => $obser,
			'kilate' => $kilate,
			'peso' => $peso,
			'descripjoya' => $descripjoya,
            'id_estado_garantia' => '1'
        ];

        $result = ClienteModel::UpdateData_GarantiaClientePotencial($xParametrosGarantia);



        if($request->hasFile('archivos')) {

            $xCont = 0;
            foreach($request->file('archivos') as $file){

                //$file = $request->file('archivos');

                $filenamewithextension = $file->getClientOriginalName();
                $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $newNombreArchivo = 'GARANTIA_IDPRESTPOT_'.$id_prest_potencial.'_IDCLIPOT_'.$id_potencial.'_APP_'.uniqid().'.'.$extension;
                $Directorio = '/garantias';
                $filenametostore = $Directorio.'/'.$newNombreArchivo;
                $path = Storage::disk('ftp')->put($filenametostore, fopen($file, 'r+'));

                if($path){
                    $id_prest_potencial_doc = ClienteModel::Generar_Codigo_clientes_potencial_prestamos_docs()->codigo;
                    $xParametrosFotoGarantia = [
                        'id_prest_potencial_doc' => $id_prest_potencial_doc,
                        'id_prest_potencial' => $id_prest_potencial,
                        'id_potencial' => $id_potencial,
                        'nom_foto' => $newNombreArchivo
                    ];
                    ClienteModel::InsertData_clientes_potencial_prestamos_docs($xParametrosFotoGarantia);

                    $xData['documentos'][$xCont] = [
                        'codigo' => $id_prest_potencial_doc,
                        'archivo' => 'https://sistema.t-presto.com.pe/images/garantias/'.$newNombreArchivo
                    ];
                    $xCont++;
                }else{
                    $xData['documentos'] = 'No Cargo Docs.';
                }
            }
        }

        $xData['estado'] = 'OK';
        $xData['mensaje'] = 'Garantia Registrado con Exito!';
        $xData['id_potencial'] = $id_potencial;
        $xData['id_prest_potencial'] = $id_prest_potencial;
        $HttpStatus = Response::HTTP_OK;

        return response()->json($xData, $HttpStatus);
    }


    public function API_RegistrarDocuGarantiaClientePotencial(Request $request)
    {
        $HttpStatus = null;
        $xData = array();

        $id_prest_potencial = $request->input('id_prest_potencial');
        $descrip_garantia = $request->input('descrip_garantia');

        $xData['estado'] = 'OK';
        $xData['mensaje'] = 'Garantia Registrado con Exito!';
        $xData['id_prest_potencial'] = $id_prest_potencial;
        $HttpStatus = Response::HTTP_OK;

        return response()->json($xData, $HttpStatus);
    }


    //=============================================================
    //=============== CREDITOS DEL CLIENTE ========================
    //=============================================================


    public function API_CreditosCliente(Request $request)
    {
        $HttpStatus = null;
        $xData = array();
        $xResponse = array();

        $cod_cli = $request->input('cod_cli');

        $ObjCreditoCliente = ClienteModel::Listar_Creditos_Pendientes_x_Cliente(['cod_cli' => $cod_cli]);

        foreach ($ObjCreditoCliente as $key1 => $DataCredito) {
            $xResponse[$key1]['cod_credito'] = $DataCredito->cod_credito;
            $xResponse[$key1]['cod_cli'] = $DataCredito->cod_cli;
            $xResponse[$key1]['fecha'] = $DataCredito->fecha;
            $xResponse[$key1]['tipo_modelo'] = $DataCredito->tipo_modelo;
            $xResponse[$key1]['cod_prod'] = $DataCredito->cod_prod;
            $xResponse[$key1]['nom_prod'] = $DataCredito->nom_prod;
            $xResponse[$key1]['cod_tippago'] = $DataCredito->cod_tippago;
            $xResponse[$key1]['desc_tippago'] = $DataCredito->desc_tippago;
            $xResponse[$key1]['tem'] = ($DataCredito->tem)*1;
            $xResponse[$key1]['monto_total'] = ($DataCredito->monto_total)*1;
            $xResponse[$key1]['descuento'] = ($DataCredito->descuento)*1;

            $xResponse[$key1]['total_cuotas'] = ($DataCredito->total_cuotas)*1;
            $xResponse[$key1]['cuotas_pagadas'] = ($DataCredito->cuotas_pagadas)*1;
            $xResponse[$key1]['cuotas_no_pagadas'] = ($DataCredito->cuotas_no_pagadas)*1;
            $xResponse[$key1]['cuotas_por_vencer'] = ($DataCredito->cuotas_por_vencer)*1;
        }

        $xData['estado'] = 'OK';
        $xData['credito'] = $xResponse;
        $HttpStatus = Response::HTTP_OK;

        return response()->json($xData, $HttpStatus);
    }



    public function API_VerCuotasCreditoCliente(Request $request)
    {
        $HttpStatus = null;
        $xData = array();
        $xResponse = array();

        $cod_credito = $request->input('cod_credito');
        $NroRegistros = 10;
        $ObjCuotas = ClienteModel::Listar_Cuotas_Pendientes_x_IdCredito(['cod_credito' => $cod_credito, 'NroRegistros' => $NroRegistros]);

        $xCont = 0;
        foreach ($ObjCuotas as $key1 => $DataCuotas) {
            $xResponse[$key1]['cod_deuda'] = $DataCuotas->cod_deuda;
            $xResponse[$key1]['cod_credito'] = $DataCuotas->cod_credito;
            $xResponse[$key1]['cod_cli'] = $DataCuotas->cod_cli;
            $xResponse[$key1]['cuota'] = ($DataCuotas->cuota)*1;
            $xResponse[$key1]['cuota_texto'] = "Cuota Nro. ".$DataCuotas->cuota." de ".$DataCuotas->totcuota."";
            $xResponse[$key1]['fecha_ven'] = $DataCuotas->fecha_ven;
            $xResponse[$key1]['cuota_normal'] = ($DataCuotas->cuota_normal*1);
            $xResponse[$key1]['cuota_cdscto'] = ($DataCuotas->cuota_cdscto*1);
            $xResponse[$key1]['saldo'] = ($DataCuotas->saldo*1);
            $xResponse[$key1]['estado_abrev'] = $DataCuotas->estado_abrev;
            $xCont++;
        }

        if($xCont > 0){

            $xData['estado'] = 'OK';
            $xData['titulo'] = 'Solo se mostrará las 10 primeras cuotas pendiente.';
            $xData['mensaje'] = 'Cliente si cuenta con cuotas pendientes';
            $xData['cuotas'] = $xResponse;
            $HttpStatus = Response::HTTP_OK;

        }else{

            $xData['estado'] = 'OK';
            $xData['titulo'] = 'Solo se mostrará las 10 primeras cuotas pendiente.';
            $xData['mensaje'] = 'Cliente no tiene cuotas pendientes';
            $xData['cuotas'] = $xResponse;
            $HttpStatus = Response::HTTP_OK;

        }
        return response()->json($xData, $HttpStatus);
    }



    public function API_RegistrarPagoCuotasCreditoCliente(Request $request)
    {
        $HttpStatus = null;
        $xData = array();
        $xArrayComprobante = array();



        $fileFotoVoucher  = $request->hasFile('fileFotoVoucher');
        $jsonDatos  = $request->input('jsonDatos');

        //$variables = $request->all();


        /*
        $cod_credito = $variables['cod_credito'];
        $cod_cli = $variables['cod_cli'];
        $cuotas_seleccionadas = $variables['cuotas_seleccionadas'];
        $monto_total = $variables['monto_total'];
        $nro_voucher = $variables['nro_voucher'];
        //$fileVoucherPago = $variables['fileVoucherPago'];
        $monto_depositado = $variables['monto_depositado'];
        $ArrayPagos = $variables['pagos'];
        */


        if($request->hasFile('fileFotoVoucher')) {

            $jsonDatos = json_decode($jsonDatos);

            $cod_credito            = $jsonDatos->cod_credito;
            $cod_cli                = $jsonDatos->cod_cli;
            $cuotas_seleccionadas   = $jsonDatos->cuotas_seleccionadas;
            $monto_total            = $jsonDatos->monto_total;
            $nro_voucher            = $jsonDatos->nro_voucher;
            $monto_depositado       = $jsonDatos->monto_depositado;
            $ArrayPagos             = $jsonDatos->pagos;

            /*
                echo "<pre>";
                print_r($ArrayPagos);
                echo "</pre>";
            */

            $cod_pago_deuda_cab = ClienteModel::Generar_Codigo_pago_deuda_cab_app()->codigo;

            $paramCAB = [
                'cod_pago_deuda_cab' => $cod_pago_deuda_cab,
                'cod_credito' => $cod_credito,
                'cod_cli' => $cod_cli,
                'tot_cuota' => $cuotas_seleccionadas,
                'monto_total' => $monto_total,
                'nro_voucher' => $nro_voucher,
                'foto_voucher' => '---',
                'monto_deposita' => $monto_depositado,
                'estado_pago' => 'PAGADO',
                'form_ingreso' => 'APP'
            ];

            $ObjCodigoCAB = ClienteModel::InsertData_pago_deuda_cab_app($paramCAB);

            if ($ObjCodigoCAB){

                //===================================
                //ADJUNTAR VOUCHER
                //===================================


                $filenamewithextension = $request->file('fileFotoVoucher')->getClientOriginalName();
                $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
                $extension = $request->file('fileFotoVoucher')->getClientOriginalExtension();
                $newNombreArchivo = 'VOUCHER_IDCLI_'.$cod_cli.'_IDPAGODEUDA_'.$cod_pago_deuda_cab.'_APP_'.uniqid().'.'.$extension;
                $Directorio = '/voucher';
                $filenametostore = $Directorio.'/'.$newNombreArchivo;
                $path = Storage::disk('ftp')->put($filenametostore, fopen($request->file('fileFotoVoucher'), 'r+'));

                if($path){
                    $xParametrosFotoVoucher = [
                        'cod_pago_deuda_cab' => $cod_pago_deuda_cab,
                        'foto_voucher' => $newNombreArchivo
                    ];
                    ClienteModel::ActualizarNombreVoucher_pago_deuda_cab_app($xParametrosFotoVoucher);
                }

                //============================================================
                //=========== INICIO - INSERTAMOS COMPROBANTE CAB ============
                //============================================================

                $ObjComprobanteCAB  = ClienteModel::Generar_codigo_comprobante_deuda_cab_app();
                $cod_comp_deuda_cab_app = $ObjComprobanteCAB->codigo;

                $CampComprobanteCAB = [
                    'cod_comp_deuda_cab_app'=>$cod_comp_deuda_cab_app,
                    'cod_cli'=>$cod_cli,
                    'cod_credito'=>$cod_credito,
                    'monto_pagado'=>$monto_depositado,
                    'numop'=>$nro_voucher,
                    'tot_cuotas'=>$cuotas_seleccionadas,
                    'cod_pago_deuda_cab'=>$cod_pago_deuda_cab
                ];
                ClienteModel::InsertData_comprobante_deuda_cab_app($CampComprobanteCAB);

                //============================================================
                //=========== FIN - INSERTAMOS COMPROBANTE CAB ============
                //============================================================


                foreach ($ArrayPagos as $key1 => $DataDeuda) {

                    $cod_pago_deuda_det = ClienteModel::Generar_Codigo_pago_deuda_det_app()->codigo;

                    $xcod_deuda = $DataDeuda->cod_deuda;
                    $xcuota     = $DataDeuda->cuota;
                    $xfecha_ven = $DataDeuda->fecha_ven;
                    $xmonto     = $DataDeuda->monto;

                    $paramDET = [
                        'cod_pago_deuda_det' => $cod_pago_deuda_det,
                        'cod_pago_deuda_cab' => $cod_pago_deuda_cab,
                        'cod_deuda' => $xcod_deuda,
                        'cuota' => $xcuota,
                        'fecha_ven' => Funciones::cambiar_Fecha_a_SqlServer($xfecha_ven),
                        'monto' => $xmonto,
                        'estado_pago' => 'PAGADO'
                    ];

                    $ObjCodigoDET = ClienteModel::InsertData_pago_deuda_det_app($paramDET);

                    ClienteModel::ActualizarEstadoDeuda_val_app(['cod_deuda' => $xcod_deuda]);

                    //============================================================
                    //=========== INICIO - INSERTAMOS COMPROBANTE DET ============
                    //============================================================

                    $ObjComprobanteDET  = ClienteModel::Generar_codigo_comprobante_deuda_det_app();
                    $cod_comp_deuda_det_app    = $ObjComprobanteDET->codigo;

                    $CamposCompDET = [
                        'cod_comp_deuda_det_app'=>$cod_comp_deuda_det_app,
                        'cod_comp_deuda_cab_app'=>$cod_comp_deuda_cab_app,
                        'cod_deuda'=>$xcod_deuda,
                        'cuota'=>$xcuota,
                        'monto_cuota'=>$xmonto,
                        'fecha_ven'=>Funciones::cambiar_Fecha_a_SqlServer($xfecha_ven)
                    ];
                    ClienteModel::InsertData_comprobante_deuda_det_app($CamposCompDET);

                    //============================================================
                    //=========== FIN - INSERTAMOS COMPROBANTE DET ============
                    //============================================================

                    if($ObjCodigoDET){

                        $xData['estado'] = 'OK';
                        $xData['titulo'] = 'Registro de pago de las cuotas seleccionadas';
                        $xData['mensaje'] = 'Se registro correctamente el registro del pago. T-Presto se encargará de realizar la validación del pago. Nos mantendremos en contacto con Usted. Hacer clic en boton aceptar para mostrar el comprobante de pago.';
                        $xData['url_voucher'] = 'https://sistema.t-presto.com.pe/images/voucher/'.$newNombreArchivo;
                        $HttpStatus = Response::HTTP_OK;

                    }else{

                        $xData['estado'] = 'ERROR';
                        $xData['titulo'] = 'Registro de pago de las cuotas seleccionadas';
                        $xData['mensaje'] = 'No se pudo registrar la detalle del pago de la deuda';
                        $xData['url_voucher'] = null;
                        $HttpStatus = Response::HTTP_NOT_FOUND;

                    }
                }

            }else{

                $xData['estado'] = 'ERROR';
                $xData['titulo'] = 'Registro de pago de las cuotas seleccionadas';
                $xData['mensaje'] = 'No se pudo registrar la cabecera del pago de la deuda';
                $xData['url_voucher'] = null;
                $HttpStatus = Response::HTTP_NOT_FOUND;

            }

            if($cod_comp_deuda_cab_app>0){

                $xArrayComprobante['CompCAB'] = ClienteModel::Ver_Datos_Comprobante_CAB_APP_x_IdCab(['cod_comp_deuda_cab_app'=>$cod_comp_deuda_cab_app]);
                $xArrayComprobante['CompDET'] = ClienteModel::Ver_Datos_Comprobante_DET_APP_x_IdCab(['cod_comp_deuda_cab_app'=>$cod_comp_deuda_cab_app]);

                $xData['comprobante'] = $xArrayComprobante;

            }else{
                $xData['comprobante'] = [];
            }

        }else{

            $xData['estado'] = 'ERROR';
            $xData['titulo'] = 'Carga de Voucher';
            $xData['mensaje'] = 'Variable "fileFotoVoucher" no encontrado';
            $xData['url_voucher'] = null;
            $HttpStatus = Response::HTTP_NOT_FOUND;

        }

        return response()->json($xData, $HttpStatus);
    }



    public function API_AdjuntarFotoCliente(Request $request)
    {
        $HttpStatus = null;
        $xData = array();

        $variables = $request->all();

        if($request->hasFile('foto')) {

            $cod_cliente = $variables['cod_cliente']; //$cod_cliente = '616';
            $cod_clifoto  = ClienteModel::Generar_Codigo_ClienteFoto()->codigoFoto;


	        $filenamewithextension = $request->file('foto')->getClientOriginalName();
	        $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
	        $extension = $request->file('foto')->getClientOriginalExtension();
	        //$filenametostore = $filename.'_'.uniqid().'.'.$extension;
            $newNombreArchivo = 'CLI_'.$cod_cliente.'_Id_'.$cod_clifoto.'_P_'.uniqid().'.'.$extension;
            $Directorio = '/cliente';
            $filenametostore = $Directorio.'/'.$newNombreArchivo;
	        $path = Storage::disk('ftp')->put($filenametostore, fopen($request->file('foto'), 'r+'));

            if($path){

                $objFotoCliApp = ClienteModel::Existe_FotoApp_ByCodCli(['cod_cli' => $cod_cliente]);

                if($objFotoCliApp){
                    if($objFotoCliApp->totreg > 0){
                        ClienteModel::Limpia_FotoApp_ByCodCli(['cod_cli' => $cod_cliente]);
                    }
                }

                $xParametrosFotoApp = [
                    'cod_clifoto' => $cod_clifoto,
                    'cod_cli' => $cod_cliente,
                    'nom_foto' => $newNombreArchivo,
                    'desc_foto' => 'Foto Perfil de Usuario App',
                    'id_estado' => '5'
                ];
                ClienteModel::InsertData_ClienteFotoApp($xParametrosFotoApp);

                $xData['estado'] = 'OK';
                $xData['titulo'] = 'Carga de Foto';
                $xData['mensaje'] = 'Se registró de manera exitosa foto del cliente';
                $xData['url_foto'] = 'https://sistema.t-presto.com.pe/images'.$filenametostore;
                $HttpStatus = Response::HTTP_OK;

            }else{
                $xData['estado'] = 'ERROR';
                $xData['titulo'] = 'Carga de Foto';
                $xData['mensaje'] = 'No se pudo registrar la foto del cliente';
                $xData['url_foto'] = null;
                $HttpStatus = Response::HTTP_NOT_FOUND;
            }

	    }else{

            $xData['estado'] = 'ERROR';
            $xData['titulo'] = 'Carga de Foto';
            $xData['mensaje'] = 'Variable "foto" no encontrado';
            $xData['url_foto'] = null;
            $HttpStatus = Response::HTTP_NOT_FOUND;

        }

        return response()->json($xData, $HttpStatus);
	    //return redirect('images')->with('status', "Image uploaded successfully.");
    }


    public function API_ModificarDatosFotoCliente(Request $request)
    {
        $HttpStatus = null;
        $xData = array();

        $variables = $request->all();
        $jsonDatos = $variables['jsonDatos'];

        $jsonDatos  = json_decode($jsonDatos);
        $cod_cli    = $jsonDatos->cod_cli;
        $nickname   = $jsonDatos->nickname;
        $telefono2  = $jsonDatos->telefono2;
        $correo2    = $jsonDatos->correo2;

        $xParametrosCliente = [
            'cod_cli' => $cod_cli,
            'nickname' => $nickname,
            'telefono2' => $telefono2,
            'correo2' => $correo2
        ];
        $ObjActualizaCli  = ClienteModel::UpdateData_ClienteDatosBasicos($xParametrosCliente);


        if($request->hasFile('fotoCliente')) {

            $cod_clifoto  = ClienteModel::Generar_Codigo_ClienteFoto()->codigoFoto;

	        $filenamewithextension = $request->file('fotoCliente')->getClientOriginalName();
	        $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
	        $extension = $request->file('fotoCliente')->getClientOriginalExtension();
	        //$filenametostore = $filename.'_'.uniqid().'.'.$extension;
            $newNombreArchivo = 'CLI_'.$cod_cli.'_Id_'.$cod_clifoto.'_P_'.uniqid().'.'.$extension;
            $Directorio = '/cliente';
            $filenametostore = $Directorio.'/'.$newNombreArchivo;
	        $path = Storage::disk('ftp')->put($filenametostore, fopen($request->file('fotoCliente'), 'r+'));

            if($path){

                $objFotoCliApp = ClienteModel::Existe_FotoApp_ByCodCli(['cod_cli' => $cod_cli]);

                if($objFotoCliApp){
                    if($objFotoCliApp->totreg > 0){
                        ClienteModel::Limpia_FotoApp_ByCodCli(['cod_cli' => $cod_cli]);
                    }
                }

                $xParametrosFotoApp = [
                    'cod_clifoto' => $cod_clifoto,
                    'cod_cli' => $cod_cli,
                    'nom_foto' => $newNombreArchivo,
                    'desc_foto' => 'Foto Perfil de Usuario App',
                    'id_estado' => '5'
                ];
                ClienteModel::InsertData_ClienteFotoApp($xParametrosFotoApp);

                $xData['estado'] = 'OK';
                $xData['titulo'] = 'Actualizacion de Datos del Cliente';
                $xData['mensaje'] = 'Se registró de manera exitosa foto del cliente';
                $xData['url_foto'] = 'https://sistema.t-presto.com.pe/images'.$filenametostore;

                $xData['cod_cli'] = $cod_cli."";
                $xData['nickname'] = $nickname;
                $xData['telefono2'] = $telefono2;
                $xData['correo2'] = $correo2;

                $HttpStatus = Response::HTTP_OK;

            }else{
                $xData['estado'] = 'ERROR';
                $xData['titulo'] = 'Actualizacion de Datos del Cliente';
                $xData['mensaje'] = 'No se pudo registrar la foto del cliente';
                $xData['url_foto'] = null;
                $HttpStatus = Response::HTTP_NOT_FOUND;
            }

	    }else{

            $xData['estado'] = 'ERROR';
            $xData['titulo'] = 'Actualizacion de Datos del Cliente';
            $xData['mensaje'] = 'Variable "fotoCliente" no encontrado';
            $xData['url_foto'] = null;
            $HttpStatus = Response::HTTP_NOT_FOUND;

        }

        return response()->json($xData, $HttpStatus);
    }








    public function API_AdjuntarVoucher_Prueba(Request $request)
    {
        $HttpStatus = null;
        $data = array();

        $xArrayComprobante = array();

        $variables = $request->all();

        //$xfile_foto = $variables['file_foto'];


            $images = $request->file('file_foto');
			$content = $request->input('content');

            if(is_array($images))
			{
				foreach($images as $key=>$v)
				{
					$path = $images[$key]->store('images','public');
                    $path = Storage::disk('avatars')->put('',$path);
					array_push($pathUrls,$path);

				}
			} else {
				$images->store('images','public');
                $path = Storage::disk('avatars')->put('',$images);
				array_push($pathUrls,$path);
			}



            $pathUrls = implode(',',$pathUrls);

			$data['albums']['images'] = explode(',', $pathUrls) ;
			$data['albums']['content'] = $content;


            //$path = Storage::disk('avatars')->put('',$request->file('file_foto'));
            //return $path;

            /*

            $images->store('images','public');
            $path =  Storage::disk('public')->path($images);
            array_push($pathUrls,$path);
            */


        return response()->json([
			'status' => 'success',
			'status_code' =>200,
			'data' => $data,
		]);



        //return response()->json($xData, $HttpStatus);
    }


}
