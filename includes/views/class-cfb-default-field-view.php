<?php
/**
 * Default Field View Type
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Fields/Views
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
 * Class CFB_Default_Field_View
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Forms/Views
 * @author     Chetan Chauhan <chetanchauhan1991@gmail.com>
 */
class CFB_Default_Field_View extends CFB_Field_View {

	/**
	 * @return string
	 */
	public function get_type(){
		return 'default';
	}

}
