<?php
/**
 * Select Field Type
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
 * Class CFB_Select_Field
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Fields
 * @author     Chetan Chauhan <chetanchauhan1991@gmail.com>
 */
class CFB_Select_Field extends CFB_Field {

	/**
	 * @var array
	 */
	public $options = array();

	/**
	 * @var bool
	 */
	public $multiple = false;

	/**
	 * @param mixed $value
	 * @param string $html_name
	 * @param string $html_id
	 *
	 * @return string
	 */
	public function single_control_html( $value, $html_name, $html_id ) {
		if ( $this->multiple ) {
			$html_name .= '[]';
		}

		$html_attributes = CFB_Html::merge_attributes(
			$this->attributes,
			array(
				'id'       => $html_id,
				'name'     => $html_name,
				'readonly' => $this->readonly,
				'disabled' => $this->disabled,
				'multiple' => $this->multiple,
				'required' => $this->required,
			)
		);

		return CFB_Html::select( $html_attributes, $this->options, $value );

	}

	/**
	 * @return bool
	 */
	public function can_repeat() {
		return false;
	}

	/**
	 * @return string
	 */
	public function get_type() {
		return 'select';
	}

}
