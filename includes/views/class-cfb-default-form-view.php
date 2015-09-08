<?php
/**
 * Default Form View Type
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Forms/Views
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
 * Class CFB_Default_Form_View
 *
 * Renders the complete form along with the "form" tag.
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Forms/Views
 * @author     Chetan Chauhan <chetanchauhan1991@gmail.com>
 */
class CFB_Default_Form_View extends CFB_Form_View {

	/**
	 * @return string
	 */
	protected function get_wrapper_tag() {
		return 'form';
	}

	/**
	 * @return array
	 */
	protected function get_wrapper_attributes() {
		return array_merge( array(
			'method' => 'post',
			'action' => '',
		), parent::get_wrapper_attributes() );
	}

	/**
	 * @return string
	 */
	public function get_type() {
		return 'default';
	}

}
