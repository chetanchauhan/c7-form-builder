<?php
/**
 * Label Field View Helper
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
 * Class CFB_Label_Field_View_Helper
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Fields/Views/Helpers
 * @author     Chetan Chauhan <chetanchauhan1991@gmail.com>
 */
class CFB_Label_Field_View_Helper extends CFB_Field_View_Helper {

	/**
	 * HTML tag to be used for rendering field label.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var string
	 */
	public $tag = 'label';

	/**
	 * @since     1.0.0
	 * @access    protected
	 * @return array
	 */
	protected function get_attributes() {
		$attributes = parent::get_attributes();
		// Add for attribute if tag is still label.
		if ( $this->tag == 'label' ) {
			// Reset the index to first field control in case of repeatable fields.
			$this->field->index = 0;
			$attributes['for']  = $this->field->get_html_id();
		}

		return $attributes;
	}

	/**
	 * @since   1.0.0
	 * @access  protected
	 * @return string
	 */
	protected function get_html_content() {
		return $this->field->label;
	}

}
