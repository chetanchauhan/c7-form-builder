<?php
/**
 * Abstract Field View Helper Class
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Abstracts
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
 * Class CFB_Field_View_Helper
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Abstracts
 * @author     Chetan Chauhan <chetanchauhan1991@gmail.com>
 */
abstract class CFB_Field_View_Helper extends CFB_View_Helper {

	/**
	 * Field being rendered by the view helper.
	 *
	 * @since     1.0.0
	 * @access    protected
	 * @var    CFB_Field
	 */
	protected $field;

	/**
	 * @since     1.0.0
	 * @access    protected
	 * @return array
	 */
	protected function get_attributes() {
		return CFB_Html::merge_attributes(
			array(
				'class' => 'cfb-field-' . $this->helper_type,
			), $this->tag_attributes
		);
	}

	/**
	 * @since     1.0.0
	 * @access    protected
	 * @return array
	 */
	protected function get_wrapper_attributes() {
		return CFB_Html::merge_attributes(
			array(
				'class' => 'cfb-field-' . $this->helper_type . '-wrapper',
			), $this->wrapper_attributes
		);
	}

}
