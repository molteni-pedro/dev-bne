<?php

class Criterios_List_Table extends WP_List_Table 
{
 
	/**
	* constructor
	*/

	var $idRecurso;

	function __construct($nRecurso = false){

		$this->idRecurso = $nRecurso;

		//global $status, $page;
		parent::__construct( 
			array(
				'singular'  => __( 'Criterio', 'traduccionrecursos' ),     //singular
				'plural'    => __( 'Criterios', 'traduccionrecursos' ),   //plural
				'ajax'      => false        //soporte ajax
			)
		);
	}

	/**
	 * columnas por defecto de nuestra tabla, devuleve el resultado a cada una, si no existe hace el default
	 */
	function column_default( $item, $column_name ) 
	{
		//print_r($item);
		switch( $column_name ) 
		{ 
			case 'CRcodigo':
			case 'CRdescripcion':
			case 'ETcodigo':
			case 'ETdescripcion':
			return $item[ $column_name];
			default:
				return print_r($item, true) ; //si no existe la columna aparecerá esto
		}
	}

	/**
	 * Labels de los encabezados de nuestra tablas
	 */
	function get_columns(){
			$columns = array(
				'cb'	 		=> '',
				'CRcodigo'		=> __( 'Criterio', 'traduccionrecursos' ),
				'CRdescripcion'	=> '',
				'ETcodigo'		=> __( 'Estandar', 'traduccionrecursos' ),
				'ETdescripcion'	=> '',
			);
			return $columns;
	}

	/**
	* Prepara los datos a paginar
	*/
	function prepare_items() 
	{
	//objecto global wp
		global $wpdb;

		//nombre de la tabla con el prefijo
		$table_name = $wpdb->prefix . 'bne_criterios 
		INNER JOIN '.$wpdb->prefix . 'bne_estandares 
		ON CRid = ETidcriterio 
		INNER JOIN '.$wpdb->prefix . 'bne_recursobloquecontenido 
		ON RBbloquecontenido = CRidbloquecontenido 
		'; 

		// items por página
		$per_page = 3;

		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();

		// columnas del encabezado de nuestra tabla
		$this->_column_headers = array($columns, $hidden, $sortable);

		/*$this->process_bulk_action();*/

		// total de items a paginar
		/*$total_items = $wpdb->get_var("SELECT COUNT(NIid) FROM $table_name");*/

		// obtenemos el número de página en la que estamos, 0 es la primera
		/*$paged = isset($_GET['paged']) ? 
		 max(0, intval($_GET['paged']) - 1) : 
		 0;*/

	   // obtenemos el campo por el que se está ordenando, si no hay ninguno lo hacemos por el id
		$orderby = (isset($_GET['orderby']) && in_array($_GET['orderby'], array_keys($this->get_sortable_columns()))) ? 
		 $_GET['orderby'] : 
		 'CRcodigo, CRdescripcion, ETcodigo, ETdescripcion';

		// orden ascendente o descendente
		$order = (isset($_GET['order']) && in_array($_GET['order'], array('asc', 'desc'))) ? 
		 $_GET['order'] : 
		 'asc';


		// Obtenemos los datos paginados en forma de array con el parámetro ARRAY_A
		$this->items = $wpdb->get_results("SELECT * FROM $table_name WHERE RBrecurso = ".$this->idRecurso." ORDER BY $orderby $order", 
			ARRAY_A
		);
		//echo "SELECT * FROM $table_name WHERE RBrecurso = ".$this->idRecurso." ORDER BY $orderby $order";
		// configuramos la paginación
		/*$this->set_pagination_args(array(
			'total_items' => $total_items, // total items
			'per_page' => $per_page, // items por página
			'total_pages' => ceil($total_items / $per_page) // páginas en total para los enlaces de la paginación
		));*/
	}

	/**
	* columnas por las que ordenar, false descendente, true ascendente
	*/
	function get_sortable_columns() 
	{
		$sortable_columns = array();
		return $sortable_columns;
	}

	/**
	* links de edición y delete para el campo id
	*/
	function column_ETcodigo($item) 
	{

		return sprintf('%s', $item['ETcodigo']);
	}

	/**
	* checkbox, el campo name debe ser por el que vamos a realizar acciones en masa
	*/
	function column_cb($item) 
	{
		global $wpdb;
		$row = NULL;
		if($this->idRecurso != false){
			$table = $wpdb->prefix . 'bne_recursoestandares';
			$row = $wpdb->get_row(
				$wpdb->prepare(
			 "SELECT * FROM $table WHERE REidrecurso = %d AND REidestandar = %d", $this->idRecurso, $item['ETid'])
			);
		}
		$checked = "";
		$class = "fa-circle";
		if($row){
			$checked = "checked";
			$class = "fa-check-circle";
		}
		return '<a id="btnMarcarEstandar" title="Asignar" class="far '.$class.' grey bigger-180 marcarEstandar" href="javascript:{}" style="display:inline-block;width:100%;"></a>
		<input name="Estandar['.$item['ETid'].']" '.$checked.' type="checkbox" value="'.$item['ETid'].'" class="hidden"/>
		';
	}

	/**
	* función que se ejecutará cuando no existan items
	*/
	function no_items() 
	{
		echo __( 'No se encontrarón criterios.', 'traduccionrecursos');
	}

}
