<?php

global $wpdb;
$table = $wpdb->prefix . 'bne_asignatura';
if (!empty($_POST)) {
	
	$data = array(
		'AStitulo' => $_POST['AStitulo'],
		'ASidbloque' => $_POST['ASidbloque'],
	);

	$format = array(
		'%s',
		'%d',
	);

	if($_GET['id'] > 0){
		$data['ASid'] = $_GET['id'];
		$format[] = '%d';
	}

	$success = $wpdb->replace($table, $data, $format);

	if(!($_GET['id'] > 0)){
		$_GET['id'] = $wpdb->insert_id;
	}

	//Insertamos Nivel
	$table = $wpdb->prefix . 'bne_asignaturanivel';
	$wpdb->delete($table, array('ANasignatura' => $_GET['id'] ), array('%d'));
	if($_POST['Nivel']){
		foreach ($_POST['Nivel'] as $key => $value) {
			$data = array(
				'ANasignatura'	=> $_GET['id'],
				'ANnivel'	=> $key,
			);
			$format = array(
				'%d',
				'%d',
			);
			$wpdb->insert($table, $data, $format);
		}
	}

	//Insertamos Modalidad
	$table = $wpdb->prefix . 'bne_modalidadasignatura';
	$wpdb->delete($table, array('MAidasignatura' => $_GET['id'] ), array('%d'));
	if($_POST['Modalidad']){
		foreach ($_POST['Modalidad'] as $key => $value) {
			$data = array(
				'MAidasignatura'	=> $_GET['id'],
				'MAidmodalidad'	=> $key,
			);
			$format = array(
				'%d',
				'%d',
			);
			$wpdb->insert($table, $data, $format);
		}
	}


	if($success){
		//echo 'data has been save' ; 
	}

	Redirect("admin.php?page=asignatura");

}elseif($_GET['action'] == "delete"){

	$ids = isset($_GET['id']) ? $_GET['id'] : array();

	if (is_array($ids)){
		$ids = implode(',', $ids);
	}

	if (!empty($ids)) {
		$wpdb->query("DELETE FROM $table WHERE ASid IN ($ids)");
		$table = $wpdb->prefix . 'bne_asignaturanivel';
		$wpdb->query("DELETE FROM $table WHERE ANasignatura IN ($ids)");
		$table = $wpdb->prefix . 'bne_modalidadasignatura';
		$wpdb->query("DELETE FROM $table WHERE MAidasignatura IN ($ids)");
	}

	Redirect("admin.php?page=asignatura");

} else {

	?>

	<div class="page-content">
		<div class="row">
			<div class="col-md-12">

				<div class="widget-box transparent invoice-box">
					<div class="widget-header">
						<h3 class="widget-title lighter">
							<span><?php echo __('Asignaturas', 'traduccionrecursos')?></span>
						</h3>
						<?php if($_GET['action'] == "edit"){ ?>
						<div class="widget-toolbar">
							<a id="btnCancelar" tabindex="7" title="<?php echo __('Salir', 'traduccionrecursos')?>" class="fa fa-times ico-boton30" href="javascript:{}"><span><?php echo __('Salir', 'traduccionrecursos')?></span></a>
						</div>
						<div class="widget-toolbar">
							<a id="btnAceptar" tabindex="6" title="<?php echo __('Guardar', 'traduccionrecursos')?>" class="fa fa-save ico-boton30"  href="javascript:{}"><span><?php echo __('Guardar', 'traduccionrecursos')?></span></a>
						</div>
						<?php }else{ ?>
						<div class="widget-toolbar">
							<a id="btnAceptar" tabindex="6" title="<?php echo __('Nuevo', 'traduccionrecursos')?>" class="fa fa-plus ico-boton30"  href="?page=asignatura&action=edit&id=0"><span><?php echo __('Nuevo', 'traduccionrecursos')?></span></a>
						</div>
						<?php } ?>
					</div>
				</div>

	<?php
	switch ($_GET['action']) {
		case 'edit':

			if(isset($_GET['id']) && strlen($_GET['id']) > 0){
				$id = $_GET['id'];
			}else{
				$id = 0;
			}
			
			if($id > 0){
				$aValores = array($id);
				$qry = $wpdb->prepare("SELECT * FROM ".$table." WHERE ASid = %d", $aValores);
				$row = $wpdb->get_row($qry);
				$sValor = $row->AStitulo;
			}else{
				$sValor = '';
			}

			$aBloques = GetTableArray($wpdb->prefix."bne_bloque","BLid","BLtitulo","BLtitulo");

			$table_name = $wpdb->prefix . 'bne_nivel';
			$table_name2 = $wpdb->prefix . 'bne_asignaturanivel';
			$sTablaNivel = $wpdb->get_results("SELECT *,(SELECT ANid FROM $table_name2 WHERE ANnivel = NIid AND ANasignatura = $id) AS ANid FROM $table_name ORDER BY NItitulo", ARRAY_A);

			$table_name = $wpdb->prefix . 'bne_modalidad';
			$table_name2 = $wpdb->prefix . 'bne_modalidadasignatura';
			$sTablaModalidad = $wpdb->get_results("SELECT *,(SELECT MAid FROM $table_name2 WHERE MAidmodalidad = MOid AND MAidasignatura = $id) AS MAid FROM $table_name ORDER BY MOtitulo", ARRAY_A);

			?>
			<form method="post" id="auxiliares" name="auxiliares">
				<div class="row">
					<div class="col-xs-12 col-sm-6 panel-info-principal">
						<fieldset>
							<legend><i class="fa fa-list"></i><?php echo __('Datos Principales', 'traduccionrecursos');?></legend>
							<div class="row">
								<div class="col-xs-6 control-label text-left">
									<span class="forms-label-top"><?php echo __('Bloque', 'traduccionrecursos');?></span>
									<?php echo GetSelectFromArray("ASidbloque",$aBloques, $row->ASidbloque); ?>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-9 control-label text-left required">
									<span class="forms-label-top"><?php echo __('Título', 'traduccionrecursos');?></span>
									<input name="AStitulo" value="<?php echo $sValor;?>" id="AStitulo" class="form-control" type="text">
								</div>
							</div>
						</fieldset>
					</div>
				</div>

				<div class="row">
					<div class="col-xs-12 col-sm-6 fieldset-theme">
						<fieldset>
							<legend><i class="fa fa-list"></i><?php echo __('Nivel', 'traduccionrecursos');?></legend>
							<table class="wp-list-table widefat striped">
								<thead>
									<tr>
										<th></th>
										<th><?php echo __('Título', 'traduccionrecursos');?></th>
									</tr>
								</thead>
								<tbody>
									<?php 

									foreach ($sTablaNivel as $rowDatos): array_map('htmlentities', $rowDatos); 
										$checked = "";
										$class = "fa-circle";
										if($rowDatos['ANid']){
											$checked = "checked";
											$class = "fa-check-circle";
										}
										?>
										<tr>
											<th class="check-column column-id">
												<a id="btnMarcarNivel" title="Asignar" class="far <?php echo $class; ?> grey bigger-180 marcarNivel" href="javascript:{}" style="display:inline-block;width:100%;"></a>
												<input name="Nivel[<?php echo  $rowDatos['NIid']; ?>]" <?php echo  $checked; ?> type="checkbox" value="<?php echo  $rowDatos['NIid']; ?>" class="hidden"/>
											</th>
											<td><?php echo  $rowDatos['NItitulo']; ?></td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</fieldset>
					</div>
					<div class="col-xs-12 col-sm-6 fieldset-theme">
						<fieldset>
							<legend><i class="fa fa-list"></i><?php echo __('Modalidad', 'traduccionrecursos');?></legend>
							<table class="wp-list-table widefat striped">
								<thead>
									<tr>
										<th></th>
										<th class="v-middle"><?php echo __('Título', 'traduccionrecursos');?></th>
									</tr>
								</thead>
								<tbody>
									<?php 

									foreach ($sTablaModalidad as $rowDatos): array_map('htmlentities', $rowDatos); 
										$checked = "";
										$class = "fa-circle";
										if($rowDatos['MAid']){
											$checked = "checked";
											$class = "fa-check-circle";
										}
										?>
										<tr>
											<th class="check-column column-id">
												<a id="btnMarcarModalidad" title="Asignar" class="far <?php echo $class; ?> grey bigger-180 marcarModalidad" href="javascript:{}" style="display:inline-block;width:100%;"></a>
												<input name="Modalidad[<?php echo  $rowDatos['MOid']; ?>]" <?php echo  $checked; ?> type="checkbox" value="<?php echo  $rowDatos['MOid']; ?>" class="hidden"/>
											</th>
											<td class="v-middle"><?php echo  $rowDatos['MOtitulo']; ?></td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</fieldset>
					</div>
				</div>

			</form>
			<?php

			break;
		case 'list':
		default:
			$aTabla = $wpdb->get_results("SELECT * FROM $table ORDER BY ASid",	ARRAY_A);
			
				?>
				<table class="wp-list-table widefat striped">
					<thead>
						<tr>
							<th class="column-id">
								<?php echo __('Id', 'traduccionrecursos');?>
							</th>
							<th>
								<?php echo __('Título', 'traduccionrecursos');?>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php 

						foreach ($aTabla as $rowDatos): array_map('htmlentities', $rowDatos); 
							?>
							<tr>
								<th class="column-id">
									<?php echo  $rowDatos['ASid']; ?>
								</th>
								<td><div class="contenido"><?php echo  $rowDatos['AStitulo']; ?></div>
									<div class="row-actions">
										<span class="edit">
											<a href="?page=asignatura&action=edit&id=<?php echo  $rowDatos['ASid']; ?>">
												Editar
											</a>
											 | 
										</span>
											<span class="delete">
												<a href="?page=asignatura&action=delete&id=<?php echo  $rowDatos['ASid']; ?>">
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

			function Guardar(){
				if (camposRequeridos()) {
					$('#auxiliares').submit();
				} else {
					return false;
				}	
			}

			function Salir(){
				location.href = "admin.php?page=asignatura";
			}

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
					asignarCheck(this);
				});

				$('.marcarModalidad').click(function(){
					asignarCheck(this);
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
			});

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


			function asignarCheck(enlace){
				var check = $(enlace).parent().find("input");
				if(check.is(":checked")){
					$(enlace).addClass("fa-circle").removeClass("fa-check-circle");
					check.prop('checked', false);

				}else{
					$(enlace).removeClass("fa-circle").addClass("fa-check-circle");
					check.prop('checked', true);
				}
			}
		});
	</script>

<?php
}
