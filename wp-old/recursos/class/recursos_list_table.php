<?php

class Recursos_List_Table extends WP_List_Table 
{
 
	/**
	* constructor
	*/
	function __construct(){
		//global $status, $page;
		parent::__construct( 
			array(
				'singular'  => __( 'Recursos', 'traduccionrecursos' ),     //singular
				'plural'    => __( 'Recursos', 'traduccionrecursos' ),   //plural
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
			case 'REid':
			case 'REtitulo':
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
				'cb'	 	=> '<input type="checkbox" />',
				'REid' 		=> __( 'Id', 'recursoslisttable' ),
				'REtitulo'	=> __( 'Recurso', 'recursoslisttable' )
			);
			return $columns;
	}

	/**
	* Prepara los datos a paginar
	*/
	function prepare_items($search ='') 
	{
	//objecto global wp
		global $wpdb;

		//nombre de la tabla con el prefijo
		$table_name = $wpdb->prefix . 'bne_recurso';
		$nIdUser = get_current_user_id();
		if(!current_user_can('administrator') ) {
			$sWhere = "REidcreador = $nIdUser";
		}else{
			$sWhere = "1 = 1";
		}
		

		// items por página
		$per_page = 25;

		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();

		// columnas del encabezado de nuestra tabla
		$this->_column_headers = array($columns, $hidden, $sortable);

		$this->process_bulk_action();

		// total de items a paginar
		$total_items = $wpdb->get_var("SELECT COUNT(REid) FROM $table_name WHERE ".$sWhere);

		// obtenemos el número de página en la que estamos, 0 es la primera
		$paged = isset($_GET['paged']) ? 
		 max(0, intval($_GET['paged']) - 1) : 
		 0;

	   // obtenemos el campo por el que se está ordenando, si no hay ninguno lo hacemos por el id
		$orderby = (isset($_GET['orderby']) && in_array($_GET['orderby'], array_keys($this->get_sortable_columns()))) ? 
		 $_GET['orderby'] : 
		 'REid';

		// orden ascendente o descendente
		$order = (isset($_GET['order']) && in_array($_GET['order'], array('asc', 'desc'))) ? 
		 $_GET['order'] : 
		 'asc';
		 
		 if(!empty($search)){
		 	$sWhere .= " AND REtitulo like '%".$search."%'";
		 }

		// Obtenemos los datos paginados en forma de array con el parámetro ARRAY_A
		$this->items = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM $table_name WHERE $sWhere ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, ($paged*$per_page)
			), 
			ARRAY_A
		);

		// configuramos la paginación
		$this->set_pagination_args(array(
			'total_items' => $total_items, // total items
			'per_page' => $per_page, // items por página
			'total_pages' => ceil($total_items / $per_page) // páginas en total para los enlaces de la paginación
		));
	}

	/**
	* columnas por las que ordenar, false descendente, true ascendente
	*/
	function get_sortable_columns() 
	{
		$sortable_columns = array(
			'REid'  => array('REid',true),
			'REtitulo' => array('REtitulo',true)
		);
		return $sortable_columns;
	}

	/**
	* links de edición y delete para el campo id
	*/
	function column_REtitulo($item) 
	{
		// botones para edición y eliminar items en cada row de la tabla
		$actions = array(
			'download' => sprintf('<a target="_blank" href="admin-post.php?action=export_page&id=%s">%s</a>', $item['REid'],'Descargar'),
			'edit' => sprintf('<a href="?page=recursos-edit&id=%s">%s</a>', $item['REid'],'Editar'),
			'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['REid'], 'Eliminar'),
		);

		return sprintf('%s %s',
			$item['REtitulo'],
			$this->row_actions($actions)
		);
	}
	/**
	* desplegable para acción en masa del encabezado
	*/
	function get_bulk_actions() 
	{
		$actions = array(
		'delete'    => 'Eliminar'
		);
		return $actions;
	}
	/**
	 * Procesa acciones en masa, en este caso elimina
	 */
	function process_bulk_action()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'bne_recurso'; 

		//si la acción actual es delete significa que estamos eliminando
		if ('delete' === $this->current_action()) 
		{
			$ids = isset($_GET['id']) ? $_GET['id'] : array();
			//si es un array de ids
			if (is_array($ids)) 
			{
				$ids = implode(',', $ids);
			}

			//si hay ids eliminamos
			if (!empty($ids)) 
			{
				$wpdb->query("DELETE FROM $table_name WHERE REid IN($ids)");
			}
		}
	}

	/**
	* checkbox, el campo name debe ser por el que vamos a realizar acciones en masa
	*/
	function column_cb($item) 
	{
		return sprintf(
			'<input type="checkbox" name="id[]" value="%s" />', $item['REid']
		);
	}

	/**
	* función que se ejecutará cuando no existan items
	*/
	function no_items() 
	{
		_e( 'No se encontrarón recursos.' );
	}

}
