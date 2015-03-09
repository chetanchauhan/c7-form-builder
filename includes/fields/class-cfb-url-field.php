<?php
/**
 * URL Field Type
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
 * Class CFB_URL_Field
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Fields
 * @author     Chetan Chauhan <chetanchauhan1991@gmail.com>
 */
class CFB_URL_Field extends CFB_Field {

	/**
	 * @var string
	 */
	protected $_input_type = 'url';

	/**
	 * @return void
	 */
	protected function initialize() {
		add_filter( "cfb_filter_{$this->get_type()}_field_value", 'esc_url_raw' );

		// Do not remove.
		parent::initialize();
	}

	/**
	 * @return string
	 */
	public function get_type() {
		return 'url';
	}

}
