<?php

function guardarFavorito(){
	global $wpdb;

	$nIdUser = 0;

	$idRecurso = $_POST['id'];
	$nIdUser = get_current_user_id();
	$sTipoGuardado = 'favorito';
	$table = $wpdb->prefix . 'bne_recursosguardados';

	if($_POST['tipo'] == "eliminar"){
		$wpdb->query("DELETE FROM $table WHERE RGrecurso = $idRecurso AND RGusuario = $nIdUser AND RGtipo LIKE '$sTipoGuardado'");
	}else{
		$data = array(
			'RGrecurso' => $idRecurso,
			'RGusuario' => $nIdUser,
			'RGtipo' 	=> $sTipoGuardado,
			'RGfecha' 	=> time(),
		);

		$format = array(
			'%d',
			'%d',
			'%s',
			'%d',
		);
		$success = $wpdb->replace($table, $data, $format);
	}

	echo "OK";
	wp_die();

}

// The widget class
class VerRecurso_Widget extends WP_Widget {

	// Main constructor
	public function __construct() {
		parent::__construct(
			'verrecurso_widget',
			__( 'Visor de recursos', 'traduccionrecursos'),
			array(
				'customize_selective_refresh' => true,
			)
		);

		add_action('wp_head', 'my_theme_widget');
		add_action('wp_ajax_guardarFavorito', 'guardarFavorito' );
		add_action('wp_ajax_getColecciones', 'getColecciones' );
	}

	// The widget form (for the backend )
	public function form( $instance ) {

		// Parse current settings with defaults
		extract( wp_parse_args( ( array ) $instance, $defaults ) );

	}

