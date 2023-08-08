<?php

// The widget class
class Buscador_Avanzado_Widget extends WP_Widget {

	// Main constructor
	public function __construct() {
		parent::__construct(
			'buscador_avanzado_widget',
			__( 'Buscador Avanzado', 'traduccionrecursos'),
			array(
				'customize_selective_refresh' => true,
			)
		);

		add_action('wp_head', 'my_theme_widget');
		add_action( 'wp_ajax_getRecursosAvanzados', 'getRecursosAvanzados');
		add_action( 'wp_ajax_nopriv_getRecursosAvanzados', 'getRecursosAvanzados');
		add_action( 'wp_ajax_getBloques', 'getBloques');
		add_action( 'wp_ajax_nopriv_getBloques', 'getBloques');
		//add_action( 'wp_ajax_getFiltroFacetado', 'getFiltroFacetado' );
		//add_action( 'wp_ajax_nopriv_getFiltroFacetado', 'getFiltroFacetado' );
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

		$table = $wpdb->prefix . 'bne_tiporecursos';
		$aTipo = $wpdb->get_results("SELECT * FROM $table ORDER BY TRtitulo",	ARRAY_A);

		$table = $wpdb->prefix . 'bne_etapa';
		$aEtapa = $wpdb->get_results("SELECT * FROM $table ORDER BY ETtitulo",	ARRAY_A);


		$table = $wpdb->prefix . 'bne_asignatura';
		$aAsignatura = $wpdb->get_results("SELECT * FROM $table ORDER BY AStitulo",	ARRAY_A);

		/*
		$table = $wpdb->prefix . 'bne_colecciones';
		$aColecciones = $wpdb->get_results("SELECT * FROM $table ORDER BY COtitulo",	ARRAY_A);
		*/
		
		$table = $wpdb->prefix . 'bne_autores';
		$aAutores = $wpdb->get_results("SELECT * FROM $table ORDER BY AUnombre",	ARRAY_A);

		$table = $wpdb->prefix . 'bne_idioma';
		$aIdiomas = $wpdb->get_results("SELECT * FROM $table ORDER BY IDtitulo",	ARRAY_A);



		$sHtmlBuscador = '
		<div class="cajon_buscar-widget">
			<label for="buscar_tipo-widget">
				<select name="buscar_tipo-widget" id="buscar_tipo-widget" class="buscar_tipo-widget combo-chosen">
					<option value="todos">'.__('Todos los campos', 'traduccionrecursos').'</option>
					<option value="titulo">'.__('Título', 'traduccionrecursos').'</option>
					<option value="autor">'.__('Autor', 'traduccionrecursos').'</option>
					<option value="tema">'.__('Tema', 'traduccionrecursos').'</option>
				</select>
			</label>
			<label for="buscar_incluye-widget">
				<select name="buscar_incluye-widget" id="buscar_incluye-widget" class="buscar_incluye-widget combo-chosen">
					<option value="si">'.__('Incluye', 'traduccionrecursos').'</option>
					<option value="no">'.__('Excluye', 'traduccionrecursos').'</option>
				</select>
			</label>
			<input class="buscar_avanzado-widget" placeholder="'.__('Buscar contenido', 'traduccionrecursos').'"  aria-label="'.__('Escriba aquí lo que desea buscar', 'traduccionrecursos').'" />
			<label for="buscar_operador-widget">
				<select name="buscar_operador-widget" id="buscar_operador-widget" class="buscar_operador-widget combo-chosen">
					<option value="and">'.__('Y', 'traduccionrecursos').'</option>
					<option value="or">'.__('O', 'traduccionrecursos').'</option>
				</select>
			</label>
		</div>';

		$sHtmlBuscador = preg_replace("/\s+/", " ", $sHtmlBuscador);
		
		$sHtmlBuscadorCargado = $sHtmlBuscador;

		if(isset($_POST['buscar_basico-widget'])){
			$valor = '<input value="'.$_POST['buscar_basico-widget'].'"';
			$sHtmlBuscadorCargado = str_replace("<input", $valor, $sHtmlBuscadorCargado);
			$sHtmlBuscadorCargado = str_replace('<option value="'.$_POST['buscar_tipo-widget'].'"', '<option value="'.$_POST['buscar_tipo-widget'].'" selected', $sHtmlBuscadorCargado);
		}
		echo '
		<div class="cabecera_buscador_avanzado_grande-widget">
			<fieldset>
				<legend>
					<h1>
						'.__('Explora nuestros recursos digitales', 'traduccionrecursos').'
					</h1>
				</legend>
				<div class="row">
					<div class="col-md-12 buscar-contenedor">
						'.$sHtmlBuscadorCargado.'
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<button type="submit" class="avanzado-criterio-widget"><span class="fa fa-plus"></span> '.__('Nuevo Criterio', 'traduccionrecursos').'</button>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<button type="submit" class="avanzado-widget"><span class="fa fa-search"></span> '.__('Buscar', 'traduccionrecursos').'</button>
					</div>
				</div>
			</fieldset>
		</div>	
		<div class="row">
			<div class="col-md-4 filtros-facetado">
				<div class="row">
					<div class="col-md-12 filtro-item-movil">
						<a class="filtro-item-titulo-movil">'.__('Filtrar resultado', 'traduccionrecursos').'<span class="fas fa-caret-down"></span></a>
						<div>
							<div class="row">
								<div class="col-md-12 filtro-item">
									<a class="filtro-item-titulo">'.__('Nivel educativo', 'traduccionrecursos').'<span class="fas fa-caret-up"></span></a>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12 filtro-item">
									<a class="filtro-item-titulo">'.__('Asignaturas', 'traduccionrecursos').'<span class="fas fa-caret-up"></span></a>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12 filtro-item">
									<a class="filtro-item-titulo">'.__('Autores', 'traduccionrecursos').'<span class="fas fa-caret-up"></span></a>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12 filtro-item">
									<a class="filtro-item-titulo">'.__('Idiomas', 'traduccionrecursos').'<span class="fas fa-caret-up"></span></a>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12 filtro-item">
									<a class="filtro-item-titulo">'.__('Tipos de recursos', 'traduccionrecursos').'<span class="fas fa-caret-up"></span></a>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12 filtro-item">
									<a class="filtro-item-titulo">'.__('Siglos', 'traduccionrecursos').'<span class="fas fa-caret-up"></span></a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-8">
				<div class="buscador-widget">
				</div>				
			</div>
		</div>
		';

		?>

		<script type="text/javascript">
			var cargando = '<div class="sk-cube-grid"><div class="sk-cube sk-cube1"></div><div class="sk-cube sk-cube2"></div><div class="sk-cube sk-cube3"></div><div class="sk-cube sk-cube4"></div><div class="sk-cube sk-cube5"></div><div class="sk-cube sk-cube6"></div><div class="sk-cube sk-cube7"></div><div class="sk-cube sk-cube8"></div><div class="sk-cube sk-cube9"></div></div>';
			var numItems = 5;
			var numBusqueda = 24;
			jQuery(function($) {
				var htmlCaja = '<?php echo $sHtmlBuscador;?>';
				$(document).ready(function(){

					lanzarBusqueda();

					$('.avanzado-widget').click(function(e){
						lanzarBusqueda();
						e.preventDefault();
						e.stopPropagation();
					});

					$('.avanzado-criterio-widget').click(function(e){
						$( ".buscar-contenedor" ).append(htmlCaja);
						$(".combo-chosen").chosen({disable_search_threshold: 10});
						enviarFormEnter();
					});

					accordeonEventos();

					var allPanels = $('.filtro-item > ul').hide();

					if($('.buscar_avanzado-widget').val().length > 0){
						lanzarBusqueda();
					}
					$(".combo-chosen").chosen({disable_search_threshold: 10});

					cargarBuscadorDefecto();
					enviarFormEnter();
				});

				function enviarFormEnter(){
					$('.buscar_avanzado-widget').keypress(function(e){
						if (e.keyCode == 13)
						{
							$('.avanzado-widget').click();
						}
					});
				}

				function obtenerBloques(){
					var asig = [];
					$(".filtro-item-detalle input[data-item='asig']").each(function(){
						
						if($(this).is(":checked")){
							asig.push($(this).val());
						}
					});
					var nivel = [];
					$(".filtro-item-detalle input[data-item='nivel']").each(function(){
						
						if($(this).is(":checked")){
							nivel.push($(this).val());
						}
					});

					var data = {
						'action': 'getBloques',
						'asig':asig,
						'nivel':nivel
					};

					$.ajax({
						type: "POST",
						url : "<?php echo admin_url('admin-ajax.php'); ?>",
						data : data,
						success: function (response) {

							console.log(response);
							if(response.length > 0){
								$('.filtro-item.bloques ul').empty().append(response);
								clickBusqueda();
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

				function lanzarBusqueda(){
					var oBusqueda = {};
					oBusqueda.cajas = {};
					$('.buscar-contenedor .cajon_buscar-widget').each(function(i){
						oBusqueda.cajas[i] = {};
						oBusqueda.cajas[i].tipo 		= $(this).find('.buscar_tipo-widget option:selected').val();
						oBusqueda.cajas[i].incluye 		= $(this).find('.buscar_incluye-widget option:selected').val();
						oBusqueda.cajas[i].contenido 	= $(this).find('.buscar_avanzado-widget').val();
						oBusqueda.cajas[i].operador 	= $(this).find('.buscar_operador-widget option:selected').val();
					});

					$('.filtro-item-detalle input, .filtro-item-detalle select').each(function(i){
						if($(this).is(":checked")){
							if(oBusqueda[$(this).data('item')] == undefined){
								oBusqueda[$(this).data('item')] = [];
							}
							oBusqueda[$(this).data('item')].push($(this).val());
						}

						if($(this).attr("id") == "siglo"){
							if(oBusqueda['siglo'] == undefined){
								oBusqueda['siglo'] = [];
							}
							oBusqueda['siglo'].push($(this).find(":selected").val());
						}
					});
					cargarContenido(oBusqueda);
				}

				function cargarContenido(busqueda){

					$(".combo-chosen").chosen({disable_search_threshold: 10});
					$('.buscador-widget').addClass("cargando").append(cargando);
					var idioma = getUrlParameter('lang');
					var data = {
						'action': 'getRecursosAvanzados',
						'busqueda': busqueda,
						'lang':idioma,
						'numBusqueda':numBusqueda
					};

					$.ajax({
						type: "POST",
						url : "<?php echo admin_url('admin-ajax.php'); ?>",
						data : data,
						success: function (response) {

							var datos = JSON.parse(response);
							$('.filtros-facetado').html(datos.filtro);
							
							$(".filtro-item-detalle.vermas a").each(function(){
								mostrarMas(this);	
							});							

							$('.buscador-widget').html(datos.contenido).removeClass("cargando");
							var allPanels = $('.filtro-item > ul').hide();
							accordeonEventos();
							$(".combo-chosen").chosen({disable_search_threshold: 10});
							var bBuscado = false;
							for (var k in busqueda) {
								if (busqueda.hasOwnProperty(k)) {
									if(k !== 'cajas'){
										if(k !== 'siglo'){
											var res = busqueda[k].toString().split(",");
											res.forEach(function(element) {
												$('#' + k +'\\[' + element + '\\]').prop("checked", true);
												$('#' + k +'\\[' + element + '\\]').parent().removeClass('hidden');
												$('#' + k +'\\[' + element + '\\]').closest('.hidden').removeClass('hidden');
												bBuscado = true;
												if($('#' + k +'\\[' + element + '\\]').closest('.filtro-item').find("a.filtro-item-titulo span").hasClass("fa-caret-down")) {
													//$('#' + k +'\\[' + element + '\\]').closest('.filtro-item').find("a.filtro-item-titulo").next().show();
													//$('#' + k +'\\[' + element + '\\]').closest('.filtro-item').find("a.filtro-item-titulo span").removeClass("fa-caret-down").addClass("fa-caret-up");	
												}
											});
										} else {
											$("#siglo option").each(function() { this.selected = (this.value == busqueda[k][0]); });
											$('#siglo').trigger("chosen:updated");
											if(busqueda[k][0] != undefined && busqueda[k][0] != null && busqueda[k][0].length > 0){
												$('#siglo').closest('.filtro-item').find("a.filtro-item-titulo").next().show();
												$('#siglo').closest('.filtro-item').find("a.filtro-item-titulo span").removeClass("fa-caret-down").addClass("fa-caret-up");
												$(".combo-chosen").chosen('destroy').chosen({disable_search_threshold: 10});
												bBuscado = true;
											}
										}
									}
								}
							}

							if ($(".filtro-item-titulo-movil").is(":visible")){
								if(bBuscado){
									$(".filtro-item-titulo-movil").next().show();
									$(".filtro-item-titulo-movil").find("span").removeClass("fa-caret-down").addClass("fa-caret-up");
								}else{
									$(".filtro-item-titulo-movil").next().hide();
									$(".filtro-item-titulo-movil").find("span").removeClass("fa-caret-up").addClass("fa-caret-down");
								}
							}

							clickBusqueda();
							$('.showmore').click(function(){
								numBusqueda += 24;
								lanzarBusqueda();
							});

							$('.filtro-item-detalle:not(.hidden) input:checked').closest('.filtro-item').find('a.filtro-item-titulo').click();

							//$('.item-titulo').ellipsis();
							$('.item-titulo').truncate({
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

				function mostrarMas(elemento){
					$(elemento).parent().parent().find('li.filtro-item-detalle:not(.vermas,.hidden)').hide().slice(0,numItems).show();
				}

				function accordeonEventos(){
					$(".filtro-item-titulo, .filtro-item-titulo-movil").unbind('click');
					$('.filtro-item-titulo, .filtro-item-titulo-movil').click(function(e){
						if($(this).find("span").hasClass("fa-caret-up")){
							$(this).next().slideUp();
							$(this).find("span").removeClass("fa-caret-up").addClass("fa-caret-down");
						}else{
							$(this).next().slideDown();
							$(this).find("span").removeClass("fa-caret-down").addClass("fa-caret-up");						
							$(".combo-chosen").chosen('destroy').chosen({disable_search_threshold: 10});
						}
						return false;
					});
				}

				function clickBusqueda(){
					$(".avanzado-aplicarfiltro").hide();
					$(".filtro-item-detalle input, #siglo").unbind('change');
					$(".filtro-item-detalle input, #siglo").change(function() {
						$(".avanzado-aplicarfiltro").show();
					});

					$(".avanzado-aplicarfiltro").unbind('click');
					$(".avanzado-aplicarfiltro").click(function() {
						lanzarBusqueda();
					});			

					$(".filtro-item-detalle.vermas a").click(function(){
						numItems = $(this).data('num');
						var nElementos = $(this).parent().parent().find(".filtro-item-detalle").length;
						if(numItems < nElementos){
							numItems = numItems + 5;
							$(this).data('num', numItems);
							mostrarMas(this);	
						}

					});

				}

				function cargarBuscadorDefecto(){
					var cadenaDatosDefecto = '';// Eliminamos la busqueda por defecto'<?php echo $_SESSION['busquedarecursos']; ?>';
					if(cadenaDatosDefecto !== ''){
						var oDatosDefecto = JSON.parse(cadenaDatosDefecto);
						var numCajas = 1;
						for (var indexCaja in oDatosDefecto['cajas']) {
							if(numCajas > 1){
								$( ".buscar-contenedor" ).append(htmlCaja);
							}
							if(oDatosDefecto['cajas'][indexCaja]['contenido'] !== ""){
								$(".buscar-contenedor .buscar_avanzado-widget:last").val(oDatosDefecto['cajas'][indexCaja]['contenido']);
								$(".buscar-contenedor .buscar_tipo-widget:last").val(oDatosDefecto['cajas'][indexCaja]['tipo']);
								$(".buscar-contenedor .buscar_incluye-widget:last").val(oDatosDefecto['cajas'][indexCaja]['incluye']);
								$(".buscar-contenedor .buscar_operador-widget:last").val(oDatosDefecto['cajas'][indexCaja]['operador']);
							}
							numCajas++;
						}

						lanzarBusqueda();
					}
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