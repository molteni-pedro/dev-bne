<?php

if(!session_id()) {
	session_start();
}

if(!defined('ABSPATH') || empty($plugin_recursos)) exit;

//si no ha sido cargada la clase WP_List_Table la requerimos
if(!class_exists('WP_List_Table')) 
{
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

// Register the widget
require_once('class/buscador.php');
require_once('class/buscador_avanzado.php');
require_once('class/mibne.php');
require_once('class/ver_recursos.php');
require_once('class/buscador_inicio.php');
require_once('class/class.text.php');

function buscador_widget() {
	register_widget('Buscador_Widget');
}

function buscador_avanzado_widget() {
	register_widget('Buscador_Avanzado_Widget');
}

function mibne_widget() {
	register_widget('Mibne_Widget');
}

function verrecursos_widget() {
	register_widget('VerRecurso_Widget');
}

function buscador_inicio_widget() {
	register_widget('Buscador_Inicio_Widget');
}

add_action( 'widgets_init', 'buscador_widget' );
add_action( 'widgets_init', 'buscador_avanzado_widget');
add_action( 'widgets_init', 'mibne_widget');
add_action( 'widgets_init', 'verrecursos_widget');
add_action( 'widgets_init', 'buscador_inicio_widget');

add_action( 'admin_menu', 'recursos_adminMenu');
//add_action('wp_login', 'login_success');

/*function my_theme_setup(){
	//echo plugin_dir_path( __FILE__ );
	$path = plugin_dir_path( __FILE__ ).'languages';
    $result = load_theme_textdomain('traduccionrecursos', $path);
    if ( $result )
        return;

   $locale = apply_filters( 'theme_locale', get_locale(), 'traduccionrecursos' );
   //die( "Could not find $path/$locale.mo" );
   //echo "Could not find $path/$locale.mo";
}
add_action( 'after_setup_theme', 'my_theme_setup' );*/

function login_success() {

	echo '
	<script type="text/javascript">
		location.href = "index.php";
	</script>';

	wp_die();
}

function GetTableArray($sTable, $sKeyField, $sValueField, $sOrderBy = NULL, $aDefaultData = NULL, $sWhere = NULL) {
	global $wpdb;
	$aData = array();
	if (is_array($aDefaultData)) {
		$aData = $aDefaultData;
	}
	$sQuery = 'SELECT * FROM ' . $sTable;
	if (!is_null($sWhere)) {
		$sQuery .= ' WHERE ' . $sWhere;
	}
	if (!is_null($sOrderBy)) {
		$sQuery .= ' ORDER BY ' . $sOrderBy;
	}

	$rwData = $wpdb->get_results($sQuery);
	foreach ($rwData as $key => $value) {
		$aData[$value->$sKeyField] = $value->$sValueField;
	}
	return $aData;
}

function GetSelectFromArray($id, $aDatos, $selected = false, $initialized = true) {
	$sHtml = '';
	$sHtml .= '<select name="'.$id.'" id="'.$id.'" class="  form-control">';
	
	if($initialized){
		$sHtml .= '<option value="0"> - Seleccionar -</option>';
	}
	
	foreach ($aDatos as $key => $value) {
		# code...
		$sHtml .= '<option value="'.$key.'" '.(($selected == $key)?'selected':'').'>'.$value.'</option>';
	}
	$sHtml .= '</select>';
	return $sHtml;
}

function Redirect($sLocation) {
	ob_end_flush();
	echo '
	<script type="text/javascript">
		window.location = "'.$sLocation.'";
	</script>
	';
	exit();
}

function recursos_adminMenu(){

	add_menu_page('Recursos', 'Recursos', 'edit_posts', 'recursos-main', 'recursos_main'); 
	add_submenu_page('recursos-main', 'Añadir', 'Añadir', 'edit_posts', 'recursos-edit', 'recursos_edit');

	//add_submenu_page('recursos-main', 'Auxiliares', 'Auxiliares', 'edit_posts', 'recursos_aux', 'recursos_aux');

	add_submenu_page('recursos-main', 'Asignaturas', 'Asignaturas', 'edit_posts', 'asignatura', 'asignatura');
	add_submenu_page('recursos-main', 'Nivel Agregación', 'Nivel Agregación', 'edit_posts', 'nivel-agregacion', 'nivel_agregacion');
	add_submenu_page('recursos-main', 'Esquema Meta Datos', 'Esquema Meta Datos', 'edit_posts', 'esquema-meta-datos', 'esquema_meta_datos');
	add_submenu_page('recursos-main', 'Metadatos catálogo', 'Metadatos catálogo', 'edit_posts', 'esquema-meta-catalogo', 'esquema_meta_catalogo');
	add_submenu_page('recursos-main', 'Idioma', 'Idioma', 'edit_posts', 'idioma', 'idioma');
	add_submenu_page('recursos-main', 'Autores', 'Autores', 'edit_posts', 'autores', 'autores');
	add_submenu_page('recursos-main', 'Bloques', 'Bloques', 'edit_posts', 'bloques', 'bloques');
	add_submenu_page('recursos-main', 'Bloques de contenido', 'Bloques de contenido', 'edit_posts', 'bloques-contenido', 'bloques_contenido');
	add_submenu_page('recursos-main', 'Colecciones', 'Colecciones', 'edit_posts', 'colecciones', 'colecciones');
	add_submenu_page('recursos-main', 'Derechos de Autor', 'Derechos de Autor', 'edit_posts', 'derechos-autor', 'derechos_autor');
	add_submenu_page('recursos-main', 'Modalidad', 'Modalidad', 'edit_posts', 'modalidad', 'modalidad');
	add_submenu_page('recursos-main', 'Etapa', 'Etapa', 'edit_posts', 'etapa', 'etapa');
	add_submenu_page('recursos-main', 'Nivel', 'Nivel', 'edit_posts', 'nivel', 'nivel');
	add_submenu_page('recursos-main', 'Paises', 'Paises', 'edit_posts', 'pais', 'pais');
	add_submenu_page('recursos-main', 'Restricciones', 'Restricciones', 'edit_posts', 'restricciones', 'restricciones');
	add_submenu_page('recursos-main', 'Tipo de Acceso', 'Tipo de Acceso', 'edit_posts', 'tipo-acceso', 'tipo_acceso');
	add_submenu_page('recursos-main', 'Tipo de Recurso', 'Tipo de Recurso', 'edit_posts', 'tipo-recurso', 'tipo_recurso');

	add_action( 'admin_enqueue_scripts', 'enqueue_my_scripts' );
}

function enqueue_my_scripts($hook) {

	$aMenus = array(
		"recursos_page_recursos-main",
		"recursos_page_recursos-edit",
		"recursos_page_asignatura",
		"recursos_page_nivel-agregacion",
		"recursos_page_esquema-meta-datos",
		"recursos_page_esquema-meta-catalogo",
		"recursos_page_idioma",
		"recursos_page_autores",
		"recursos_page_bloques",
		"recursos_page_bloques-contenido",
		"recursos_page_colecciones",
		"recursos_page_derechos-autor",
		"recursos_page_modalidad",
		"recursos_page_etapa",
		"recursos_page_nivel",
		"recursos_page_pais",
		"recursos_page_restricciones",
		"recursos_page_tipo-acceso",
		"recursos_page_tipo-recurso",
		"toplevel_page_recursos-main",
	);

	//echo $hook;

	if (!in_array($hook, $aMenus)) {
		return;
	}
	if($hook == "toplevel_page_recursos-main"){
		echo '<link rel="stylesheet" href="'.RKR_PLUGIN_PATH.'/theme/css/recursos.css?ver=1" type="text/css" media="screen" />';
		echo '<link rel="stylesheet" href="'.RKR_PLUGIN_PATH.'/theme/css/all.css?ver=1" type="text/css" media="screen" />';
	}else{
		add_action('admin_head', 'my_theme');
	}

}

function my_theme() {

	echo '<link rel="stylesheet" href="'.RKR_PLUGIN_PATH.'/theme/css/bootstrap.min.css?ver=1" type="text/css" media="screen" />';
	echo '<link rel="stylesheet" href="'.RKR_PLUGIN_PATH.'/theme/css/recursos.css?ver=1" type="text/css" media="screen" />';
	echo '<link rel="stylesheet" href="'.RKR_PLUGIN_PATH.'/theme/css/all.css?ver=1" type="text/css" media="screen" />';
	echo '<link rel="stylesheet" href="'.RKR_PLUGIN_PATH.'/theme/css/jquery-confirm.min.css?ver=1" type="text/css" media="screen" />';
	echo '<link rel="stylesheet" href="'.RKR_PLUGIN_PATH.'/theme/css/datatables.min.css?ver=1" type="text/css" media="screen" />';

	//echo '<link rel="stylesheet" href="'.RKR_PLUGIN_PATH.'/theme/css/sb-admin-2.css" type="text/css" media="all" />';

	echo '<script src="'.RKR_PLUGIN_PATH.'/theme/js/jquery-3.3.1.min.js" type="text/javascript"></script>';
	echo '<script src="'.RKR_PLUGIN_PATH.'/theme/js/bootstrap.min.js" type="text/javascript"></script>';
	echo '<script src="'.RKR_PLUGIN_PATH.'/theme/js/tinymce/tinymce.min.js" type="text/javascript"></script>';
	echo '<script src="'.RKR_PLUGIN_PATH.'/theme/js/jquery-confirm.min.js" type="text/javascript"></script>';
	echo '<script src="'.RKR_PLUGIN_PATH.'/theme/js/datatables.min.js" type="text/javascript"></script>';
}

function my_theme_widget() {

	echo '<link rel="stylesheet" href="'.RKR_PLUGIN_PATH.'/theme/css/bootstrap.min.css?ver=1" type="text/css" media="screen" />';
	echo '<link rel="stylesheet" href="'.RKR_PLUGIN_PATH.'/theme/css/recursos.css?ver=1" type="text/css" media="screen" />';
	echo '<link rel="stylesheet" href="'.RKR_PLUGIN_PATH.'/theme/css/all.css?ver=1" type="text/css" media="screen" />';
	echo '<link rel="stylesheet" href="'.RKR_PLUGIN_PATH.'/theme/css/chosen.min.css?ver=1" type="text/css" media="screen" />';
	echo '<link rel="stylesheet" href="'.RKR_PLUGIN_PATH.'/theme/css/datatables.min.css?ver=1" type="text/css" media="screen" />';
	echo '<link rel="stylesheet" href="'.RKR_PLUGIN_PATH.'/theme/css/jquery-confirm.min.css?ver=1" type="text/css" media="screen" />';

	echo '<script src="'.RKR_PLUGIN_PATH.'/theme/js/bootstrap.min.js" type="text/javascript"></script>';
	echo '<script src="'.RKR_PLUGIN_PATH.'/theme/js/chosen.jquery.min.js" type="text/javascript"></script>';
	echo '<script src="'.RKR_PLUGIN_PATH.'/theme/js/datatables.min.js" type="text/javascript"></script>';
	echo '<script src="'.RKR_PLUGIN_PATH.'/theme/js/jquery-confirm.min.js" type="text/javascript"></script>';
	echo '<script src="'.RKR_PLUGIN_PATH.'/theme/js/truncate.min.js" type="text/javascript"></script>';
}

add_action('wp_ajax_obtener_datos', 'obtener_datos');
add_action('wp_ajax_guardar_autor', 'guardar_autor');
add_action('wp_ajax_guardar_trazabilidad', 'guardar_trazabilidad');
add_action('wp_ajax_getRecursosTrazabilidad', 'getRecursosTrazabilidad');
add_action('admin_post_export_page', 'export_page');
add_action('admin_post_nopriv_export_page', 'export_page');

function export_page() {
	global $wpdb;

	$sPathGuardar = ABSPATH . 'wp-content/plugins/recursos/plantilla/generated/';
	$sPathPlantilla = ABSPATH . 'wp-content/plugins/recursos/plantilla/html/';
	$idRecurso = $_GET['id'];
	//if(is_writable($sPathGuardar)){

		//limpiamos los ficheros antiguos
		limpiarDirectorio($sPathGuardar);
		//Copiamos la plantilla
		$sPathGuardar .= $idRecurso;
		recurse_copy($sPathPlantilla,$sPathGuardar);
		// Obtenemos la información del recurso
		$table = $wpdb->prefix . 'bne_recurso';
		$aTabla = $wpdb->get_results("
			SELECT * 
			FROM $table 
			WHERE REid =".$idRecurso."
			ORDER BY REtitulo",	ARRAY_A);
		
		$tableAutores = $wpdb->prefix.'bne_autores';
		$tableAutores2 = $wpdb->prefix.'bne_recursoautor';
		$aTablaAutores = $wpdb->get_results("
		SELECT * 
		FROM $tableAutores 
		INNER JOIN $tableAutores2 ON AUid = RAautor
		WHERE RArecurso = ".$idRecurso."
		ORDER BY AUnombre",	ARRAY_A);

		$table1 = $wpdb->prefix.'bne_nivel';
		$table2 = $wpdb->prefix.'bne_recursonivel';
		$aTablaDatosNivel = $wpdb->get_results("
		SELECT * 
		FROM $table1 
		INNER JOIN $table2 ON NIid = BNnivel
		WHERE BNrecurso = ".$idRecurso."
		ORDER BY NItitulo",	ARRAY_A);

		//Abrimos el fichero html y guardamos la información de la BD
		$linkFichero = fopen($sPathGuardar."/index.html", 'r') or die("Can't open file.");
		$sHtml = fread($linkFichero, filesize($sPathGuardar."/index.html"));
		fclose($linkFichero);

		//Generamos el contenido del Fichero
		//Titulo 
		$sHtml = str_replace('$$nombrerecurso$$', $aTabla[0]['REtitulo'], $sHtml);
		//Datos izquierda

		$sImg2 = $aTabla[0]['REurlimagen'];

		if(strlen($sImg2) == 0) {
			$sImg2 = RKR_PLUGIN_PATH.'/theme/img/logoBNEpositivo.jpg';
		}

		/*try{
			$sImg = "image.jpg";
			$content = file_get_contents($sImg2);
			$fp = fopen($sPathGuardar."/image.jpg", "w");
			fwrite($fp, $content);
			fclose($fp);
		}catch(Exception $e){
			$sImg = $sImg2;
		}*/

		$sImg = $sImg2;
		$sHtmlBody = '
		<div class="recurso-datos">
			<img src="'.$sImg.'" />
			<a href="'.$aTabla[0]['REurlregistro'].'" target="_blank"><span class="fas fa-angle-right"></span>'.__('Ver en la Biblioteca Digital Hispánica', 'traduccionrecursos').'</a>
			<a href="'.$aTabla[0]['REurlacceso'].'" target="_blank"><span class="fas fa-angle-right"></span>'.__('Ver obra', 'traduccionrecursos').'</a>
			<ul>
				<li><span>'.__('Descripción física', 'traduccionrecursos').': </span>'.$aTabla[0]['REtamanio'].'</li>
				<li><span>'.__('Fecha de publicación', 'traduccionrecursos').': </span>'.$sFecha.'</li>
				<li>
					<span>'.__('Autores', 'traduccionrecursos').'</span>';
					foreach ($aTablaAutores as $key => $value) {
						$sHtmlBody .='<div>'.$value['AUnombre'].'</div>';
					}
				$sHtmlBody .='
				</li>
			</ul>
		</div>';

		$sHtml = str_replace('$$datosizquierda$$', $sHtmlBody, $sHtml);

		//Datos centro
		$sHtmlBody = '
		<ul class="nav nav-pills">
			<li class="active"><a data-toggle="tab" href="#datos">'.__('Descripción', 'traduccionrecursos').'</a></li>
			<li><a data-toggle="tab" href="#nivel">'.__('Nivel y Asignatura', 'traduccionrecursos').'</a></li>
			<li><a data-toggle="tab" href="#bloque" alt="'.__('Bloque de contenidos', 'traduccionrecursos').'">'.__('Bloque C.', 'traduccionrecursos').'</a></li>
			<li><a data-toggle="tab" href="#contenidos" alt="'.__('Contenidos Didácticos', 'traduccionrecursos').'">'.__('Contenidos', 'traduccionrecursos').'</a></li>
			<li><a data-toggle="tab" href="#estandares">'.__('Estandares', 'traduccionrecursos').'</a></li>
			<li><a data-toggle="tab" href="#tema">'.__('Tema', 'traduccionrecursos').'</a></li>

		</ul>
		<div class="tab-content">
			<div id="datos" class="tab-pane fade in active">
				<div class="recurso-datos-descripcion">'.$aTabla[0]['REdescripcion'].'</div>
			</div>
			<div id="nivel" class="tab-pane fade">';

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
					$sAsig .= '<div class="asig-detalle">- '.$value2['AStitulo'].'</div>';
				}
				$sHtmlBody .='
					<div class="nivel-detalle">
						'.$value['NItitulo'].'
						'.$sAsig.'
					</div>
					
				';
			}

			$sHtmlBody .='
			</div>

			<div id="bloque" class="tab-pane fade">';

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
					$sBloque = "";

					foreach ($aTablaDatosBloque as $key3 => $value3) {
						$sBloque .= "<div>- ".$value3['BCtexto']." ".$value3['BCcontenido']."<div>".$value3['BCcontenidotexto']."</div></div>";
					}

					$sHtmlBody .='
						<div class="nivel-detalle">
							'.$value['NItitulo'].' - '.$value2['AStitulo'].'
							<div class="asig-detalle">
								'.$sBloque.'
							</div>
						</div>
						
					';


				}
			}

			$sHtmlBody .='
			</div>

			<div id="contenidos" class="tab-pane fade">';

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
					//Contenido
					if(strlen($value2['RAobservacion']) > 0){
						$sHtmlBody .='
							<div class="nivel-detalle">
								'.$value['NItitulo'].' - '.$value2['AStitulo'].'
								<div class="asig-detalle">
									'.$value2['RAobservacion'].'
								</div>
							</div>
							
						';										
					}

				}
			}

			$sHtmlBody .='
			</div>

			<div id="estandares" class="tab-pane fade">';

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
					//Bloque
					$table1 = $wpdb->prefix.'bne_estandares';
					$table2 = $wpdb->prefix.'bne_recursoestandares';
					$table3 = $wpdb->prefix.'bne_criterios';
					$aTablaDatosEstandar = $wpdb->get_results("
					SELECT * 
					FROM $table1 
					INNER JOIN $table2 ON ETid = REidestandar
					INNER JOIN $table3 ON CRid = ETidcriterio
					WHERE REidrecurso = ".$idRecurso." 
					ORDER BY CRcodigo,CRdescripcion,ETcodigo,ETdescripcion",	ARRAY_A);
					$sEstandar = "";
					$sCodigoCrit = "";

					foreach ($aTablaDatosEstandar as $key3 => $value3) {

						if($sCodigoCrit != $value3['CRcodigo']){

							if($sCodigoCrit != ""){
								$sEstandar .= "</div>";
							}
							$sCodigoCrit = $value3['CRcodigo'];
							$sEstandar .= "<div>".$value3['CRcodigo']." ".$value3['CRdescripcion'];
						}

						$sEstandar .= "<div>".$value3['ETcodigo']." ".$value3['ETdescripcion']."</div>";
					}

					if(count($aTablaDatosEstandar) > 0){
						$sEstandar .= "</div>";	
						$sHtmlBody .='
							<div class="nivel-detalle">
								'.$value['NItitulo'].' - '.$value2['AStitulo'].'
								<div class="asig-detalle">
									'.$sEstandar.'
								</div>
							</div>
						';
					}

				}
			}

			$sHtmlBody .='
			</div>

			<div id="tema" class="tab-pane fade">
				<div class="recurso-datos-descripcion">'.$aTabla[0]['REtema'].'</div>
			</div>
		</div>';

		$sHtml = str_replace('$$datosrecurso$$', $sHtmlBody, $sHtml);


		
		//Guardamos el contenido en el fichero
		$linkFichero = fopen($sPathGuardar."/index.html", 'w+') or die("Can't open file.");
		fwrite($linkFichero, $sHtml);
		fclose($linkFichero);
		
		$za  = new FlxZipArchive;
		if (file_exists($sPathGuardar.".zip")) {
			unlink($sPathGuardar.".zip");
		}

		$res = $za->open($sPathGuardar.".zip", ZipArchive::CREATE);
		if ($res === TRUE) {
			$za->addDirDo($sPathGuardar, "");
			$za->close();
			echo "OK";
		} else {
			echo 'No se pudo crear el archivo, inténtalo de nuevo.';
		}
		rrmdir($sPathGuardar);

	//}else{
		//echo "No tienes permiso para escribir en el directorio:".$sPathGuardar;	
	//}

	

	//Generamos la Descarga
	header('Content-Description:FileTransfer');
	header('Content-Type:application/octet-stream');
	header('Content-Disposition:attachment;filename='.Text::SanitizeText($aTabla[0]['REtitulo']).'.zip');
	header('Content-Transfer-Encoding:binary');
	header('Expires:0');
	header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
	header('Pragma:public');
	header('Content-Length:'.filesize($sPathGuardar.".zip"));
	ob_clean();
	flush();
	readfile($sPathGuardar.".zip");
	exit();

}

