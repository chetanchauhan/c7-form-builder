<?php
/**
 * Plugin Name:       C7 Form Builder
 * Plugin URI:        https://github.com/chetanchauhan/c7-form-builder/
 * Description:       Provides an easy to use and powerful API for building forms that can be displayed, customized and saved any way you want.
 * Version:           1.0.0-beta
 * Author:            Chetan Chauhan
 * Author URI:        https://github.com/chetanchauhan/
 * Text Domain:       c7-form-builder
 * Domain Path:       /languages
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Copyright (c) 2014-2015 Chetan Chauhan (email : chetanchauhan1991@gmail.com)
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * @package           C7_Form_Builder
 **/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// C7 Form Builder version, used for cache-busting of style and script file references.
if ( ! defined( 'C7_FORM_BUILDER_VERSION' ) ) {
	define( 'C7_FORM_BUILDER_VERSION', '1.0.0-beta' );
}

// Define the plugin directory path.
if ( ! defined( 'C7_FORM_BUILDER_DIR' ) ) {
	define( 'C7_FORM_BUILDER_DIR', plugin_dir_path( __FILE__ ) );
}

// Define the plugin directory URL.
if ( ! defined( 'C7_FORM_BUILDER_URL' ) ) {
	define( 'C7_FORM_BUILDER_URL', plugin_dir_url( __FILE__ ) );
}

// Set the plugin includes directory path.
if ( ! defined( 'C7_FORM_BUILDER_INCLUDES' ) ) {
	define( 'C7_FORM_BUILDER_INCLUDES', C7_FORM_BUILDER_DIR . trailingslashit( 'includes' ) );
}

// Include the required functions file.
require_once C7_FORM_BUILDER_INCLUDES . 'functions.php';

/**
 * Autoload plugin classes on demand.
 *
 * @since   1.0.0
 *
 * @param string $class_name Class name to load.
 */
function _cfb_autoload_classes( $class_name ) {
	if ( strpos( $class_name, 'CFB_' ) !== 0 ) {
		return;
	}

	$path             = '';
	$file_name        = 'class-' . str_replace( '_', '-', strtolower( $class_name ) ) . '.php';
	$class_group      = substr( $class_name, strrpos( $class_name, '_' ) + 1 );
	$abstract_classes = array(
		'CFB_Core'              => true,
		'CFB_Field'             => true,
		'CFB_Field_View'        => true,
		'CFB_Field_View_Helper' => true,
		'CFB_Form'              => true,
		'CFB_Form_View'         => true,
		'CFB_Form_View_Helper'  => true,
		'CFB_Storage'           => true,
		'CFB_View'              => true,
		'CFB_View_Helper'       => true,
	);

	if ( isset( $abstract_classes[ $class_name ] ) ) {
		$path = 'abstracts/';
	} elseif ( 'Form' === $class_group ) {
		$path = 'forms/';
	} elseif ( 'Field' === $class_group ) {
		$path = 'fields/';
	} elseif ( 'Storage' === $class_group ) {
		$path = 'storage/';
	} elseif ( 'View' === $class_group ) {
		$path = 'views/';
	} elseif ( 'Helper' === $class_group ) {
		$path = 'views/_helpers/';
	}

	$path = C7_FORM_BUILDER_INCLUDES . $path . $file_name;
	if ( file_exists( $path ) ) {
		require_once( $path );
	}
}

// Register autoloader for loading core classes.
spl_autoload_register( '_cfb_autoload_classes' );

/**
 * Returns the one true instance of the plugin.
 *
 * Use this function like you would a global variable, except
 * this prevents the need to use globals.
 *
 * Example:    <?php $cfb = c7_form_builder() ?>
 *
 * @since      1.0.0
 * @return CFB_Main
 */
function c7_form_builder() {
	global $c7_form_builder;

	if ( ! isset( $c7_form_builder ) ) {
		$c7_form_builder = new CFB_Main();
	}

	return $c7_form_builder;
}

// Start the C7 Form Builder.
add_action( 'plugins_loaded', 'c7_form_builder' );
