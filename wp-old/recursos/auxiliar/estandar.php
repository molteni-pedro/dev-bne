<?php

global $wpdb;
$table = $wpdb->prefix . 'bne_idioma';
if (!empty($_POST)) {
	
	$data = array(
		'IDcodigo' => $_POST['IDcodigo'],
		'IDtitulo' => $_POST['IDtitulo'],
	);

	$format = array(
		'%s',
		'%s',
	);

	if(!empty($_POST['valorinicial'])){
		$aWhere = array( 'IDcodigo' => $_POST['valorinicial'] );
		$formatWhere = array(
			'%s',
		);
		$success = $wpdb->update($table, $data,$aWhere, $format,$formatWhere);
	}else{
		$success = $wpdb->replace($table, $data, $format);
	}
	

	if($success){
		//echo 'data has been save' ; 
	}

	Redirect("admin.php?page=idioma");

}elseif($_GET['action'] == "delete"){

	$ids = isset($_GET['id']) ? $_GET['id'] : '';
	$wpdb->query("DELETE FROM $table WHERE IDcodigo = '$ids'");

	Redirect("admin.php?page=idioma");

} else {

	?>

	<div class="page-content">
		<div class="row">
			<div class="col-md-12">

				<div class="widget-box transparent invoice-box">
					<div class="widget-header">
						<h3 class="widget-title lighter">
							<span><?php echo __('Idioma', 'traduccionrecursos');?></span>
						</h3>
						<?php if($_GET['action'] == "edit"){ ?>
						<div class="widget-toolbar">
							<a id="btnCancelar" tabindex="7" title="<?php echo __('Salir', 'traduccionrecursos');?>" class="fa fa-times ico-boton30" href="javascript:{}"><span><?php echo __('Salir', 'traduccionrecursos');?></span></a>
						</div>
						<div class="widget-toolbar">
							<a id="btnAceptar" tabindex="6" title="<?php echo __('Guardar', 'traduccionrecursos');?>" class="fa fa-save ico-boton30"  href="javascript:{}"><span><?php echo __('Guardar', 'traduccionrecursos');?></span></a>
						</div>
						<?php }else{ ?>
						<div class="widget-toolbar">
							<a id="btnAceptar" tabindex="6" title="<?php echo __('Nuevo', 'traduccionrecursos');?>" class="fa fa-plus ico-boton30"  href="?page=idioma&action=edit&id=0"><span><?php echo __('Nuevo', 'traduccionrecursos');?></span></a>
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
				$id = '';
			}
			
			if(strlen($id) > 0){
				$aValores = array($id);
				$qry = $wpdb->prepare("SELECT * FROM ".$table." WHERE IDcodigo = %s", $aValores);

				$row = $wpdb->get_row($qry);
				$sCodigo = $row->IDcodigo;
				$sValor = $row->IDtitulo;
			}else{
				$sCodigo = '';
				$sValor = '';
			}

			?>
			<form method="post" id="auxiliares" name="auxiliares">
				<input type="hidden" name="valorinicial" value="<?php echo $sCodigo;?>">
				<div class="row">
					<div class="col-xs-12 col-sm-6 panel-info-principal">
						<fieldset>
							<legend><i class="fa fa-list"></i><?php echo __('Datos Principales', 'traduccionrecursos');?></legend>
							<div class="row">
								<div class="col-xs-12 control-label text-left required">
									<span class="forms-label-top"><?php echo __('Código', 'traduccionrecursos');?></span>
									<input name="IDcodigo" value="<?php echo $sCodigo;?>" id="IDcodigo" class="form-control" type="text">
								</div>
								<div class="col-xs-12 control-label text-left required">
									<span class="forms-label-top"><?php echo __('Título', 'traduccionrecursos');?></span>
									<input name="IDtitulo" value="<?php echo $sValor;?>" id="IDtitulo" class="form-control" type="text">
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
			$aTabla = $wpdb->get_results("SELECT * FROM $table ORDER BY IDcodigo",	ARRAY_A);
			
				?>
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

						foreach ($aTabla as $rowDatos): array_map('htmlentities', $rowDatos); 
							?>
							<tr>
								<th class="column-id">
									<?php echo  $rowDatos['IDcodigo']; ?>
								</th>
								<td><div class="contenido"><?php echo  $rowDatos['IDtitulo']; ?></div>
									<div class="row-actions">
										<span class="edit">
											<a href="?page=idioma&action=edit&id=<?php echo  $rowDatos['IDcodigo']; ?>">
												Editar
											</a>
											 | 
										</span>
											<span class="delete">
												<a href="?page=idioma&action=delete&id=<?php echo  $rowDatos['IDcodigo']; ?>">
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
				location.href = "admin.php?page=idioma";
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
		});
	</script>

<?php
}
