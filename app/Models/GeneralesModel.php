<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralesModel extends Model
{
    //=====================================================
    //===================      UBIGEOS     ================
    //=====================================================

     public static function AppModel_ListarDepartamentos()
     {
        $Resultado = DB::select("
            SELECT
            ubig_dpto_id as coddep,
            ubig_departamento as departamento
            FROM ubigeo
            WHERE ubig_eliminado = 0
            GROUP BY
            ubig_dpto_id,
            ubig_departamento
            ORDER BY ubig_departamento
        ");
        return $Resultado;
     }


     public static function AppModel_ListarProvincias($arrayCampos = [])
     {
        $Resultado = DB::select("
            SELECT
            ubig_dpto_id as coddep,
            ubig_prov_id as codprov,
            ubig_provincia as provincia
            FROM ubigeo
            WHERE ubig_eliminado = 0
            AND ubig_dpto_id = :coddep
            GROUP BY
            ubig_dpto_id,
            ubig_prov_id,
            ubig_provincia
            ORDER BY ubig_provincia
        ", $arrayCampos);
        return $Resultado;
     }


     public static function AppModel_ListarDistritos($arrayCampos = [])
     {
         $Resultado = DB::select("
            SELECT
            ubig_dpto_id as coddep,
            ubig_prov_id as codprov,
            ubig_dist_id as coddist,
            ubig_distrito as distrito
            FROM ubigeo
            WHERE ubig_eliminado = 0
            AND ubig_dpto_id = :coddep
            AND ubig_prov_id = :codprov
            GROUP BY
            ubig_dpto_id,
            ubig_prov_id,
            ubig_dist_id,
            ubig_distrito
            ORDER BY ubig_distrito
        ", $arrayCampos);
        return $Resultado;
     }

     public static function AppModel_VerDistrito_ByID($arrayCampos = [])
     {
         $Resultado = DB::selectOne("
            SELECT
            ubig_dpto_id as coddep,
            ubig_prov_id as codprov,
            ubig_dist_id as coddist,
            ubig_distrito as distrito
            FROM ubigeo
            WHERE ubig_eliminado = 0
            AND ubig_dpto_id = :coddep
            AND ubig_prov_id = :codprov
            AND ubig_dist_id = :coddist
            GROUP BY
            ubig_dpto_id,
            ubig_prov_id,
            ubig_dist_id,
            ubig_distrito
            ORDER BY ubig_distrito
        ", $arrayCampos);
        return $Resultado;
     }

    //======================================================
    //======      CADENA ENCRIPTADA/DESENCRIPTADA     ======
    //======================================================

    public static function AppModel_Encriptar($cadena_a_Encriptar)
    {
         $Resultado = DB::selectOne("SELECT dbo.ENCRIPTA_PASS('$cadena_a_Encriptar') AS ENCRIPTADO");
         return $Resultado;
    }

    public static function AppModel_Desencriptar($cadena_Encriptado)
    {
         $Resultado = DB::selectOne("SELECT dbo.DESENCRIPTAR_PASS($cadena_Encriptado) AS DESENCRIPTADO");
         return $Resultado;
    }

    //===============

    public static function AppModel_EncriptarCadena($string, $key)
    {
        $result = '';
        for($i=0; $i<strlen($string); $i++) {
           $char = substr($string, $i, 1);
           $keychar = substr($key, ($i % strlen($key))-1, 1);
           $char = chr(ord($char)+ord($keychar));
           $result.=$char;
        }
        return base64_encode($result);
    }

    public static function AppModel_DesencriptarCadena($string, $key)
    {
        $result = '';
        $string = base64_decode($string);
        for($i=0; $i<strlen($string); $i++) {
           $char = substr($string, $i, 1);
           $keychar = substr($key, ($i % strlen($key))-1, 1);
           $char = chr(ord($char)-ord($keychar));
           $result.=$char;
        }
        return $result;
    }

    //=====================================================
    //==============      TIPOS DE DOCUMENTOS     ===========
    //=====================================================

    public static function AppModel_ListarTipoDocumentos()
    {
         $Resultado = DB::select("
            SELECT * FROM tipo_documento WHERE id_estado = 1 ORDER BY desc_tipdoc
         ");
         return $Resultado;
    }


    //=====================================================
    //==============      TIPOS DE PRESTAMO     ===========
    //=====================================================

    public static function AppModel_ListarTipoPrestamos()
    {
         $Resultado = DB::select("
            SELECT * FROM productos WHERE id_estado = 1 and cod_prod in (1,2,3,4,5)
         ");
         return $Resultado;
    }

    public static function AppModel_VerTipoPrestamos_ById($arrayCampos = [])
    {
         $Resultado = DB::selectOne("
            SELECT * FROM productos WHERE id_estado = 1 and cod_prod = :cod_prod
         ", $arrayCampos);
         return $Resultado;
    }

    //=====================================================
    //==============      TIPOS DE PAGO     ===========
    //=====================================================

    public static function AppModel_ListarTipoPagoApp()
    {
         $Resultado = DB::select("
            SELECT cod_tippago, nom_tippago, desc_tippago, tea_app, tem_app, id_estado
            FROM tipo_pago WHERE id_estado = 1 and app = 1
         ");
         return $Resultado;
    }

    public static function AppModel_VerTipoPago_ById($arrayCampos = [])
    {
         $Resultado = DB::selectOne("
            SELECT * FROM tipo_pago WHERE id_estado = 1 and cod_tippago = :cod_tippago
         ", $arrayCampos);
         return $Resultado;
    }


    //=====================================================
    //==============      TEXTO WHATSAPP     ===========
    //=====================================================

    public static function AppModel_TextoWhatsApp()
    {
         $Resultado = DB::selectOne("SELECT * FROM parametros WHERE id_estado = 1 AND cod_parametro = '2' ");
         return $Resultado;
    }

    public static function AppModel_TextoEmail()
    {
         $Resultado = DB::selectOne("SELECT * FROM parametros WHERE id_estado = 1 AND cod_parametro = '3' ");
         return $Resultado;
    }


    //=====================================================
    //==============      TEXTO WHATSAPP     ===========
    //=====================================================

    public static function VerEmpresaDatos()
    {
        $Resultado = DB::selectOne("SELECT * FROM empresa WHERE id_empresa = '1' ");
        return $Resultado;
    }

}
