<?php
/**
 * Color Field Type
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Fields
 * @author     Chetan Chauhan <chetanchauhan1991@gmail.com>
 * @license    GPL-2.0+
 * @link       https://github.com/chetanchauhan/c7-form-builder/
 * @copyright  2014 Chetan Chauhan
 * @since      1.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class CFB_Color_Field
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Fields
 * @author     Chetan Chauhan <chetanchauhan1991@gmail.com>
 */
class CFB_Color_Field extends CFB_Field {

	/**
	 * @var string
	 */
	protected $_input_type = 'text';

	/**
	 * @var string
	 */
	protected $_input_class = '';

	/**
	 * @return    void
	 */
	public function enqueue() {

		wp_enqueue_style( 'wp-color-picker' );

		wp_enqueue_script( 'iris', admin_url( 'js/iris.min.js' ), array(
			'jquery-ui-draggable',
			'jquery-ui-slider',
			'jquery-touch-punch'
		), false, 1 );

		wp_enqueue_script( 'wp-color-picker', admin_url( 'js/color-picker.min.js' ), array( 'iris' ), false, 1 );

		wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', array(
			'clear'         => __( 'Clear', 'c7-form-builder' ),
			'defaultString' => __( 'Default', 'c7-form-builder' ),
			'pick'          => __( 'Select Color', 'c7-form-builder' ),
			'current'       => __( 'Current Color', 'c7-form-builder' ),
		) );

		parent::enqueue();
	}

	/**
	 * @return string
	 */
	public function get_type() {
		return 'color';
	}

}