function limpiarDirectorio($dir){
	$files = glob($dir."*");
	$now   = time();

	foreach ($files as $file) {
		if (is_file($file)) {
			if ($now - filemtime($file) >= 60 * 60 * 24) {
				unlink($file);
			}
		}
	}
}

function recurse_copy($src, $dst) {
	$dir = opendir($src);
	@mkdir($dst);
	while (false !== ($file = readdir($dir))) {
		if (($file != '.') && ($file != '..')) {
			if (is_dir($src . '/' . $file)) {
				recurse_copy($src . '/' . $file, $dst . '/' . $file);
			} else {
				copy($src . '/' . $file, $dst . '/' . $file);
			}
		}
	}
	closedir($dir);
}

class FlxZipArchive extends ZipArchive {
	/** Add a Dir with Files and Subdirs to the archive;;;;; @param string $location Real Location;;;;  @param string $name Name in Archive;;; @author Nicolas Heimann;;;; @access private  **/
	
	public function addDir($location, $name)
	{
		//$name = str_replace('/ficheros_scorm', '', $name);
		$this->addEmptyDir($name);
		//echo $name.'<br>';
		//if ($name !== 'ficheros_scorm')
		$this->addDirDo($location, $name . '/');
		
	} // EO addDir;
	
	/**  Add Files & Dirs to archive;;;; @param string $location Real Location;  @param string $name Name in Archive;;;;;; @author Nicolas Heimann
	 * @access private   **/
	public function addDirDo($location, $name)
	{
		//$name .= '/';
		$location .= '/';
		
		// Read all Files in Dir
		$dir = opendir($location);
		while ($file = readdir($dir)) {
			
			if ($file == '.' || $file == '..')
				continue;
			// Rekursiv, If dir: FlxZipArchive::addDir(), else ::File();
			//echo $location .$file.' tipo: '.filetype( $location . $file).'<br>';
			$do = (filetype($location . $file) == 'dir') ? 'addDir' : 'addFile';
			//echo $do.'<br>';
			//echo $name.$file.'<br>';
			$this->$do($location . $file, $name . $file);
		}
	} // EO addDirDo();
}

function rrmdir($dir) {
	if (is_dir($dir)) {
		$objects = scandir($dir);
		foreach ($objects as $object) {
			if ($object != "." && $object != "..") {
				if (filetype($dir . "/" . $object) == "dir")
					rrmdir($dir . "/" . $object);
				else
					unlink($dir . "/" . $object);
			}
		}
		reset($objects);
		rmdir($dir);
	}
}

function recursos_main(){

	

	if(!class_exists('Recursos_List_Table')) 
	{
		require_once('class/recursos_list_table.php');
	}

	$recursosListTable = new Recursos_List_Table();
	$recursosListTable->prepare_items();

	//si se ha eliminado algo creamos el mensaje con las clases de wordpress
	$message = '';
	if ('delete' === $recursosListTable->current_action()) 
	{
		$message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Items eliminados: %d', 'traduccionrecursos'), 
			count($_GET['id'])) . '</p></div>';
	}
	?>
	<div class="wrap recursos">
		<div class="widget-box transparent invoice-box">
			<div class="widget-header">
				<h3 class="widget-title lighter">
					<span><?php echo __('Recursos', 'traduccionrecursos');?></span>
				</h3>
				 <div class="widget-toolbar">
					<a id="btnAceptar" tabindex="6" title="Guardar" class="fa fa-plus ico-boton30"  href="admin.php?page=recursos-edit"><span>Nuevo</span></a>
				</div>
			</div>
		</div>		
		<?php echo $message; ?>

		<form id="search-table" method="GET">
			<input type="hidden" name="page" value="<?php echo $_GET['page'] ?>"/>
			<?php
			
			if( isset($_GET['s']) ){
				$recursosListTable->prepare_items($_GET['s']);
			} else {
				$recursosListTable->prepare_items();
			}

			$recursosListTable->search_box(__('Buscar', 'traduccionrecursos'), 'search_id' ); 

			?>
			<?php $recursosListTable->display(); ?>
		</form>
	</div>
	<?php
}

