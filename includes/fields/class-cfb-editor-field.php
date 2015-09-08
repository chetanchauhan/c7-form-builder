<?php
/**
 * Editor Field Type
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Fields
 * @author     Chetan Chauhan <chetanchauhan1991@gmail.com>
 * @license    GPL-2.0+
 * @link       https://github.com/chetanchauhan/c7-form-builder/
 * @copyright  2014-2015 Chetan Chauhan
 * @since      1.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class CFB_Editor_Field
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Fields
 * @author     Chetan Chauhan <chetanchauhan1991@gmail.com>
 */
class CFB_Editor_Field extends CFB_Field {

	/**
	 * @since  1.0.0
	 * @access public
	 * @var array
	 */
	public $options = array();

	/**
	 * @param mixed $value
	 * @param string $html_name
	 * @param string $html_id
	 *
	 * @return string
	 */
	public function single_control_html( $value, $html_name, $html_id ) {
		$options = array_merge( $this->options, array(
				'textarea_name' => $html_name,
			)
		);

		$options['tinymce']['wp_skip_init'] = true;

		ob_start();
		wp_editor( $value, $html_id, $options );
		$editor = ob_get_clean();

		return str_replace( '<div id="wp-' . $html_id . '-wrap"', '<div id="wp-' . $html_id . '-wrap" data-editor-settings="' . $html_id . '"', $editor );
	}

	/**
	 * @return string
	 */
	public function get_type() {
		return 'editor';
	}
}
