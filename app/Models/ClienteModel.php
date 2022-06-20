<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class ClienteModel extends Authenticatable
{
    use Notifiable;
    protected $table    = 'clientes';
    protected $fillable = ['cod_cli', 'usuario', 'clave'];
    protected $hidden   = ['clave', 'api_token'];

    public function getAuthPassword()
    {
        return $this->clave;
    }

    public static function Listar_TodosClientes()
    {
        $Resultado = DB::select("
            SELECT
                cod_cli,
                cod_tipdoc,
                ISNULL(num_doc,'') as num_doc,
                ISNULL(correo_cli,'') as correo_cli,
                ISNULL(usuario,'') as usuario,
                ISNULL(clave,'') as clave,
                ISNULL(api_token,'') as api_token
            FROM clientes
            WHERE id_estado = 1
        ");
        return $Resultado;
    }

    public static function Generar_UserClaveCliente($arrayCampos = [])
    {
        $Resultado = DB::update("UPDATE clientes SET usuario = :usuario, clave = :clave WHERE cod_cli = :cod_cli", $arrayCampos);
        return $Resultado;
    }

    public static function Verificar_Existe_Correo_Cliente($arrayCampos = [])
    {
        $Resultado = DB::selectOne("
            SELECT count(*) AS TOT_REG
            FROM clientes cli
            WHERE cli.id_estado = 1
            AND ltrim(rtrim(cli.correo_cli)) = :correo_cli
        ", $arrayCampos);
        return $Resultado;
    }

    public static function Mostrar_Datos_Cliente_By_Correo($arrayCampos = [])
    {
        $Resultado = DB::selectOne("
            SELECT
                c.cod_cli,
                c.cod_tipcli,
                tc.desc_tipcli,
                c.cod_tipdoc,
                td.abrv_tipdoc,
                c.num_doc,
                c.cod_tipsexo,
                c.nom_cli,
                c.apepat_cli,
                c.apemat_cli,
                (ISNULL(c.nom_cli,'')+' '+ISNULL(c.apepat_cli,'')+' '+ISNULL(c.apemat_cli,'')) as nomCompletoCliente,
                convert(varchar(10),c.fecha_nac,105) as fecha_nac_2,
                c.correo_cli
            FROM clientes c
            LEFT JOIN tipo_cliente tc ON (tc.cod_tipcli = c.cod_tipcli)
            LEFT JOIN tipo_documento td ON (td.cod_tipdoc = c.cod_tipdoc)
            WHERE c.id_estado = 1
            AND ltrim(rtrim(c.correo_cli)) = :correo_cli
        ", $arrayCampos);
        return $Resultado;
    }


    public static function Verificar_Existe_User_Cliente($arrayCampos = [])
    {
        $Resultado = DB::selectOne("
            SELECT count(*) AS TOT_REG
            FROM clientes
            WHERE id_estado = 1 AND ltrim(rtrim(usuario)) = :usuario
        ", $arrayCampos);
        return $Resultado;
    }


    public static function Mostrar_Datos_Cliente_By_CodCli($arrayCampos = [])
    {
        $Resultado = DB::selectOne("
            SELECT
            c.cod_cli,
            c.cod_tipcli,
            tc.desc_tipcli,
            c.cod_tipdoc,
            td.abrv_tipdoc,
            c.num_doc,
            c.nom_cli,
            c.apepat_cli,
            c.apemat_cli,
            convert(varchar(10),c.fecha_nac,105) as fecha_nac,
            ISNULL(c.cod_depa,'') as cod_depa,
            ISNULL(depa.ubig_departamento,'') as departamento,
            ISNULL(c.cod_prov,'') as cod_prov,
            ISNULL(prov.ubig_provincia,'') as provincia,
            ISNULL(c.cod_dist,'') as cod_dist,
            ISNULL(dist.ubig_distrito,'') as distrito,
            c.dir_cli,
            c.correo_cli,
            c.cel_cli,
            ('https://sistema.t-presto.com.pe/images/cliente/'+isnull((select top 1 cf.nom_foto from clientes_fotos cf where cf.id_estado = 5 and cf.cod_cli = c.cod_cli),'Sin_Foto.png')) as urlfoto,
            c.id_estado,
            e.desc_estado,
            c.nickname,
            c.telefono2,
            c.correo2
            FROM clientes c
            LEFT JOIN tipo_cliente tc ON (tc.cod_tipcli = c.cod_tipcli)
            LEFT JOIN tipo_documento td ON (td.cod_tipdoc = c.cod_tipdoc)
            LEFT JOIN (SELECT ubig_dpto_id, ubig_departamento FROM ubigeo WHERE ubig_eliminado = 0 GROUP BY ubig_dpto_id, ubig_departamento) depa ON (depa.ubig_dpto_id = c.cod_depa)
            LEFT JOIN (SELECT ubig_dpto_id, ubig_prov_id, ubig_provincia FROM ubigeo WHERE ubig_eliminado = 0 GROUP BY ubig_dpto_id, ubig_prov_id, ubig_provincia) prov ON (prov.ubig_dpto_id = c.cod_depa AND prov.ubig_prov_id = c.cod_prov)
            LEFT JOIN (SELECT ubig_dpto_id, ubig_prov_id, ubig_dist_id, ubig_distrito FROM ubigeo WHERE ubig_eliminado = 0 GROUP BY ubig_dpto_id, ubig_prov_id, ubig_dist_id, ubig_distrito) dist ON (dist.ubig_dpto_id = c.cod_depa AND dist.ubig_prov_id = c.cod_prov AND dist.ubig_dist_id = c.cod_dist)
            LEFT JOIN estado e ON (e.id_estado = c.id_estado)
            WHERE c.id_estado = 1
            AND ltrim(rtrim(c.cod_cli)) = :cod_cli
        ", $arrayCampos);
        return $Resultado;
    }


    public static function Verificar_Existe_TipoDoc_NumDoc_ClientePotencial($arrayCampos = [])
    {
        $Resultado = DB::selectOne("
            SELECT * FROM clientes_potencial
            WHERE id_estado = 1
            AND tipo_doc = :tipo_doc
            AND nro_doc = :nro_doc
        ", $arrayCampos);
        return $Resultado;
    }

    public static function VerClientesPotenciales_x_ID($arrayCampos = [])
    {
        $Resultado = DB::selectOne("
            SELECT * FROM clientes_potencial WHERE id_potencial = :id_potencial
        ", $arrayCampos);
        return $Resultado;
    }

    public static function Generar_Codigo_ClientePotencial()
    {
        $Resultado = DB::selectOne("
            SELECT (isnull(MAX(id_potencial),0) + 1) AS codigo FROM clientes_potencial
        ");
        return $Resultado;
    }

    public static function InsertData_ClientePotencial($arrayCampos = [])
    {
        $Resultado = DB::insert("

            INSERT INTO clientes_potencial (
                id_potencial,
                nombres_cli,
                apepat_cli,
                apemat_cli,
                tipo_doc,
                nro_doc,
                cod_depa,
                cod_prov,
                cod_dist,
                distrito,
                direc,
                tele_fijo,
                tele_cel,
                correo,
                cod_prod,
                tipo_prestamo,
                observacion,
                pasado,
                fecha,
                id_estado,
                form_ingreso
            ) VALUES(
                :id_potencial,
                :nombres_cli,
                :apepat_cli,
                :apemat_cli,
                :tipo_doc,
                :nro_doc,
                :cod_depa,
                :cod_prov,
                :cod_dist,
                :distrito,
                :direc,
                :tele_fijo,
                :tele_cel,
                :correo,
                :cod_prod,
                :tipo_prestamo,
                :observacion,
                :pasado,
                GETDATE(),
                :id_estado,
                :form_ingreso
            )
        ", $arrayCampos);

        return $Resultado;
    }


    public static function UpdateData_ClientePotencial($arrayCampos = [])
    {
        $Resultado = DB::update("
            UPDATE clientes_potencial SET
                nombres_cli  = :nombres_cli,
                apepat_cli  = :apepat_cli,
                apemat_cli  = :apemat_cli,
                tipo_doc    = :tipo_doc,
                nro_doc     = :nro_doc,
                cod_depa    = :cod_depa,
                cod_prov    = :cod_prov,
                cod_dist    = :cod_dist,
                distrito    = :distrito,
                direc       = :direc,
                tele_fijo   = :tele_fijo,
                tele_cel    = :tele_cel,
                correo      = :correo,
                cod_prod    = :cod_prod,
                tipo_prestamo   = :tipo_prestamo,
                observacion     = :observacion,
                pasado          = :pasado,
                id_estado       = :id_estado,
                form_ingreso    = :form_ingreso,
                fecha_actualiza = GETDATE()
            WHERE id_potencial = :id_potencial
        ", $arrayCampos);

        return $Resultado;
    }

    public static function Generar_Codigo_PrestamoClientePotencial()
    {
        $Resultado = DB::selectOne("
            SELECT (isnull(MAX(id_prest_potencial),0) + 1) AS codigo FROM clientes_potencial_prestamos
        ");
        return $Resultado;
    }


    public static function InsertData_PrestamoClientePotencial($arrayCampos = [])
    {
        $Resultado = DB::insert("

            INSERT INTO clientes_potencial_prestamos (
                id_prest_potencial,
                id_potencial,
                form_ingreso,
                tipoprestamo,
                montoprestamo,
                cod_pago,
                interes,
                fechaprestamo,
                cuotas,
                fecharegistro,
                id_estado
            ) VALUES(
                :id_prest_potencial,
                :id_potencial,
                :form_ingreso,
                :tipoprestamo,
                :montoprestamo,
                :cod_pago,
                :interes,
                :fechaprestamo,
                :cuotas,
                GETDATE(),
                :id_estado
            )
        ", $arrayCampos);

        return $Resultado;
    }


    public static function UpdateData_GarantiaClientePotencial($arrayCampos = [])
    {
        $Resultado = DB::update("
            UPDATE clientes_potencial_prestamos SET
                descrip_garantia  = :descrip_garantia,
                marca  = :marca,
                modelo  = :modelo,
                ano  = :ano,
                color  = :color,
                estado  = :estado,
                valor  = :valor,
                linea  = :linea,
                ubi  = :ubi,
                obser  = :obser,
				kilate  = :kilate,
				peso  = :peso,
				descripjoya  = :descripjoya,
                id_estado_garantia  = :id_estado_garantia
            WHERE id_prest_potencial = :id_prest_potencial and id_potencial = :id_potencial
        ", $arrayCampos);

        return $Resultado;
    }


    public static function Generar_Codigo_clientes_potencial_prestamos_docs()
    {
        $Resultado = DB::selectOne("
            SELECT (isnull(MAX(id_prest_potencial_doc),0) + 1) AS codigo FROM clientes_potencial_prestamos_docs
        ");
        return $Resultado;
    }


    public static function InsertData_clientes_potencial_prestamos_docs($arrayCampos = [])
    {
        $Resultado = DB::insert("

            INSERT INTO clientes_potencial_prestamos_docs (
                id_prest_potencial_doc,
                id_prest_potencial,
                id_potencial,
                nom_foto,
                fecha_registra,
                id_estado
            ) VALUES(
                :id_prest_potencial_doc,
                :id_prest_potencial,
                :id_potencial,
                :nom_foto,
                GETDATE(),
                '1'
            )
        ", $arrayCampos);

        return $Resultado;
    }




    public static function Listar_Creditos_Pendientes_x_Cliente($arrayCampos = [])
    {
        $Resultado = DB::select("
          SELECT
          c.cod_credito,
          c.cod_cli,
          c.cod_garante,
          CONVERT(VARCHAR(10),c.fecha_pedido,105) AS fecha,
          UPPER(c.tipo_modelo) AS tipo_modelo,
          c.cod_prod,
          UPPER(p.nom_prod) AS nom_prod,
          c.cod_tippago,
          UPPER(tp.desc_tippago) AS desc_tippago,
          c.tem,
          c.monto,
          c.monto_adicional,
          (c.monto + ISNULL(c.monto_adicional,0)) AS monto_total,
          c.descuento,
          (select count(d1.cod_credito) from deudas d1 where d1.cod_credito = c.cod_credito) as total_cuotas,
		  (select count(d2.cod_credito) from deudas d2 where d2.cod_credito = c.cod_credito and d2.cod_voucher >0 and d2.completo = 'SI') as cuotas_pagadas,
		  (select count(d3.cod_credito) from deudas d3 where d3.cod_credito = c.cod_credito and isnull(d3.cod_voucher,0) = 0 and d3.fecha_ven <= GETDATE()) as cuotas_no_pagadas,
		  (select count(d4.cod_credito) from deudas d4 where d4.cod_credito = c.cod_credito and isnull(d4.cod_voucher,0) = 0 and d4.fecha_ven > GETDATE()) as cuotas_por_vencer
          FROM creditos c
          LEFT JOIN productos p ON (c.cod_prod = p.cod_prod)
          LEFT JOIN tipo_pago tp ON (tp.cod_tippago = c.cod_tippago)
          WHERE UPPER(c.estado) = 'SI'
          AND c.cod_cli = :cod_cli
          ORDER BY c.fecha_pedido
        ", $arrayCampos);
        return $Resultado;
    }

    public static function Listar_Cuotas_Pendientes_x_IdCredito($arrayCampos = [])
    {
        $NroRegistros = $arrayCampos['NroRegistros'];
        $cod_credito = $arrayCampos['cod_credito'];

        if($NroRegistros != '' || $NroRegistros > 0){
            $TopRegistros = "TOP ".$NroRegistros." ";
        }else{
            $TopRegistros = " ";
        }


        $Resultado = DB::select("
            SELECT ".$TopRegistros."
            d.cod_deuda,
            c.cod_credito,
            UPPER(c.tipo_modelo) AS tipo_modelo,
            c.cod_cli,
            d.cuota,
            (SELECT  count(d2.cuota) as totcuota FROM creditos c2 INNER JOIN deudas d2 on (c2.cod_credito = d2.cod_credito) WHERE c2.cod_credito = $cod_credito AND UPPER(c2.estado) = 'SI' ) AS totcuota,
            CONVERT(VARCHAR(10),d.fecha_ven,105) AS fecha_ven,
            d.debe1 as cuota_normal,
            d.debe2 as cuota_cdscto,
            (CASE WHEN (ISNULL(d.debe, 0) > 0) THEN d.debe ELSE d.debe2 END) AS saldo,
            d.cod_voucher,
            d.acuenta,
            d.adelanto,
            d.debe,
            d.completo,
            d.codvoucher_adel,
            d.val_app,
            d.val_web,
            (CASE WHEN (ISNULL(d.val_app,'') = 'NO') THEN 'XA'
                WHEN (ISNULL(d.val_app,'') = '' AND CAST(d.fecha_ven AS DATE) > CAST(GETDATE() AS DATE) ) THEN 'PE'
                WHEN (ISNULL(d.val_app,'') = '' AND CAST(d.fecha_ven AS DATE) <= CAST(GETDATE() AS DATE) ) THEN 'VE'
                ELSE 'OT'
            END) AS estado_abrev
            FROM creditos c
            INNER JOIN deudas d on (c.cod_credito = d.cod_credito)
            WHERE c.cod_credito = $cod_credito
            AND UPPER(c.estado) = 'SI'
            AND ISNULL(d.cod_voucher,'0') = '0'
            ORDER BY d.fecha_ven
        ");

        return $Resultado;
    }


    //=========================================================================
    //=========================================================================
    //=========================================================================


    public static function Generar_Codigo_pago_deuda_cab_app()
    {
        $Resultado = DB::selectOne("
            SELECT (isnull(MAX(cod_pago_deuda_cab),0) + 1) AS codigo FROM pago_deuda_cab_app
        ");
        return $Resultado;
    }

    public static function InsertData_pago_deuda_cab_app($arrayCampos = [])
    {
        $Resultado = DB::insert("

            INSERT INTO pago_deuda_cab_app (
                cod_pago_deuda_cab,
                cod_credito,
                cod_cli,
                tot_cuota,
                monto_total,
                nro_voucher,
                foto_voucher,
                monto_deposita,
                estado_pago,
                fecha_registro,
                id_estado,
                form_ingreso
            ) VALUES(
                :cod_pago_deuda_cab,
                :cod_credito,
                :cod_cli,
                :tot_cuota,
                :monto_total,
                :nro_voucher,
                :foto_voucher,
                :monto_deposita,
                :estado_pago,
                GETDATE(),
                '1',
                :form_ingreso
            )
        ", $arrayCampos);

        return $Resultado;
    }


    public static function ActualizarNombreVoucher_pago_deuda_cab_app($arrayCampos = [])
    {
        $Resultado = DB::update("
            UPDATE pago_deuda_cab_app
            SET foto_voucher = :foto_voucher
            WHERE cod_pago_deuda_cab = :cod_pago_deuda_cab
        ", $arrayCampos);

        return $Resultado;
    }


    public static function Generar_Codigo_pago_deuda_det_app()
    {
        $Resultado = DB::selectOne("
            SELECT (isnull(MAX(cod_pago_deuda_det),0) + 1) AS codigo FROM pago_deuda_det_app
        ");
        return $Resultado;
    }


    public static function InsertData_pago_deuda_det_app($arrayCampos = [])
    {
        $Resultado = DB::insert("

            INSERT INTO pago_deuda_det_app (
                cod_pago_deuda_det,
                cod_pago_deuda_cab,
                cod_deuda,
                cuota,
                fecha_ven,
                monto,
                estado_pago,
                fecha_registro,
                id_estado
            ) VALUES(
                :cod_pago_deuda_det,
                :cod_pago_deuda_cab,
                :cod_deuda,
                :cuota,
                :fecha_ven,
                :monto,
                :estado_pago,
                GETDATE(),
                '1'
            )
        ", $arrayCampos);

        return $Resultado;
    }


    public static function ActualizarEstadoDeuda_val_app($arrayCampos = [])
    {
        $Resultado = DB::update("
            UPDATE deudas SET val_app = 'NO' WHERE cod_deuda = :cod_deuda
        ", $arrayCampos);

        return $Resultado;
    }



    //=========================================================================
    //=========================================================================
    //=========================================================================


    public static function Generar_codigo_comprobante_deuda_cab_app()
    {
        $Resultado = DB::selectOne("
            SELECT (isnull(MAX(cod_comp_deuda_cab_app),0) + 1) AS codigo FROM comprobante_deuda_cab_app
        ");
        return $Resultado;
    }


    public static function InsertData_comprobante_deuda_cab_app($arrayCampos = [])
    {
        $Resultado = DB::insert("

            INSERT INTO comprobante_deuda_cab_app (
                cod_comp_deuda_cab_app,
                cod_cli,
                cod_credito,
                fecha_pago,
                monto_pagado,
                numop,
                tot_cuotas,
                fecha_reg,
                id_estado,
                cod_pago_deuda_cab
            ) VALUES(
                :cod_comp_deuda_cab_app,
                :cod_cli,
                :cod_credito,
                GETDATE(),
                :monto_pagado,
                :numop,
                :tot_cuotas,
                GETDATE(),
                '1',
                :cod_pago_deuda_cab
            )
        ", $arrayCampos);

        return $Resultado;
    }


    public static function Generar_codigo_comprobante_deuda_det_app()
    {
        $Resultado = DB::selectOne("
            SELECT (isnull(MAX(cod_comp_deuda_det_app),0) + 1) AS codigo FROM comprobante_deuda_det_app
        ");
        return $Resultado;
    }


    public static function InsertData_comprobante_deuda_det_app($arrayCampos = [])
    {

        $Resultado = DB::insert("

            INSERT INTO comprobante_deuda_det_app (
                cod_comp_deuda_det_app,
                cod_comp_deuda_cab_app,
                cod_deuda,
                cuota,
                monto_cuota,
                fecha_ven,
                fecha_pago,
                fecha_registro,
                id_estado
            ) VALUES(
                :cod_comp_deuda_det_app,
                :cod_comp_deuda_cab_app,
                :cod_deuda,
                :cuota,
                :monto_cuota,
                :fecha_ven,
                GETDATE(),
                GETDATE(),
                '1'
            )
        ", $arrayCampos);

        return $Resultado;
    }


    public static function Ver_Datos_Comprobante_CAB_APP_x_IdCab($arrayCampos = [])
    {
        $Resultado = DB::selectOne("
            SELECT
            c.cod_comp_deuda_cab_app,
            c.cod_pago_deuda_cab,
            c.cod_cli,
            (ltrim(rtrim(ISNULL(cc.apepat_cli,'')))+' '+ltrim(rtrim(ISNULL(cc.apemat_cli,'')))+' '+ltrim(rtrim(ISNULL(cc.nom_cli,''))) ) AS cliente,
            c.cod_credito,
            p.nom_prod,
            convert(varchar(10),c.fecha_pago,105) as fecha_pago,
            tp.nom_tippago,
            fp.desc_formpago,
            cr.tem,
            c.monto_pagado,
            c.numop,
            c.tot_cuotas
            FROM comprobante_deuda_cab_app c
            LEFT JOIN creditos cr ON (c.cod_credito = cr.cod_credito)
            LEFT JOIN clientes cc ON (c.cod_cli = cc.cod_cli)
            LEFT JOIN productos p ON (p.cod_prod = cr.cod_prod)
            LEFT JOIN tipo_pago tp ON (tp.cod_tippago = cr.cod_tippago)
            LEFT JOIN forma_pago fp ON (fp.cod_formpago = 1)
            WHERE c.cod_comp_deuda_cab_app = :cod_comp_deuda_cab_app
        ",$arrayCampos);
        return $Resultado;
    }


    public static function Ver_Datos_Comprobante_DET_APP_x_IdCab($arrayCampos = [])
    {
        $Resultado = DB::select("
        SELECT
        d.cod_deuda,
        d.cuota,
        d.monto_cuota,
        convert(varchar(10),de.fecha_ven,105) as fecha_ven,
        convert(varchar(10),d.fecha_pago,105) as fecha_pago
        FROM comprobante_deuda_det_app d
        LEFT JOIN deudas de ON (de.cod_deuda = d.cod_deuda)
        WHERE d.cod_comp_deuda_cab_app = :cod_comp_deuda_cab_app
        ",$arrayCampos);
        return $Resultado;
    }



    public static function Generar_Codigo_ClienteFoto()
    {
        $Resultado = DB::selectOne('SELECT (isnull(max(cod_clifoto),0) + 1) as codigoFoto FROM clientes_fotos');
        return $Resultado;
    }

    public static function Existe_FotoApp_ByCodCli($arrayCampos = [])
    {
        $Resultado = DB::selectOne("
            SELECT cod_cli, count(*) as totreg
            FROM clientes_fotos
            WHERE id_estado = 5
            AND cod_cli = :cod_cli
            GROUP BY cod_cli
        ",$arrayCampos);
        return $Resultado;
    }

    public static function Limpia_FotoApp_ByCodCli($arrayCampos = [])
    {
        $Resultado = DB::delete("
            DELETE FROM clientes_fotos WHERE id_estado = 5 AND cod_cli = :cod_cli
        ",$arrayCampos);
        return $Resultado;
    }


    public static function InsertData_ClienteFotoApp($arrayCampos = [])
    {
        $Resultado = DB::insert("
            INSERT INTO clientes_fotos (
                cod_clifoto,
                cod_cli,
                nom_foto,
                desc_foto,
                fecha_registra,
                id_estado
            ) VALUES(
                :cod_clifoto,
                :cod_cli,
                :nom_foto,
                :desc_foto,
                GETDATE(),
                :id_estado
            )
        ", $arrayCampos);

        return $Resultado;
    }


    public static function UpdateData_ClienteDatosBasicos($arrayCampos = [])
    {
        $Resultado = DB::update("
            UPDATE clientes SET
                nickname  = :nickname,
                telefono2  = :telefono2,
                correo2  = :correo2
            WHERE cod_cli = :cod_cli
        ", $arrayCampos);

        return $Resultado;
    }


}
