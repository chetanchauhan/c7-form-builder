<?php
/**
 * Textarea Field Type
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
 * Class CFB_Textarea_Field
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Fields
 * @author     Chetan Chauhan <chetanchauhan1991@gmail.com>
 */
class CFB_Textarea_Field extends CFB_Field {

	/**
	 * @var int
	 */
	public $rows = 4;

	/**
	 * @var int
	 */
	public $cols = 60;

	/**
	 * @return void
	 */
	protected function initialize() {
		add_filter( "cfb_filter_{$this->get_type()}_field_value", 'wp_kses_post' );

		// Do not remove.
		parent::initialize();
	}

	/**
	 * @param mixed $value
	 * @param string $html_name
	 * @param string $html_id
	 *
	 * @return string
	 */
	public function single_control_html( $value, $html_name, $html_id ) {
		$html_attributes = CFB_Html::merge_attributes(
			$this->attributes,
			array(
				'id'          => $html_id,
				'name'        => $html_name,
				'rows'        => $this->rows,
				'cols'        => $this->cols,
				'placeholder' => $this->placeholder,
				'readonly'    => $this->readonly,
				'disabled'    => $this->disabled,
			)
		);

		return CFB_Html::tag( 'textarea', $html_attributes, esc_textarea( $value ), false );

	}

	/**
	 * @return string
	 */
	public function get_type() {
		return 'textarea';
	}

}
