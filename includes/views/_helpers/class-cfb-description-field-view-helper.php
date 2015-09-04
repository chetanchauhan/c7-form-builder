<?php
/**
 * Description Field View Helper
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
 * Class CFB_Description_Field_View_Helper
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Fields/Views/Helpers
 * @author     Chetan Chauhan <chetanchauhan1991@gmail.com>
 */
class CFB_Description_Field_View_Helper extends CFB_Field_View_Helper {

	/**
	 * @since   1.0.0
	 * @access  protected
	 * @return string
	 */
	protected function get_html_content() {
		return $this->field->description;
	}

}
