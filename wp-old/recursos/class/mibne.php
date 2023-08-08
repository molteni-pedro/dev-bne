<?php

// The widget class
class Mibne_Widget extends WP_Widget {

	// Main constructor
	public function __construct() {

		parent::__construct(
			'mibne_widget',
			__( 'Mi Bne', 'traduccionrecursos' ),
			array(
				'customize_selective_refresh' => true,
			)
		);

		add_action('wp_head', 'my_theme_widget');
		add_action( 'wp_ajax_setColecciones', 'setColecciones');
		add_action( 'wp_ajax_nopriv_setColecciones', 'setColecciones');
		add_action( 'wp_ajax_getListaMibne', 'getListaMibne');
		add_action( 'wp_ajax_nopriv_getListaMibne', 'getListaMibne');
		add_action( 'wp_ajax_getPerfil', 'getPerfil');
		add_action( 'wp_ajax_nopriv_getPerfil', 'getPerfil');
		add_action( 'wp_ajax_setPerfil', 'setPerfil');
		add_action( 'wp_ajax_nopriv_setPerfil', 'setPerfil');
		add_action( 'wp_ajax_uploadImage', 'uploadImage');
		add_action( 'wp_ajax_nopriv_uploadImage', 'uploadImage');

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

		function transformaDataToHTML($aData){

			$sHtml = '';
			$url = "https://www.labygram.com/project/";

			foreach ($aData as $key => $value) {

				$sImg = '';
				if (isset($value->image,  $value->image->url)) {
                    $sImg = $value->image->url;
                } elseif (isset($value->backgroundImage,  $value->backgroundImage->url)) {
                    $sImg = $value->backgroundImage->url;
                } elseif (isset($value->background,  $value->background->type)) {
                    if ('unsplash' == $value->background->type && isset($value->background->value)) {
                        if (isset($value->background->value->urls)) {
                            $sImg = $value->background->value->urls->thumb;
                        } else {
                            $sImg = RKR_PLUGIN_PATH.'/theme/img/logoBNEpositivo.jpg';
                        }
                    } else {
                        $sImg = RKR_PLUGIN_PATH.'/theme/img/logoBNEpositivo.jpg';
                    }
                } elseif (isset($value->images[0], $value->images[0]->url)) {
                    $sImg = $value->images[0]->url;
                } else {
                    $sImg = RKR_PLUGIN_PATH.'/theme/img/logoBNEpositivo.jpg';
                }

				$sHtml .= '<div class="masonry-brick">';
				$sHtml .= '<a alt="'.$value->title.'" href="'.$url.$value->_id.'" target="_blank">';
				$sHtml .= '
				<div class="imagen-medio">
					<img class="detalles" src="'.$sImg.'" alt="'.__('Foto recurso', 'traduccionrecursos').'" />
				</div>';
				$sHtml .= '<span>'.$value->title.'</span>';
				$sHtml .= '</a>';
				//$sHtml .= '<a class="editar" alt="'.__('Ver recurso', 'traduccionrecursos').'" href="'.$url.$value->_id.'">';
				//$sHtml .= '<span class="far fa-eye"></span></a>';
				$sHtml .= '</div>';
			}
			return $sHtml;
		}

		function getLabygramData($token){
			$sUrlBase 		= 'https://core.labygram.com';
			$sUrlAll 		= '/BNE/project/all';
			$sUrlMine 		= '/BNE/project/own';
			$sUrlFeatured 	= '/BNE/project/featured';
			$sHtml 			= '';

			$opts = array(
				'http' => array(
					'header'=> 'Cookie: ' . $_SERVER['HTTP_COOKIE']."\r\n" . "jwt: ".$token."\r\n"
				)
			);

			$context = stream_context_create($opts);


			$sHtml .= '
			<ul class="nav nav-pills mb-3 labygram-pills" id="pills-tab" role="tablist">
				<li class="nav-item active">
					<a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-todo" role="tab" aria-controls="pills-home" aria-selected="true">'.__('TODOS', 'traduccionrecursos').'</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-mio" role="tab" aria-controls="pills-profile" aria-selected="false">'.__('CREADOS POR MI', 'traduccionrecursos').'</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-invitado" role="tab" aria-controls="pills-contact" aria-selected="false">'.__('INVITADO A PARTICIPAR', 'traduccionrecursos').'</a>
				</li>
			</ul>
			<div class="tab-content" id="pills-tabContent">
				<div class="tab-pane fade active in" id="pills-todo" role="tabpanel" aria-labelledby="pills-home-tab">';
					$sHtml .= '<div class="masonry">';
					$aData = json_decode(file_get_contents($sUrlBase.$sUrlAll, false, $context));
					$sHtml .= transformaDataToHTML($aData);
					$sHtml .= '</div>';
				$sHtml .= '
				</div>
				<div class="tab-pane fade" id="pills-mio" role="tabpanel" aria-labelledby="pills-profile-tab">';
					$sHtml .= '<div class="masonry">';
					$aData = json_decode(file_get_contents($sUrlBase.$sUrlMine, false, $context));
					$sHtml .= transformaDataToHTML($aData);
					$sHtml .= '</div>';
				$sHtml .= '
				</div>
				<div class="tab-pane fade" id="pills-invitado" role="tabpanel" aria-labelledby="pills-contact-tab">';
					$sHtml .= '<div class="masonry">';
					$aData = json_decode(file_get_contents($sUrlBase.$sUrlFeatured, false, $context));
					$sHtml .= transformaDataToHTML($aData);
					$sHtml .= '</div>';
				$sHtml .= '
				</div>
			</div>';

			return $sHtml;
		}

		$sIdiomaRef = '';

		if(isset($_GET['lang']) && strlen($_GET['lang']) > 0){
			$sIdiomaRef = '&lang='.$_GET['lang'];
		}

		if (is_user_logged_in()) {

			$nIdUser = get_current_user_id();

			$table = $wpdb->prefix.'bne_recurso';


			$aTablaRecursos = $wpdb->get_results("
			SELECT * 
			FROM $table 
			WHERE REidcreador = $nIdUser
			ORDER BY REtitulo",	ARRAY_A);

			$table2 = $wpdb->prefix.'bne_recursosguardados';
			$aTabla = $wpdb->get_results("
			SELECT * 
			FROM $table 
			INNER JOIN $table2 ON RGrecurso = REid
			WHERE RGusuario = $nIdUser
			ORDER BY RGtipo,RGfecha DESC,REtitulo",	ARRAY_A);

			foreach ($aTabla as $key => $value) {

				$sImg = $value['REurlimagen'];
				if(strlen($sImg) == 0) {
					$sImg = RKR_PLUGIN_PATH.'/theme/img/logoBNEpositivo.jpg';
				}

				$aTablaGuardados[$value['RGtipo']][$value['REid']]['img'] = $sImg;
				$aTablaGuardados[$value['RGtipo']][$value['REid']]['titulo'] = $value['REtitulo'];
			}
			
			$tableColeccion = $wpdb->prefix.'bne_coleccionesusuario';
			$aTablaColeccion = $wpdb->get_results("
			SELECT * 
			FROM $tableColeccion 
			WHERE CUusuario = $nIdUser
			ORDER BY CUtitulo",	ARRAY_A);

			$oUser = get_currentuserinfo();
			
			$upload_dir   = wp_upload_dir();
			$user_urlupload = $upload_dir['baseurl'].'/recursos/usuarios/'.$oUser->ID.'/';
			$user_dirupload = $upload_dir['basedir'].'/recursos/usuarios/'.$oUser->ID.'/';
			$files = glob($user_dirupload.'*');
			foreach($files as $file){
				if(is_file($file))
					$imagenPerfil = str_replace($user_dirupload,$user_urlupload,$file);
			}

			if(strlen($imagenPerfil) == 0){
				$imagenPerfil = RKR_PLUGIN_PATH.'/theme/img/user_default.jpg';
			}

			//echo "<pre>";print_r($upload_dir);echo "</pre>";

			$sHtml .= '
			<div class="mibne">
				<div class="row">
					<div class="col-md-2 col-sm-12">
						<div class="imagen-usuario">
							<div style="background-image:url(\''.$imagenPerfil.'\');" alt="'.__('Foto de perfil', 'traduccionrecursos').'"></div>
						</div>
						<ul class="mibne-navegacion">
							<li class="datos-usuario">
								<span class="fas fa-angle-right"></span>
								<div>
									<div class="nombre-usuario">'.$oUser->user_firstname.' '.$oUser->user_lastname.'</div>
									<div class="email-usuario">'.$oUser->user_email.'</div>
								</div>
							</li>
							<li class="url-usuario datos-ventana"><a href="javascript:void(0);"><span class="fas fa-angle-right"></span>'.__('Datos de usuario', 'traduccionrecursos').'</a></li>
							<li class="url-usuario"><span class="fas fa-angle-right"></span><a target="_blank" href="https://docs.google.com/forms/d/e/1FAIpQLScj4hEE8BWxcbsPWv2AhYPMh1bCkDt8og8Lxhm8Vrbl9-1GZg/viewform">'.__('Proponer recursos para BNEscolar', 'traduccionrecursos').'</a></li>
							<li class="url-usuario"><span class="fas fa-angle-right"></span><a href="'.wp_logout_url().'">'.__('Cerrar sesión', 'traduccionrecursos').'</a></li>
						';

							if(current_user_can('contributor') || current_user_can('administrator')) {
								$sHtml .= '<li data-ancla="#misrecursos"><a href="javascript:void(0);"><span class="fas fa-angle-right"></span>'.__('Mis recursos', 'traduccionrecursos').'</a></li>';
							}
							$sHtml .= '
							<li data-ancla="#favoritos"><a href="javascript:void(0);"><span class="fas fa-angle-right"></span>'.__('Favoritos', 'traduccionrecursos').'</a></li>
							<li data-ancla="#historial"><a href="javascript:void(0);"><span class="fas fa-angle-right"></span>'.__('Historial de visualización', 'traduccionrecursos').'</a></li>
							<li data-ancla="#colecciones"><a href="javascript:void(0);"><span class="fas fa-angle-right"></span>'.__('Colecciones', 'traduccionrecursos').'</a></li>
							<li data-ancla="#labygram"><a href="javascript:void(0);"><span class="fas fa-angle-right"></span>'.__('Labygram', 'traduccionrecursos').'</a></li>
						</ul>

					</div>
					<div class="col-md-10 col-sm-12">';
						if(current_user_can('contributor') || current_user_can('administrator')) {
							$sHtml .= '
							<div class="row">
								<div class="col-md-12">
									<h2 class="titulo" id="misrecursos">'.__('Mis recursos', 'traduccionrecursos').'</h2>
									<div class="masonry">';
									/*
									$sHtml .= '
										<div class="masonry-brick no-sombra">
											<div class="btn_lila_panel">
												<a href="'.get_site_url().'/wp-admin/admin.php?page=recursos-edit">
													<span class="fa fa-plus-circle">
														<p class="pblanco">'.__('CREAR NUEVO OBJETO', 'traduccionrecursos').'</p>
													</span>
												</a>
											</div>
										</div>';
										if(count($aTablaRecursos) > 0){
											foreach ($aTablaRecursos as $key2 => $value2) {

												$sImg = $value2['REurlimagen'];
												if(strlen($sImg) == 0) {
													$sImg = RKR_PLUGIN_PATH.'/theme/img/logoBNEpositivo.jpg';
												}
												//col-md-3 col-sm-4 col-xs-6
												$sHtml .= '<div class="masonry-brick">';
												$sHtml .= '<a alt="'.$value2['REtitulo'].'" href="'.get_site_url().'/visor/?id='.$value2['REid'].$sIdiomaRef.'">';
												$sHtml .= '<img class="detalles" src="'.$sImg.'" alt="'.__('Foto de perfil', 'traduccionrecursos').'" />';
												$sHtml .= '<span>'.$value2['REtitulo'].'</span>';
												$sHtml .= '</a>';
												$sHtml .= '<a class="editar" alt="'.__('Editar recurso', 'traduccionrecursos').'" href="'.get_site_url().'/wp-admin/admin.php?page=recursos-edit&id='.$value2['REid'].'">';
												$sHtml .= '<span class="fas fa-pencil-alt"></span></a>';
												$sHtml .= '</div>';
											}
										}
										*/
										$sHtml .= '
									</div>
									<a id="misrecursosmostrar" class="showmore" href="javascript:void(0);">'.__('Mostrar más', 'traduccionrecursos').'</a>
								</div>
							</div>';
						}
						$sHtml .= '
						<div class="row">
							<div class="col-md-12">
								<h2 class="titulo" id="favoritos">'.__('Favoritos', 'traduccionrecursos').'</h2>
								<h2 class="subtitulo" id="subfavoritos">'.__('Marca tus contenidos como favoritos, y los tendrás disponibles siempre en este espacio. Para ello, una vez que hayas iniciado sesión en Mi BNEscolar, accede a la página del recurso que quieras añadir, y marca la estrella que aparece en la parte superior derecha de la pantalla.', 'traduccionrecursos').'</h2>
								<div class="masonry">';
									/*if(count($aTablaGuardados) > 0){
										foreach ($aTablaGuardados['favorito'] as $key2 => $value2) {
											$sHtml .= '<div class="masonry-brick">';
											$sHtml .= '<a alt="'.$value2['titulo'].'" href="'.get_site_url().'/visor/?id='.$key2.$sIdiomaRef.'">';
											$sHtml .= '<img class="detalles" src="'.$value2['img'].'" alt="'.__('Foto de perfil', 'traduccionrecursos').'" />';
											$sHtml .= '<span>'.$value2['titulo'].'</span>';
											$sHtml .= '</a>';
											$sHtml .= '<a class="editar" alt="'.__('Ver recurso', 'traduccionrecursos').'" href="'.get_site_url().'/visor/?id='.$key2.$sIdiomaRef.'">';
											$sHtml .= '<span class="far fa-eye"></span></a>';
											$sHtml .= '</div>';
										}
									}*/
									$sHtml .= '
								</div>
								<a id="favmostrar" class="showmore" href="javascript:void(0);">'.__('Mostrar más', 'traduccionrecursos').'</a>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<h2 class="titulo" id="historial">'.__('Historial de visualización', 'traduccionrecursos').'</h2>
								<h2 class="subtitulo" id="subhistorial">'.__('En este espacio se te mostrarán los recursos que hayas consultado.', 'traduccionrecursos').'</h2>
								<div class="masonry">';
									/*if(count($aTablaGuardados) > 0){
										foreach ($aTablaGuardados['visto'] as $key2 => $value2) {
											$sHtml .= '<div class="masonry-brick">';
											$sHtml .= '<a alt="'.$value2['titulo'].'" href="visor/?id='.$key2.$sIdiomaRef.'">';
											$sHtml .= '<img class="detalles" src="'.$value2['img'].'" alt="'.__('Foto de perfil', 'traduccionrecursos').'" />';
											$sHtml .= '<span>'.$value2['titulo'].'</span>';
											$sHtml .= '</a>';
											$sHtml .= '<a class="editar" alt="'.__('Editar recurso', 'traduccionrecursos').'" href="'.get_site_url().'/visor/?id='.$key2.$sIdiomaRef.'">';
											$sHtml .= '<span class="far fa-eye"></span></a>';
											$sHtml .= '</div>';
										}
									}*/
									$sHtml .= '
								</div>
								<a id="vistomostrar" class="showmore" href="javascript:void(0);">'.__('Mostrar más', 'traduccionrecursos').'</a>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<h2 class="titulo" id="colecciones">
									'.__('Colecciones', 'traduccionrecursos').'
									<a class="fa fa-plus-circle nuevacoleccion" href="javascript:void(0);"><span>'.__('Crear', 'traduccionrecursos').'</span></a>
								</h2>
								<h2 class="subtitulo" id="subcolecciones">
									'.__('Crea tus propias colecciones para que puedas clasificar los recursos por temas, y que te sea más fácil su consulta. Para ello, primero crea las colecciones en este espacio, y después, en el recurso, elige la colección en la que clasificarlo, seleccionando “colecciones” en la parte superior derecha de la pantalla.', 'traduccionrecursos').'
								</h2>
								';
								if(count($aTablaColeccion) > 0){
									$tableColeccionRe = $wpdb->prefix.'bne_coleccionesusuariorecursos';
									foreach ($aTablaColeccion as $key2 => $value2) {
										
										$sHtml .= '
										<div class="colecciones-acordeon">
											<a class="acordeon-titulo" href="javascript:void(0);" title="'.$value2['CUtitulo'].'">
												<span class="no-ico">'.$value2['CUtitulo'].'</span>
												<span class="fas fa-caret-down"></span>
												<span class="fas fa-trash" data-id="'.$value2['CUid'].'"></span>
												<span class="fas fa-pencil-alt" data-id="'.$value2['CUid'].'"></span>
											</a>
										</div>
										<div>
											<div class="colecciones-acordeon-contenido">
										';
										$sHtml .= '<div class="masonry">';

										$aTablaColeccionRe = $wpdb->get_results("
										SELECT * 
										FROM $tableColeccionRe 
										INNER JOIN $table ON CRrecurso = REid 
										WHERE CRcoleccionusuario = ".$value2['CUid']."
										ORDER BY REtitulo",	ARRAY_A);
										if(count($aTablaColeccionRe) > 0){
											foreach ($aTablaColeccionRe as $key3 => $value3) {
												$sImg = $value3['REurlimagen'];
												if(strlen($sImg) == 0) {
													$sImg = RKR_PLUGIN_PATH.'/theme/img/logoBNEpositivo.jpg';
												}
												$sHtml .= '<div class="masonry-brick">';
												$sHtml .= '<a alt="'.$value3['REtitulo'].'" href="visor/?id='.$value3['REid'].$sIdiomaRef.'">';
												$sHtml .= '<img class="detalles" src="'.$sImg.'" alt="'.__('Imagen recurso', 'traduccionrecursos').'" />';
												$sHtml .= '<span>'.$value3['REtitulo'].'</span>';
												$sHtml .= '</a>';
												$sHtml .= '<a class="editar" alt="'.__('Editar recurso', 'traduccionrecursos').'" href="'.get_site_url().'/visor/?id='.$value3['REid'].$sIdiomaRef.'">';
												$sHtml .= '<span class="far fa-eye"></span></a>';
												$sHtml .= '</div>';
											}
										}
										$sHtml .= '</div>';
										$sHtml .= '</div>';
										$sHtml .= '</div>';
									}
								}
								$sHtml .= '
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<h2 class="titulo" id="labygram">
									'.__('Contenidos en Labygram', 'traduccionrecursos').'
									<a class="iralabygram" href="https://www.labygram.com" title="'.__('Conectar a Labygram', 'traduccionrecursos').'" target="_blank"><span>'.__('Conectar a Labygram', 'traduccionrecursos').'</span></a>
									<a class="iralabygram" href="'.get_site_url().'/wp-content/uploads/2020/10/Manual-BNE-LABYGRAM.pdf" title="'.__('Manual de Labygram', 'traduccionrecursos').'" target="_blank"><span>'.__('Manual de Labygram', 'traduccionrecursos').'</span></a>
								</h2>
								<h2 class="subtitulo" id="sublabygram">
									'.__('Conéctate a Labygram. Regístrate con el código "escuela-bnescolar" para poder visualizar y participar en los proyectos relacionados con el portal BNEscolar. Podrás colaborar en proyectos ya existentes o diseñar los tuyos propios, añadiendo elementos a colecciones multimedia y geolocalizando contenido. Consulta el tutorial de la herramienta disponible en la parte superior, y explora todas las opciones que te ofrece Labygram.', 'traduccionrecursos').'
								</h2>
								';

									if(isset($_SESSION['labygram_token']) && strlen($_SESSION['labygram_token']) > 0){

										//$sHtml .= getLabygramData("eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImtpZCI6Ik1EQkVOVEpETkRBMk1EQTVOVGMwTnpGQk9FUkRPVFkwTTBNNE9URXlSRVZHUVVWRFJEYzJSQSJ9.eyJlbWFpbCI6ImJnYXJjaWFAaXRpbmVyYXJpdW0uY2F0IiwiZW1haWxfdmVyaWZpZWQiOnRydWUsImdpdmVuX25hbWUiOiJCcmlhbiIsImZhbWlseV9uYW1lIjoiR2FyY8OtYSIsIm5pY2tuYW1lIjoiYmdhcmNpYSIsIm5hbWUiOiJCcmlhbiBHYXJjw61hIiwicGljdHVyZSI6Imh0dHBzOi8vbGg1Lmdvb2dsZXVzZXJjb250ZW50LmNvbS8tU3hLSTk4S2JLUHcvQUFBQUFBQUFBQUkvQUFBQUFBQUFBQUEvQUNIaTNyZERSb2VmZXYyWmJRb0hNOEQ1OEZfX0g2dk93dy9tby9waG90by5qcGciLCJnZW5kZXIiOiJtYWxlIiwibG9jYWxlIjoiY2EiLCJ1cGRhdGVkX2F0IjoiMjAxOS0wMy0xNFQwOToxNTozNS42NTdaIiwiaXNzIjoiaHR0cHM6Ly9ibmVsYWIuZXUuYXV0aDAuY29tLyIsInN1YiI6Imdvb2dsZS1vYXV0aDJ8MTAxMjY5NDAyNTM5NTQ1MTM1NjY0IiwiYXVkIjoiaDJ6UTBoNFUwWlpqN2hkdW9YeUsyNWN0OFZSSU5aM24iLCJpYXQiOjE1NTI1NTQ5MzgsImV4cCI6MTU1MjU5MDkzOH0.ndqYA9OLfO49QVyo2WwICU5yl0ctgmfQTGpdaq85FidBSyeqUMxtgHZ9gz2QqBloVraZHB4ntPS-nYhuC73fjqcY9yZOi_JDOGHJGlR8bRaK25zijJ06TNajUBtGcPFN5QkPKxxh_BtuYWwuaBL2xhN7rkOW0quNBE7wPw_2n2R1CY1SNzhjQ-dbB5JWxD4BpkRJ8qyw29ehu0rVqDjMv5JCtwS3a633wFIdrkClP7aT4Y79a1Zuk_ilk-vVG_8rH7IlUP-_c5kx2Z6kzW_4-CzmY1mSXOtgBN9zMZRsW21kRN2htVMFbcu9BxSh2BC9YqSzxKA_0jc4SL6EGbKWWA");
										$sHtml .= getLabygramData($_SESSION['labygram_token']);

									}else{
										//$sHtml .= getLabygramData("eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImtpZCI6Ik1EQkVOVEpETkRBMk1EQTVOVGMwTnpGQk9FUkRPVFkwTTBNNE9URXlSRVZHUVVWRFJEYzJSQSJ9.eyJlbWFpbCI6ImJnYXJjaWFAaXRpbmVyYXJpdW0uY2F0IiwiZW1haWxfdmVyaWZpZWQiOnRydWUsImdpdmVuX25hbWUiOiJCcmlhbiIsImZhbWlseV9uYW1lIjoiR2FyY8OtYSIsIm5pY2tuYW1lIjoiYmdhcmNpYSIsIm5hbWUiOiJCcmlhbiBHYXJjw61hIiwicGljdHVyZSI6Imh0dHBzOi8vbGg1Lmdvb2dsZXVzZXJjb250ZW50LmNvbS8tU3hLSTk4S2JLUHcvQUFBQUFBQUFBQUkvQUFBQUFBQUFBQUEvQUNIaTNyZERSb2VmZXYyWmJRb0hNOEQ1OEZfX0g2dk93dy9tby9waG90by5qcGciLCJnZW5kZXIiOiJtYWxlIiwibG9jYWxlIjoiY2EiLCJ1cGRhdGVkX2F0IjoiMjAxOS0wMy0xNFQwOToxNTozNS42NTdaIiwiaXNzIjoiaHR0cHM6Ly9ibmVsYWIuZXUuYXV0aDAuY29tLyIsInN1YiI6Imdvb2dsZS1vYXV0aDJ8MTAxMjY5NDAyNTM5NTQ1MTM1NjY0IiwiYXVkIjoiaDJ6UTBoNFUwWlpqN2hkdW9YeUsyNWN0OFZSSU5aM24iLCJpYXQiOjE1NTI1NTQ5MzgsImV4cCI6MTU1MjU5MDkzOH0.ndqYA9OLfO49QVyo2WwICU5yl0ctgmfQTGpdaq85FidBSyeqUMxtgHZ9gz2QqBloVraZHB4ntPS-nYhuC73fjqcY9yZOi_JDOGHJGlR8bRaK25zijJ06TNajUBtGcPFN5QkPKxxh_BtuYWwuaBL2xhN7rkOW0quNBE7wPw_2n2R1CY1SNzhjQ-dbB5JWxD4BpkRJ8qyw29ehu0rVqDjMv5JCtwS3a633wFIdrkClP7aT4Y79a1Zuk_ilk-vVG_8rH7IlUP-_c5kx2Z6kzW_4-CzmY1mSXOtgBN9zMZRsW21kRN2htVMFbcu9BxSh2BC9YqSzxKA_0jc4SL6EGbKWWA");
										$sHtml .= __('No está conectado a Labygram', 'traduccionrecursos');
									}

								$sHtml .= '
							</div>
						</div>
					</div>
				</div>
			</div>

			';


			echo $sHtml;

		} else {
			$url = get_site_url()."/mi-bne";
			$url = urlencode($url);
			echo '
			<div class="iniciosesion">
				<p>'.__('Accede a Mi BNEscolar y podrás disponer de un espacio personal donde organizar tus recursos favoritos y crear colecciones por temas. Además, podrás crear tus proyectos o participar en las diferentes propuestas de BNEscolar, y compartir tus creaciones con el resto de la comunidad.', 'traduccionrecursos').'</p>
				<p>'.__('¿A qué esperas para comenzar? ¡Te esperamos!', 'traduccionrecursos').'</p>
				<p><a href="'.get_site_url().'/wp-login.php?redirect_to='.$url.'&reauth=1" class="btn_pare">'.__('INICIAR SESIÓN', 'traduccionrecursos').'</a> </p>
				<img src="'.RKR_PLUGIN_PATH.'/theme/img/mano_img.png">
			</div>';
		}
		?>

		<script type="text/javascript">

			jQuery(function($) {
				var oNum = {};
				oNum.misrecursos 	= 9;
				oNum.favorito 		= 10;
				oNum.visto 			= 10;
				$(document).ready(function(){

					$('.masonry-brick > a span').truncate({
						lines: 2
					});

					$('.colecciones-acordeon').next().slideUp();

					$(".mibne-navegacion li:not(.datos-usuario, .url-usuario, .logout-usuario)").click(function() {
						var elemento = $(this);
						$([document.documentElement, document.body]).animate({
							scrollTop: $($(elemento).data('ancla')).offset().top - 40
						}, 1000);
					});
					$(".nuevacoleccion").click(function() {
						cargarDatos(0, '');
					});
					accordeonEventos();
					cargarMibne('todo');

					$("#favmostrar").click(function() {
						oNum.favorito += 10;
						cargarMibne('favorito');
					});

					$("#vistomostrar").click(function() {
						oNum.visto += 10;
						cargarMibne('visto');
					});

					$("#misrecursosmostrar").click(function() {
						oNum.misrecursos += 10;
						cargarMibne('misrecursos');
					});

					$(".url-usuario.datos-ventana").click(function() {
						cargarDatosUsuario();
					});		
					
					$(".subirImagen").unbind('click').click(function() {
						subirImagen();
					});

				});

				function accordeonEventos(){
					
					$(".colecciones-acordeon .fa-pencil-alt").unbind('click');
					$(".colecciones-acordeon .fa-trash").unbind('click');

					$(".colecciones-acordeon .fa-pencil-alt").click(function(e){
						var id = $(this).data("id");
						var titulo = $(this).parent().find('span').text().trim();
						cargarDatos(id, titulo);
						e.preventDefault();
						e.stopPropagation();
					});

					$(".colecciones-acordeon .fa-trash").click(function(e){
						var id = $(this).data("id");
						var titulo = $(this).parent().find('span').text().trim();
						eliminarColeccion(id, titulo);
						e.preventDefault();
						e.stopPropagation();
					});

					$(".colecciones-acordeon").unbind('click');
					$('.colecciones-acordeon').click(function(e){
						if($(this).find("span:not(.no-ico)").hasClass("fa-caret-up")){
							$(this).next().slideUp();
							$(this).find("span:not(.no-ico)").removeClass("fa-caret-up").addClass("fa-caret-down");
						}else{
							$(this).next().slideDown();
							$(this).find("span:not(.no-ico)").removeClass("fa-caret-down").addClass("fa-caret-up");						
						}
						return false;
					});
				}

				function cargarMibne(tipo){
					var idioma = getUrlParameter('lang');
					var data = {
						'action': 'getListaMibne',
						'tipo': tipo,
						'usuario':<?php echo $nIdUser; ?>,
						'num':oNum,
						'lang':idioma
					};

					$.ajax({
						type: "POST",
						url : "<?php echo admin_url('admin-ajax.php'); ?>",
						data : data,
						success: function (response) {
							var oDatos = JSON.parse(response);

							if(oDatos.misrecursos != undefined){
								$('#misrecursos').parent().find(".masonry").html(oDatos.misrecursos.html);
								if(oDatos.misrecursos.mas == 0){
									$("#misrecursosmostrar").remove();
								}
							}
							if(oDatos.favorito != undefined){
								$('#favoritos').parent().find(".masonry").html(oDatos.favorito.html);
								if(oDatos.favorito.mas == 0){
									$("#favmostrar").remove();
								}
							}
							if(oDatos.visto != undefined){
								$('#historial').parent().find(".masonry").html(oDatos.visto.html);
								if(oDatos.visto.mas == 0){
									$("#vistomostrar").remove();
								}
							}

							$('.masonry-brick > a span').truncate({
								lines: 2
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

				function cargarDatos(id, titulo){

					var ventana = '';
					if(id == 0){
						ventana = '<?php echo __('Crear colección', 'traduccionrecursos'); ?>';
					}else{
						ventana = '<?php echo __('Editar colección', 'traduccionrecursos'); ?>';
					}

					$.confirm({
						title: ventana,
						content: '<input autofocus="" type="text" id="input-name" value="' + titulo + '" placeholder="<?php echo __('Título', 'traduccionrecursos'); ?>" class="form-control colecciones-titulo">',
						buttons: {
							save: {
								text: '<?php echo __('Guardar', 'traduccionrecursos'); ?>',
								btnClass: 'btn-success',
								action: function(){
									var input = this.$content.find('input#input-name');
									var errorText = this.$content.find('.text-danger');
									if(!input.val().trim()){
										$.alert({
											title: "<?php echo __('Error', 'traduccionrecursos'); ?>",
											content: "<?php echo __('Rellene el campo título', 'traduccionrecursos'); ?>",
											type: 'red'
										});
										return false;
									}else{
										var tipo = 'guardar';
										var data = {
											'action': 'setColecciones',
											'id': id,
											'tipo': tipo,
											'titulo':input.val(),
											'usuario':<?php echo $nIdUser; ?>
										};

										$.ajax({
											type: "POST",
											url : "<?php echo admin_url('admin-ajax.php'); ?>",
											data : data,
											success: function (response) {
												if(id > 0){
													$('span.fa-pencil-alt[data-id="' + id + '"').parent().find('span').text(input.val());
												}else{
													location.reload();
												}
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
							},
							cancel: {
								text: '<?php echo __('Cancelar', 'traduccionrecursos'); ?>',
								action: function(){
									// do nothing.
								}
							}
						}
					});
				}

				function eliminarColeccion(id, titulo){
					$.confirm({
						title: '<?php echo __('Eliminar', 'traduccionrecursos');?>',
						content: '<?php echo __('¿Seguro que desea eliminar el siguiente elemento?', 'traduccionrecursos');?>' +
						" <br />"+ titulo,
						type: 'red',
						buttons: {
							aceptar: {
								text: '<?php echo __('Eliminar', 'traduccionrecursos');?>',
								btnClass: 'btn-danger',
								action: function(){
									var tipo = 'eliminar';
									var data = {
										'action': 'setColecciones',
										'id': id,
										'tipo': tipo
									};

									$.ajax({
										type: "POST",
										url : "<?php echo admin_url('admin-ajax.php'); ?>",
										data : data,
										success: function (response) {
											location.reload();
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
							cerrar: {
								text: 'Cancelar',
								action: function(){
									return true;
								}
							}
						}
					});
				}

				function cargarDatosUsuario(){

					var ventana = '';
					ventana = '<?php echo __('Datos de usuario', 'traduccionrecursos'); ?>';
					var idioma = getUrlParameter('lang');

					$.confirm({
						title: ventana,
						columnClass: 'col-md-6 col-md-offset-3 col-sm-12 col-xs-12',
						//content: sHtml,
						content: function () {
							var self = this;
							var data = {
								'action': 'getPerfil',
								'lang':idioma
							};
							return $.ajax({
								type: "POST",
								url : "<?php echo admin_url('admin-ajax.php'); ?>",
								data : data,
								success: function (response) {
									self.setContent(response);
								},
								failure: function (response) {
									console.log(response);
								},
								error: function (response) {
									console.log(response);
								}
							}).done(function(){
								var intervalo = setInterval(function(){
									if($('#fichero').length > 0){
										$('#fichero').on('change',function(){
											var files = $(this)[0].files;		
											upload(files);
										});
										clearInterval(intervalo);
									}
								},200);

							});
						},
						buttons: {
							save: {
								text: '<?php echo __('Guardar', 'traduccionrecursos'); ?>',
								btnClass: 'btn-success',
								action: function(){
									var email = this.$content.find('input#email');
									var nombre = this.$content.find('input#nombre');
									var apellidos = this.$content.find('input#apellidos');

									var data = {
										'action': 'setPerfil',
										'nombre': nombre.val(),
										'apellidos': apellidos.val(),
										'email':email.val()
									};

									return $.ajax({
										type: "POST",
										url : "<?php echo admin_url('admin-ajax.php'); ?>",
										data : data,
										success: function (response) {

											$('.datos-usuario > div').html(response);

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
				}

				function subirImagen(){

					$this.find('input.fichero').click();

				}

				function upload(files){
					var idioma = getUrlParameter('lang');
					var fd = new FormData();
					fd.append('data', files[0]);
					fd.append('action', 'uploadImage');
					fd.append('lang', idioma);

					$.ajax({
						type: 'POST',
						url: "<?php echo admin_url('admin-ajax.php'); ?>",
						data: fd,
						processData: false,
						contentType: false,
						xhr: function() {
							var xhr = new window.XMLHttpRequest();
							xhr.upload.addEventListener("progress", function(evt) {
								if (evt.lengthComputable) {
									var percentComplete = evt.loaded / evt.total;
									//parameters.onProgress(percentComplete);
									//console.log(percentComplete);
								}
							}, false);

							return xhr;
						}
					}).done(function(response){
						if(!response.includes("Error:")){
							$('.imagen-usuario div').css("backgroundImage","URL(" + response + ")");
							$('.error-subirimagen').html('').hide();
						}else{
							$('.error-subirimagen').html(response).show();
						}						
					});


				}

				function getUrlParameter(sParam) {
					var sPageURL = window.location.search.substring(1);
					var sURLVariables = sPageURL.split('&');
					for (var i = 0; i < sURLVariables.length; i++) 
					{
						var sParameterName = sURLVariables[i].split('=');
						if (sParameterName[0] == sParam) 
						{
							return sParameterName[1];
						}
					}
				}



			});


		</script>

		<?php

		// WordPress core after_widget hook (always include )
		echo $after_widget;
	}

}
