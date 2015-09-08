<?php
/**
 * Theme Form Type
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Forms
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
 * Class CFB_Theme_Form
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Forms
 * @author     Chetan Chauhan <chetanchauhan1991@gmail.com>
 */
class CFB_Theme_Form extends CFB_Form {

	/**
	 * @since  1.0.0
	 * @access protected
	 */
	protected function initialize() {
		add_action( 'init', array( $this, 'save_form' ), 100 );

		// Do not remove.
		parent::initialize();
	}

	/**
	 * Retrieves 'theme' as form type.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return string
	 */
	public function get_type() {
		return 'theme';
	}

}
