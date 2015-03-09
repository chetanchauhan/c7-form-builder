<?php
/**
 * User Form Type
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Forms
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
 * Class CFB_User_Form
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Forms
 * @author     Chetan Chauhan <chetanchauhan1991@gmail.com>
 */
class CFB_User_Form extends CFB_Form {

	/**
	 * @since  1.0.0
	 * @access protected
	 */
	protected function initialize() {
		add_action( 'show_user_profile', array( $this, 'the_form' ) );
		add_action( 'edit_user_profile', array( $this, 'the_form' ) );

		add_action( 'personal_options_update', array( $this, 'save_form' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_form' ) );

		// Do not remove.
		parent::initialize();
	}

	/**
	 * @since  1.0.0
	 * @access protected
	 * @return array
	 */
	protected function get_default_args() {
		return array(
			'view' => array(
				'type'    => 'partial',
				'helpers' => array(
					'fields' => array(
						'wrapper_tag'        => 'table',
						'wrapper_attributes' => array(
							'class' => 'form-table',
						),
						'tag'                => 'tbody',
					),
				),
			)
		);
	}

	/**
	 * Add default args to all the fields of user form type.
	 *
	 * This currently adds 'meta' as default storage and custom template
	 * to display the fields correctly on profile edit page.
	 *
	 * @since     1.0.0
	 * @access    protected
	 *
	 * @param array $field_args Original field args array
	 *
	 * @return array $field_args Optionally modified args array
	 */
	protected function process_field_args( $field_args ) {
		$defaults = array(
			'view'    => array(
				'wrapper_tag' => 'tr',
				'template'    => '<th scope="row">{label}</th><td>{control}{description}</td>',
			),
			'storage' => array(
				'type'      => 'meta',
				'meta_type' => 'user',
			),
		);

		return array_replace_recursive( $defaults, $field_args );
	}

	/**
	 * Retrieves 'user' as form type.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return string
	 */
	public function get_type() {
		return 'user';
	}

}
