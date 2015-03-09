<?php
/**
 * Control Field View Helper
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Fields/Views/Helpers
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
 * Class CFB_Control_Field_View_Helper
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Fields/Views/Helpers
 * @author     Chetan Chauhan <chetanchauhan1991@gmail.com>
 */
class CFB_Control_Field_View_Helper extends CFB_Field_View_Helper {

	/**
	 * Returns the field control HTML for both repeatable and
	 * non-repeatable fields.
	 *
	 * @since     1.0.0
	 * @access    protected
	 * @return    string
	 */
	protected function get_html_content() {
		$values  = $this->field->get_value();

		if ( ! $this->field->is_repeatable() ) {
			return $this->field->single_control_html( $values, $this->field->get_html_name(), $this->field->get_html_id());
		}

		foreach ( $values as $this->field->index => &$value ) {
			$value = $this->field->single_control_html( $value, $this->field->get_html_name(), $this->field->get_html_id() );
		}

		// Reset the repeatable field index.
		$this->field->index = null;

		return $values;
	}

	/**
	 * @since     1.0.0
	 * @access    protected
	 * @return array
	 */
	protected function get_attributes() {
		$attributes = parent::get_attributes();
		if ( $this->iterator === 'x' ) {
			$attributes['class'] = $attributes['class'] . ' cfb-empty-control';
		}

		return $attributes;
	}

}

