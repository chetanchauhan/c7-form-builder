<?php
/**
 * Partial Form View Type
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Forms/Views
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
 * Class CFB_Partial_Form_View
 *
 * Renders the form without form tag. This is default view
 * type used for rendering post, taxonomy and user form types.
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Forms/Views
 * @author     Chetan Chauhan <chetanchauhan1991@gmail.com>
 */
class CFB_Partial_Form_View extends CFB_Form_View {

	/**
	 * @return string
	 */
	public function get_type(){
		return 'partial';
	}

}
