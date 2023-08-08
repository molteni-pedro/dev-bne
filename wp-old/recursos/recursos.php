<?php

/*
	Plugin Name: Recursos
	Plugin URI: http://www.hermesinteractiva.com/
	Description: Gestión de los recursos de la plataforma
	Version: 1.0.0
	Author: Hermes Interactiva
	Author URI: http://www.hermesinteractiva.com/
	Requires at least: 4.9
	Tested up to: 4.9.7
	Text Domain: recursos
	Domain Path: /languages/
 */

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
    echo 'Esto es un plugin, no puede ser llamado directamente';
    exit;
}

define( 'RECURSOS_CONTROL_HOSTING_VERSION', '1.0' );
define( 'RECURSOS__MINIMUM_WP_VERSION', '4.9' );
define( 'RECURSOS__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'RECURSOS_DELETE_LIMIT', 100000 );
define( 'RKR_BLOG_NAME', get_bloginfo('name') );
define( 'RKR_BLOG_URL', strtok(get_bloginfo('url'), '?'));
define( 'RKR_PLUGIN_PATH', RKR_BLOG_URL.'/wp-content/plugins/recursos' );

// Load libreries
$plugin_recursos = array(
  'version' => '1.0.0',
  'filename' => __FILE__,
  'path' => dirname(__FILE__),
  'initializer' => dirname(__FILE__) . '/initializer.php'
);

// Set plugin like dissabled
function recursos_deactivate_plugin() {
  deactivate_plugins(plugin_basename(__FILE__));
  if(!empty($_GET['activate'])) {
    unset($_GET['activate']);
  }
}

// Display WP version error notice
function recursos_wp_version_notice() {
  echo '<div class="error"><p>Plugin requires WordPress version 4.6 or newer</p></div>';
}

// Display PHP version error notice
function recursos_php_version_notice() {
  echo '<div class="error"><p>Plugin requires PHP version 5.3.3 or newer</p></div>';
}

// Check for minimum supporter WP version
if(version_compare(get_bloginfo('version'), '4.6', '<')) {
  add_action('admin_notices', 'recursos_wp_version_notice');
  // deactivate the plugin
  add_action('admin_init', 'recursos_deactivate_plugin');
  return;
}

// Check for minimum supported PHP version
if(version_compare(phpversion(), '5.3.3', '<')) {
  add_action('admin_notices', 'recursos_php_version_notice');
  // deactivate the plugin
  add_action('admin_init', 'recursos_deactivate_plugin');
  return;
}

// Display missing core dependencies error notice
function recursos_core_dependency_notice() {
  $notice = __('Demo gestión clientes cannot start because it is missing core files. Please reinstall the plugin.', 'Hermes Interactiva');
  printf('<div class="error"><p>%1$s</p></div>', $notice);
}

// Check for presence of core dependencies
if(!file_exists($plugin_recursos['initializer'])) {
  add_action('admin_notices', 'recursos_core_dependency_notice');
  // deactivate the plugin
  add_action('admin_init', 'recursos_deactivate_plugin');
  return;
}

// Initialize plugin
require_once($plugin_recursos['initializer']);