function getRecursos() {
	global $wpdb;

	$busqueda = $_POST['busqueda'];
	$tipo = $_POST['tipo'];

	$sWhere = "";
	$sTablaRA = $wpdb->prefix.'bne_recursoautor';
	$sTablaAU = $wpdb->prefix.'bne_autores';
	$sTablaAS = $wpdb->prefix.'bne_asignatura';
	$sTablaAR = $wpdb->prefix.'bne_recursoasignatura';

	if($tipo == '' || $tipo == 'todos'){
		$sWhere = "(
			(REtitulo LIKE '%".$busqueda."%') 
			OR (REdescripcion LIKE '%".$busqueda."%') 
			OR (REtitulo LIKE '%".$busqueda."%') 
			OR (0 < (
					SELECT COUNT(AUid) 
					FROM ".$sTablaRA."
					INNER JOIN ".$sTablaAU." ON AUid = RAautor
					WHERE RArecurso = REid AND AUnombre LIKE '%".$busqueda."%'
				)
			)
			OR (0 < (
					SELECT COUNT(ASid) 
					FROM ".$sTablaAR."
					INNER JOIN ".$sTablaAS." ON ASid = RAasignatura
					WHERE RArecurso = REid AND AStitulo LIKE '%".$busqueda."%'
				)
			)
		) OR ('".$busqueda."' = '')
		";

	}elseif($tipo == 'titulo'){
		$sWhere = "
			(REtitulo LIKE '%".$busqueda."%') 
		";

	}elseif($tipo == 'autor'){
		$sWhere = "
			(0 < (
					SELECT COUNT(AUid) 
					FROM ".$sTablaRA."
					INNER JOIN ".$sTablaAU." ON AUid = RAautor
					WHERE RArecurso = REid AND AUnombre LIKE '%".$busqueda."%'
				)
			)
		";
	}elseif($tipo == 'materia'){
		$sWhere = "
			(0 < (
					SELECT COUNT(ASid) 
					FROM ".$sTablaAR."
					INNER JOIN ".$sTablaAS." ON ASid = RAasignatura
					WHERE RArecurso = REid AND AStitulo LIKE '%".$busqueda."%'
				)
			)
		";
	}elseif($tipo == 'descripcion'){
		$sWhere = "
		(REdescripcion LIKE '%".$busqueda."%') 
		";
	}

	//Tipo Recursos
	$table = $wpdb->prefix . 'bne_recurso';
	$aTabla = $wpdb->get_results("
		SELECT * 
		FROM $table 
		WHERE ".$sWhere."
		ORDER BY REtitulo",	ARRAY_A);
	$sHtml = '';
	
	
	foreach ($aTabla as $key => $value) {

		//$table_name = $wpdb->prefix . 'bne_autores';
		//$table_name2 = $wpdb->prefix . 'bne_recursoautor';
		//$sTablaAutores = $wpdb->get_results("SELECT * FROM $table_name INNER JOIN $table_name2 ON RAautor = AUid WHERE RArecurso = ".$value['REid']." ORDER BY AUnombre",	ARRAY_A);

		if(($key % 6) == 0){
			$sHtml .= '<div class="row">';
		}
		
		$sImg = $value['REurlimagen'];
		if(strlen($sImg) == 0) {
			$sImg = RKR_PLUGIN_PATH.'/theme/img/logoBNEpositivo.jpg';
		}

		$sHtml .= '
		<div class="col-md-2">
			<a class="buscador-item" href="'.get_site_url().'/visor/?id='.$value['REid'].'">
				<img src="'.$sImg.'" alt="" class="img-responsive wp-image-82" srcset="" width="230" height="311">
				<p class="item-titulo">'.$value['REtitulo'].'</p>
		';

		/*
		foreach ($sTablaAutores as $keyA => $valueA) {
			$sHtml .= '<p class="item-autor">'.$valueA['AUnombre'].'</p>';
		}
		*/
		
		$sHtml .= '</a></div>';

		if(($key % 6) == 5){
			$sHtml .= '</div>';
		}

	}

	if(((count($aTabla)-1) % 4) != 3 && count($aTabla) > 0){
		$sHtml .= '</div>';
	}
	echo $sHtml;

	wp_die();
}

