<?php
global $wpdb;

$table = $wpdb->prefix . 'bne_recurso';
if (!empty($_POST)) {

	$nIdUser = get_current_user_id();
	$data = array(
		'REtitulo' 					=> $_POST['REtitulo'],
		'REdescripcion'    			=> $_POST['REdescripcion'],
		//'REpublicacion'    			=> $_POST['REpublicacion'],
		//'RElugar'    				=> $_POST['RElugar'],
		//'REcoleccion'    			=> $_POST['REcoleccion'],
		'REtamanio'    				=> $_POST['REtamanio'],
		'REurlregistro'    			=> $_POST['REurlregistro'],
		'REurlacceso'    			=> $_POST['REurlacceso'],
		'REidnivelagragacion'   	=> $_POST['REidnivelagragacion'],
		'REidmetadatoscatalogo' 	=> $_POST['REidmetadatoscatalogo'],
		//'REmetadatosentrada'  	  => $_POST['REmetadatosentrada'],
		'REidesquemametadatos'  	=> $_POST['REidesquemametadatos'],
		'REcodigoidioma'    		=> $_POST['REcodigoidioma'],
		//'REcontenido'    			=> $_POST['REcontenido'],
		'REtema'    				=> $_POST['REtema'],
		'ID'    					=> 0,
		//'REinterespedagogico' 	  => $_POST['REinterespedagogico'],
		'REidcoleccion'    			=> $_POST['REidcoleccion'],
		'REpublicacionperiodo1' 	=> $_POST['REpublicacionperiodo1'],
		'REpublicacionperiodo2' 	=> $_POST['REpublicacionperiodo2'],
		'REidpais'    				=> $_POST['REidpais'],
		'REurlimagen'    			=> $_POST['REurlimagen'],
		'REidderecho'    			=> $_POST['REidderecho'],
		'REidrestriccion'    		=> $_POST['REidrestriccion'],
		'REidtipoacceso'    		=> $_POST['REidtipoacceso'],
		'REidcreador'    			=> $nIdUser,
		'REurlimagenaltacalidad'    => $_POST['REurlimagenaltacalidad'],
	);

	$format = array(
		'%s',
		'%s',
		//'%s',
		//'%s',
		//'%s',
		'%s',
		'%s',
		'%s',
		'%d',
		'%d',
		//'%s',
		'%d',
		'%s',
		//'%s',
		'%s',
		'%d',
		//'%s',
		'%d',
		'%d',
		'%d',
		'%d',
		'%s',
		'%d',
		'%d',
		'%d',
		'%d',
		'%s',
	);
	
	$nuevo = true;
	if(isset($_GET['id']) && strlen($_GET['id'])){
		$data['REid'] = $_GET['id'];
		$format[] = '%d';
		$nuevo = false;
	}

	$success = $wpdb->replace($table, $data, $format);
	
	/*echo $table;
	echo "<pre>";print_r($data);echo "</pre>";
	echo "<pre>";print_r($format);echo "</pre>";
	echo $success;
	exit();*/
	

	if(!isset($_GET['id']) || strlen($_GET['id']) == 0){
		$_GET['id'] = $wpdb->insert_id;
	}

	$table = $wpdb->prefix . 'bne_recursonivel';
	$wpdb->delete($table, array('BNrecurso' => $_GET['id'] ), array('%d'));
	if($_POST['nivel']){
		foreach ($_POST['nivel'] as $key => $value) {
			$data = array(
				'BNrecurso'	=> $_GET['id'],
				'BNnivel'	=> $key,
			);
			$format = array(
				'%d',
				'%d',
			);
			$wpdb->insert($table, $data, $format);
		}
	}

	$table = $wpdb->prefix . 'bne_recursoasignatura';
	$wpdb->delete($table, array('RArecurso' => $_GET['id'] ), array('%d'));
	if($_POST['asig']){
		foreach ($_POST['asig'] as $nivel => $niveldatos) {
			foreach ($niveldatos as $asig => $asigdatos) {
				$data = array(
					'RArecurso'		=> $_GET['id'],
					'REnivel'		=> $nivel,
					'RAasignatura'	=> $asig,
					'RAobservacion'	=> '',
				);
				$format = array(
					'%d',
					'%d',
					'%d',
					'%s',
				);
				$wpdb->insert($table, $data, $format);
			}
		}
	}

	$table = $wpdb->prefix . 'bne_recursobloquecontenido';
	$wpdb->delete($table, array('RBrecurso' => $_GET['id'] ), array('%d'));
	if($_POST['bloque']){
		foreach ($_POST['bloque'] as $nivel => $niveldatos) {
			foreach ($niveldatos as $asig => $asigdatos) {
				foreach ($asigdatos as $bloque => $bloquedatos) {
					$data = array(
						'RBrecurso'			=> $_GET['id'],
						'RBbloquecontenido'	=> $bloque,
					);
					$format = array(
						'%d',
						'%d',
					);
					$wpdb->insert($table, $data, $format);					
				}
			}
		}
	}

	$table = $wpdb->prefix . 'bne_recursoasignatura';
	if($_POST['RAobservacion']){
		foreach ($_POST['RAobservacion'] as $key => $value) {
			foreach ($value as $key2 => $value2) {
				$data = array(
					'RAobservacion'	=> $value2,
				);
				$format = array(
					'%s'
				);
				$where = array(
					'RArecurso' => $_GET['id'],
					'REnivel' => $key,
					'RAasignatura' => $key2,
				);
				$formatWhere = array(
					'%d',
					'%d',
					'%d',
				);
				$wpdb->update($table, $data, $where, $format, $formatWhere);
			}
		}
	}


	$table = $wpdb->prefix . 'bne_recursotiporecurso';
	$wpdb->delete($table, array('RRrecurso' => $_GET['id'] ), array('%d'));
	if($_POST['TipoRecurso']){
		foreach ($_POST['TipoRecurso'] as $key => $value) {
			$data = array(
				'RRrecurso'	=> $_GET['id'],
				'RRtiporecurso'	=> $key,
			);
			$format = array(
				'%d',
				'%d',
			);
			$wpdb->insert($table, $data, $format);
		}
	}

	$table = $wpdb->prefix . 'bne_recursoestandares';
	$wpdb->delete($table, array('REidrecurso' => $_GET['id'] ), array('%d'));
	if($_POST['Estandar']){
		foreach ($_POST['Estandar'] as $key => $value) {
			$data = array(
				'REidrecurso'	=> $_GET['id'],
				'REidestandar'	=> $key,
			);
			$format = array(
				'%d',
				'%d',
			);
			$wpdb->insert($table, $data, $format);
		}
	}

	// si insertamos autores desde obtener datos
	if($_GET['id'] > 0){
		if($_POST['registroAutores']){
			$registroAutores = json_decode(stripslashes($_POST['registroAutores']));
			if(is_array($registroAutores)){
				$table = $wpdb->prefix . 'bne_recursoautor';
				$wpdb->delete($table, array('RArecurso' => $_GET['id'] ), array('%d'));			
				foreach ($registroAutores as $key => $value) {
					$data = array(
						'RArecurso'	=> $_GET['id'],
						'RAautor'	=> $value->id,
					);
					$format = array(
						'%d',
						'%d',
					);
					$wpdb->insert($table, $data, $format);
				}			
			}
		}
	}

	//Si es nuevo e insertamos un autor con el botón y es nuevo

	if($nuevo && isset($_POST['Autores']) && $_POST['Autores'] > 0){
		$table = $wpdb->prefix . 'bne_recursoautor';
		$data = array(
			'RArecurso'	=> $_GET['id'],
			'RAautor'	=> $_POST['Autores'],
		);
		$format = array(
			'%d',
			'%d',
		);
		$wpdb->insert($table, $data, $format);
	}

	if($success){
		//echo 'data has been save' ; 
	}

	if($_GET['back'] == 1){
		$urlBack = "admin.php?page=recursos-edit&id=".$_GET['id'];
		if(isset($_GET['tab'])){
			$urlBack .= "&tab=".$_GET['tab'];
		}
		Redirect($urlBack);
	}else{
		Redirect("admin.php?page=recursos-main");
	}
	

} else {

	$urlFormulario = "admin.php?page=recursos-edit";

	if(isset($_GET['id']) && strlen($_GET['id']) > 0){
		$id = $_GET['id'];
	}else{
		$id = 0;
	}
	
	$aAutores 		= GetTableArray($wpdb->prefix."bne_autores","AUid","AUnombre","AUnombre");
	$aColecciones 	= GetTableArray($wpdb->prefix."bne_colecciones","COid","COtitulo","COtitulo");
	$aPaises 		= GetTableArray($wpdb->prefix."bne_paises","PAid","PAtitulo","PAtitulo");
	$aDerechos 		= GetTableArray($wpdb->prefix."bne_derechos","DEid","DEtitulo","DEtitulo");
	$aRestricion 	= GetTableArray($wpdb->prefix."bne_restriccion","RSid","RStitulo","RStitulo");
	$aTipoAcceso 	= GetTableArray($wpdb->prefix."bne_tipoacceso","TAid","TAtitulo","TAtitulo");

	$aAgregacion 	= GetTableArray($wpdb->prefix."bne_nivelagregacion","NAid","NAtitulo","NAtitulo");
	$aCatalogo	 	= GetTableArray($wpdb->prefix."bne_metadatoscatalogo","MCid","MCtitulo","MCtitulo");
	$aIdioma	 	= GetTableArray($wpdb->prefix."bne_idioma","IDcodigo","IDtitulo","IDtitulo");
	$aEsquema	 	= GetTableArray($wpdb->prefix."bne_esquemametadatos","ESid","EStitulo","EStitulo");

	$nivelListTable = new Nivel_List_Table($id);
	$nivelListTable->prepare_items();

	$asignaturaListTable = new Asignatura_List_Table($id);
	$asignaturaListTable->prepare_items();

	$bloqueListTable = new Bloque_List_Table($id);
	$bloqueListTable->prepare_items();

	$criteriosListTable = new Criterios_List_Table($id);
	$criteriosListTable->prepare_items();

	if($id > 0){
		$aValores = array($id);
		$qry = $wpdb->prepare("SELECT * FROM ".$table." WHERE REid = %d", $aValores);
		$row = $wpdb->get_row($qry);
		//print_r($row);
		$urlFormulario = "admin.php?page=recursos-edit&id=".$id;
	}

	//Catalogación Curricular 2

	$sHtmlTablaCurricular2 = '';
	$table_name = $wpdb->prefix . 'bne_asignatura 
	INNER JOIN '.$wpdb->prefix . 'bne_asignaturanivel 
	ON ASid = ANasignatura 
	INNER JOIN '.$wpdb->prefix . 'bne_nivel 
	ON ANnivel = NIid 
	INNER JOIN '.$wpdb->prefix . 'bne_recursoasignatura 
	ON REnivel = NIid AND RAasignatura = ASid 
	';
	$sTablaCurricular2 = $wpdb->get_results("SELECT RAid,NItitulo, AStitulo, RAobservacion,REnivel,RAasignatura FROM $table_name WHERE RArecurso = $id ORDER BY NItitulo,AStitulo",	ARRAY_A);

	//TipoRecurso

	$table_name = $wpdb->prefix . 'bne_tiporecursos';
	$table_name2 = $wpdb->prefix . 'bne_recursotiporecurso';
	$sTablaRecursos = $wpdb->get_results("SELECT *,(SELECT RRid FROM $table_name2 WHERE RRtiporecurso = TRid AND RRrecurso = $id) AS RRid FROM $table_name ORDER BY TRtitulo",	ARRAY_A);

	//Autores
	$table_name = $wpdb->prefix . 'bne_autores';
	$table_name2 = $wpdb->prefix . 'bne_recursoautor';
	$sTablaAutores = $wpdb->get_results("SELECT * FROM $table_name INNER JOIN $table_name2 ON RAautor = AUid WHERE RArecurso = $id ORDER BY AUnombre",	ARRAY_A);

	//Trazabilidad
	$table_name = $wpdb->prefix . 'bne_recurso';
	$table_name2 = $wpdb->prefix . 'bne_recursohijos';
	$table_name3 = $wpdb->prefix . 'bne_colecciones';
	$aTablaTrazabilidad = $wpdb->get_results("
		SELECT * FROM $table_name 
		INNER JOIN $table_name2 ON RRrecursohijo = REid 
		LEFT OUTER JOIN $table_name3 ON REcoleccion = COid 
		WHERE RRrecurso = $id ORDER BY REtitulo
	", ARRAY_A
	);

	?>
	<form method="post" id="recursos" name="recursos" action="<?php echo $urlFormulario;?>">
		<div class="page-content">
			<div class="row">
				<div class="col-md-12">

					<div class="widget-box transparent invoice-box">
						<div class="widget-header">
							<h4 class="widget-title lighter">
								<span><?php echo __('Recursos', 'traduccionrecursos');?></span>
							</h4>
							<div class="widget-toolbar">
								<a onclick="javascript:void(0);" id="btnCancelar" tabindex="7" title="<?php echo __('Salir', 'traduccionrecursos');?>" class="fa fa-times ico-boton30" href="javascript:{}"><span><?php echo __('Salir', 'traduccionrecursos');?></span></a>
							</div>
							<div class="widget-toolbar">
								<a onclick="javascript:void(0);" id="btnAceptar" tabindex="6" title="<?php echo __('Guardar', 'traduccionrecursos');?>" class="fa fa-save ico-boton30"  href="javascript:{}"><span><?php echo __('Guardar', 'traduccionrecursos');?></span></a>
							</div>

						</div>
					</div>

					<ul class="nav nav-tabs">
						<li class="active"><a data-toggle="tab" href="#datos"><?php echo __('Datos');?></a></li>
						<li><a data-toggle="tab" href="#curriculo1"><?php echo __('Catalogación curricular');?></a></li>
						<li><a data-toggle="tab" href="#curriculo2"><?php echo __('Catalogación curricular II');?></a></li>
						<li><a data-toggle="tab" href="#etiquetado"><?php echo __('Etiquetado estandar');?></a></li>
						<li><a data-toggle="tab" href="#trazabilidad"><?php echo __('Trazabilidad');?></a></li>
						<li><a data-toggle="tab" href="#criterios"><?php echo __('Criterios - Estándares');?></a></li>

					</ul>

					<div class="tab-content">
						<div id="datos" class="tab-pane fade in active">
							<!--<h3><?php echo __('Datos');?></h3>-->
							<div class="row">
								<div class="col-xs-12 col-sm-6   panel-info-principal">
									<fieldset>
										<legend><i class="fa fa-info"></i>DATOS PRINCIPALES</legend>

										<div class="row">

											<div class="col-xs-12  control-label text-left required">
												<span class="forms-label-top">(*) Título</span>
												<input name="REtitulo" value="<?php echo $row->REtitulo;?>" maxlength="2000" id="REtitulo" class="form-control" type="text">
											</div>
										</div>
										<div class="row">

											<div class="col-xs-12  control-label text-left">
												<span class="forms-label-top">Descripción</span>
												<textarea name="REdescripcion" rows="10" cols="20" id="REdescripcion" class="form-control"><?php echo $row->REdescripcion;?></textarea>
											</div>
										</div>
										<div class="row">
											<div class="col-xs-12 control-label text-left">
												<span class="forms-label-top">Descripción física</span>
												<input name="REtamanio" maxlength="300" id="REtamanio" class="form-control" type="text" value="<?php echo $row->REtamanio;?>">
											</div>
										</div>
									</fieldset>
								</div>

								<div class="col-xs-12 col-sm-6   fieldset-theme">
									<input type="hidden" name="registroAutores" id="registroAutores" />
									<fieldset>
										<legend><i class="fa fa-list"></i>OBTENER DATOS</legend>
										<div class="row">
											<div class="col-xs-12 col-sm-8 control-label text-left">
												<a id="REurlregistroHyperLink" target="_blank" href="<?php echo $row->REurlregistro;?>">URL registro de la BDH</a>
												<input name="REurlregistro" maxlength="200" id="REurlregistro" class="  form-control" type="text"  value="<?php echo $row->REurlregistro;?>">
											</div>
											<div class="col-xs-12 col-sm-4 control-label text-left">
												<div class="space-10"></div>
												<a id="ObtenerLinkButton" tabindex="5" title="Obtener datos" class="btn btn-sm btn-xs btn-info"><i class="fa fa-bullseye  red "></i> &nbsp;&nbsp;<span>Obtener datos</span></a>
											</div>
										</div>
										<div class="space-10"></div>
									</fieldset>
									<fieldset>
										<legend><i class="fa fa-list"></i>OTROS DATOS</legend>
										<div class="row">
											<div class="col-xs-12 col-sm-3 control-label text-left">
												<span class="forms-label-top">Publicación 1 (año)</span>
												<input name="REpublicacionperiodo1" value="<?php echo $row->REpublicacionperiodo1;?>" maxlength="10" id="REpublicacionperiodo1" class="form-control center" onkeypress="return soloNumeros(event)" type="text">
											</div>
											<div class="col-xs-12 col-sm-3 control-label text-left">
												<span class="forms-label-top">Publicación 2 (año)</span>
												<input name="REpublicacionperiodo2" value="<?php echo $row->REpublicacionperiodo2;?>" maxlength="10" id="REpublicacionperiodo2" class="form-control center" onkeypress="return soloNumeros(event)" type="text">
											</div>

											<div class="col-xs-12 col-sm-6 control-label text-left">
												<span class="forms-label-top">Tipo de acceso</span>
												<?php echo GetSelectFromArray("REidtipoacceso",$aTipoAcceso,$row->REidtipoacceso); ?>
											</div>

										</div>

										<div class=" row">

											<div class="col-xs-12 col-sm-6 control-label text-left">
												<span class="forms-label-top">Colección</span>
												<?php echo GetSelectFromArray("REidcoleccion",$aColecciones,$row->REidcoleccion); ?>
											</div>

											<div class="col-xs-12 col-sm-6 control-label text-left">
												<span class="forms-label-top">País</span>
												<?php echo GetSelectFromArray("REidpais",$aPaises,$row->REidpais); ?>
											</div>
										</div>

										<div class=" row">

											<div class="col-xs-12 col-sm-6 control-label text-left">
												<span class="forms-label-top">Derechos de autor</span>
												<?php echo GetSelectFromArray("REidderecho",$aDerechos,$row->REidderecho); ?>
											</div>
											<div class="col-xs-12 col-sm-6 control-label text-left">
												<span class="forms-label-top">Restricción</span>
												<?php echo GetSelectFromArray("REidrestriccion",$aRestricion,$row->REidrestriccion); ?>
											</div>
										</div>
										<div class="row">
											<div class="col-xs-12 control-label text-left">
												<a id="REurlaccesoHyperLink" target="_blank" href="<?php echo $row->REurlacceso;?>">URL aceso al documento</a>
												<input name="REurlacceso" value="<?php echo $row->REurlacceso;?>" maxlength="200" id="REurlacceso" class="form-control" type="text">
											</div>
										</div>
									</fieldset>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12 col-sm-6 fieldset-theme">
									<fieldset>
										<legend><i class="fa fa-pencil-alt"></i>AUTORES</legend>
										<div class="row">
											<div class="col-xs-12 col-sm-8 control-label text-left">
												<span class="forms-label-top">Autor</span>
												<?php echo GetSelectFromArray("Autores", $aAutores); ?>
											</div>
											<div class="col-xs-12 col-sm-4 center control-label text-left">
												<div class="space-10"></div>
												<a id="AutorInsertarLinkButton" class="btn btn-sm btn-success">Insertar autor</a>
											</div>
										</div>
										<div class="row">
											<div class="col-xs-12 control-label text-left">

												<table class="wp-list-table widefat striped autortable">
													<thead>
														<tr>
															<th></th>
															<th><?php echo __('Autor');?></th>
														</tr>
													</thead>
													<tbody>
														<?php foreach ($sTablaAutores as $rowDatos): array_map('htmlentities', $rowDatos); ?>
															<tr>
																<td>
																	<a id="eliminarAutorRecurso" data-id="<?php echo  $rowDatos['AUid']; ?>" title="<?php echo __('Eliminar');?>" class="fa fa-trash-alt bigger-180" href="javascript:{}"></a>
																</td>
																<td><?php echo  $rowDatos['AUnombre']; ?></td>
															</tr>
														<?php endforeach; ?>
													</tbody>
												</table>

											</div>
										</div>
									</fieldset></div>

									<div class="col-xs-12 col-sm-6 fieldset-theme">
										<fieldset>
											<legend><i class="fa fa-image  "></i>IMAGEN</legend>
											<div class="row">
												<div class="col-xs-12 control-label text-left">
													<span id="Label5" class="forms-label-top">URL imagen</span>
													<input name="REurlimagen" value="<?php echo $row->REurlimagen;?>" maxlength="500" id="REurlimagen" class="form-control" type="text">
												</div>
											</div>
											<div id="ImagenPanel">
												<div class="row">
													<div class="col-xs-12 col-sm-10 control-label text-left">
														<span id="ImagenLabel" class="forms-label-top">Imagen</span>
														<img id="REurlimagenImage" class="imagen-miniatura" src="<?php echo $row->REurlimagen;?>" style="border-width:0px;">
													</div>
												</div>
											</div>
											<br />
											<div class="row">
												<div class="col-xs-12 control-label text-left">
													<span id="Label5" class="forms-label-top">URL imagen de alta calidad</span>
													<input name="REurlimagenaltacalidad" value="<?php echo $row->REurlimagenaltacalidad;?>" maxlength="500" id="REurlimagenaltacalidad" class="form-control" type="text">
												</div>
											</div>
											<br />
											<div id="ImagenPanelAlta">
												<div class="row">
													<div class="col-xs-12 col-sm-10 control-label text-left">
														<span id="ImagenLabel" class="forms-label-top">Imagen</span>
														<img id="REurlimagenaltacalidadImage" class="imagen-altacalidad" src="<?php echo $row->REurlimagenaltacalidad;?>" style="border-width:0px;">
													</div>
												</div>
											</div>	
										</fieldset>
									</div>
								</div>
							</div>
						<div id="curriculo1" class="tab-pane fade">
							<div class="row">
								<div class="col-xs-12 col-sm-3 fieldset-theme">
									<fieldset>
										<legend>
											<i class="fa fa-list-ol"></i> 1. <?php echo __('Nivel');?>
										</legend>
										<input placeholder="<?php echo __('Buscar');?>" class="buscarActividadesVentana bnivel" type="text">
										<?php $nivelListTable->display() ?>
									</fieldset>
								</div>
								<div class="col-xs-12 col-sm-4 fieldset-theme">
									<fieldset>
										<legend>
											<i class="fa fa-list-ol"></i> 2. <?php echo __('Asignatura');?>
										</legend>
										<input placeholder="<?php echo __('Buscar');?>" class="buscarActividadesVentana basig" type="text">
										<?php $asignaturaListTable->display()?>
									</fieldset>
								</div>
								<div class="col-xs-12 col-sm-5 fieldset-theme">
									<fieldset>
										<legend>
											<i class="fa fa-list-ol"></i> 3. <?php echo __('Bloque de Contenido');?>
										</legend>
										<input placeholder="<?php echo __('Buscar');?>" class="buscarActividadesVentana bbloque" type="text">
										<?php $bloqueListTable->display() ?>
									</fieldset>
								</div>
							</div>
						</div>
						<div id="curriculo2" class="tab-pane fade">
							<div class="row">
								<div class="col-xs-12 col-sm-8   fieldset-theme">
									<fieldset>
										<legend>
											<i class="fa fa-list-ol"></i>
											<?php echo __('Contenido Didáctico');?>
										</legend>
										<table class="wp-list-table widefat fixed striped curricular2">
											<thead>
												<tr>
													<th></th>
													<th><?php echo __('Nivel');?></th>
													<th><?php echo __('Asignatura');?></th>
													<th><?php echo __('Contenido Didáctico');?></th>
												</tr>
											</thead>
											<tbody>
												<?php foreach ($sTablaCurricular2 as $rowDatos): array_map('htmlentities', $rowDatos); ?>
													<tr>
														<td>
															<a id="editarRAobservacion" data-id="<?php echo  $rowDatos['RAid']; ?>" title="Editar" class="fa fa-pencil-alt bigger-180" href="javascript:{}"></a>
														</td>
														<td><?php echo  $rowDatos['NItitulo']; ?></td>
														<td><?php echo  $rowDatos['AStitulo']; ?></td>
														<td id="RAobservacionContenido"><?php echo  $rowDatos['RAobservacion']; ?></td>
													</tr>
												<?php endforeach; ?>
											</tbody>
										</table>
										
										<div class="panelContenidoDidactico">
											<?php foreach ($sTablaCurricular2 as $rowDatos): array_map('htmlentities', $rowDatos); ?>
												<?php echo '<textarea class="editortinymce" id="RAobservacion['.$rowDatos['RAid'].']" name="RAobservacion['.$rowDatos['REnivel'].']['.$rowDatos['RAasignatura'].']">'.$rowDatos['RAobservacion'].'</textarea>'; ?>
											<?php endforeach; ?>
											<div class="space-10"></div>
											<a class="btn btn-success "><?php echo __('Aceptar');?></a>
											<a class="btn btn-danger "><?php echo __('Cancelar');?></a>
										</div>
									</fieldset>
								</div>
							</div>
						</div>
						<div id="etiquetado" class="tab-pane fade">
							<div class="row">
								<div class="col-xs-12 col-sm-6   fieldset-theme">
									<fieldset>
										<legend><i class="fa fa-list"></i><?php echo __('CATALOGACIÓN ESTANDAR');?></legend>
										<div class="row">
											<div class="col-xs-12 col-sm-6 control-label text-left">
												<span class="forms-label-top"><?php echo __('Nivel agregación');?></span>
												<?php echo GetSelectFromArray("REidnivelagragacion", $aAgregacion, $row->REidnivelagragacion, false); ?>
											</div>
											<div class="col-xs-12 col-sm-6 control-label text-left">
												<span class="forms-label-top"><?php echo __('Metadatos: Catálogo');?></span>
												<?php echo GetSelectFromArray("REidmetadatoscatalogo", $aCatalogo, $row->REidmetadatoscatalogo, false); ?>
											</div>
										</div>
										<div class="row">
											<div class="col-xs-12 col-sm-6 control-label text-left">
												<span class="forms-label-top"><?php echo __('Esquema de Metadatos');?></span>
												<?php echo GetSelectFromArray("REidesquemametadatos", $aEsquema, $row->REidesquemametadatos, false); ?>
											</div>
											<div class="col-xs-12 col-sm-6 control-label text-left">
												<span class="forms-label-top"><?php echo __('Idioma');?></span>
												<?php echo GetSelectFromArray("REcodigoidioma", $aIdioma, $row->REcodigoidioma, false); ?>
											</div>
										</div>
										<div class="row">

											<div class="col-xs-12  control-label text-left">
												<span class="forms-label-top"><?php echo __('Tema');?></span>
												<textarea name="REtema" rows="4" cols="20" id="REtema" class="  form-control"><?php echo $row->REtema;?></textarea>
											</div>
										</div>

										<div class="row">

											<div class="col-xs-12 col-sm-3  control-label text-left">
												<span class="forms-label-top">Metadatos: Entrada</span>
												<input name="REid" value="<?php echo $id;?>" readonly="readonly" id="REid" class="form-control" type="text">
											</div>
										</div>
									</fieldset>
								</div>
								<div class="col-xs-12 col-sm-6   fieldset-theme">
									<fieldset>
										<legend><i class="fa fa-list-alt "></i>TIPOS DE RECURSOS</legend>
										<div class="row">

											<div class="col-xs-12 control-label  ">
												<div>
													<table class="wp-list-table widefat fixed striped">
														<thead>
															<tr>
																<th></th>
																<th><?php echo __('Título');?></th>
															</tr>
														</thead>
														<tbody>
															<?php 

															foreach ($sTablaRecursos as $rowDatos): array_map('htmlentities', $rowDatos); 
																$checked = "";
																$class = "fa-circle";
																if($rowDatos['RRid']){
																	$checked = "checked";
																	$class = "fa-check-circle";
																}
																?>
																<tr>
																	<th class="check-column">
																		<a id="btnMarcarNivel" title="Asignar" class="far <?php echo $class; ?> grey bigger-180 marcarNivel" href="javascript:{}" style="display:inline-block;width:100%;"></a>
																		<input name="TipoRecurso[<?php echo  $rowDatos['TRid']; ?>]" <?php echo  $checked; ?> type="checkbox" value="<?php echo  $rowDatos['TRid']; ?>" class="hidden"/>
																	</th>
																	<td><?php echo  $rowDatos['TRtitulo']; ?></td>
																</tr>
															<?php endforeach; ?>
														</tbody>
													</table>
												</div>
											</div>
										</div>

									</fieldset>
								</div>
							</div>
						</div>
						<div id="trazabilidad" class="tab-pane fade">
							<table class="wp-list-table widefat striped trazabilidadtable">
								<thead>
									<tr>
										<th>
											<a id="nuevoTrazabilidad" title="<?php echo __('Nuevo');?>" class="fa fa-plus bigger-180" href="javascript:{}"></a>
										</th>
										<th><?php echo __('Recursos');?></th>
										<th><?php echo __('Fecha Publicación 1');?></th>
										<th><?php echo __('Colección');?></th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($aTablaTrazabilidad as $rowDatos): array_map('htmlentities', $rowDatos); ?>
										<tr>
											<td>
												<a id="eliminarTrazabilidad" data-id="<?php echo  $rowDatos['REid']; ?>" title="<?php echo __('Eliminar');?>" class="fa fa-trash-alt bigger-180" href="javascript:{}"></a>
											</td>
											<td><?php echo  $rowDatos['REtitulo']; ?></td>
											<td><?php echo  $rowDatos['REpublicacionperiodo1']; ?></td>
											<td><?php echo  $rowDatos['COtitulo']; ?></td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
							<div class="recursosTotalDiv">
								<div class="widget-header">
									<h4 class="widget-title lighter">
										<span><?php echo __('Recursos (Marque los recursos a añadir y pulse aceptar)');?></span>
									</h4>
									<div class="widget-toolbar">
										<a id="btnCancelarTrazabilidad" tabindex="7" title="Cancelar" class="fa fa-times ico-boton30 cancelarTrazabilidad" href="javascript:{}"><span><?php echo __('Cancelar');?></span></a>
									</div>
									<div class="widget-toolbar">
										<a id="btnAceptarTrazabilidad" tabindex="6" title="Aceptar" class="fa fa-check ico-boton30 aceptarTrazabilidad" href="javascript:{}"><span><?php echo __('Aceptar');?></span></a>
									</div>

								</div>
								<table id="recursosTotal" style="width:100%">
									<thead>
										<tr>
											<th></th>
											<th><?php echo __('Recursos');?></th>
											<th><?php echo __('Fecha Publicación 1');?></th>
											<th><?php echo __('Colección');?></th>
										</tr>
									</thead>
								</table>
							</div>
						</div>
						<div id="criterios" class="tab-pane fade">
							<?php $criteriosListTable->display() ?>
						</div>

					</div>
				</div>
			</div>
		</div>
	</form>
	<script type="text/javascript">
		jQuery(function($) {
			var checkArray = [];

			var refresco = false;

			function Guardar(){
				if(camposRequeridos()){
					$('#recursos').submit();
				}
			}

			function Salir(){
				location.href = "admin.php?page=recursos-main";
			}

			var contenidodidacticoID = 0;

			$(document).ready(function(){

				$('#btnAceptar').unbind("click");
				$('#btnAceptar').click(function(e){
					Guardar();
				});

				$('#btnCancelar').unbind("click");
				$('#btnCancelar').click(function(e){
					Salir();
				});

				$('.marcarNivel').click(function(){
					asignarCheck(this,'nivel');
					estadoAsig();
					estadoBloque();
				});

				$('.marcarAsig').click(function(){
					refresco = true;
					asignarCheck(this,'asig');
					estadoBloque();
				});

				$('.marcarBloque').click(function(){
					refresco = true;
					asignarCheck(this,'bloque');
				});

				$('.marcarEstandar').click(function(){
					asignarCheck(this,'estandar');
				});

				estadoNivel();
				estadoAsig();
				estadoBloque();

				$(".buscarActividadesVentana.bnivel").keyup(function(){
					$texto = $(this).val().toLowerCase();
					$(this).parent().find('table tbody tr:not(.hide)').each(function(){
						//console.log("1 - " + $texto);
						//console.log("2 - " + $(this).find("td").text().toLowerCase().indexOf($texto));
						if ($(this).find("td").text().toLowerCase().indexOf($texto) == -1){
							$(this).fadeOut(100);
						} else {
							$(this).fadeIn(100);
						}
					});
				});

				$(".buscarActividadesVentana.basig").keyup(function(){
					$texto = $(this).val().toLowerCase();
					$(this).parent().find('table tbody tr:not(.hide)').each(function(){
						if ($(this).find("td:nth-child(2)").text().toLowerCase().indexOf($texto) == -1
							&& $(this).find("td:nth-child(2) + td").text().toLowerCase().indexOf($texto) == -1){
							$(this).fadeOut(100);
						} else {
							$(this).fadeIn(100);
						}
					});
				});

				$(".buscarActividadesVentana.bbloque").keyup(function(){
					$texto = $(this).val().toLowerCase();
					$(this).parent().find('table tbody tr:not(.hide)').each(function(){
						if ($(this).find("td:nth-child(2)").text().toLowerCase().indexOf($texto) == -1
							&& $(this).find("td:nth-child(2) + td").text().toLowerCase().indexOf($texto) == -1
							&& $(this).find("td:nth-child(2) + td + td").text().toLowerCase().indexOf($texto) == -1){
							$(this).fadeOut(100);
						} else {
							$(this).fadeIn(100);
						}
					});
				});

				$('.fieldset-theme .tablenav,.fieldset-theme .toggle-row').remove();

				asignarEditor('textarea');

				$('.nav.nav-tabs a').click(function(e){

					if(getParameterByName("id") == null || refresco){

						if(refresco && getParameterByName("id") == null || getParameterByName("id") == null){
							$('#recursos').attr("action","admin.php?page=recursos-edit&back=1&tab=" + $(this).attr('href').replace("#",""));
						}else{
							$('#recursos').attr("action","admin.php?page=recursos-edit&id=" + getParameterByName("id") + "&back=1&tab=" + $(this).attr('href').replace("#",""));
						}

						Guardar();
						e.preventDefault();
						e.stopPropagation();
					}

					if($(this).attr('href') == "#curriculo2"){
						$('.panelContenidoDidactico .mce-tinymce').hide();
						$('table.curricular2').show();
						$('.panelContenidoDidactico').hide();
					}else if($(this).attr('href') == "#trazabilidad"){
						$('.trazabilidadtable').show();
						$('.recursosTotalDiv').hide();
					}
				});

				$('.panelContenidoDidactico .btn-success').click(function(){

					var taObject = $('.panelContenidoDidactico textarea#RAobservacion\\['+contenidodidacticoID+'\\]');
					var tinyObject = $(taObject).prev();
					var texto = tinyMCE.get($(taObject).attr("id")).getContent();
					$('.curricular2 .fa-pencil-alt[data-id="'+contenidodidacticoID+'"]').parent().parent().find('#RAobservacionContenido').html(texto);
					$('table.curricular2').show();
					$('.panelContenidoDidactico').hide();
				});
				$('.panelContenidoDidactico .btn-danger').click(function(){

					var texto = $('.curricular2 .fa-pencil-alt[data-id="'+contenidodidacticoID+'"]').parent().parent().find('#RAobservacionContenido').html();
					var taObject = $('.panelContenidoDidactico textarea#RAobservacion\\['+contenidodidacticoID+'\\]');
					tinyMCE.get($(taObject).attr("id")).setContent(texto);

					$('table.curricular2').show();
					$('.panelContenidoDidactico').hide();
				});

				$('.curricular2 .fa-pencil-alt').click(function(){
					var id = $(this).data("id");
					contenidodidacticoID = id;
					$('.panelContenidoDidactico textarea').prev().hide();
					$('.panelContenidoDidactico textarea#RAobservacion\\['+id+'\\]').prev().show();
					$('table.curricular2').hide();
					$('.panelContenidoDidactico').show();
				});

				$('#ObtenerLinkButton').click(function(){
					obtenerDatos();
				});

				$('#AutorInsertarLinkButton').click(function(e){
					insertarAutor(e);
				});

				$('.autortable a.fa-trash-alt').click(function(evento){
					var valor = $(this).data('id');
					evento.stopPropagation();
					evento.preventDefault();
					$.confirm({
						title: '<?php echo __('Eliminar');?>',
						content: '<?php echo __('¿Seguro que desea eliminar el siguiente elemento?');?>',
						type: 'red',
						buttons: {
							aceptar: {
								text: '<?php echo __('Eliminar');?>',
								btnClass: 'btn-danger',
								action: function(){
									eliminarAutor(evento, valor);
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

				var tablaTrazabilidad = undefined;

				$('.trazabilidadtable a.fa-plus').click(function(evento){

					checkArray = undefined;
					checkArray = [];

					$('#btnAceptarTrazabilidad').unbind("click");
					$('#btnAceptarTrazabilidad').click(function(e){
						insertarTrazabilidad(e);
						$('.recursosTotalDiv').hide();
						$('.trazabilidadtable').show();
					});

					$('#btnCancelarTrazabilidad').unbind("click");
					$('#btnCancelarTrazabilidad').click(function(){
						$('.recursosTotalDiv').hide();
						$('.trazabilidadtable').show();
					});

					$('.recursosTotalDiv').show();
					$('.trazabilidadtable').hide();
					
					var locale = "<?php echo get_locale(); ?>";
					var language = "";

					switch(locale) {
						case "ca_ES":
							language = "<?php echo RKR_PLUGIN_PATH; ?>/theme/js/Catalan.json";
						break;
						default:
							language ="<?php echo RKR_PLUGIN_PATH; ?>/theme/js/Spanish.json";
					}

					var data = {
						'action': 'getRecursosTrazabilidad',
					};
					
					if(tablaTrazabilidad != undefined){
						tablaTrazabilidad.destroy();
					}
					tablaTrazabilidad = $('#recursosTotal').DataTable({
						"language": {
							"url": language
						},
						"pageLength": 25,
						"bLengthChange": false,
						"ajax" :{
							type: "POST",
							url : "<?php echo admin_url('admin-ajax.php'); ?>",
							data : data
						}
					}).on('draw', function(){ 
 						$(".checkRecurso").change(function(){
 							if($(this).is(":checked")){
								checkArray.push($(this).val());
 							}else{
 								var index = checkArray.indexOf($(this).val());
 								checkArray.splice(index, 1);
 							}
 							
 						});
 					});
				});

				eventosTrazabilidad();
				
				if(getParameterByName("tab") != null && getParameterByName("tab") != ''){
					$('.nav.nav-tabs a[href="#' + getParameterByName("tab") + '"]').click();
				}

			});

			function eventosTrazabilidad(){
				$('.trazabilidadtable a.fa-trash-alt').click(function(evento){
					var valor = $(this).data('id');
					evento.stopPropagation();
					evento.preventDefault();
					$.confirm({
						title: '<?php echo __('Eliminar');?>',
						content: '<?php echo __('¿Seguro que desea eliminar el siguiente elemento?');?>',
						type: 'red',
						buttons: {
							aceptar: {
								text: '<?php echo __('Eliminar');?>',
								btnClass: 'btn-danger',
								action: function(){
									eliminarTrazabilidad(evento, valor);
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

			function insertarAutor(e){

				var texto = $( "#Autores option:selected" ).text();
				var valor = $( "#Autores option:selected" ).val();

				if(valor > 0){
					if(getParameterByName("id") == null){
						$('#recursos').attr("action","admin.php?page=recursos-edit&back=1");
						Guardar();
						e.preventDefault();
						e.stopPropagation();
					}

					var data = {
						'action': 'guardar_autor',
						"recurso": getParameterByName("id"),
						"autor": valor,
						"tipo":"guardar"
					};

					$.ajax({
						type: "POST",
						url : "<?php echo admin_url('admin-ajax.php'); ?>",
						data : data,
						success: function (response) {
							var sHtml = '<tr><td><a id="eliminarAutorRecurso" data-id="' + valor + '" title="Editar" class="fa fa-trash-alt bigger-180" href="javascript:{}"></a></td><td>' + texto + '</td></tr>';
							$('.autortable tbody').append(sHtml);
						},
						failure: function (response) {
							console.log(response);
						},
						error: function (response) {
							console.log(response);
						}
					});
				}
			}

			function eliminarAutor(e, valor){
				var data = {
					'action': 'guardar_autor',
					"recurso": getParameterByName("id"),
					"autor": valor,
					"tipo":"eliminar"
				};

				$.ajax({
					type: "POST",
					url : "<?php echo admin_url('admin-ajax.php'); ?>",
					data : data,
					success: function (response) {
						$('.autortable a[data-id="'+valor+'"]').parent().parent().remove();
					},
					failure: function (response) {
						console.log(response);
					},
					error: function (response) {
						console.log(response);
					}
				});
			}

			function insertarTrazabilidad(e){

				var data = {
					'action': 'guardar_trazabilidad',
					"recurso": getParameterByName("id"),
					"hijos": checkArray,
					"tipo":"guardar"
				};

				$.ajax({
					type: "POST",
					url : "<?php echo admin_url('admin-ajax.php'); ?>",
					data : data,
					success: function (response) {
						$('.trazabilidadtable tbody').html(response);
						eventosTrazabilidad();
					},
					failure: function (response) {
						console.log(response);
					},
					error: function (response) {
						console.log(response);
					}
				});

			}

			function eliminarTrazabilidad(e, valor){
				var data = {
					'action': 'guardar_trazabilidad',
					"recurso": getParameterByName("id"),
					"hijo": valor,
					"tipo":"eliminar"
				};

				$.ajax({
					type: "POST",
					url : "<?php echo admin_url('admin-ajax.php'); ?>",
					data : data,
					success: function (response) {
						$('.trazabilidadtable a[data-id="'+valor+'"]').parent().parent().remove();
					},
					failure: function (response) {
						console.log(response);
					},
					error: function (response) {
						console.log(response);
					}
				});
			}

			function obtenerDatos(){
				
				var data = {
					'action': 'obtener_datos',
					"url": $('#REurlregistro').val()
				};

				$('#REurlregistroHyperLink').attr("href",data.url);

				$.ajax({
					type: "POST",
					url : "<?php echo admin_url('admin-ajax.php'); ?>",
					data : data,
					success: function (response) {
						//console.log(response)
						var datos = JSON.parse(response)
						console.log(datos)
						if($('#REtitulo').val().length == 0){
							$('#REtitulo').val(datos.titulo);
						}

						if($('#REtamanio').val().length == 0){
							$('#REtamanio').val(datos.tamanio);
						}

						if($('#REdescripcion').val().length == 0){
							$('#REdescripcion').val(datos.descripcion);
						}

						if($('#REurlimagen').val().length == 0){
							$('#REurlimagen').val(datos.urlImage);
							$('#REurlimagenImage').attr("src", datos.urlImage);

							if(~datos.urlImage.indexOf('low.raw?id=')){
								$('#REurlimagenaltacalidad').val(datos.urlImage.replace('low.raw?id=','pdf.raw?query=id:') + '&jpeg=true');
								$('#REurlimagenaltacalidadImage').attr("src", $('#REurlimagenaltacalidad').val());
							}
							if(~datos.urlImage.indexOf('pdf.raw?query=id')){
								$('#REurlimagenaltacalidad').val(datos.urlImage);
								$('#REurlimagenaltacalidadImage').attr("src", $('#REurlimagenaltacalidad').val());
							}
						}

						if($('#REurlacceso').val().length == 0){
							$('#REurlacceso').val(datos.urlAcceso);
							$('#REurlaccesoHyperLink').attr("href",datos.urlAcceso);
						}

						if($('#REpublicacionperiodo1').val().length == 0){
							$('#REpublicacionperiodo1').val(datos.fecha1);
						}

						if($('#REpublicacionperiodo2').val().length == 0){
							$('#REpublicacionperiodo2').val(datos.fecha2);
						}

						

						if(datos.coleccion.valor.length > 0){
							if($('#REidcoleccion option[value="'+datos.coleccion.id+'"]').length == 0){
								$('#REidcoleccion').append('<option value="'+datos.coleccion.id+'">'+datos.coleccion.valor+'</option>');
							}
							$('#REidcoleccion option[value="'+datos.coleccion.id+'"]').prop('selected', true)
						}

						try{
							var jsonAutores = JSON.stringify(datos.autores);
							$('#registroAutores').val(jsonAutores);
						}catch(e){

						}

						$('#recursos').attr("action",$('#recursos').attr("action") + "&back=1");
						Guardar();

					},
					failure: function (response) {
						console.log(response);
					},
					error: function (response) {
						console.log(response);
					}
				});

			}

			function asignarCheck(enlace, tipo){
				var check = $(enlace).parent().find("input");
				if(check.is(":checked")){
					$(enlace).addClass("fa-circle").removeClass("fa-check-circle");
					check.prop('checked', false);

					if(tipo == 'nivel'){

						$('.asignaturas input[data-nivel="' + $(check).val() + '"]').prop('checked', false);
						$('.asignaturas input[data-nivel="' + $(check).val() + '"]').parent().find('a.marcarAsig').addClass("fa-circle").removeClass("fa-check-circle")

						$('.bloquesdecontenido input[data-nivel="' + $(check).val() + '"]').prop('checked', false);
						$('.bloquesdecontenido input[data-nivel="' + $(check).val() + '"]').parent().find('a.marcarBloque').addClass("fa-circle").removeClass("fa-check-circle")

					}else if(tipo == 'asig'){
					
						$('.bloquesdecontenido input[data-nivel="' + $(check).data('nivel') + '"]').parent().find('a.marcarBloque').addClass("fa-circle").removeClass("fa-check-circle")
						$('.bloquesdecontenido input[data-nivel="' + $(check).data('nivel') + '"]').prop('checked', false);
					
					}

				}else{
					$(enlace).removeClass("fa-circle").addClass("fa-check-circle");
					check.prop('checked', true);
				}
			}

			function estadoNivel(){

				$('.niveles input:checked').each(function(){
					$(this).parent().find('a').removeClass("fa-circle").addClass("fa-check-circle");
				});

			}

			function estadoAsig(){
				$('.asignaturas tr').addClass('hide');
				$('.niveles input:checked').each(function(){
					$('.asignaturas input[data-nivel="' + $(this).val() + '"]').parent().parent().removeClass('hide');
				});
			}

			function estadoBloque(){
				$('.bloquesdecontenido tr').addClass('hide');
				$('.asignaturas input:checked').each(function(){
					$('.bloquesdecontenido input[data-nivel="' + $(this).data('nivel') + '"][data-asig="' + $(this).val() + '"]').parent().parent().removeClass('hide');
				});
			}

			function asignarEditor(selector){
				tinymce.init({
					mode : "specific_textareas",
					editor_selector : "editortinymce",
					height: 500,
					menubar: false,
					plugins: [
					'advlist autolink lists link image charmap print preview anchor textcolor',
					'searchreplace visualblocks code fullscreen',
					'insertdatetime media table contextmenu paste code help wordcount'
					],
					toolbar: 'undo redo |  formatselect | bold italic backcolor  | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
					content_css: [
					'//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
					'//www.tinymce.com/css/codepen.min.css']/*,
					setup : function(ed) {
						console.log(ed);
						ed.onPostRender.add(function(ed,cm) {
							console.log('After render: ' + ed.id);
							$('#'+ed.id).hide();
						});
					}*/
				});
			}

			function getParameterByName(name, url) {
				if (!url) url = window.location.href;
				name = name.replace(/[\[\]]/g, '\\$&');
				var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
					results = regex.exec(url);
				if (!results) return null;
				if (!results[2]) return '';
				return decodeURIComponent(results[2].replace(/\+/g, ' '));
			}
		});
	</script>

<?php
}
