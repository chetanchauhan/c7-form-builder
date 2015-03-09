<?php
/**
 * Hidden Field View Type
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
 * Class CFB_Hidden_Field_View
 *
 * Default field view type for the hidden fields.
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Fields/Views
 * @author     Chetan Chauhan <chetanchauhan1991@gmail.com>
 */
class CFB_Hidden_Field_View extends CFB_Field_View {

	/**
	 * @var array
	 */
	protected $_available_helpers = array(
		'control' => 'CFB_Control_Field_View_Helper',
	);

	/**
	 * @var string
	 */
	public $template = '{control}';

	/**
	 * @return string
	 */
	public function get_type(){
		return 'hidden';
	}

}