function getBloques() {
	global $wpdb;
	
	$idNivel = "";
	$idAsignatura = "";
	$sWhere = "";

	//Recoger los niveles
	if(isset($_POST['nivel'])){
		foreach ($_POST['nivel'] as $key => $value) {
			$idNivel .= $value.",";
		}
	}
	$idNivel = rtrim($idNivel,",");
	if(strlen($idNivel) > 0){
		$sWhere .= " BCidnivel IN ($idNivel) ";
	}
	
	//Recoger las asignaturas
	if(isset($_POST['asig'])){
		foreach ($_POST['asig'] as $key => $value) {
			$idAsignatura .= $value.",";
		}
	}
	$idAsignatura = rtrim($idAsignatura,",");

	if(strlen($idAsignatura) > 0){
		if(strlen($sWhere) > 0){
			$sWhere .= " AND ";
		}
		$sWhere .= " BCidasignatura IN ($idAsignatura) ";
	}

	$table = $wpdb->prefix . 'bne_bloquecontenido';
	$aBloques = $wpdb->get_results("
		SELECT * FROM $table 
		WHERE $sWhere AND IFNULL(BCcontenidotexto,'') <> ''
		ORDER BY BCtexto, BCcontenido",	ARRAY_A);
	
	foreach ($aBloques as $key => $value) {
		echo '
		<li class="filtro-item-detalle">
			<input data-item="bloques" id="bloques['.$value['BCid'].']" name="bloques['.$value['BCid'].']" value="'.$value['BCid'].'" type="checkbox">
			<label class="noselect" for="bloques['.$value['BCid'].']">'.$value['BCtexto'].' '.$value['BCcontenido'].'</label>
		</li>
		';
	}

	echo $sHtml;
	wp_die();
}



function getListaMibne() {
	global $wpdb;
	
	if(isset($_POST['lang']) && strlen($_POST['lang']) > 0){
		$languageCode = htmlspecialchars(trim($_POST['lang']));
		do_action( 'wpml_switch_language', $languageCode);
	}

	$nIdUser = $_POST['usuario'];
	$oNum = $_POST['num'];
	//print_r($oNum);
	switch ($_POST['tipo']) {
		case 'misrecursos':
			$aDatos['misrecursos'] 	= getListaMisRecursos($oNum['misrecursos'], $nIdUser);
			break;
		case 'visto':
			$aDatos['visto'] 		= getListaVisto($oNum['visto'], $nIdUser);
			break;
		case 'favorito':
			$aDatos['favorito'] 	= getListaFavorito($oNum['favorito'], $nIdUser);
			break;
		default:
			$aDatos['favorito'] 	= getListaFavorito($oNum['favorito'], $nIdUser);
			$aDatos['visto'] 		= getListaVisto($oNum['visto'], $nIdUser);
			$aDatos['misrecursos'] 	= getListaMisRecursos($oNum['misrecursos'], $nIdUser);
			break;
	}
	echo json_encode($aDatos);

	wp_die();
}

function getListaFavorito($num, $nIdUser){
	global $wpdb;

	if(isset($_POST['lang']) && strlen($_POST['lang']) > 0){
		$sIdiomaRef = '&lang='.$_POST['lang'];
	}

	$mostrarboton = 0;
	$table = $wpdb->prefix.'bne_recurso';
	$table2 = $wpdb->prefix.'bne_recursosguardados';
	$aTabla = $wpdb->get_results("
		SELECT *,(
			SELECT count(REid) as num
			FROM $table 
			INNER JOIN $table2 ON RGrecurso = REid
			WHERE RGusuario = $nIdUser AND RGtipo like 'favorito'
		) as num
		FROM $table 
		INNER JOIN $table2 ON RGrecurso = REid
		WHERE RGusuario = $nIdUser AND RGtipo like 'favorito'
		ORDER BY RGtipo,RGfecha DESC,REtitulo
		LIMIT 0, ".$num."
		",	ARRAY_A);
	$sHtml = '';

	if(count($aTabla) > 0){
		foreach ($aTabla as $key => $value) {
			if($num < $value['num']){
				$mostrarboton = 1;
			}
			$sImg = $value['REurlimagen'];
			if(strlen($sImg) == 0) {
				$sImg = RKR_PLUGIN_PATH.'/theme/img/logoBNEpositivo.jpg';
			}
			$sHtml .= '<div class="masonry-brick">';
			$sHtml .= '<a alt="'.$value['REtitulo'].'" href="'.get_site_url().'/visor/?id='.$value['REid'].$sIdiomaRef.'">';
			$sHtml .= '
			<div class="imagen-medio">
				<img class="detalles" src="'.$sImg.'" alt="'.__('Foto recurso', 'traduccionrecursos').'" />
			</div>';
			$sHtml .= '<span>'.$value['REtitulo'].'</span>';
			$sHtml .= '</a>';
			$sHtml .= '<a class="editar" alt="'.__('Ver recurso', 'traduccionrecursos').'" href="'.get_site_url().'/visor/?id='.$value['REid'].$sIdiomaRef.'">';
			$sHtml .= '<span class="far fa-eye"></span></a>';
			$sHtml .= '</div>';
		}
	}
	$aDatos['html'] = $sHtml;
	$aDatos['mas'] 	= $mostrarboton;
	return $aDatos;
}

function getListaVisto($num, $nIdUser){
	global $wpdb;

	if(isset($_POST['lang']) && strlen($_POST['lang']) > 0){
		$sIdiomaRef = '&lang='.$_POST['lang'];
	}

	$mostrarboton = 0;
	$table = $wpdb->prefix.'bne_recurso';
	$table2 = $wpdb->prefix.'bne_recursosguardados';
	$aTabla = $wpdb->get_results("
		SELECT *,(
			SELECT count(REid) as num
			FROM $table 
			INNER JOIN $table2 ON RGrecurso = REid
			WHERE RGusuario = $nIdUser AND RGtipo like 'visto'
		) as num
		FROM $table 
		INNER JOIN $table2 ON RGrecurso = REid
		WHERE RGusuario = $nIdUser AND RGtipo like 'visto'
		ORDER BY RGtipo,RGfecha DESC,REtitulo
		LIMIT 0, ".$num."
		",	ARRAY_A);
	$sHtml = '';

	if(count($aTabla) > 0){
		foreach ($aTabla as $key => $value) {
			if($num < $value['num']){
				$mostrarboton = 1;
			}
			$sImg = $value['REurlimagen'];
			if(strlen($sImg) == 0) {
				$sImg = RKR_PLUGIN_PATH.'/theme/img/logoBNEpositivo.jpg';
			}
			$sHtml .= '<div class="masonry-brick">';
			$sHtml .= '<a alt="'.$value['REtitulo'].'" href="'.get_site_url().'/visor/?id='.$value['REid'].$sIdiomaRef.'">';
			$sHtml .= '
			<div class="imagen-medio">
				<img class="detalles" src="'.$sImg.'" alt="'.__('Foto recurso', 'traduccionrecursos').'" />
			</div>';
			$sHtml .= '<span>'.$value['REtitulo'].'</span>';
			$sHtml .= '</a>';
			$sHtml .= '<a class="editar" alt="'.__('Ver recurso', 'traduccionrecursos').'" href="'.get_site_url().'/visor/?id='.$value['REid'].$sIdiomaRef.'">';
			$sHtml .= '<span class="far fa-eye"></span></a>';
			$sHtml .= '</div>';
		}
	}
	$aDatos['html'] = $sHtml;
	$aDatos['mas'] 	= $mostrarboton;
	return $aDatos;
}

function getPerfil() {
	global $wpdb;

	if(isset($_POST['lang']) && strlen($_POST['lang']) > 0){
		$languageCode = htmlspecialchars(trim($_POST['lang']));
		do_action( 'wpml_switch_language', $languageCode);
	}

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

	$sHtml = '';
	$sHtml .= '<div class="size100">';
		$sHtml .= '<div class="row">';
			$sHtml .= '<div class="col-md-4 center">';
				$sHtml .= '<div class="imagen-usuario">';
					$sHtml .= '<div style="background-image:url(\''.$imagenPerfil.'\');" alt="'.__('Foto de perfil', 'traduccionrecursos').'"></div>';
				$sHtml .= '</div>';
				//$sHtml .= '<div class="imagen-usuario"><img class="imagen-usuario" style="background-image:url('.$imagenPerfil.');"></div>';
				$sHtml .= '
					<label class="custom-file-upload">
						<input id="fichero" type="file" accept=".jpg,.jpeg,.png"/>
						'.__('Editar imagen', 'traduccionrecursos').'
					</label>
					<label class="error-subirimagen">Error</label>
				';
			$sHtml .= '</div>';
			$sHtml .= '<div class="col-md-8">';
				$sHtml .= '<div class="form-group">';
					$sHtml .= '<label for="email">'.__('Email', 'traduccionrecursos').'</label>';
					$sHtml .= '<input id="email" type="email" class="form-control" value="'.$oUser->user_email.'"/>';
				$sHtml .= '</div>';
				$sHtml .= '<div class="form-group">';
					$sHtml .= '<label for="nombre">'.__('Nombre', 'traduccionrecursos').'</label>';
					$sHtml .= '<input type="text" class="form-control" id="nombre" value="'.$oUser->user_firstname.'">';
				$sHtml .= '</div>';
				$sHtml .= '<div class="form-group">';
					$sHtml .= '<label for="apellidos">'.__('Apellidos', 'traduccionrecursos').'</label>';
					$sHtml .= '<input type="text" class="form-control" id="apellidos" value="'.$oUser->user_lastname.'">';
				$sHtml .= '</div>';
			$sHtml .= '</div>';
		$sHtml .= '</div>';
	$sHtml .= '</div>';

	echo $sHtml;

	wp_die();
}

function setPerfil() {
	global $wpdb;
	$user_id = get_current_user_id();
	wp_update_user( array( 'ID' => $user_id, 'user_email' => $_POST['email'] ) );
	update_user_meta($user_id, 'first_name', $_POST['nombre']);
	update_user_meta($user_id, 'last_name', $_POST['apellidos']);

	$sHtml = '
	<div class="nombre-usuario">'.$_POST['nombre'].' '.$_POST['apellidos'].'</div>
	<div class="email-usuario">'.$_POST['email'].'</div>';

	echo $sHtml;

	wp_die();
}

function uploadImage(){
	global $wpdb;

	if(isset($_POST['lang']) && strlen($_POST['lang']) > 0){
		$languageCode = htmlspecialchars(trim($_POST['lang']));
		do_action('wpml_switch_language', $languageCode);
	}

	$current_user = wp_get_current_user();
	$upload_dir   = wp_upload_dir();

	if ( isset( $current_user->ID ) && ! empty( $upload_dir['basedir'] ) ) {
		$user_urlupload = $upload_dir['baseurl'].'/recursos/usuarios/'.$current_user->ID.'/';
		$user_dirupload = $upload_dir['basedir'].'/recursos/usuarios/'.$current_user->ID.'/';
		if ( ! file_exists( $user_dirupload ) ) {
			wp_mkdir_p( $user_dirupload );
		}
		$source_file = $_FILES['data']['tmp_name'];
		$destination_file = $_FILES['data']['name'];
		$ext = pathinfo($destination_file, PATHINFO_EXTENSION);
		$extAllowed = array("jpg", "jpeg", "png");
		if(in_array($ext, $extAllowed)){
			if (move_uploaded_file($source_file, $user_dirupload.$destination_file)) {
				$files = glob($user_dirupload.'*');
				foreach($files as $file){
					if(is_file($file) && strpos($file, $destination_file) === false)
						unlink($file);
				}
				echo $user_urlupload.$destination_file;
			}else{
				echo 'Error: '.__('Error al subir el fichero.', 'traduccionrecursos');
			}
		}else{
			echo 'Error: '.__('La extensión no es válida, debe ser .png, .jpg o .jpeg.', 'traduccionrecursos');
		}
	}
	wp_die();
}

function getListaMisRecursos($num, $nIdUser){
	global $wpdb;

	if(isset($_POST['lang']) && strlen($_POST['lang']) > 0){
		$sIdiomaRef = '&lang='.$_POST['lang'];
	}
	
	$mostrarboton = 0;
	$table = $wpdb->prefix.'bne_recurso';
	$aTablaRecursos = $wpdb->get_results("
		SELECT *,(
			SELECT count(REid) as num
			FROM $table 
			WHERE REidcreador = $nIdUser
		) as num
		FROM $table 
		WHERE REidcreador = $nIdUser
		ORDER BY REtitulo 
		LIMIT 0, ".$num,	ARRAY_A);

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
			if($num < $value2['num']){
				$mostrarboton = 1;
			}
			$sImg = $value2['REurlimagen'];
			if(strlen($sImg) == 0) {
				$sImg = RKR_PLUGIN_PATH.'/theme/img/logoBNEpositivo.jpg';
			}
			//col-md-3 col-sm-4 col-xs-6
			$sHtml .= '<div class="masonry-brick">';
			$sHtml .= '<a alt="'.$value2['REtitulo'].'" href="'.get_site_url().'/visor/?id='.$value2['REid'].$sIdiomaRef.'">';
			$sHtml .= '
			<div class="imagen-medio">
				<img class="detalles" src="'.$sImg.'" alt="'.__('Foto de perfil', 'traduccionrecursos').'" />
			</div>';
			$sHtml .= '<span>'.$value2['REtitulo'].'</span>';
			$sHtml .= '</a>';
			$sHtml .= '<a class="editar" alt="'.__('Editar recurso', 'traduccionrecursos').'" href="'.get_site_url().'/wp-admin/admin.php?page=recursos-edit&id='.$value2['REid'].'">';
			$sHtml .= '<span class="fas fa-pencil-alt"></span></a>';
			$sHtml .= '</div>';
		}
	}
	$aDatos['html'] = $sHtml;
	$aDatos['mas'] 	= $mostrarboton;
	return $aDatos;
}

function getRecursosAvanzados() {
	global $wpdb;

	//error_reporting(E_ALL);
	//ini_set("display_errors", 1);
	$mostrarboton = 0;
	if(isset($_POST['lang']) && strlen($_POST['lang']) > 0){
		$languageCode = htmlspecialchars(trim($_POST['lang']));
		do_action( 'wpml_switch_language', $languageCode);
	}

	$busqueda = $_POST['busqueda'];
	$numBusqueda = $_POST['numBusqueda'];

	//Eliminamos la busqueda por defecto
	//$_SESSION['busquedarecursos'] = json_encode($busqueda);


	$aTabla = getTablaRecursos($busqueda, $numBusqueda);

	$aTablaAux = $aTabla['aux'];
	$sql = $aTabla['sql'];
	$aTabla = $aTabla['recursos'];
	
	//$pila = array();
	if($numBusqueda < $aTablaAux['num']){
		$mostrarboton = 1;
	}	
	foreach ($aTabla as $key => $value) {

		//array_push($pila,$value['REid']);
		//$table_name = $wpdb->prefix . 'bne_autores';
		//$table_name2 = $wpdb->prefix . 'bne_recursoautor';
		//$sTablaAutores = $wpdb->get_results("SELECT * FROM $table_name INNER JOIN $table_name2 ON RAautor = AUid WHERE RArecurso = ".$value['REid']." ORDER BY AUnombre",	ARRAY_A);

		if(($key % 4) == 0){
			$sHtml .= '<div class="row">';
		}

		$sImg = $value['REurlimagen'];
		if(strlen($sImg) == 0) {
			$sImg = RKR_PLUGIN_PATH.'/theme/img/logoBNEpositivo.jpg';
		}

		$sIdiomaRef = '';

		if(isset($_POST['lang']) && strlen($_POST['lang']) > 0){
			$sIdiomaRef = '&lang='.$_POST['lang'];
		}

		$sHtml .= '
		<div class="col-md-3 col-xs-6">
			<a class="buscador-item" href="'.get_site_url().'/visor/?id='.$value['REid'].$sIdiomaRef.'" aria-label="'.$value['REtitulo'].'">
				<div class="imagen-medio">
					<img src="'.$sImg.'" alt="" class="img-responsive wp-image-82">
				</div>
				<p class="item-titulo" aria-label="'.__('Ver recurso', 'traduccionrecursos').'">'.$value['REtitulo'].'</p>
		';
		/*
		foreach ($sTablaAutores as $keyA => $valueA) {
			$sHtml .= '<p class="item-autor">'.$valueA['AUnombre'].'</p>';
		}
		*/
		$sHtml .= '</a></div>';

		if(($key % 4) == 3){
			$sHtml .= '</div>';
		}

	}

	if(((count($aTabla)-1) % 4) != 3 && count($aTabla) > 0){
		$sHtml .= '</div>';
	}

	if($mostrarboton == 1){
		$sHtml .= '<a class="showmore" href="javascript:void(0);">'.__('Mostrar más', 'traduccionrecursos').'</a>';
	}

	if(strlen($sHtml) == 0){
		$sHtml .= '<span class="sinresultados">'.__('Sin resultados', 'traduccionrecursos').'</span>';
	}

	//$sRecursos = implode(',',$pila);
	$aDatos['contenido'] = $sHtml;
	$aDatos['filtro'] = getFiltroFacetado2($sql, $busqueda);

	echo json_encode($aDatos);
	wp_die();
}

function getTablaRecursos($busqueda, $numBusqueda) {

	global $wpdb;
	//print_r($busqueda);

	$sWhere = "";
	$sTablaRA = $wpdb->prefix.'bne_recursoautor';
	$sTablaAU = $wpdb->prefix.'bne_autores';
	$sTablaAS = $wpdb->prefix.'bne_asignatura';
	$sTablaAR = $wpdb->prefix.'bne_recursoasignatura';
	$sTablaRT = $wpdb->prefix.'bne_recursotiporecurso';
	$sTablaRN = $wpdb->prefix.'bne_recursonivel';
	$sTablaRS = $wpdb->prefix.'bne_recursoasignatura';
	$sTablaBC = $wpdb->prefix.'bne_recursobloquecontenido';
	$sTablaNI = $wpdb->prefix.'bne_nivel';

	foreach ($busqueda['cajas'] as $keyCajas => $valueCajas) {

		$incluye = '';
		if($valueCajas['incluye'] == "si"){
			$incluye = "";
		}else{
			$incluye = " NOT ";
		}
		if($valueCajas['tipo'] == 'todos'){

			$sWhere .= "
			(
				(REtitulo ".$incluye." LIKE '%".$valueCajas['contenido']."%') 
				OR (REdescripcion ".$incluye." LIKE '%".$valueCajas['contenido']."%') 
				OR (REtitulo ".$incluye." LIKE '%".$valueCajas['contenido']."%') 
				OR (REtema ".$incluye." LIKE '%".$valueCajas['contenido']."%') 
				OR (0 < (
						SELECT COUNT(AUid) 
						FROM ".$sTablaRA."
						INNER JOIN ".$sTablaAU." ON AUid = RAautor
						WHERE RArecurso = REid AND AUnombre ".$incluye." LIKE '%".$valueCajas['contenido']."%'
					)
				)
				OR (0 < (
						SELECT COUNT(ASid) 
						FROM ".$sTablaAR."
						INNER JOIN ".$sTablaAS." ON ASid = RAasignatura
						WHERE RArecurso = REid AND AStitulo ".$incluye." LIKE '%".$valueCajas['contenido']."%'
					)
				)
			)";
		}elseif($valueCajas['tipo'] == 'titulo'){

			$sWhere .= "
				(REtitulo ".$incluye." LIKE '%".$valueCajas['contenido']."%') 
			";
		}elseif($valueCajas['tipo'] == 'tema'){

			$sWhere .= "
				(REtema ".$incluye." LIKE '%".$valueCajas['contenido']."%') 
			";
		}elseif($valueCajas['tipo'] == 'autor'){

			$sWhere .= "
				(0 < (
						SELECT COUNT(AUid) 
						FROM ".$sTablaRA."
						INNER JOIN ".$sTablaAU." ON AUid = RAautor
						WHERE RArecurso = REid AND AUnombre ".$incluye." LIKE '%".$valueCajas['contenido']."%'
					)
				)
			";
		}elseif($valueCajas['tipo'] == 'materia'){

			$sWhere .= "
				(0 < (
						SELECT COUNT(ASid) 
						FROM ".$sTablaAR."
						INNER JOIN ".$sTablaAS." ON ASid = RAasignatura
						WHERE RArecurso = REid AND AStitulo ".$incluye." LIKE '%".$valueCajas['contenido']."%'
					)
				)
			";
		} elseif($valueCajas['tipo'] == 'descripcion') {
			$sWhere .= "
			(REdescripcion ".$incluye." LIKE '%".$valueCajas['contenido']."%') 
			";
		}
		if($keyCajas < (count($busqueda['cajas']) - 1)){

			if($valueCajas['operador'] == "and"){
				$sWhere .= " AND ";
			}else{
				$sWhere .= " OR ";
			}
		}
	}

	if(strlen($sWhere) == 0){
		$sWhere .= "1 = 1";
	}

	$sSiglo = "";
	foreach ($busqueda['siglo'] as $key => $value) {
		$sSiglo = $value;
	}
	$sSiglo = rtrim($sSiglo, ",");
	if(strlen($sSiglo) > 0){
		$sWhere = "(".$sWhere.") AND (REtema LIKE '%".$sSiglo."%') ";
	}

	$sTipo = "";
	$num = 0;
	foreach ($busqueda['tipo'] as $key => $value) {
		$sTipo .= $value.",";
		$num++;
	}
	$sTipo = rtrim($sTipo, ",");
	if(strlen($sTipo) > 0){
		$sWhere = "(".$sWhere.") AND 
					(REid IN (
						SELECT RRrecurso 
						FROM ".$sTablaRT."
						WHERE RRtiporecurso IN (".$sTipo.")
						GROUP BY RRrecurso
						HAVING (COUNT(DISTINCT RRtiporecurso) >= ".$num.")
						)
					)";
					//
	}

	$sEtapa = "";
	$num = 0;
	foreach ($busqueda['etapa'] as $key => $value) {
		$sEtapa .= $value.",";
		$num++;
	}
	$sEtapa = rtrim($sEtapa, ",");
	if(strlen($sEtapa) > 0){
		$sWhere = "(".$sWhere.") AND 
					(REid IN (
						SELECT BNrecurso 
						FROM ".$sTablaRN."  INNER JOIN ".$sTablaNI." ON BNnivel = NIid
						WHERE NIetapa IN (".$sEtapa.")
						GROUP BY BNrecurso 
						
						)
					)";
					//HAVING (COUNT(DISTINCT NIetapa) >= ".$num.")
	}

	$sNivel = "";
	$num = 0;
	foreach ($busqueda['nivel'] as $key => $value) {
		$sNivel .= $value.",";
		$num++;
	}
	$sNivel = rtrim($sNivel, ",");
	if(strlen($sNivel) > 0){
		$sWhere = "(".$sWhere.") AND 
					(REid IN (
						SELECT BNrecurso 
						FROM ".$sTablaRN." 
						WHERE BNnivel IN (".$sNivel.")
						GROUP BY BNrecurso
						
						)
					)";
					//HAVING (COUNT(DISTINCT BNnivel) >= ".$num.")
	}

	$sAsig = "";
	$num = 0;
	foreach ($busqueda['asig'] as $key => $value) {
		$sAsig .= $value.",";
		$num++;
	}
	$sAsig = rtrim($sAsig, ",");
	if(strlen($sAsig) > 0){
		$sWhere = "(".$sWhere.") AND 
					(REid IN (
						SELECT RArecurso 
						FROM ".$sTablaRS."
						WHERE RAasignatura IN (".$sAsig.")
						GROUP BY RArecurso 
						
						)
					)";
					//HAVING (COUNT(DISTINCT RAasignatura) >= ".$num.")
	}

	$sBloque = "";
	$num = 0;
	foreach ($busqueda['bloques'] as $key => $value) {
		$sBloque .= $value.",";
		$num++;
	}
	$sBloque = rtrim($sBloque, ",");
	if(strlen($sBloque) > 0){
		$sWhere = "(".$sWhere.") AND 
					(REid IN (
						SELECT RBrecurso 
						FROM ".$sTablaBC."
						WHERE RBbloquecontenido IN (".$sBloque.")
						GROUP BY RBrecurso 
						
						)
					)";
					//HAVING (COUNT(DISTINCT RBbloquecontenido) >= ".$num.")
	}

	$sColeccion = "";
	foreach ($busqueda['coleccion'] as $key => $value) {
		$sColeccion .= $value.",";
	}
	$sColeccion = rtrim($sColeccion, ",");
	if(strlen($sColeccion) > 0){
		$sWhere = "(".$sWhere.") AND 
					(REidcoleccion IN (".$sColeccion."))";
	}

	$sAutores = "";
	$num = 0;
	foreach ($busqueda['autor'] as $key => $value) {
		$sAutores .= $value.",";
		$num++;
	}
	$sAutores = rtrim($sAutores, ",");
	if(strlen($sAutores) > 0){
		$sWhere = "(".$sWhere.") AND 
					(REid IN (
						SELECT RArecurso 
						FROM ".$sTablaRA."
						WHERE RAautor IN (".$sAutores.")
						GROUP BY RArecurso
						
						)
					)";
					//HAVING (COUNT(DISTINCT RAautor) >= ".$num.")
	}

	$sIdioma = "";
	foreach ($busqueda['idioma'] as $key => $value) {
		$sIdioma .= "'".$value."',";
	}
	$sIdioma = rtrim($sIdioma, ",");
	if(strlen($sIdioma) > 0){
		$sWhere = "(".$sWhere.") AND 
					(REcodigoidioma IN (".$sIdioma."))";
	}

	if(strlen($sWhere) > 0){
		$sWhere = "WHERE ".$sWhere;
	}

	//echo $sWhere;
	//print_r($busqueda);
	//Tipo Recursos
	$table = $wpdb->prefix . 'bne_recurso';
	$aTabla = $wpdb->get_results("
		SELECT *
		FROM $table 
		".$sWhere."
		ORDER BY REtitulo
 		LIMIT ".$numBusqueda,	ARRAY_A);

	$aTablaRecTotal = $wpdb->get_results("
		SELECT REid 
		FROM $table 
		".$sWhere,	ARRAY_A);

	$aTablaAux['num'] = sizeof($aTablaRecTotal);

	/*
	$aTablaAux['totalid'] = '';
	foreach ($aTablaRecTotal as $key => $value) {
		$aTablaAux['totalid'] .= $value['REid'].',';
	}
	if(strlen($aTablaAux['totalid']) > 0){
		$aTablaAux['totalid'] = trim($aTablaAux['totalid'], ',');
	}
	*/
	$aTablaTodo['recursos'] = $aTabla;
	$aTablaTodo['aux'] 		= $aTablaAux;
	$aTablaTodo['sql'] 		= "SELECT REid FROM $table ".$sWhere;
	return $aTablaTodo;
}

function getFiltroFacetado($sRecursos, $busqueda){
	global $wpdb;

	//echo $sRecursos;

	$table = $wpdb->prefix . 'bne_tiporecursos';
	$table2 = $wpdb->prefix . 'bne_recursotiporecurso';
	$aTipo = $wpdb->get_results("
		SELECT TRid, TRtitulo, Count(DISTINCT RRrecurso) AS num
		FROM $table INNER JOIN $table2 ON RRtiporecurso = TRid
		WHERE RRrecurso IN (".$sRecursos.")
		GROUP BY TRid,TRtitulo
		ORDER BY num DESC,TRtitulo",	ARRAY_A);

	$sEtapa = "";
	$num = 0;
	foreach ($busqueda['etapa'] as $key => $value) {
		$sEtapa .= $value.",";
		$num++;
	}
	$sEtapa = rtrim($sEtapa, ",");
	$sWhereEtapa = "";
	if(strlen($sEtapa) > 0){
		$sWhereEtapa = " AND ETid in (".$sEtapa.") ";
	}

	$sNivel = "";
	$num = 0;
	foreach ($busqueda['nivel'] as $key => $value) {
		$sNivel .= $value.",";
		$num++;
	}
	$sNivel = rtrim($sNivel, ",");
	$sWhereNivel = "";
	if(strlen($sNivel) > 0){
		$sWhereNivel = " AND NIid in (".$sNivel.") ";
	}

	$table = $wpdb->prefix . 'bne_etapa';
	$table2 = $wpdb->prefix . 'bne_nivel';
	$table3 = $wpdb->prefix . 'bne_recursonivel';
	$aEtapa = $wpdb->get_results("
		SELECT  ETid, ETtitulo, Count(DISTINCT BNrecurso) AS num
		FROM $table INNER JOIN $table2 ON NIetapa = ETid 
		INNER JOIN $table3 ON NIid = BNnivel 
		WHERE BNrecurso IN ($sRecursos) ".$sWhereEtapa.$sWhereNivel."
		GROUP BY ETid, ETtitulo
		ORDER BY num DESC,ETtitulo",	ARRAY_A);

	$table = $wpdb->prefix . 'bne_asignatura';
	$table2 = $wpdb->prefix . 'bne_recursoasignatura';
	$aAsignatura = $wpdb->get_results("
		SELECT ASid, AStitulo, Count(DISTINCT RArecurso) AS num 
		FROM $table INNER JOIN $table2 ON RAasignatura = ASid 
		WHERE RArecurso IN ($sRecursos) 
		GROUP BY ASid, AStitulo
		ORDER BY num DESC,AStitulo",	ARRAY_A);

	$table = $wpdb->prefix . 'bne_autores';
	$table2 = $wpdb->prefix . 'bne_recursoautor';
	$aAutores = $wpdb->get_results("
		SELECT AUid, AUnombre, Count(DISTINCT RArecurso) AS num 
		FROM $table INNER JOIN $table2 ON RAautor = AUid 
		WHERE RArecurso IN ($sRecursos) 
		GROUP BY AUid, AUnombre 
		ORDER BY num DESC,AUnombre",	ARRAY_A);

	$table = $wpdb->prefix . 'bne_idioma';
	$table2 = $wpdb->prefix . 'bne_recurso';
	$aIdiomas = $wpdb->get_results("
		SELECT IDcodigo,IDtitulo, Count(DISTINCT REid) AS num
		FROM $table INNER JOIN $table2 ON REcodigoidioma = IDcodigo 
		WHERE REid IN ($sRecursos) 
		GROUP BY IDcodigo, IDtitulo 
		ORDER BY num DESC,IDtitulo",	ARRAY_A);


	$sHtml .= '	
				<div class="row">
					<div class="col-md-12">
						<button class="avanzado-aplicarfiltro"><span class="fa fa-search"></span> '.__('Aplicar filtro', 'traduccionrecursos').'</button>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12 filtro-item-movil">
					<a class="filtro-item-titulo-movil">'.__('Filtrar resultado', 'traduccionrecursos').'<span class="fas fa-caret-down"></span></a>
				<div>
				';

	$sHtml .= '<div class="row">
				<div class="col-md-12 filtro-item">
					<a class="filtro-item-titulo" href="javascript:void(0);">'.__('Nivel educativo', 'traduccionrecursos').'<span class="fas fa-caret-down"></span></a>
					<ul>						
					';

					foreach ($aEtapa as $key => $value) {

						$sHtml .=  '
						<li class="filtro-item-detalle">
							<input data-item="etapa" id="etapa['.$value['ETid'].']" name="etapa['.$value['ETid'].']" value="'.$value['ETid'].'" type="checkbox">
							<label class="noselect" for="etapa['.$value['ETid'].']">'.$value['ETtitulo'].' ('.$value['num'].')</label>
						
							<ul>';

							$table = $wpdb->prefix . 'bne_nivel';
							$table2 = $wpdb->prefix . 'bne_recursonivel';
							$aNivel = $wpdb->get_results("
								SELECT  NIid, NItitulo, Count(*) AS num
								FROM $table INNER JOIN $table2 ON BNnivel = NIid
								WHERE BNrecurso IN ($sRecursos) 
								AND NIetapa = ".$value['ETid'].$sWhereNivel." 
								GROUP BY NIid, NItitulo
								ORDER BY num DESC,NItitulo",	ARRAY_A);

							foreach ($aNivel as $key2 => $value2) {
								$sHtml .=  '
								<li class="filtro-item-detalle">
									<input data-item="nivel" id="nivel['.$value2['NIid'].']" name="nivel['.$value2['NIid'].']" value="'.$value2['NIid'].'" type="checkbox">
									<label class="noselect" for="nivel['.$value2['NIid'].']">'.$value2['NItitulo'].' ('.$value2['num'].')</label>
								</li>
								';
							}

							$sHtml .=  '
							</ul>
						</li>
						';
					}
		$sHtml .=  	'
					</ul>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12 filtro-item">
					<a class="filtro-item-titulo" href="javascript:void(0);">'.__('Asignaturas', 'traduccionrecursos').'<span class="fas fa-caret-down"></span></a>
					<ul>';
					foreach ($aAsignatura as $key => $value) {
						$sHtml .=  '
						<li class="filtro-item-detalle">
							<input data-item="asig" id="asig['.$value['ASid'].']" name="asig['.$value['ASid'].']" value="'.$value['ASid'].'" type="checkbox">
							<label class="noselect" for="asig['.$value['ASid'].']">'.$value['AStitulo'].' ('.$value['num'].')</label>
						</li>
						';
					}
					if(sizeof($aAsignatura) > 5){
						$sHtml .=  '
						<li class="filtro-item-detalle vermas">
							<a data-num="5">'.__('Ver más', 'traduccionrecursos').' &raquo;</a>
						</li>
						';
					}

	$sHtml .=  '
					</ul>
				</div>
			</div>

			<div class="row">
				<div class="col-md-12 filtro-item">
					<a class="filtro-item-titulo" href="javascript:void(0);">'.__('Autores', 'traduccionrecursos').'<span class="fas fa-caret-down"></span></a>
					<ul>';
					foreach ($aAutores as $key => $value) {
						$sHtml .=  '
						<li class="filtro-item-detalle">
							<input data-item="autor" id="autor['.$value['AUid'].']" name="autor['.$value['AUid'].']" value="'.$value['AUid'].'" type="checkbox">
							<label class="noselect" for="autor['.$value['AUid'].']">'.$value['AUnombre'].' ('.$value['num'].')</label>
						</li>
						';
					}
					if(sizeof($aAutores) > 5){
						$sHtml .=  '
						<li class="filtro-item-detalle vermas">
							<a data-num="5">'.__('Ver más', 'traduccionrecursos').' &raquo;</a>
						</li>
						';
					}
	$sHtml .=  '
					</ul>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-12 filtro-item">
					<a class="filtro-item-titulo" href="javascript:void(0);">'.__('Idiomas', 'traduccionrecursos').'<span class="fas fa-caret-down"></span></a>
					<ul>';
					foreach ($aIdiomas as $key => $value) {
						$sHtml .=  '
						<li class="filtro-item-detalle">
							<input data-item="idioma" id="idioma['.$value['IDcodigo'].']" name="idioma['.$value['IDcodigo'].']" value="'.$value['IDcodigo'].'" type="checkbox">
							<label class="noselect" for="idioma['.$value['IDcodigo'].']">'.$value['IDtitulo'].' ('.$value['num'].')</label>
						</li>
						';
					}
					if(sizeof($aIdiomas) > 5){
						$sHtml .=  '
						<li class="filtro-item-detalle vermas">
							<a data-num="5">'.__('Ver más', 'traduccionrecursos').' &raquo;</a>
						</li>
						';
					}
	$sHtml .=  '
					</ul>
				</div>
			</div>
			

			<div class="row">
				<div class="col-md-12 filtro-item">
					<a class="filtro-item-titulo" href="javascript:void(0);">'.__('Tipos de recursos', 'traduccionrecursos').'<span class="fas fa-caret-down"></span></a>
					<ul>';
					foreach ($aTipo as $key => $value) {
						$sHtml .=  '
						<li class="filtro-item-detalle">
							<input data-item="tipo" id="tipo['.$value['TRid'].']" name="tipo['.$value['TRid'].']" value="'.$value['TRid'].'" type="checkbox">
							<label class="noselect" for="tipo['.$value['TRid'].']">'.$value['TRtitulo'].' ('.$value['num'].')</label>
						</li>
						';
					}
					if(sizeof($aTipo) > 5){
						$sHtml .=  '
						<li class="filtro-item-detalle vermas">
							<a data-num="5">'.__('Ver más', 'traduccionrecursos').' &raquo;</a>
						</li>
						';
					}
	$sHtml .=  '
					</ul>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12 filtro-item">
					<a class="filtro-item-titulo" href="javascript:void(0);">'.__('Siglos', 'traduccionrecursos').'<span class="fas fa-caret-down"></span></a>
					<ul>
						<li class="filtro-item-detalle">
							<select class="siglo combo-chosen" id="siglo" name="siglo">
								<option value="">'.__('Seleccione una opción', 'traduccionrecursos').'</option>
								<option value="Edad Media">'.__('Edad Media', 'traduccionrecursos').'</option>
								<option value="Siglo XV">'.__('Siglo XV', 'traduccionrecursos').'</option>
								<option value="Siglo XVI">'.__('Siglo XVI', 'traduccionrecursos').'</option>
								<option value="Siglo XVII">'.__('Siglo XVII', 'traduccionrecursos').'</option>
								<option value="Siglo XVIII">'.__('Siglo XVIII', 'traduccionrecursos').'</option>
								<option value="Siglo XIX">'.__('Siglo XIX', 'traduccionrecursos').'</option>
								<option value="Siglo XX">'.__('Siglo XX', 'traduccionrecursos').'</option>
							</select>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<button class="avanzado-aplicarfiltro"><span class="fa fa-search"></span> '.__('Aplicar filtro', 'traduccionrecursos').'</button>
			</div>
		</div>
	</div>
	</div>
	';

	return $sHtml;
}

function getFiltroTablasDefault(){
	global $wpdb;

	//Etapas
	$table = $wpdb->prefix . 'bne_etapa';
	$aTablasDefault['etapa'] = $wpdb->get_results("
		SELECT  ETid, ETtitulo, 0 AS num
		FROM $table 
		ORDER BY num DESC,ETtitulo", ARRAY_A);

	//Niveles
	$table = $wpdb->prefix . 'bne_nivel';
	$aTablasDefault['nivel'] = $wpdb->get_results("
		SELECT  NIid, NItitulo, NIetapa, 0 AS num
		FROM $table 
		ORDER BY num DESC,NItitulo", ARRAY_A);

	//Asignaturas
	$table = $wpdb->prefix . 'bne_asignatura';
	$aTablasDefault['asig'] = $wpdb->get_results("
		SELECT  ASid, AStitulo, 0 AS num
		FROM $table 
		ORDER BY num DESC,AStitulo", ARRAY_A);

	//Idiomas
	$table = $wpdb->prefix . 'bne_idioma';
	$aTablasDefault['idioma'] = $wpdb->get_results("
		SELECT  IDcodigo, IDtitulo, 0 AS num
		FROM $table 
		ORDER BY num DESC,IDtitulo", ARRAY_A);

	//Autores
	$table = $wpdb->prefix . 'bne_autores';
	$aTablasDefault['autor'] = $wpdb->get_results("
		SELECT  AUid, AUnombre, 0 AS num
		FROM $table 
		ORDER BY num DESC, AUnombre", ARRAY_A);

	//Tipos
	$table = $wpdb->prefix . 'bne_tiporecursos';
	$aTablasDefault['tipo'] = $wpdb->get_results("
		SELECT  TRid, TRtitulo, 0 AS num
		FROM $table 
		ORDER BY num DESC, TRtitulo", ARRAY_A);

	return $aTablasDefault;
}

function findTextByValueInArray($searchArray, $searchKey, $searchValue){
    foreach ($searchArray as $item )
    {
        if ($item[$searchKey] == $searchValue) {
            return $item;
        }
    }
    return false;
}

function getFiltroFacetado2($sRecursos, $busqueda){
	global $wpdb;

	$aTablasDefault = getFiltroTablasDefault();
	//print_r($aTablasDefault);
	$sEtapa = "";
	foreach ($busqueda['etapa'] as $key => $value) {
		$sEtapa .= $value.",";
	}
	$sEtapa = rtrim($sEtapa, ",");
	$sWhereEtapa = "";
	if(strlen($sEtapa) > 0){
		$sWhereEtapa = " AND ETid in (".$sEtapa.") ";
	}

	$sNivel = "";
	foreach ($busqueda['nivel'] as $key => $value) {
		$sNivel .= $value.",";
	}
	$sNivel = rtrim($sNivel, ",");
	$sWhereNivel = "";
	if(strlen($sNivel) > 0){
		$sWhereNivel = " AND NIid in (".$sNivel.") ";
	}

	$table = $wpdb->prefix . 'bne_etapa';
	$table2 = $wpdb->prefix . 'bne_nivel';
	$table3 = $wpdb->prefix . 'bne_recursonivel';
	$aEtapa = $wpdb->get_results("
		SELECT  ETid, ETtitulo, Count(DISTINCT BNrecurso) AS num
		FROM $table INNER JOIN $table2 ON NIetapa = ETid 
		INNER JOIN $table3 ON NIid = BNnivel 
		WHERE BNrecurso IN ($sRecursos) ".$sWhereEtapa.$sWhereNivel."
		GROUP BY ETid, ETtitulo
		ORDER BY num DESC,ETtitulo", ARRAY_A);

	$sTipo = "";
	foreach ($busqueda['tipo'] as $key => $value) {
		$sTipo .= $value.",";
	}
	$sTipo = rtrim($sTipo, ",");
	$sWhereTipo = "";
	if(strlen($sTipo) > 0){
		$sWhereTipo = " AND TRid IN (".$sTipo.") ";
	}

	$table = $wpdb->prefix . 'bne_tiporecursos';
	$table2 = $wpdb->prefix . 'bne_recursotiporecurso';
	$aTipo = $wpdb->get_results("
		SELECT TRid, TRtitulo, Count(DISTINCT RRrecurso) AS num
		FROM $table INNER JOIN $table2 ON RRtiporecurso = TRid
		WHERE RRrecurso IN (".$sRecursos.")".$sWhereTipo." 
		GROUP BY TRid,TRtitulo
		ORDER BY num DESC,TRtitulo",	ARRAY_A);

	$sAsig = "";
	foreach ($busqueda['asig'] as $key => $value) {
		$sAsig .= $value.",";
	}
	$sAsig = rtrim($sAsig, ",");
	$sWhereAsig = "";
	if(strlen($sAsig) > 0){
		$sWhereAsig = " AND ASid IN (".$sAsig.") ";
	}

	$table = $wpdb->prefix . 'bne_asignatura';
	$table2 = $wpdb->prefix . 'bne_recursoasignatura';
	$aAsignatura = $wpdb->get_results("
		SELECT ASid, AStitulo, Count(DISTINCT RArecurso) AS num 
		FROM $table INNER JOIN $table2 ON RAasignatura = ASid 
		WHERE RArecurso IN ($sRecursos)".$sWhereAsig." 
		GROUP BY ASid, AStitulo
		ORDER BY num DESC,AStitulo",	ARRAY_A);

	$sAutor = "";
	foreach ($busqueda['autor'] as $key => $value) {
		$sAutor .= $value.",";
	}
	$sAutor = rtrim($sAutor, ",");
	$sWhereAutor = "";
	if(strlen($sAutor) > 0){
		$sWhereAutor = " AND AUid IN (".$sAutor.") ";
	}

	$table = $wpdb->prefix . 'bne_autores';
	$table2 = $wpdb->prefix . 'bne_recursoautor';
	$aAutores = $wpdb->get_results("
		SELECT AUid, AUnombre, Count(DISTINCT RArecurso) AS num 
		FROM $table INNER JOIN $table2 ON RAautor = AUid 
		WHERE RArecurso IN ($sRecursos)".$sWhereAutor." 
		GROUP BY AUid, AUnombre 
		ORDER BY num DESC,AUnombre",	ARRAY_A);

	$sIdioma = "";
	foreach ($busqueda['idioma'] as $key => $value) {
		$sIdioma .= $value.",";
	}
	$sIdioma = rtrim($sIdioma, ",");
	$sWhereIdioma = "";
	if(strlen($sIdioma) > 0){
		$sWhereIdioma = " AND IDcodigo IN (".$sIdioma.") ";
	}

	$table = $wpdb->prefix . 'bne_idioma';
	$table2 = $wpdb->prefix . 'bne_recurso';
	$aIdiomas = $wpdb->get_results("
		SELECT IDcodigo,IDtitulo, Count(DISTINCT REid) AS num
		FROM $table INNER JOIN $table2 ON REcodigoidioma = IDcodigo 
		WHERE REid IN ($sRecursos)".$sWhereIdioma." 
		GROUP BY IDcodigo, IDtitulo 
		ORDER BY num DESC,IDtitulo",	ARRAY_A);


	$sHtml .= '	
				<div class="row">
					<div class="col-md-12">
						<button class="avanzado-aplicarfiltro"><span class="fa fa-check"></span> '.__('Aplicar filtro', 'traduccionrecursos').'</button>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12 filtro-item-movil">
					<a class="filtro-item-titulo-movil">'.__('Filtrar resultado', 'traduccionrecursos').'<span class="fas fa-caret-down"></span></a>
				<div>
				';

	$sHtml .= '<div class="row">
				<div class="col-md-12 filtro-item">
					<a class="filtro-item-titulo" href="javascript:void(0);">'.__('Nivel educativo', 'traduccionrecursos').'<span class="fas fa-caret-down"></span></a>
					<ul>						
					';

					foreach ($aTablasDefault['etapa'] as $key => $value) {
						$found = findTextByValueInArray($aEtapa,"ETid",$value['ETid']);
						$hidden = "";
						if(!$found && !is_array($found)){
							$hidden = "hidden";
						}
						if(!isset($found['num'])){
							$found['num'] = 0;
						}
						$sHtml .=  '
							<li class="filtro-item-detalle '.$hidden.'">
							<input data-item="etapa" id="etapa['.$value['ETid'].']" name="etapa['.$value['ETid'].']" value="'.$value['ETid'].'" type="checkbox">
							<label class="noselect" for="etapa['.$value['ETid'].']">'.$value['ETtitulo'].' ('.$found['num'].')</label>
						
							<ul>';

							$table = $wpdb->prefix . 'bne_nivel';
							$table2 = $wpdb->prefix . 'bne_recursonivel';
							$aNivel = $wpdb->get_results("
								SELECT  NIid, NItitulo, Count(*) AS num
								FROM $table INNER JOIN $table2 ON BNnivel = NIid
								WHERE BNrecurso IN ($sRecursos) 
								AND NIetapa = ".$value['ETid'].$sWhereNivel." 
								GROUP BY NIid, NItitulo
								ORDER BY num DESC,NItitulo",	ARRAY_A);

							foreach ($aTablasDefault['nivel'] as $key2 => $value2) {



								if($value['ETid'] == $value2['NIetapa']){

									$found = findTextByValueInArray($aNivel,"NIid",$value2['NIid']);
									$hidden = "";
									if(!$found && !is_array($found)){
										$hidden = "hidden";
									}
									if(!isset($found['num'])){
										$found['num'] = 0;
									}
										$sHtml .=  '
										<li class="filtro-item-detalle '.$hidden.'">
										<input data-item="nivel" id="nivel['.$value2['NIid'].']" name="nivel['.$value2['NIid'].']" value="'.$value2['NIid'].'" type="checkbox">
										<label class="noselect" for="nivel['.$value2['NIid'].']">'.$value2['NItitulo'].' ('.$found['num'].')</label>
										</li>';
								}
							}

							$sHtml .=  '
							</ul>
						</li>
						';
					}
		$sHtml .=  	'
					</ul>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12 filtro-item">
					<a class="filtro-item-titulo" href="javascript:void(0);">'.__('Asignaturas', 'traduccionrecursos').'<span class="fas fa-caret-down"></span></a>
					<ul>';
					foreach ($aTablasDefault['asig'] as $key => $value) {
						$found = findTextByValueInArray($aAsignatura, "ASid", $value['ASid']);
						$hidden = "";
						if(!$found && !is_array($found)){
							$hidden = "hidden";
						}
						if(!isset($found['num'])){
							$found['num'] = 0;
						}
						$sHtml .=  '
						<li class="filtro-item-detalle '.$hidden.'">
							<input data-item="asig" id="asig['.$value['ASid'].']" name="asig['.$value['ASid'].']" value="'.$value['ASid'].'" type="checkbox">
							<label class="noselect" for="asig['.$value['ASid'].']">'.$value['AStitulo'].' ('.$found['num'].')</label>
						</li>
						';
					}
					if(sizeof($aAsignatura) > 5){
						$sHtml .=  '
						<li class="filtro-item-detalle vermas">
							<a data-num="5">'.__('Ver más', 'traduccionrecursos').' &raquo;</a>
						</li>
						';
					}

	$sHtml .=  '
					</ul>
				</div>
			</div>

			<div class="row">
				<div class="col-md-12 filtro-item">
					<a class="filtro-item-titulo" href="javascript:void(0);">'.__('Autores', 'traduccionrecursos').'<span class="fas fa-caret-down"></span></a>
					<ul>';
					foreach ($aTablasDefault['autor'] as $key => $value) {
						$found = findTextByValueInArray($aAutores, "AUid", $value['AUid']);
						$hidden = "";
						if(!$found && !is_array($found)){
							$hidden = "hidden";
						}
						if(!isset($found['num'])){
							$found['num'] = 0;
						}
						$sHtml .=  '
						<li class="filtro-item-detalle '.$hidden.'">
							<input data-item="autor" id="autor['.$value['AUid'].']" name="autor['.$value['AUid'].']" value="'.$value['AUid'].'" type="checkbox">
							<label class="noselect" for="autor['.$value['AUid'].']">'.$value['AUnombre'].' ('.$found['num'].')</label>
						</li>
						';
					}
					if(sizeof($aAutores) > 5){
						$sHtml .=  '
						<li class="filtro-item-detalle vermas">
							<a data-num="5">'.__('Ver más', 'traduccionrecursos').' &raquo;</a>
						</li>
						';
					}
	$sHtml .=  '
					</ul>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-12 filtro-item">
					<a class="filtro-item-titulo" href="javascript:void(0);">'.__('Idiomas', 'traduccionrecursos').'<span class="fas fa-caret-down"></span></a>
					<ul>';
					foreach ($aTablasDefault['idioma'] as $key => $value) {
						$found = findTextByValueInArray($aIdiomas,"IDcodigo",$value['IDcodigo']);
						$hidden = "";
						if(!$found && !is_array($found)){
							$hidden = "hidden";
						}
						if(!isset($found['num'])){
							$found['num'] = 0;
						}
						$sHtml .=  '
						<li class="filtro-item-detalle '.$hidden.'">
							<input data-item="idioma" id="idioma['.$value['IDcodigo'].']" name="idioma['.$value['IDcodigo'].']" value="'.$value['IDcodigo'].'" type="checkbox">
							<label class="noselect" for="idioma['.$value['IDcodigo'].']">'.$value['IDtitulo'].' ('.$found['num'].')</label>
						</li>
						';
					}
					if(sizeof($aIdiomas) > 5){
						$sHtml .=  '
						<li class="filtro-item-detalle vermas">
							<a data-num="5">'.__('Ver más', 'traduccionrecursos').' &raquo;</a>
						</li>
						';
					}
	$sHtml .=  '
					</ul>
				</div>
			</div>
			

			<div class="row">
				<div class="col-md-12 filtro-item">
					<a class="filtro-item-titulo" href="javascript:void(0);">'.__('Tipos de recursos', 'traduccionrecursos').'<span class="fas fa-caret-down"></span></a>
					<ul>';
					foreach ($aTablasDefault['tipo'] as $key => $value) {
						$found = findTextByValueInArray($aTipo, "TRid", $value['TRid']);
						$hidden = "";
						if(!$found && !is_array($found)){
							$hidden = "hidden";
						}
						if(!isset($found['num'])){
							$found['num'] = 0;
						}
						$sHtml .=  '
						<li class="filtro-item-detalle '.$hidden.'">
							<input data-item="tipo" id="tipo['.$value['TRid'].']" name="tipo['.$value['TRid'].']" value="'.$value['TRid'].'" type="checkbox">
							<label class="noselect" for="tipo['.$value['TRid'].']">'.$value['TRtitulo'].' ('.$found['num'].')</label>
						</li>
						';
					}
					if(sizeof($aTipo) > 5){
						$sHtml .=  '
						<li class="filtro-item-detalle vermas">
							<a data-num="5">'.__('Ver más', 'traduccionrecursos').' &raquo;</a>
						</li>
						';
					}
	$sHtml .=  '
					</ul>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12 filtro-item">
					<a class="filtro-item-titulo" href="javascript:void(0);">'.__('Siglos', 'traduccionrecursos').'<span class="fas fa-caret-down"></span></a>
					<ul>
						<li class="filtro-item-detalle">
							<select class="siglo combo-chosen" id="siglo" name="siglo">
								<option value="">'.__('Seleccione una opción', 'traduccionrecursos').'</option>
								<option value="Edad Media">'.__('Edad Media', 'traduccionrecursos').'</option>
								<option value="Siglo XV">'.__('Siglo XV', 'traduccionrecursos').'</option>
								<option value="Siglo XVI">'.__('Siglo XVI', 'traduccionrecursos').'</option>
								<option value="Siglo XVII">'.__('Siglo XVII', 'traduccionrecursos').'</option>
								<option value="Siglo XVIII">'.__('Siglo XVIII', 'traduccionrecursos').'</option>
								<option value="Siglo XIX">'.__('Siglo XIX', 'traduccionrecursos').'</option>
								<option value="Siglo XX">'.__('Siglo XX', 'traduccionrecursos').'</option>
							</select>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<button class="avanzado-aplicarfiltro"><span class="fa fa-check"></span> '.__('Aplicar filtro', 'traduccionrecursos').'</button>
			</div>
		</div>
	</div>
	</div>
	';

	return $sHtml;
}

function setColecciones(){
	global $wpdb;
	$table = $wpdb->prefix.'bne_coleccionesusuario';
	if($_POST['tipo'] == 'guardar'){

		$data = array(
			'CUtitulo' => $_POST['titulo'],
			'CUusuario' => $_POST['usuario'],
		);
		$format = array(
			'%s',
			'%d',
		);
		if($_POST['id'] > 0){
			$data['CUid'] = $_POST['id'];
			$format[] = '%d';
		}
		$wpdb->replace($table, $data, $format);

	}elseif($_POST['tipo'] == 'guardarusuario'){
		//print_r($_POST['colecciones']);
		$table = $wpdb->prefix.'bne_coleccionesusuariorecursos';
		foreach ($_POST['colecciones'] as $key => $value) {
			if($value['active'] == 1){
				$data = array(
					'CRcoleccionusuario' => $value['id'],
					'CRrecurso' => $_POST['recurso'],
				);
				$format = array(
					'%d',
					'%d',
				);
				$wpdb->replace($table, $data, $format);
			}else{
				$wpdb->query("
					DELETE FROM $table 
					WHERE CRcoleccionusuario = ".$value['id']." AND CRrecurso = ".$_POST['recurso']);
			}
		}
		/*
		$data = array(
			'CUtitulo' => $_POST['titulo'],
			'CUusuario' => $_POST['usuario'],
		);
		$format = array(
			'%d',
			'%d',
		);
		$wpdb->replace($table, $data, $format);*/

	}elseif($_POST['tipo'] == 'eliminar'){
		$table = $wpdb->prefix.'bne_coleccionesusuario';
		$wpdb->query("
			DELETE FROM $table 
			WHERE CUid = ".$_POST['id']);
		$table = $wpdb->prefix.'bne_coleccionesusuariorecursos';
		$wpdb->query("
			DELETE FROM $table 
			WHERE CRcoleccionusuario = ".$_POST['id']);
	}

	wp_die();
}

function getColecciones(){
	global $wpdb;
	$resultado = '';
	if($_POST['marcador'] == 1){
		$resultado = htmlColecciones($_POST['usuario'], $_POST['recurso']);
	}else{

	}
	echo $resultado;
	wp_die();
}

function htmlColecciones($nIdUser, $recurso){
	global $wpdb;

	$tableColeccion = $wpdb->prefix.'bne_coleccionesusuario';
	$tableColeccionRe = $wpdb->prefix.'bne_coleccionesusuariorecursos';
	$aTablaColeccion = $wpdb->get_results("
	SELECT * ,(
		SELECT COUNT(*)
		FROM $tableColeccionRe
		WHERE CRcoleccionusuario = CUid AND CRrecurso = $recurso
	) AS numRecurso
	FROM $tableColeccion 
	WHERE CUusuario = $nIdUser
	ORDER BY CUtitulo",	ARRAY_A);
	$sHtml = "";
	foreach ($aTablaColeccion as $key => $value) {
		$sHtml .= '<div class="colecciones-lista '.(($value['numRecurso'] > 0)?'active':'').'" data-coleccion="'.$value['CUid'].'">'.$value['CUtitulo'].'<span class="far fa-check-circle"></span></div>';
	}
	return $sHtml;
}

function getRecursosBusqueda() {
	global $wpdb;



	wp_die();
}

function recursos_edit(){

	

	if(!class_exists('Recursos_List_Table')) 
	{
		require_once('class/nivel_list_table.php');
	}

	if(!class_exists('Asignatura_List_Table')) 
	{
		require_once('class/asignatura_list_table.php');
	}

	if(!class_exists('Bloque_List_Table')) 
	{
		require_once('class/bloque_list_table.php');
	}

	if(!class_exists('Criterios_List_Table')) 
	{
		require_once('class/criterios_list_table.php');
	}

	require_once( 'recursos_editar.php' );
}

function nivel_agregacion(){
	
	require_once( 'auxiliar/nivel_agregacion.php' );
}

function esquema_meta_datos(){
	
	require_once( 'auxiliar/esquema_meta_datos.php' );
}

function esquema_meta_catalogo(){
	
	require_once( 'auxiliar/esquema_meta_catalogo.php' );
}

function idioma(){
	
	require_once( 'auxiliar/idioma.php' );
}

function autores(){
	
	require_once( 'auxiliar/autores.php' );
}

function bloques(){
	
	require_once( 'auxiliar/bloques.php' );
}

function bloques_contenido(){
	
	require_once( 'auxiliar/bloques_contenido.php' );
}

function colecciones(){
	
	require_once( 'auxiliar/colecciones.php' );
}

function derechos_autor(){
	
	require_once( 'auxiliar/derechos_autor.php' );
}

function modalidad(){
	
	require_once( 'auxiliar/modalidad.php' );
}

function etapa(){
	
	require_once( 'auxiliar/etapa.php' );
}

function nivel(){
	
	require_once( 'auxiliar/nivel.php' );
}

function pais(){
	
	require_once( 'auxiliar/pais.php' );
}

function restricciones(){
	
	require_once( 'auxiliar/restricciones.php' );
}

function tipo_acceso(){
	
	require_once( 'auxiliar/tipo_acceso.php' );
}

function tipo_recurso(){
	
	require_once( 'auxiliar/tipo_recurso.php' );
}

function asignatura(){
	
	require_once( 'auxiliar/asignatura.php' );
}

function guardar_autor() {
	global $wpdb; // this is how you get access to the database
	$table = $wpdb->prefix.'bne_recursoautor';
	if($_POST['tipo'] == 'guardar'){
		$data = array(
			'RAautor' => $_POST['autor'],
			'RArecurso' => $_POST['recurso'],
		);
		$format = array(
			'%d',
			'%d',
		);
		$wpdb->replace($table, $data, $format);
	}elseif($_POST['tipo'] == 'eliminar'){
		$wpdb->query("DELETE FROM $table WHERE RAautor = ".$_POST['autor']." AND RArecurso = ".$_POST['recurso']);
	}

	wp_die();
}

function guardar_trazabilidad() {
	global $wpdb; // this is how you get access to the database
	$table = $wpdb->prefix.'bne_recursohijos';
	if($_POST['tipo'] == 'guardar'){
		foreach ($_POST['hijos'] as $key => $value) {
			$data = array(
				'RRrecursohijo' => $value,
				'RRrecurso' => $_POST['recurso'],
			);
			$format = array(
				'%d',
				'%d',
			);
			$wpdb->replace($table, $data, $format);
		}

		$table_name = $wpdb->prefix . 'bne_recurso';
		$table_name2 = $wpdb->prefix . 'bne_recursohijos';
		$table_name3 = $wpdb->prefix . 'bne_colecciones';
		$aTablaTrazabilidad = $wpdb->get_results("
			SELECT * FROM $table_name 
			INNER JOIN $table_name2 ON RRrecursohijo = REid 
			LEFT OUTER JOIN $table_name3 ON REcoleccion = COid 
			WHERE RRrecurso = ".$_POST['recurso']." ORDER BY REtitulo
		", ARRAY_A
		);
		?>
			<?php foreach ($aTablaTrazabilidad as $rowDatos): array_map('htmlentities', $rowDatos); ?>
					<tr>
						<td>
							<a id="eliminarTrazabilidad" data-id="<?php echo  $rowDatos['REid']; ?>" title="<?php echo __('Eliminar', 'traduccionrecursos');?>" class="fa fa-trash-alt bigger-180" href="javascript:{}"></a>
						</td>
						<td><?php echo  $rowDatos['REtitulo']; ?></td>
						<td><?php echo  $rowDatos['REpublicacionperiodo1']; ?></td>
						<td><?php echo  $rowDatos['COtitulo']; ?></td>
					</tr>
				<?php endforeach; ?>
		<?php

	}elseif($_POST['tipo'] == 'eliminar'){
		$wpdb->query("DELETE FROM $table WHERE RRrecursohijo = ".$_POST['hijo']." AND RRrecurso = ".$_POST['recurso']);
	}

	wp_die();
}

function getRecursosTrazabilidad() {
	global $wpdb; // this is how you get access to the database
	$table 	= $wpdb->prefix.'bne_recurso';
	$table2 	= $wpdb->prefix.'bne_colecciones';
	$rows 	= $wpdb->get_results("
		SELECT * FROM $table 
		LEFT OUTER JOIN $table2 ON REcoleccion = COid ORDER BY REtitulo", ARRAY_A);

	$aJson = ' { "data":[';
	//print_r($rows);
	foreach ($rows as $key => $value) {
		
		$aJson .= '
				[
					"<input value=\"'.$value['REid'].'\" type=\"checkbox\" class=\"checkRecurso\" />",
					"'.str_replace('"', '\"', str_replace('\\', '',$value['REtitulo'])).'",
					"'.str_replace('"', '\"', str_replace('\\', '',$value['REpublicacionperiodo1'])).'",
					"'.str_replace('"', '\"', str_replace('\\', '',$value['COtitulo'])).'"
				],';

	}
	$aJson = rtrim($aJson,",");
	$aJson .= ' ]}';
	echo $aJson;
	wp_die();
}

function obtener_datos() {
	global $wpdb; // this is how you get access to the database
	$sHtml = "";
	$url = $_POST['url'];
	if(isset($url) && strlen($url) > 0){
		 $sHtml = file_get_contents($url);
	}
	$aDatos = array();
	if(strlen($sHtml) > 0) {

		$aDatos['titulo'] = getTitulo($sHtml);
		$aDatos['tamanio'] = getDatos($sHtml,"Descripción física");
		$aDatos['descripcion'] = getDatos($sHtml,"Descripción y notas");
		$aDatos['urlImage'] = getUrlImage($sHtml);
		$aDatos['urlAcceso'] = getUrlAcceso($url);
		$aFecha = getFecha($sHtml);
		$aDatos['fecha1'] = $aFecha[0];
		$aDatos['fecha2'] = $aFecha[1];
		$aDatos['coleccion'] = getColeccion($sHtml);
		$aDatos['autores'] = getAutores($sHtml);
	}
	echo json_encode($aDatos);
	wp_die(); // this is required to terminate immediately and return a proper response
}

function getUrlAcceso($url){

	$url = str_replace("http://bdh.bne.es/bnesearch/detalle/bdh", "http://bdh-rd.bne.es/viewer.vm?id=", $url)."&page=1";

	return $url;
}

function getTitulo($sHtml){
	$sPatron = '<meta property="og:title" content="';
	$nPosition = strpos($sHtml, $sPatron);
	$sValor = '';
	if($nPosition > 0){
		$nPosition += strlen($sPatron);
		$nPositionFinal = strpos($sHtml, '"/>',$nPosition);
		$sValor = substr($sHtml, $nPosition,$nPositionFinal - $nPosition);
	}
	return $sValor;
}

function getDatos($sHtml, $sCampo){
	//$sCAMPO = "Descripción física";
	$sPatron = '<div class="valor">';
	$nPosition = strpos($sHtml, $sCampo);
	$sValor = '';
	if($nPosition > 0){
		$nPosition = strpos($sHtml, $sPatron, $nPosition);
		$sValor = trim(substr(
			$sHtml, 
			$nPosition + strlen($sPatron), 
			strpos($sHtml, '</div>',$nPosition) - $nPosition
		));

	}

	if(strlen($sValor) > 0){

		$sValor = str_replace("\t", "", $sValor);
		$sValor = str_replace("\r\n", "", $sValor);
		$sValor = str_replace("<br/>", "\n\r", $sValor);
		$sValor = str_replace("</div>", "", $sValor);
		$sValor = str_replace("  ", "", $sValor);

	}

	return $sValor;
}

function getUrlImage($sHtml){
	//$sCampo = "<!-- combine=CONTROL -->";
	$sCampo = '<div class="thumbnail">';
	$sPatron = '<a class="identificador"';

	$sImagen = '';	$nPosition = strpos($sHtml, $sCampo);
	if($nPosition > 0){

		$nPosition = strpos($sHtml, $sPatron, $nPosition);
		if($nPosition > 0){
			$nPositionFinal = strpos($sHtml, '</a>',$nPosition);
			$sImagen = trim(substr(
				$sHtml, 
				$nPosition + strlen($sPatron), 
				$nPositionFinal
			));
			$nPosition = strpos($sImagen, '<img') + strlen('<img');
			$nPosition = strpos($sImagen, 'src="', $nPosition) + strlen('src="');
			if($nPosition > 0){
				$nPositionFinal = strpos($sImagen, '"', $nPosition);
				$sImagen = substr($sImagen, $nPosition, $nPositionFinal - $nPosition);
				$sImagen = html_entity_decode($sImagen);
			}else{
				$sImagen = '';
			}
		}
	}

	return $sImagen;
}

function getFecha($sHtml){
	$sCampo = "Fecha";
	$sPatron = '<div class="valor">';
	$nPosition = strpos($sHtml, $sCampo);
	$sValor = '';
	if($nPosition > 0){
		$nPosition = strpos($sHtml, $sPatron, $nPosition);
		$sValor = trim(substr(
			$sHtml, 
			$nPosition + strlen($sPatron), 
			strpos($sHtml, '</div>',$nPosition) - $nPosition
		));

	}

	if(strlen($sValor) > 0){

		$sValor = str_replace("\t", "", $sValor);
		$sValor = str_replace("\r\n", "", $sValor);
		$sValor = str_replace("<br/>", "\n\r", $sValor);
		$sValor = str_replace("</div>", "", $sValor);
		$sValor = str_replace("  ", "", $sValor);

	}
	$sValor = str_replace("?", "", $sValor);
	if(strpos($sValor, "entre") !== false && strpos($sValor, "y") !== false ){
		$sValor = str_replace("entre", "", $sValor);
		$aFecha = explode("y", $sValor);
	}else{
		$aFecha[0] = $sValor;
		$aFecha[1] = "";
	}

	
	return $aFecha;
}

function getColeccion($sHtml){

	global $wpdb;

	$sCampo = "Colecciones relacionadas";
	$sPatron = '<div class="valor">';
	$nPosition = strpos($sHtml, $sCampo);
	$id = '';
	$sValor = '';
	if($nPosition > 0){
		$nPosition = strpos($sHtml, $sPatron, $nPosition);
		$nPosition = strpos($sHtml, "-->", $nPosition) + strlen("-->");
		$sValor = trim(substr(
			$sHtml, 
			$nPosition, 
			strpos($sHtml, '</div>',$nPosition) - $nPosition
		));

	}

	if(strlen($sValor) > 0){

		$sValor = str_replace("\t", "", $sValor);
		$sValor = str_replace("\r\n", "", $sValor);
		$sValor = str_replace("<br/>", "\n\r", $sValor);
		$sValor = str_replace("</div>", "", $sValor);
		$sValor = str_replace("  ", "", $sValor);

	}

	if(strlen($sValor) > 0){
		$table = $wpdb->prefix . 'bne_colecciones';
		$aTabla = $wpdb->get_results("SELECT * FROM $table WHERE COtitulo LIKE '".$sValor."'",	ARRAY_A);

		if(count($aTabla) == 0){
			$data = array(
				'COtitulo' => $sValor,
			);
			$format = array(
				'%s',
			);
			$wpdb->replace($table, $data, $format);
			$id = $wpdb->insert_id;

		}else{
			$id = $aTabla[0]["COid"];
		}

	}

	$aColecion["id"] 	= $id;
	$aColecion["valor"]	= $sValor;

	return $aColecion;
}

function getAutores($sHtml){

	global $wpdb;

	$sCampo = "Autor";
	$sPatron = '<div class="valor">';
	$sPatron2 = '<h2 class="valor">';
	$nPosition = strpos($sHtml, $sPatron2);
	//$nPosition = strpos($sHtml, $sCampo, $nPosition);
	$id = '';
	$sValor = '';
	if($nPosition > 0){
		//$nPosition = strpos($sHtml, $sPatron2, $nPosition) + strlen($sPatron2);
		$i=0;
		while($nPosition = strpos($sHtml, 'visor=">', $nPosition)){
			
			$nPosition += strlen('visor=">');
			$sValor = trim(substr(
				$sHtml, 
				$nPosition, 
				strpos($sHtml, '</a>',$nPosition) - $nPosition
			));

			$nPosition = strpos($sHtml, '</a>',$nPosition);

			if(strlen($sValor) > 0){
				$sValor = str_replace("\t", "", $sValor);
				$sValor = str_replace("\r\n", "", $sValor);
				$sValor = str_replace("<br/>", "\n\r", $sValor);
				$sValor = str_replace("</div>", "", $sValor);
				$sValor = str_replace("  ", "", $sValor);
			}

			if(strlen($sValor) > 0){
				$table = $wpdb->prefix . 'bne_autores';
				$aTabla = $wpdb->get_results("SELECT * FROM $table WHERE AUnombre LIKE '".$sValor."'",	ARRAY_A);

				if(count($aTabla) == 0){
					$data = array(
						'AUnombre' => $sValor,
					);
					$format = array(
						'%s',
					);
					$wpdb->replace($table, $data, $format);
					$id = $wpdb->insert_id;

				}else{
					$id = $aTabla[0]["AUid"];
				}

			}

			$aAutor[$i]["id"] 	= $id;
			//$aAutor[$i]["valor"]	= $sValor;

			$i++;
		}
	}

	return $aAutor;
}