<?php
/**
 * Admin Form Type
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
 * Class CFB_Admin_Form
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Forms
 * @author     Chetan Chauhan <chetanchauhan1991@gmail.com>
 */
class CFB_Admin_Form extends CFB_Form {

	/**
	 * The parent menu slug name when adding submenu page.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @var     string
	 */
	public $parent_slug;

	/**
	 * The text to be used for the menu.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @var     string
	 */
	public $menu_title;

	/**
	 * The capability required for viewing this admin page.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @var     string
	 */
	public $capability = 'manage_options';

	/**
	 * Unique slug name for this menu.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @var     string
	 */
	public $menu_slug;

	/**
	 * The icon url to be used for this menu.
	 *
	 * This is applicable for top level menu page.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @var     string
	 */
	public $icon_url = '';

	/**
	 * The position in the menu order this menu should appear.
	 *
	 * This is applicable for top level menu page.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @var     int
	 */
	public $position;

	/**
	 * @since  1.0.0
	 * @access protected
	 */
	protected function initialize() {
		add_action( 'admin_init', array( $this, 'save_form' ), 100 );
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );

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
				'type'               => 'default',
				'wrapper_attributes' => array(
					'class' => 'wrap',
				),
				'helpers'            => array(
					'title'  => array(
						'tag' => 'h2',
					),
					'fields' => array(
						'wrapper_tag'        => 'table',
						'wrapper_attributes' => array(
							'class' => 'form-table',
						),
						'tag'                => 'tbody',
					),
				),
			),
		);
	}

	/**
	 * Callback to add top level menu or submenu admin page.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return bool|string
	 */
	public function add_admin_menu() {
		if ( empty( $this->parent_slug ) ) {
			return add_menu_page( $this->title, $this->menu_title, $this->capability, $this->menu_slug, array(
				$this,
				'the_form'
			), $this->icon_url, $this->position );
		}

		return add_submenu_page( $this->parent_slug, $this->title, $this->menu_title, $this->capability, $this->menu_slug, array(
			$this,
			'the_form'
		) );
	}

	/**
	 * Add default args to all the fields of admin form type.
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
				'type' => 'option',
			),
		);

		return array_replace_recursive( $defaults, $field_args );
	}

	/**
	 * Ensure form gets saved only if user has required capability.
	 *
	 * @since     1.0.0
	 * @access    protected
	 *
	 * @param array $submitted_data
	 *
	 * @return bool
	 */
	protected function is_submission_valid( $submitted_data ) {
		return parent::is_submission_valid( $submitted_data ) && current_user_can( $this->capability );
	}

	/**
	 * Retrieves 'admin' as form type.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return string
	 */
	public function get_type() {
		return 'admin';
	}

}
