<?php

// The widget class
class Buscador_Inicio_Widget extends WP_Widget {

	// Main constructor
	public function __construct() {
		parent::__construct(
			'buscadorinicio_widget',
			__( 'Buscador Inicio', 'traduccionrecursos' ),
			array(
				'customize_selective_refresh' => true,
			)
		);

		add_action('wp_head', 'my_theme_widget');
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

		if(isset($_GET['lang'])){
			$lang = '?lang='.$_GET['lang'];
		}else{
			$lang = '';
		}

		echo '
		<form id="formularioInicio" action="'.get_site_url().'/buscador/'.$lang.'" method="POST" >
			<div class="row">
				<div class="col-md-12 buscar-contenedor">
					<div class="cajon_buscar-widget cajon_buscar_basico-widget">
						<input name="buscar_basico-widget"  class="buscar_basico-widget" placeholder="'.__('Buscar contenido', 'traduccionrecursos').'" aria-label="'.__('Escriba aquí lo que desea buscar', 'traduccionrecursos').'">
						<div class="buscar_inicio_derecha">
							<label for="buscar_operador-widget">
								<select name="buscar_tipo-widget" id="buscar_tipo-widget" class="buscar_tipo-widget" class="form-control">
									<option value="todos">'.__('Todos los campos', 'traduccionrecursos').'</option>
									<option value="titulo">'.__('Título', 'traduccionrecursos').'</option>
									<option value="autor">'.__('Autor', 'traduccionrecursos').'</option>
									<option value="tema">'.__('Tema', 'traduccionrecursos').'</option>
								</select>
							</label>
							<div class="caja_buscador_lupa">
								<button name="buscar-boton" type="submit" class="basico-widget"><span class="fa fa-search"></span></button>
							</div>
						</div>
					</div>

				</div>

				<div class="col-md-12 boton_izquierda">
					<a href="'.get_site_url().'/buscador/'.$lang.'" class="btn_ir_avanzada">'.__('Ir a la búsqueda avanzada', 'traduccionrecursos').'</a>
				</div>
			</div>
		</form>';

		?>

		<script type="text/javascript">

			jQuery(function($) {
			
				$(document).ready(function(){
					$(".buscar_tipo-widget").chosen({disable_search_threshold: 10});
				});

				$('.buscar_basico-widget').keypress(function(e){
					if (e.keyCode == 13)
					{
						$('#formularioInicio').submit();
					}
				});

			});


		</script>
		<?php

		// WordPress core after_widget hook (always include )
		echo $after_widget;
	}

}