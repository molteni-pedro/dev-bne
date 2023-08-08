<?php

global $wpdb;
$table = $wpdb->prefix . 'bne_bloquecontenido';
$tableCriterio = $wpdb->prefix . 'bne_criterios';
$tableEstandares = $wpdb->prefix . 'bne_estandares';
$urlCancelar = 'admin.php?page=bloques-contenido';
if (!empty($_POST)) {
	
	if(!empty($_POST['CRcodigo'])){
		$table = $tableCriterio;
		$data = array(
			'CRidbloquecontenido' 	=> $_GET['id'],
			'CRcodigo' 				=> $_POST['CRcodigo'],
			'CRdescripcion' 		=> $_POST['CRdescripcion'],
		);

		$format = array(
			'%d',
			'%s',
			'%s',
		);

		if($_GET['criterio'] > 0){
			$data['CRid'] = $_GET['criterio'];
			$format[] = '%d';
		}
		$url = "admin.php?page=bloques-contenido&action=edit&id=".$_GET['id'].'&tab=criterios';
	}elseif(!empty($_POST['ETcodigo'])){
		$table = $tableEstandares;
		$data = array(
			'ETidcriterio' 		=> $_POST['ETidcriterio'],
			'ETcodigo' 			=> $_POST['ETcodigo'],
			'ETdescripcion' 	=> $_POST['ETdescripcion'],
		);

		$format = array(
			'%d',
			'%s',
			'%s',
		);

		if($_GET['estandar'] > 0){
			$data['ETid'] = $_GET['estandar'];
			$format[] = '%d';
		}
		$url = "admin.php?page=bloques-contenido&action=edit&id=".$_GET['id'].'&tab=estandares';
	}else{
		$data = array(
			'BCidasignatura' 	=> $_POST['BCidasignatura'],
			'BCidnivel' 		=> $_POST['BCidnivel'],
			'BCtexto' 			=> $_POST['BCtexto'],
			'BCcontenido' 		=> $_POST['BCcontenido'],
			'BCcontenidotexto' 	=> $_POST['BCcontenidotexto'],
		);

		$format = array(
			'%d',
			'%d',
			'%s',
			'%s',
			'%s',
		);

		if($_GET['id'] > 0){
			$data['BCid'] = $_GET['id'];
			$format[] = '%d';
		}

		$url = "admin.php?page=bloques-contenido";
	}

	$success = $wpdb->replace($table, $data, $format);

	if($success){
		if($_GET['id'] == 0 && $_GET['backedit'] == 1){
			$lastid = $wpdb->insert_id;
			$url = "admin.php?page=bloques-contenido&action=edit&id=".$lastid.'&tab='.$_GET['tab'];
		}
	}

	Redirect($url);

} elseif($_GET['action'] == "delete") {

	$ids = isset($_GET['id']) ? $_GET['id'] : array();

	if (is_array($ids)){
		$ids = implode(',', $ids);
	}

	if (!empty($ids)) {
		$wpdb->query("DELETE FROM $table WHERE BCid IN ($ids)");
	}

	Redirect("admin.php?page=bloques-contenido");

} elseif($_GET['action'] == "deletecriterio") {
	$table = $tableCriterio;
	$ids = isset($_GET['criterio']) ? $_GET['criterio'] : array();

	if (is_array($ids)){
		$ids = implode(',', $ids);
	}

	if (!empty($ids)) {
		$wpdb->query("DELETE FROM $table WHERE CRid IN ($ids)");
	}

	Redirect("admin.php?page=bloques-contenido&action=edit&id=".$_GET['id'].'&tab=criterios');

} elseif($_GET['action'] == "deleteestandar") {
	$table = $tableEstandares;
	$ids = isset($_GET['estandar']) ? $_GET['estandar'] : array();

	if (is_array($ids)){
		$ids = implode(',', $ids);
	}

	if (!empty($ids)) {
		$wpdb->query("DELETE FROM $table WHERE ETid IN ($ids)");
	}

	Redirect("admin.php?page=bloques-contenido&action=edit&id=".$_GET['id'].'&tab=estandares');
} else {

	?>

	<div class="page-content">
		<div class="row">
			<div class="col-md-12">

				<div class="widget-box transparent invoice-box">
					<div class="widget-header">
						<h3 class="widget-title lighter">
							<span><?php echo __('Bloques de contenido', 'traduccionrecursos');?></span>
						</h3>
						<?php if($_GET['action'] == "edit" || $_GET['action'] == "editcriterio" || $_GET['action'] == "editestandar"){ ?>
						<div class="widget-toolbar">
							<a id="btnCancelar" tabindex="7" title="<?php echo __('Salir', 'traduccionrecursos');?>" class="fa fa-times ico-boton30" href="javascript:{}"><span><?php echo __('Salir', 'traduccionrecursos');?></span></a>
						</div>
						<div class="widget-toolbar">
							<a id="btnAceptar" tabindex="6" title="<?php echo __('Guardar', 'traduccionrecursos');?>" class="fa fa-save ico-boton30"  href="javascript:{}"><span><?php echo __('Guardar', 'traduccionrecursos');?></span></a>
						</div>
						<?php }else{ ?>
						<div class="widget-toolbar">
							<a id="btnAceptar" tabindex="6" title="<?php echo __('Nuevo', 'traduccionrecursos');?>" class="fa fa-plus ico-boton30"  href="?page=bloques-contenido&action=edit&id=0"><span><?php echo __('Nuevo', 'traduccionrecursos');?></span></a>
						</div>
						<?php } ?>
					</div>
				</div>

	<?php
	switch ($_GET['action']) {
		case 'edit':
			$urlCancelar = 'admin.php?page=bloques-contenido';
			if(isset($_GET['id']) && strlen($_GET['id']) > 0){
				$id = $_GET['id'];
			}else{
				$id = 0;
			}
			
			if($id > 0){
				$aValores = array($id);
				$qry = $wpdb->prepare("SELECT * FROM ".$table." WHERE BCid = %d", $aValores);
				$row = $wpdb->get_row($qry);
				$sBloque = $row->BCtexto;
				$sTitulo = $row->BCcontenido;
				$sContenido = $row->BCcontenidotexto;
			}else{
				$sBloque = '';
				$sTitulo = '';
				$sContenido = '';
			}

			$aNivel 		= GetTableArray($wpdb->prefix."bne_nivel","NIid","NItitulo","NItitulo");
			$aAsignatura 	= GetTableArray($wpdb->prefix."bne_asignatura","ASid","AStitulo","AStitulo");
			$urlForm = $_SERVER['REQUEST_URI'];
			?>
			<form method="post" id="auxiliares" name="auxiliares" action="<?php echo $urlForm;?>">
				<ul class="nav nav-tabs" id="myTab">
					<li class="active"><a data-toggle="tab" href="#datos"><?php echo __('Datos');?></a></li>
					<li><a data-toggle="tab" href="#criterios"><?php echo __('Criterios');?></a></li>
					<li><a data-toggle="tab" href="#estandares"><?php echo __('Estándares');?></a></li>
				</ul>
				<div class="tab-content">
					<div id="datos" class="tab-pane fade in active">
						<div class="row">
							<div class="col-xs-12 col-sm-6 panel-info-principal">
								<fieldset>
									<legend><i class="fa fa-info"></i><?php echo __('Datos Principales', 'traduccionrecursos');?></legend>
									<div class="row">
										<div class="col-xs-4 control-label text-left required">
											<span class="forms-label-top"><?php echo __('Nivel', 'traduccionrecursos');?></span>
											<?php echo GetSelectFromArray("BCidnivel",$aNivel, $row->BCidnivel); ?>
										</div>
										<div class="col-xs-8 control-label text-left required">
											<span class="forms-label-top"><?php echo __('Asignatura', 'traduccionrecursos');?></span>
											<?php echo GetSelectFromArray("BCidasignatura",$aAsignatura, $row->BCidasignatura); ?>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-4 control-label text-left required">
											<span class="forms-label-top"><?php echo __('Bloque', 'traduccionrecursos');?></span>
											<input name="BCtexto" value="<?php echo $sBloque;?>" id="BCtexto" class="form-control" type="text">
										</div>
										<div class="col-xs-8 control-label text-left required">
											<span class="forms-label-top"><?php echo __('Título', 'traduccionrecursos');?></span>
											<input name="BCcontenido" value="<?php echo $sTitulo;?>" id="BCcontenido" class="form-control" type="text">
										</div>
									</div>
									<div class="row">
										<div class="col-xs-12 control-label text-left">
											<span class="forms-label-top"><?php echo __('Contenido', 'traduccionrecursos');?></span>
											<textarea name="BCcontenidotexto" rows="10" id="BCcontenidotexto" class="form-control" type="text"><?php echo $sContenido;?></textarea>
										</div>
									</div>
								</fieldset>
							</div>
						</div>
					</div>
					<div id="criterios" class="tab-pane fade">

						<div class="row">
							<div class="col-xs-12 col-sm-8 fieldset-theme">
								<fieldset>
									<legend><i class="fa fa-list"></i><?php echo __('Criterios', 'traduccionrecursos');?></legend>
									<?php
									$urlNuevoCriterio = "?page=bloques-contenido&action=editcriterio&id=".$_GET['id']."&criterio=0";
									$aTablaCrit = $wpdb->get_results("SELECT * FROM $tableCriterio WHERE CRidbloquecontenido = $id ORDER BY CRcodigo",	ARRAY_A);
									?>
									<div class="widget-header">
										<div class="widget-toolbar">
											<a id="btnNuevo" tabindex="6" title="<?php echo __('Nuevo', 'traduccionrecursos');?>" class="fa fa-plus ico-boton30"  href="<?php echo $urlNuevoCriterio;?>"><span><?php echo __('Nuevo', 'traduccionrecursos');?></span></a>
										</div>
									</div>
									<table class="wp-list-table widefat striped">
										<thead>
											<tr>
												<th class="column-id">
													<?php echo __('Código', 'traduccionrecursos');?>
												</th>
												<th>
													<?php echo __('Título', 'traduccionrecursos');?>
												</th>
											</tr>
										</thead>
										<tbody>
											<?php 
											foreach ($aTablaCrit as $rowDatosCrit): array_map('htmlentities', $rowDatosCrit); 
												?>
												<tr>
													<th class="column-id">
														<?php echo  $rowDatosCrit['CRcodigo']; ?>
													</th>
													<td><div class="contenido"><?php echo  $rowDatosCrit['CRdescripcion']; ?></div>
														<div class="row-actions">
															<span class="edit">
																<a href="?page=bloques-contenido&action=editcriterio&id=<?php echo  $id ?>&criterio=<?php echo  $rowDatosCrit['CRid']; ?>">
																	Editar
																</a>
																 | 
															</span>
																<span class="delete">
																	<a href="?page=bloques-contenido&action=deletecriterio&id=<?php echo  $id ?>&criterio=<?php echo  $rowDatosCrit['CRid']; ?>">
																	Eliminar
																</a>
															</span>
														</div>
													</td>
												</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</fieldset>
							</div>
						</div>
					</div>
					<div id="estandares" class="tab-pane fade">
						<div class="row">
							<div class="col-xs-12 col-sm-8 fieldset-theme">
								<fieldset>
									<legend><i class="fa fa-list"></i><?php echo __('Estándares', 'traduccionrecursos');?></legend>
									<?php
									$urlNuevoEstandar = "?page=bloques-contenido&action=editestandar&id=".$_GET['id']."&estandar=0";
									$aTablaEst = $wpdb->get_results("
										SELECT * FROM $tableEstandares INNER JOIN $tableCriterio ON CRid = ETidcriterio 
										WHERE CRidbloquecontenido = $id ORDER BY CRcodigo",	ARRAY_A);
									?>
									<div class="widget-header">
										<div class="widget-toolbar">
											<a id="btnNuevo" tabindex="6" title="<?php echo __('Nuevo', 'traduccionrecursos');?>" class="fa fa-plus ico-boton30"  href="<?php echo $urlNuevoEstandar;?>"><span><?php echo __('Nuevo', 'traduccionrecursos');?></span></a>
										</div>
									</div>
									<table class="wp-list-table widefat striped">
										<thead>
											<tr>
												<th class="column-id">
													<?php echo __('Criterio', 'traduccionrecursos');?>
												</th>
												<th class="column-id">
													<?php echo __('Código', 'traduccionrecursos');?>
												</th>
												<th>
													<?php echo __('Título', 'traduccionrecursos');?>
												</th>
											</tr>
										</thead>
										<tbody>
											<?php 

											foreach ($aTablaEst as $rowDatosEst): array_map('htmlentities', $rowDatosEst); 
												?>
												<tr>
													<th class="column-id">
														<?php echo  $rowDatosEst['CRcodigo']; ?>
													</th>
													<th class="column-id">
														<?php echo  $rowDatosEst['ETcodigo']; ?>
													</th>
													<td><div class="contenido"><?php echo  $rowDatosEst['ETdescripcion']; ?></div>
														<div class="row-actions">
															<span class="edit">
																<a href="?page=bloques-contenido&action=editestandar&id=<?php echo  $id ?>&estandar=<?php echo  $rowDatosEst['ETid']; ?>">
																	Editar
																</a>
																 | 
															</span>
															<span class="delete">
																<a href="?page=bloques-contenido&action=deleteestandar&id=<?php echo  $id ?>&estandar=<?php echo  $rowDatosEst['ETid']; ?>">
																	Eliminar
																</a>
															</span>
														</div>
													</td>
												</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</fieldset>
							</div>
						</div>
					</div>
				</div>
			</form>
			<?php

			break;
		
		case 'editcriterio':
			$urlCancelar = "admin.php?page=bloques-contenido&action=edit&id=".$_GET['id'].'&tab=criterios';
			if(isset($_GET['criterio']) && strlen($_GET['criterio']) > 0){
				$id = $_GET['criterio'];
			}else{
				$id = 0;
			}
			
			if($id > 0){
				$aValores = array($id);
				$qry = $wpdb->prepare("SELECT * FROM ".$tableCriterio." WHERE CRid = %d", $aValores);
				$row = $wpdb->get_row($qry);
				$sCodigo = $row->CRcodigo;
				$sContenido = $row->CRdescripcion;
			}else{
				$sCodigo = '';
				$sContenido = '';
			}
			?>
			<form method="post" id="auxiliares" name="auxiliares">
				<div class="row">
					<div class="col-xs-12 col-sm-6 panel-info-principal">
						<fieldset>
							<legend><i class="fa fa-info"></i><?php echo __('Criterios', 'traduccionrecursos');?></legend>
							<div class="row">
								<div class="col-xs-3 control-label text-left required">
									<span class="forms-label-top"><?php echo __('Código', 'traduccionrecursos');?></span>
									<input name="CRcodigo" value="<?php echo $sCodigo;?>" id="CRcodigo" class="form-control" type="text">
								</div>
								<div class="col-xs-12 control-label text-left required">
									<span class="forms-label-top"><?php echo __('Descripción', 'traduccionrecursos');?></span>
									<textarea name="CRdescripcion" id="CRdescripcion" class="form-control"><?php echo $sContenido;?></textarea>
								</div>
							</div>
						</fieldset>
					</div>
				</div>
			</form>
			<?php
		break;
		case 'editestandar':
			$urlCancelar = "admin.php?page=bloques-contenido&action=edit&id=".$_GET['id'].'&tab=estandares';
			if(isset($_GET['estandar']) && strlen($_GET['estandar']) > 0){
				$id = $_GET['estandar'];
			}else{
				$id = 0;
			}
			
			if($id > 0){
				$aValores 	= array($id);
				$qry 		= $wpdb->prepare("SELECT * FROM ".$tableEstandares." WHERE ETid = %d", $aValores);
				$row 		= $wpdb->get_row($qry);
				$sCodigo 	= $row->ETcodigo;
				$sContenido = $row->ETdescripcion;
			}else{
				$sCodigo = '';
				$sContenido = '';
			}
			$aCriterios = GetTableArray($wpdb->prefix."bne_criterios","CRid","CRcodigo","CRcodigo",NULL, " CRidbloquecontenido = ".$_GET['id']);
			?>
			<form method="post" id="auxiliares" name="auxiliares">
				<div class="row">
					<div class="col-xs-12 col-sm-6 panel-info-principal">
						<fieldset>
							<legend><i class="fa fa-info"></i><?php echo __('Estándares', 'traduccionrecursos');?></legend>
							<div class="row">
								<div class="col-xs-4 control-label text-left required">
									<span class="forms-label-top"><?php echo __('Criterios', 'traduccionrecursos');?></span>
									<?php echo GetSelectFromArray("ETidcriterio", $aCriterios, $row->ETidcriterio); ?>
								</div>
								<div class="col-xs-4 control-label text-left required">
									<span class="forms-label-top"><?php echo __('Código', 'traduccionrecursos');?></span>
									<input name="ETcodigo" value="<?php echo $sCodigo;?>" id="ETcodigo" class="form-control" type="text">
								</div>
								<div class="col-xs-12 control-label text-left required">
									<span class="forms-label-top"><?php echo __('Descripción', 'traduccionrecursos');?></span>
									<textarea name="ETdescripcion" id="ETdescripcion" class="form-control"><?php echo $sContenido;?></textarea>
								</div>
							</div>
						</fieldset>
					</div>
				</div>
			</form>
			<?php
		break;
		case 'list':
		default:
			$urlCancelar = 'admin.php?page=bloques-contenido';
			$aTabla = $wpdb->get_results("
				SELECT * FROM ".$table." 
				INNER JOIN ".$wpdb->prefix."bne_nivel ON BCidnivel = NIid
				INNER JOIN ".$wpdb->prefix."bne_asignatura ON BCidasignatura = ASid
				ORDER BY NItitulo,AStitulo,BCtexto, BCcontenido
				",	ARRAY_A);
			
				?>
				<div class="row">
					<div class="col-xs-12 col-sm-4">
						<input placeholder="<?php echo __('Buscar por Nivel', 'traduccionrecursos');?>" class="buscarActividadesVentana bnivel" type="text">
					</div>
					<div class="col-xs-12 col-sm-4">
						<input placeholder="<?php echo __('Buscar por Asignatura', 'traduccionrecursos');?>" class="buscarActividadesVentana basignatura" type="text">
					</div>
					<div class="col-xs-12 col-sm-4">
						<input placeholder="<?php echo __('Buscar por Bloque', 'traduccionrecursos');?>" class="buscarActividadesVentana bbloque" type="text">
					</div>
				</div>
				<table class="wp-list-table widefat striped">
					<thead>
						<tr>
							<th>
								<?php echo __('Nivel', 'traduccionrecursos');?>
							</th>
							<th>
								<?php echo __('Asignatura', 'traduccionrecursos');?>
							</th>
							<th>
								<?php echo __('Bloque', 'traduccionrecursos');?>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php 

						foreach ($aTabla as $rowDatos): array_map('htmlentities', $rowDatos); 
							?>
							<tr>
								<td>
									<?php echo  $rowDatos['NItitulo']; ?>
								</td>
								<td>
									<?php echo  $rowDatos['AStitulo']; ?>
								</td>
								<td><div class="contenido"><?php echo  $rowDatos['BCtexto']." ".$rowDatos['BCcontenido']; ?></div>
									<div class="row-actions">
										<span class="edit">
											<a href="?page=bloques-contenido&action=edit&id=<?php echo  $rowDatos['BCid']; ?>">
												Editar
											</a>
											 | 
										</span>
											<span class="delete">
												<a href="?page=bloques-contenido&action=delete&id=<?php echo  $rowDatos['BCid']; ?>">
												Eliminar
											</a>
										</span>
									</div>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php
			break;
	}

	?>
				
			</div>
		</div>
	</div>

	<script type="text/javascript">
		jQuery(function($) {
			var tabActiva = undefined;
			function Guardar(){
				if (camposRequeridos()) {
					$('#auxiliares').submit();
				} else {
					return false;
				}
				
			}

			function Salir(){
				location.href = "<?php echo $urlCancelar; ?>";
			}

			$(document).ready(function(){
				tabActiva = getUrlParameter("tab");
				console.log(tabActiva);
				$('#btnAceptar').unbind("click");
				$('#btnAceptar').click(function(e){
					Guardar();
				});

				$('#btnCancelar').unbind("click");
				$('#btnCancelar').click(function(e){
					Salir();
				});

				$(".buscarActividadesVentana.bnivel").keyup(function(){
					filtrar();
				});

				$(".buscarActividadesVentana.basignatura").keyup(function(){
					filtrar();
				});

				$(".buscarActividadesVentana.bbloque").keyup(function(){
					filtrar();
				});

				//camposRequeridos();
				$('.delete').unbind("click");
				$('.delete').click(function(event){
					var element = $(this).find('a');
					var texto 	= $(this).parent().parent().find('.contenido').html();
					event.stopPropagation();
					event.preventDefault();
					$.confirm({
						title: '<?php echo __('Eliminar', 'traduccionrecursos');?>',
						content: '<?php echo __('¿Seguro que desea eliminar el siguiente elemento?', 'traduccionrecursos');?>' +
						" <br />"+ texto,
						type: 'red',
						buttons: {
							aceptar: {
								text: '<?php echo __('Eliminar', 'traduccionrecursos');?>',
								btnClass: 'btn-danger',
								action: function(){
									location.href = element.attr("href");
								}
							},
							cerrar: {
								text: 'Cancelar',
								action: function(){
									return true;
								}
							}
						}
					});
				});

				$('#myTab a').unbind("click");
				$('#myTab a').click(function (e) {
					var tab = $(this);
					<?php
					if (0==$_GET['id']) {
					?>
						$('form#auxiliares').attr('action', function(i, value) {
							return value + "&backedit=1&tab=" + $(tab).attr('href').replace('#','');
						});
						//$('form#auxiliares').submit();
						Guardar();
						return false;
					<?php
					}else{
						?>

						if(tabActiva !== undefined){
							urlPath = location.href.replace(tabActiva,$(tab).attr('href').replace('#',''));
						}
		    			window.history.pushState({"html":null,"pageTitle":null},"", urlPath);
		 				tabActiva = getUrlParameter("tab");
		 				<?php
					}
					?>
				});

				if(tabActiva !== undefined){
					$('a[href="#' + tabActiva+'"]').click();
				}

			});

			function filtrar(){
					$texto1 = $(".buscarActividadesVentana.bnivel").val().toLowerCase();
					$texto2 = $(".buscarActividadesVentana.basignatura").val().toLowerCase();
					$texto3 = $(".buscarActividadesVentana.bbloque").val().toLowerCase();
					//console.log($texto1 + " 2 " + $texto2 + " 3 " + $texto3);
					$(".page-content").parent().parent().parent().find('table tbody tr').each(function(){
						//console.log("valor 1 " + $(this).find("td:nth-child(1)").text().toLowerCase().indexOf($texto1));
						//console.log("valor 2 " + $(this).find("td:nth-child(1) + td").text().toLowerCase().indexOf($texto2));
						//console.log("valor 3 " + $(this).find("td:nth-child(1) + td + td").text().toLowerCase().indexOf($texto3));
						if ((($(this).find("td:nth-child(1)").text().toLowerCase().indexOf($texto1) > -1 && $texto1.length > 0) || $texto1.length == 0) 
							&& (($(this).find("td:nth-child(1) + td").text().toLowerCase().indexOf($texto2) > -1 && $texto2.length > 0) || $texto2.length == 0) 
							&& (($(this).find("td:nth-child(1) + td + td").text().toLowerCase().indexOf($texto3) > -1 && $texto3.length > 0) || $texto3.length == 0)){
							$(this).fadeIn(100);
						} else {
							$(this).fadeOut(100);
						}
					});
			}

			function camposRequeridos() {

				var valid = true;
				$(".required input:not('.disabled'), .required textarea:not('.disabled'), .required select:not('.disabled')").each(function () {
					if ($(this).val() === "") {
						valid = false;
						$(this).addClass("has-error");
					} else {
						$(this).removeClass("has-error");
					}
					$(this).blur(function () {
						if ($(this).val() === "") {
							valid = false;
							$(this).addClass("has-error");
						} else {
							$(this).removeClass("has-error");
						}
					});
				});
				return valid;
			}

			function getUrlParameter(sParam) {
				var sPageURL = decodeURIComponent(window.location.search.substring(1)),
				sURLVariables = sPageURL.split('&'),
				sParameterName,
				i;

				for (i = 0; i < sURLVariables.length; i++) {
					sParameterName = sURLVariables[i].split('=');

					if (sParameterName[0] === sParam) {
						return sParameterName[1] === undefined ? true : sParameterName[1];
					}
				}
			}

		});
	</script>

<?php
}