	// Update widget settings
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		return $instance;
	}

	// Display the widget
	public function widget( $args, $instance ) {
		global $wpdb;
		extract( $args );

		// WordPress core before_widget hook (always include )
		echo $before_widget;

		$nIdUser = 0;

		$idRecurso = $_GET['id'];

		$table = $wpdb->prefix . 'bne_recurso';
		$aTabla = $wpdb->get_results("
		SELECT * 
		FROM $table 
		WHERE REid = ".$idRecurso."
		ORDER BY REtitulo",	ARRAY_A);

		$tableAutores = $wpdb->prefix.'bne_autores';
		$tableAutores2 = $wpdb->prefix.'bne_recursoautor';
		$aTablaAutores = $wpdb->get_results("
		SELECT * 
		FROM $tableAutores 
		INNER JOIN $tableAutores2 ON AUid = RAautor
		WHERE RArecurso = ".$idRecurso."
		ORDER BY AUnombre",	ARRAY_A);

		$tableTipoDoc = $wpdb->prefix.'bne_tiporecursos';
		$tableTipoDoc2 = $wpdb->prefix.'bne_recursotiporecurso';
		$aTablaTipoDoc = $wpdb->get_results("
		SELECT * 
		FROM $tableTipoDoc 
		INNER JOIN $tableTipoDoc2 ON TRid = RRtiporecurso
		WHERE RRrecurso = ".$idRecurso."
		ORDER BY TRtitulo",	ARRAY_A);

		$table1 = $wpdb->prefix.'bne_nivel';
		$table2 = $wpdb->prefix.'bne_recursonivel';
		$aTablaDatosNivel = $wpdb->get_results("
		SELECT * 
		FROM $table1 
		INNER JOIN $table2 ON NIid = BNnivel
		WHERE BNrecurso = ".$idRecurso."
		ORDER BY NItitulo",	ARRAY_A);

		if(is_user_logged_in()){
			$nIdUser = get_current_user_id();
			$tableFavoritos = $wpdb->prefix.'bne_recursosguardados';
			$aTablaFavoritos = $wpdb->get_results("
			SELECT * 
			FROM $tableFavoritos 
			WHERE RGrecurso = ".$idRecurso." AND RGtipo = 'favorito' AND RGusuario = ".$nIdUser,	ARRAY_A);
		}
		$sHtml = '';
		if(count($aTabla) > 0){

			$sImg = $aTabla[0]['REurlimagenaltacalidad'];
			if(strlen($sImg) == 0) {
				$sImg = $aTabla[0]['REurlimagen'];
			}
			if(strlen($sImg) == 0) {
				$sImg = RKR_PLUGIN_PATH.'/theme/img/logoBNEpositivo.jpg';
			}
			
			/*
			if(false !== strpos($sImg, 'low.raw')) {
				$firstPos = strpos($sImg, 'low.raw?id=') + strlen('low.raw?id=');
				$lastPos = strpos($sImg, '&name=', $firstPos);
				$idBDH = substr($sImg, $firstPos, $lastPos - $firstPos);
				$sImg = "http://bdh-rd.bne.es/pdf.raw?query=id:".$idBDH."&jpeg=true";
			}
			*/

			$sFecha = '';
			if($aTabla[0]['REpublicacionperiodo1'] > 0 && $aTabla[0]['REpublicacionperiodo2'] > 0){
				$sFecha = 'Desde '.$aTabla[0]['REpublicacionperiodo1'].' hasta '.$aTabla[0]['REpublicacionperiodo2'];
			}elseif($aTabla[0]['REpublicacionperiodo1'] > 0){
				$sFecha = $aTabla[0]['REpublicacionperiodo1'];
			}



			$sHtml .='
			<div class="verRecurso">
				<input type="hidden" id="idRecurso" name="idRecurso" value="'.$idRecurso.'"/>
				<div class="row">
					<div class="col-md-3">
						<div class="recurso-datos">
							<img src="'.$sImg.'" alt="'.$aTabla[0]['REtitulo'].'" />
							<a href="'.$aTabla[0]['REurlacceso'].'" target="_blank"><span class="fas fa-angle-right"></span>'.__('Ver obra en BDH', 'traduccionrecursos').'</a>
							<a href="'.$aTabla[0]['REurlregistro'].'" target="_blank"><span class="fas fa-angle-right"></span>'.__('Ver registro en BDH', 'traduccionrecursos').'</a>
							<a href="'.get_site_url().'/wp-admin/admin-post.php?action=export_page&id='.$idRecurso.'" target="_blank"><span class="fas fa-angle-right"></span>'.__('Descargar scorm', 'traduccionrecursos').'</a>
						</div>
					</div>
			

					<div class="col-md-9">
						<div class="recurso-datos-titulo">'.$aTabla[0]['REtitulo'];

							if(is_user_logged_in()){
								$nIdUser = get_current_user_id();
								if(count($aTablaFavoritos) > 0){
									$sClass = "fas activo";
								}else{
									$sClass = "far";
								}
								$sHtml .='
								<div>
									<a class="'.$sClass.' fa-star"><span>'.__('favorito', 'traduccionrecursos').'</span></a>
									<a class="far fa-list-alt"><span>'.__('colecciones', 'traduccionrecursos').'</span></a>
								</div>';
							}
					

							$sHtml .='
                        </div>';
                        if(!empty($aTablaAutores)){
                            $sHtml .='
						        <div class="recurso-datos-row">
							        <span class="subtitulo">'.__('Autores', 'traduccionrecursos').':</span>';
							
                                        foreach ($aTablaAutores as $key => $value) {
                                            $sHtml .='<div>'.$value['AUnombre'].'</div>';
                                        }
                                        $sHtml .='</div>';
                        }
                        
                        if(!empty($aTabla[0]['REdescripcion'])){
                            $sHtml .='
          				        <div class="recurso-datos-row">
							        <span class="subtitulo">'.__('Descripción', 'traduccionrecursos').':</span>';
                                $sHtml .='<div>'.$aTabla[0]['REdescripcion'].'</div>';
                                $sHtml .='</div>';
                        }

						if(!empty($aTabla[0]['REtamanio'])){	
							$sHtml .='
						    <div class="recurso-datos-row">
							<span class="subtitulo">'.__('Descripción física', 'traduccionrecursos').':</span>';
                                $sHtml .='<div>'.$aTabla[0]['REtamanio'].'</div>';
                                $sHtml .='</div>';    
                        }

                        if(!empty($aTablaTipoDoc)){
							$sHtml .='
						        <div class="recurso-datos-row">
							    <span class="subtitulo">'.__('Tipo de documento', 'traduccionrecursos').':</span>';
							
								foreach ($aTablaTipoDoc as $key => $value) {
										$sHtml .='<div>'.$value['TRtitulo'].'</div>';
								}
                                $sHtml .='</div>'; 
                        }

                        if(!empty($sFecha)){
                            $sHtml .='
						    <div class="recurso-datos-row">
							<span class="subtitulo">'.__('Fecha de publicación', 'traduccionrecursos').':</span>';
                                $sHtml .='<div>'.$sFecha.'</div>';
                                $sHtml .='</div>'; 
                        }
							$sHtml .='
						<div class="recurso-datos-row">
							<span class="subtitulo">'.__('Ficha Curricular', 'traduccionrecursos').':</span>';

							foreach ($aTablaDatosNivel as $key => $value) {

								$table1 = $wpdb->prefix.'bne_asignatura';
								$table2 = $wpdb->prefix.'bne_recursoasignatura';
								$aTablaDatosAsig = $wpdb->get_results("
								SELECT * 
								FROM $table1 
								INNER JOIN $table2 ON ASid = RAasignatura
								WHERE RArecurso = ".$idRecurso." AND REnivel = ".$value['NIid']."
								ORDER BY AStitulo",	ARRAY_A);
								$sAsig = "";

								foreach ($aTablaDatosAsig as $key2 => $value2) {
									if(isset($value['NItitulo'])){
                                        $testPrintNivel=$value2['AStitulo']." (".$value['NItitulo'].")";
                                    }
									$sHtml .='
									<div class="acordeon">
										<a class="acordeon-titulo" href="javascript:void(0);" title="'.$value2['AStitulo'].'">'.$testPrintNivel.'<span class="fas fa-caret-up"></span></a>
									</div>
									<div>';
										//Bloque
										$table1 = $wpdb->prefix.'bne_bloquecontenido';
										$table2 = $wpdb->prefix.'bne_recursobloquecontenido';
										$aTablaDatosBloque = $wpdb->get_results("
										SELECT * 
										FROM $table1 
										INNER JOIN $table2 ON BCid = RBbloquecontenido
										WHERE RBrecurso = ".$idRecurso." 
										AND BCidnivel = ".$value['NIid']." 
										AND BCidasignatura = ".$value2['ASid']." 
										ORDER BY BCtexto",	ARRAY_A);
										foreach ($aTablaDatosBloque as $key3 => $value3) {
											$sHtml .='
											<div class="acordeon-contenido">
												<span class="subtitulo">'.$value3['BCtexto']." ".$value3['BCcontenido'].'</span>'.$value3['BCcontenidotexto'];
													//Criterios
													$table1 = $wpdb->prefix.'bne_estandares';
													$table2 = $wpdb->prefix.'bne_recursoestandares';
													$table3 = $wpdb->prefix.'bne_criterios';
													$aTablaDatosCriterios = $wpdb->get_results("
													SELECT CRid,CRcodigo,CRdescripcion 
													FROM $table1 
													INNER JOIN $table2 ON ETid = REidestandar
													INNER JOIN $table3 ON CRid = ETidcriterio
													WHERE REidrecurso = ".$idRecurso." AND CRidbloquecontenido = ".$value3['BCid']."
													GROUP BY CRid,CRcodigo,CRdescripcion
													ORDER BY CRcodigo,CRdescripcion",ARRAY_A);
													$sEstandar = "";
													$sCodigoCrit = "";
													$sHtml .='
													<div class="acordeon-contenido">
														<span class="subtitulo">'.__('Criterios de evaluación y estándares de aprendizaje', 'traduccionrecursos').'</span>';
													foreach ($aTablaDatosCriterios as $key4 => $value4) {
														$sHtml .='<div>'.$value4['CRcodigo'].' '.$value4['CRdescripcion'].'</div>';
														//Estandares
														$table1 = $wpdb->prefix.'bne_estandares';
														$table2 = $wpdb->prefix.'bne_recursoestandares';
														$table3 = $wpdb->prefix.'bne_criterios';
														$aTablaDatosEstandar = $wpdb->get_results("
														SELECT * 
														FROM $table1 
														INNER JOIN $table2 ON ETid = REidestandar
														INNER JOIN $table3 ON CRid = ETidcriterio
														WHERE REidrecurso = ".$idRecurso." AND CRid = ".$value4['CRid']."
														ORDER BY CRcodigo,CRdescripcion,ETcodigo,ETdescripcion",ARRAY_A);
														$sEstandar = "";
														$sCodigoCrit = "";
														$sHtml .='
														<div class="acordeon-contenido">';
														foreach ($aTablaDatosEstandar as $key5 => $value5) {
															$sHtml .='<div>'.$value5['ETcodigo'].' '.$value5['ETdescripcion'].'</div>';
														
														}
														$sHtml .='
														</div>';
													}
													$sHtml .='
													</div>';
											$sHtml .='
											</div>';
										}
									$sHtml .='
									</div>
									';
								}
							}

							$sHtml .='
						</div>
						<div class="recurso-datos-row">
							<span class="subtitulo">'.__('Obras Relacionadas', 'traduccionrecursos').':</span>
							<div class="buscador-widget">
								<div class="row">';
									$table1 = $wpdb->prefix.'bne_recurso';
									$table2 = $wpdb->prefix.'bne_recursohijos';
									$aTablaDatosRecursoHijo = $wpdb->get_results("
									SELECT * 
									FROM $table1 
									INNER JOIN $table2 ON REid = RRrecursohijo
									WHERE RRrecurso = ".$idRecurso." 
									ORDER BY REtitulo",	ARRAY_A);
									//shuffle($aTablaDatosRecursoHijo);
									foreach ($aTablaDatosRecursoHijo as $keyHijo => $valueHijo) {
										$sImg = $valueHijo['REurlimagen'];
										if(strlen($sImg) == 0) {
											$sImg = RKR_PLUGIN_PATH.'/theme/img/logoBNEpositivo.jpg';
										}
										$sHtml .= '
										<div class="col-md-3">
											<a class="buscador-item" href="'.get_site_url().'/visor/?id='.$valueHijo['REid'].'" aria-label="'.$valueHijo['REtitulo'].'">
												<div class="imagen-medio">
													<img src="'.$sImg.'" alt="" class="img-responsive wp-image-82">
												</div>
												<p class="item-titulo">'.$valueHijo['REtitulo'].'</p>
											</a>
										</div>';
									}
									$sHtml .='
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			';

			if (is_user_logged_in()) {
				$nIdUser = get_current_user_id();
				$sTipo = 'visto';

				$table = $wpdb->prefix . 'bne_recursosguardados';
				$wpdb->query("DELETE FROM $table WHERE RGrecurso = $idRecurso AND RGusuario = $nIdUser AND RGtipo LIKE '$sTipo'");

				$data = array(
					'RGrecurso' => $idRecurso,
					'RGusuario' => $nIdUser,
					'RGtipo' 	=> $sTipo,
					'RGfecha' 	=> time(),
				);

				$format = array(
					'%d',
					'%d',
					'%s',
					'%d',
				);
				$success = $wpdb->replace($table, $data, $format);
			}

		}else{
			echo '<h1>'.__('Vista del recurso no disponible', 'traduccionrecursos').'</h1>';
		}

		echo $sHtml;

		?>

		<script type="text/javascript">
			
			jQuery(function($) {
				var cargando = '<div class="sk-cube-grid"><div class="sk-cube sk-cube1"></div><div class="sk-cube sk-cube2"></div><div class="sk-cube sk-cube3"></div><div class="sk-cube sk-cube4"></div><div class="sk-cube sk-cube5"></div><div class="sk-cube sk-cube6"></div><div class="sk-cube sk-cube7"></div><div class="sk-cube sk-cube8"></div><div class="sk-cube sk-cube9"></div></div>';
				$(document).ready(function(){

					$('.fa-star').click(function(e){
						marcarFavorito(this);
						e.preventDefault();
						e.stopPropagation();
					});

					$('.fa-list-alt').click(function(e){
						marcarColecciones(this);
						e.preventDefault();
						e.stopPropagation();
					});

					accordeonEventos();

					$('.item-titulo').truncate({
						lines: 2
					});
					
				});

				function marcarColecciones(elemento){

					$.confirm({
						title: '<?php echo __('Colecciones', 'traduccionrecursos'); ?>',
						content: '<div class="cargando relative">' + cargando + '</div>',
						buttons: {
							save: {
								text: '<?php echo __('Guardar', 'traduccionrecursos'); ?>',
								btnClass: 'btn-success',
								action: function(){
									var colecciones = [];
									$('.colecciones-lista').each(function(){
										var coleccion = {};
										var active = 0;
										if($(this).hasClass('active')){
											active = 1;
										}
										coleccion.id = $(this).data('coleccion');
										coleccion.active = active;
										colecciones.push(coleccion);
									});

									var tipo = 'guardarusuario';
									var data = {
										'action': 'setColecciones',
										'tipo': tipo,
										'recurso': $('#idRecurso').val(),
										'colecciones': colecciones
									};

									$.ajax({
										type: "POST",
										url : "<?php echo admin_url('admin-ajax.php'); ?>",
										data : data,
										success: function (response) {

										},
										failure: function (response) {
											console.log(response);
										},
										error: function (response) {
											console.log(response);
										}
									});
								}
							},
							cancel: {
								text: '<?php echo __('Cancelar', 'traduccionrecursos'); ?>',
								action: function(){
									// do nothing.
								}
							}
						}
					});

					var data = {
						'action': 'getColecciones',
						'id': $('#idRecurso').val(),
						'usuario': <?php echo $nIdUser; ?>,
						'marcador':1,
						'recurso': $('#idRecurso').val()
					};

					$.ajax({
						type: "POST",
						url : "<?php echo admin_url('admin-ajax.php'); ?>",
						data : data,
						success: function (response) {
							divContenedor = $('.cargando.relative').parent();
							$('.cargando.relative').remove();
							divContenedor.append(response);
							$('div.colecciones-lista').click(function(){
								if($(this).hasClass('active')){
									$(this).removeClass('active');
								}else{
									$(this).addClass('active');
								}
							});
						},
						failure: function (response) {
							console.log(response);
						},
						error: function (response) {
							console.log(response);
						}
					});

				}

				function marcarFavorito(elemento){

					var tipo = "";
					if($(elemento).hasClass("activo")){
						tipo = "eliminar";
						$(elemento).removeClass("fas").addClass("far").removeClass("activo");
					}else{
						tipo = "guardar";
						$(elemento).removeClass("far").addClass("fas").addClass("activo");
					}

					var data = {
						'action': 'guardarFavorito',
						'id': $('#idRecurso').val(),
						"tipo":tipo
					};

					$.ajax({
						type: "POST",
						url : "<?php echo admin_url('admin-ajax.php'); ?>",
						data : data,
						success: function (response) {

						},
						failure: function (response) {
							console.log(response);
						},
						error: function (response) {
							console.log(response);
						}
					});
				}

				function accordeonEventos(){
					$(".acordeon").unbind('click');
					$('.acordeon').on("touchstart, click", function(e){
						if($(this).find("span").hasClass("fa-caret-up")){
							$(this).next().slideUp();
							$(this).find("span").removeClass("fa-caret-up").addClass("fa-caret-down");
						}else{
							$(this).next().slideDown();
							$(this).find("span").removeClass("fa-caret-down").addClass("fa-caret-up");						
						}
						return false;
					});
				}

			});

		</script>

		<?php
		// WordPress core after_widget hook (always include )
		echo $after_widget;
	}

}