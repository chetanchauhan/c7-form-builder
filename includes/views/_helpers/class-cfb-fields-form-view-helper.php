<?php
/**
 * Fields Form View Helper
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Forms/Views/Helpers
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
 * Class CFB_Fields_Form_View_Helper
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Forms/Views/Helpers
 * @author     Chetan Chauhan <chetanchauhan1991@gmail.com>
 */
class CFB_Fields_Form_View_Helper extends CFB_Form_View_Helper {

	/**
	 * Retrieves the form fields HTML.
	 *
	 * @since     1.0.0
	 * @access    protected
	 * @return    string
	 */
	protected function get_html_content() {
		$fields_html = array();
		$field_names = $this->form->get_field_names();

		foreach ( $field_names as $field_name ) {
			$field         = $this->form->get_field( $field_name );
			$fields_html[] = $field->render();
		}

		return implode( "\n", $fields_html );
	}

}

