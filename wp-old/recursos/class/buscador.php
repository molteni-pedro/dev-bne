<?php

// The widget class
class Buscador_Widget extends WP_Widget {

	// Main constructor
	public function __construct() {
		parent::__construct(
			'buscador_widget',
			__( 'Buscador', 'traduccionrecursos' ),
			array(
				'customize_selective_refresh' => true,
			)
		);

		add_action('wp_head', 'my_theme_widget');
		add_action( 'wp_ajax_getRecursos', 'getRecursos' );
		add_action( 'wp_ajax_nopriv_getRecursos', 'getRecursos' );
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

		extract( $args );

		// WordPress core before_widget hook (always include )
		echo $before_widget;

		echo '
		<div class="row cabecera_buscador_basico_grande-widget">
			<div class="col-md-10">
				<div class="cajon_buscar-widget">
					<input name="buscar_basico-widget" class="buscar_basico-widget" placeholder="'.__('Buscar contenido', 'traduccionrecursos').'" value="'.$_POST['buscar_basico-widget'].'">
					<select name="buscar_tipo-widget" id="buscar_tipo-widget" class="buscar_tipo-widget" class="form-control">
						<option value="todos" '.(($_POST['buscar_tipo-widget'] == "todos")?'selected':'').'>'.__('Todos los campos', 'traduccionrecursos').'</option>
						<option value="titulo" '.(($_POST['buscar_tipo-widget'] == "titulo")?'selected':'').'>'.__('Título', 'traduccionrecursos').'</option>
						<option value="autor" '.(($_POST['buscar_tipo-widget'] == "autor")?'selected':'').'>'.__('Autor', 'traduccionrecursos').'</option>
						<option value="materia" '.(($_POST['buscar_tipo-widget'] == "materia")?'selected':'').'>'.__('Materia', 'traduccionrecursos').'</option>
						<option value="descripcion" '.(($_POST['buscar_tipo-widget'] == "descripcion")?'selected':'').'>'.__('Descripción', 'traduccionrecursos').'</option>
					</select>
				</div>
			</div>
			<div class="col-md-2">
				<button type="submit" class="basico-widget"><i class="fa fa-search"></i></button>
			</div>
			<div class="col-md-10">
				<a href="'.get_site_url().'/Buscador_avanzado_new/" class="btn_ir_avanzada">'.__('Ir a la búsqueda avanzada', 'traduccionrecursos').'</a>
			</div>
		</div>
		<div class="buscador-widget">


		</div>

		';

		?>

		<script type="text/javascript">
			var cargando = '<div class="sk-cube-grid"><div class="sk-cube sk-cube1"></div><div class="sk-cube sk-cube2"></div><div class="sk-cube sk-cube3"></div><div class="sk-cube sk-cube4"></div><div class="sk-cube sk-cube5"></div><div class="sk-cube sk-cube6"></div><div class="sk-cube sk-cube7"></div><div class="sk-cube sk-cube8"></div><div class="sk-cube sk-cube9"></div></div>';
			jQuery(function($) {
			
				$(document).ready(function(){

					if($('.buscar_basico-widget').val().length > 0){
						cargarContenido($('.buscar_basico-widget').val(), $('.buscar_tipo-widget option:selected').val());
					}else{
						//cargarContenido();
					}
					
					$('.basico-widget').click(function(e){
						cargarContenido($('.buscar_basico-widget').val(), $('.buscar_tipo-widget option:selected').val());
						e.preventDefault();
						e.stopPropagation();
					});
				});

				function cargarContenido(busqueda,tipo){

					console.log(tipo);
					$(".buscar_tipo-widget").chosen({disable_search_threshold: 10});
					$('.buscador-widget').addClass("cargando").append(cargando);
					
					if(busqueda == undefined){
						busqueda = '';
					}

					if(tipo == undefined){
						tipo = '';
					}

					var data = {
						'action': 'getRecursos',
						'busqueda': busqueda,
						'tipo': tipo,
					};

					$.ajax({
						type: "POST",
						url : "<?php echo admin_url('admin-ajax.php'); ?>",
						data : data,
						success: function (response) {
							$('.buscador-widget').html(response).removeClass("cargando");
							//console.log(response);
						},
						failure: function (response) {
							console.log(response);
						},
						error: function (response) {
							console.log(response);
						}
					});
				}			

			});


		</script>
		<?php

		// WordPress core after_widget hook (always include )
		echo $after_widget;
	}

}