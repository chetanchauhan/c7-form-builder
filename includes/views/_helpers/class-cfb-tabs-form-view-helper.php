<?php
/**
 * Tabs Form View Helper
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Forms/Views/Helpers
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
 * Class CFB_Tabs_Form_View_Helper
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Forms/Views/Helpers
 * @author     Chetan Chauhan <chetanchauhan1991@gmail.com>
 */
class CFB_Tabs_Form_View_Helper extends CFB_Form_View_Helper {

	/**
	 * @var string
	 */
	public $wrapper_tag = 'ul';

	/**
	 * @var string
	 */
	public $tag = 'li';

	/**
	 * @return array
	 */
	protected function get_html_content() {
		$tabs = $this->form->tabs;

		// Remove tabs that don't have any field associated with them.
		foreach ( $tabs as $tab_id => $tab_title ) {
			if ( count( $this->form->get_field_names( $tab_id ) ) == 0 ) {
				unset( $tabs[ $tab_id ] );
			}
		}

		return $tabs;
	}

	/**
	 * @return array
	 */
	protected function get_attributes() {
		$attributes             = parent::get_attributes();
		$attributes['data-tab'] = $this->iterator;

		return $attributes;
	}

	/**
	 * @return array
	 */
	protected function get_wrapper_attributes() {
		$attributes                     = parent::get_wrapper_attributes();
		$attributes['data-default-tab'] = $this->form->default_tab;

		return $attributes;
	}

}
