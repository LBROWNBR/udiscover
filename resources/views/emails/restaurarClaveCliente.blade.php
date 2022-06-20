<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
</head>
@php
	$xDataMail = $eData;

@endphp

<body>


<table style="width:100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#e51910">
    <tbody>

		<tr>
			<td>
				<table style="width:100%" cellspacing="0" cellpadding="48px" border="0">
					<tbody>
						<tr>
						<td style="width:100%;padding:32px 0" align="center">
								<header>
									<a href="https://t-presto.com.pe/" style="color:inherit" target="_blank"><img alt="T-Presto" src="https://sistema.t-presto.com.pe/images/tprestoBlanco.png"  width="190"></a>
								</header>
						</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>


		<tr>
			<td align="center">
				<table  style="width:100%;max-width:600px;margin:auto" cellspacing="0" cellpadding="48px" border="0">
					<tbody>
						<tr>
		  					<td style="font-family:&quot;Helvetica Neue&quot;;font-size:16px;font-weight:400;border-radius:4px;border-collapse:collapse;line-height:150%;padding:48px" bgcolor="#FFFFFF">
		    					<p style="font-family:&quot;Helvetica Neue&quot;;font-size:32px;font-weight:600;line-height:1.25;margin:16px 0">
									Hola <?php echo $xDataMail['sName'];?>
								</p>

								<p style="font-family:&quot;Helvetica Neue&quot;;font-size:16px;font-weight:400">¿Olvidaste tu contraseña? Para restablecer su contraseña, siga el enlace a continuación: </p>

								<p style="border-radius:4px;word-break:break-word;color:#007475"><a href="https://sistema.t-presto.com.pe/api/web/formresetpasswordcliente/<?php echo $xDataMail['sVal'];?>" style="color:inherit" target="_blank" >https://sistema.t-presto.com.pe/api/web/formresetpasswordcliente/<?php echo $xDataMail['sVal'];?></a></p>

								<p style="font-family:&quot;Helvetica Neue&quot;;font-size:16px;font-weight:400">Si no está seguro de por qué recibe este mensaje, puede informarnos enviando un correo electrónico a <u>informes@t-presto.com.pe</u>. </p>

								<p style="font-family:&quot;Helvetica Neue&quot;;font-size:16px;font-weight:400">Si necesita más ayuda, comuníquese con nuestro equipo de ayuda.</p>

		    					<p>Gracias, <br>T-Presto</p>
		  					</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>




      <tr>
        <td>
			<table style="width:100%;max-width:600px;margin:auto;padding:32px 0" cellspacing="0" cellpadding="48px" border="0">
				<tbody>
					<tr>
						<td style="border-radius:4px;border-collapse:collapse;line-height:150%;padding:48px" bgcolor="#FFFFFF" align="left">

			  				<p style="border-bottom-width:1px;border-bottom-color:#e7e7e8;border-bottom-style:solid;margin:8px 0"></p>

			  				<p style="margin:8px 0"></p>
							<div style="font-family:&quot;Helvetica Neue&quot;;font-size:12px;font-weight:400;color:#717275">DIRECCIÓN: CALLE 11 MZ. N DPTO 8 C 2DO PISO URB. PEDRO CUEVA</div>
							<div style="font-family:&quot;Helvetica Neue&quot;;font-size:12px;font-weight:400;color:#717275">CALLAO - CALLAO - VENTANILLA </div>
							<div style="font-family:&quot;Helvetica Neue&quot;;font-size:12px;font-weight:400;color:#717275">TELÉFONO: 951 210 017</div>

			  				<p style="margin:8px 0"></p>

						</td>
					</tr>
				</tbody>
			</table>
        </td>
      </tr>


    </tbody>

</table>

</body>
</html>
