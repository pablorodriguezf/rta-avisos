<?php 
	include('functions.php');

if (!(isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' ||
   $_SERVER['HTTPS'] == 1) ||
   isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
   $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'))
{
   $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
   header('HTTP/1.1 301 Moved Permanently');
   header('Location: ' . $redirect);
   exit();
}


	if (!isLoggedIn()) {
		$_SESSION['msg'] = "Debe loguearse primero";
		header('location: login.php');
	}
	
	date_default_timezone_set("Europe/Madrid");
	$HoraMinutos = date("H:i");
?>
<!DOCTYPE html>
<html>
<head>
	<title>Pagina de Gesti&oacute;n de casos de RTA</title>
	<link rel="stylesheet" type="text/css" href="style.css">
	<meta name="viewport" content="width=device-width, user-scalable=yes">
	<script type="text/javascript">
	
		function mostrarOpcionesAvisos()
		{
			if (document.formavisos.warning.checked == true) {
				document.getElementById("CasosAvisos").style.display='block';
				document.getElementById("LugarArrollamiento").style.display='none';
				document.getElementById("inputother3").value="";
			} 
			else {
				document.getElementById('CasosAvisos').style.display='none';
			}
		}

		function mostrarOpcionesUrgente(){
                if (document.formavisos.urgente.checked==true) {
                        document.getElementById("LugarArrollamiento").style.display='block';
			document.getElementById('CasosAvisos').style.display='none';
			document.getElementsByName("causaviso").value="";
                } else {
                        document.getElementById("LugarArrollamiento").style.display='none';
                }
		}

		function ocultarOpciones(){
			document.getElementById('CasosAvisos').style.display='none';
			document.getElementsByName("causaviso").value="";
			document.getElementById("LugarArrollamiento").style.display='none';
                        document.getElementById("inputother3").value="";
		}
	
	</script>


</head>
<body>
	<div class="header">
		<img src="images/LogoBlanco.png" style="max-width: 175px;float:left;">
                <h2 style="width: 85%;">Gesti&oacute;n de casos de RTA</h2>
	</div>
	<div class="content">
		<!-- notification message -->
		<?php if (isset($_SESSION['success'])) : ?>
			<div class="error success" >
				<h3>
					<?php 
						echo $_SESSION['success']; 
						unset($_SESSION['success']);
					?>
				</h3>
			</div>
		<?php endif ?>
		<!-- logged in user information -->
		<div class="profile_info">
			<!--<img src="images/user_profile.png"  >-->

			<div style="width:100%;">
				<?php  if (isset($_SESSION['user'])) : ?>
					Conectado como <strong><?php echo $_SESSION['user']['username']; ?></strong>

					<small style="width:100% !important;">
						<i  style="color: #888;">(<?php echo ucfirst($_SESSION['user']['user_type']); ?>)</i> 
						<br>
						<a href="index.php?logout='1'" style="color: red;">Cerrar sesi&oacute;n</a>
					</small>
					<br/><br/>
					Otras opciones: <small style="width:100% !important;">
                                                <br>
                                                <a href="pendientes.php" style="color: red;">Revisar casos pendientes</a>
						<a href="estadisticas.php" style="color: red;">Estadisticas Apps</a>
                                        </small>
					<br/>
					<br/>
					<form action="envio_avisos.php" name="formavisos" method="post" id="formavisos" style="margin: 0px;width:auto;padding:10px;">
						<p>Selecciona el transporte:</p>
						<select name="transporte" form="formavisos" placeholder="Selecciona un transporte..." required>
						  <option value="">Seleccione una opcion...</option>
						  <option value="CERCANIAS">Cercan&iacute;as</option>
						  <option value="AUTOBUSES URBANOS">Autobuses Urbanos</option>
						  <option value="AUTOBUSES INTERURBANOS">Autobuses Interurbanos</option>
						  <option value="GENERAL">Informaci&oacute;n general</option>
						</select>
						<br/><br/>
						<p>Selecciona un tipo de caso:</p>
							<div>
							  <input type="radio" id="warning" name="avisos" value="AVISO" onclick="mostrarOpcionesAvisos();"><label for="warning">AVISO</label><br/>
							  <input type="radio" id="urgente" name="avisos" value="URGENTE" onclick="mostrarOpcionesUrgente();"><label for="urgente">URGENTE</label><br/>
							  <input type="radio" id="info" name="avisos" value="INFO" onclick="ocultarOpciones();"><label for="info">INFO</label><br/>
							  <input type="radio" id="recuerda" name="avisos" value="RECUERDA" onclick="ocultarOpciones();"><label for="recuerda">RECUERDA</label><br/>
							</div>
						<br/>
                        <div id="CasosAvisos" style="font-size: 16px !important; margin:0 0 0 25px; border-left:1px solid black; padding: 0 0 0 10px; display:none;"><p>Selecciona una causa del AVISO:</p>
						<select name="causaviso" form="formavisos" >
							<option value="">Seleccione opcion...</option>
                            <option value="RED">Fallo en la red ferroviaria</option>
                            <option value="TREN">Problemas con el tren</option>
                            <option value="ARROLLAMIENTO">Arrollamiento en la v&iacute;a</option>
							<option value="RETRASO">Retrasos horarios</option>
							<option value="ALTERACIONES">Alteraciones por vandalismo</option>
                        </select>
                        </div>
						<div id="LugarArrollamiento" style="font-size: 16px !important; margin:0 0 0 25px; border-left:1px solid black; padding: 0 0 0 10px; display:none;">Si fue arrollamiento, escriba el municipio donde sucedi&oacute;: <br/><input id="inputother3" type="text" name="arrollamientos" value="" maxlength="60" size="35" onchange="setArrollamiento(this)"  style="width: 100%;max-width: 350px;">
                            <script type="text/javascript">
                                function setArrollamiento(ele) {
                                    document.getElementById("arrollamientos").value = ele.value;
                                }
                            </script>
						</div>
                        <br/><br/>
						<p>Selecciona un subt&iacute;tulo para el caso:</p>
							<div>
							  <input type="radio" id="hora" name="asuntos" value='<?php echo $HoraMinutos ?>' checked>
							  <label for="hora">Hora (- HH:MM)</label><br/>
								<input id="asunto" type="radio" name="asuntos" value=" "> Texto libre: <br/><input id="inputother" type="text" name="" value="" maxlength="60" size="35" onchange="setAsunto(this)"  style="width: 100%;">
								<script type="text/javascript">
									function setAsunto(ele) {
										document.getElementById("asunto").value = ele.value;
									}
								</script>
							</div>
						<br/><br/>		
						<p>Inserte el texto del caso:</p>
						<div>
							<textarea rows="4" cols="50" name="textolibre" form="formavisos" style="width: 100%;" placeholder="Inserte aqu&iacute; el asunto del caso" required></textarea>
						</div>
						<br/>		
						Enlace de m&aacute;s info: <br/><input id="inputother2" type="text" name="enlace" value="" maxlength="600" size="35" style="width: 100%;max-width: 350px;">
						<br/><br/>
						<input id="temporalidad" type="checkbox" name="temporalidad" value="yes" > Marque esta casilla si el caso va durar m&aacute;s de un d&iacute;a.
						<br/><br/>
						<input id="reporte_rrss" type="checkbox" name="reporte_rrss" value="yes" checked> Notificar en las redes sociales de RTA.
						<br/><br/>
						<input type="submit" value="Enviar aviso">
					</form>					
				<?php endif ?>
			</div>
		</div>
	</div>
<br/>
</body>
</html>
