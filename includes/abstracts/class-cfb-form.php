<?php
/**
 * Abstract Form Class
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Abstracts
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
 * Class CFB_Form
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Abstracts
 * @author     Chetan Chauhan <chetanchauhan1991@gmail.com>
 */
abstract class CFB_Form extends CFB_Core {

	/**
	 * Unique name of the form.
	 *
	 * @since     1.0.0
	 * @access    protected
	 * @var string
	 */
	protected $name;

	/**
	 * Nonce key to be used for outputting the nonce field.
	 *
	 * @since     1.0.0
	 * @access    protected
	 * @var string
	 */
	protected $_nonce_key = 'cfb_%s_nonce';

	/**
	 * Holds a key value pair of tabs and field names array.
	 *
	 * This is used internally to associate field with a
	 * particular tab.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @var array
	 */
	protected $_fields_by_tab = array();

	/**
	 * Holds the form view object or form view args array.
	 *
	 * At the time of creation of form view, these are delegated
	 * to the form view object.
	 *
	 * @since     1.0.0
	 * @access    protected
	 * @var array|CFB_Form_View
	 */
	protected $view;

	/**
	 * Holds the registered form fields.
	 *
	 * @since     1.0.0
	 * @access    protected
	 * @var array
	 */
	protected $fields = array();

	/**
	 * Title for the form.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var string
	 */
	public $title = '';

	/**
	 * Description for the form.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var string
	 */
	public $description = '';

	/**
	 * Form Tabs
	 *
	 * An array that holds all of the tabs applicable for
	 * the current form in the form of key/value pair.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var        array
	 */
	public $tabs = array();

	/**
	 * Default Form Tab
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var string
	 */
	public $default_tab;

	/**
	 * Holds the redirect URL.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var string
	 */
	public $redirect = '';

	/**
	 *
	 */
	protected function initialize() {
		// Sort user registered fields by priority.
		$this->fields = array_reverse( $this->fields );
		uasort( $this->fields, '_cfb_cmp_priority' );

		// Prepare all the fields.
		$fields = array();
		foreach ( $this->fields as $field_name => $field ) {

			// Skip the current field, if type is either not specified or invalid.
			if ( ! isset( $field['type'] ) || ! c7_form_builder()->has_field_type( $field['type'] ) ) {
				continue;
			}

			$field['tab'] = isset( $field['tab'] ) && isset( $this->tabs[ $field['tab'] ] ) ? $field['tab'] : false;

			if ( $field['tab'] ) {
				$this->_fields_by_tab[ $field['tab'] ][] = $field_name;
			}

			$fields[ sanitize_key( $field_name ) ] = $field;
		}

		$this->fields = $fields;
	}

	/**
	 * @return array
	 */
	protected function get_default_args() {
		return array( 'view' => array( 'type' => 'default' ) );
	}

	/**
	 * Retrieves the form type.
	 *
	 * This helps identify the type of form, once the form object
	 * is created.
	 *
	 * Subclasses should implement this method to return the form
	 * type alias with which it is registered.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return string
	 */
	abstract public function get_type();

	/**
	 * Returns the name of the form.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Enqueue all scripts and styles required by the form type.
	 *
	 * @since     1.0.0
	 * @access    public
	 */
	public function enqueue() {
	}

	/**
	 * Renders the form.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return string
	 */
	public function render() {
		if ( $view = $this->get_view() ) {
			return $view->render();
		}

		return '';
	}

	/**
	 * Output the form HTML.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return    void
	 */
	public function the_form() {
		echo $this->render();
	}

	/**
	 * Handles the saving of form fields.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return    bool
	 */
	final public function save_form() {
		// Bail early if the form is not submitted.
		if ( empty( $_REQUEST['c7_form_builder'][ $this->get_name() ] ) ) {
			return false;
		}

		// Get the submitted data.
		$submitted_data = $_REQUEST['c7_form_builder'][ $this->get_name() ];

		// Bail if the submission is not valid.
		if ( ! apply_filters( 'cfb_form_is_submission_valid', $this->is_submission_valid( $submitted_data ), $submitted_data, $this ) ) {
			return false;
		}

		// Set the submitted field value to all the fields.
		foreach ( $submitted_data as $field_name => $value ) {
			if ( $field = $this->get_field( $field_name ) ) {
				$field->set_value( $value );
			}
		}

		if ( $is_valid = apply_filters( 'cfb_form_validate', true, $this ) ) {
			foreach ( $this->get_field_names() as $field_name ) {
				$field = $this->get_field( $field_name );

				if ( $storage = $field->get_storage() ) {
					$storage->update_value( $field->get_value() );
				}
			}
		}

		if ( apply_filters( "cfb_do_redirect_{$this->get_type()}_form_type", true, $this ) ) {
			$redirect_url = empty( $this->redirect ) || ! $is_valid ? cfb_get_current_url() : $this->redirect;
			wp_redirect( $redirect_url );
			exit();
		}

		return false;
	}

	/**
	 * Checks if the form submission is valid or not.
	 *
	 * Currently, this only checks for the nonce. Subclasses may
	 * override this to add additional checks.
	 *
	 * @since     1.0.0
	 * @access    protected
	 *
	 * @param    array $submitted_data
	 *
	 * @return    bool
	 */
	protected function is_submission_valid( $submitted_data ) {
		if ( ! isset( $submitted_data['_cfb_nonce'] ) ) {
			return false;
		}

		return wp_verify_nonce( $submitted_data['_cfb_nonce'], sprintf( $this->_nonce_key, $this->get_name() ) );
	}

	/**
	 * Set the form view.
	 *
	 * @since     1.0.0
	 * @access    public
	 *
	 * @param array $view_args
	 */
	public function set_view( $view_args ) {
		$this->view = $view_args;
	}

	/**
	 * Retrieves the form view object, if available.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return false|CFB_Form_View
	 */
	public function get_view() {
		if ( $this->view && ! $this->view instanceof CFB_Form_View ) {
			$this->view         = (array) $this->view;
			$this->view['type'] = isset( $this->view['type'] ) ? $this->view['type'] : 'default';
			$this->view         = c7_form_builder()->build_form_view( $this->view['type'], $this, $this->view );
		}

		return $this->view;
	}

	/**
	 * Gets the nonce field for the current form.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @return string
	 */
	public function get_nonce_field() {
		return wp_nonce_field( sprintf( $this->_nonce_key, $this->get_name() ), 'c7_form_builder[' . $this->get_name() . '][_cfb_nonce]', false, false );
	}

	/**
	 * Checks if the form has field with the given name.
	 *
	 * @since     1.0.0
	 * @access    public
	 *
	 * @param string $field_name
	 *
	 * @return bool
	 */
	public function has_field( $field_name ) {
		return isset( $this->fields[ $field_name ] );
	}

	/**
	 * Returns a currently registered field with the given name.
	 *
	 * @since     1.0.0
	 * @access    public
	 *
	 * @param string $field_name
	 *
	 * @return false|CFB_Field
	 */
	public function get_field( $field_name ) {
		if ( ! $this->has_field( $field_name ) ) {
			return false;
		}

		if ( ! $this->fields[ $field_name ] instanceof CFB_Field ) {
			$field                       = $this->process_field_args( $this->fields[ $field_name ] );
			$this->fields[ $field_name ] = c7_form_builder()->build_field( $field['type'], $this, $field_name, $field );
		}

		return $this->fields[ $field_name ];
	}

	/**
	 * Process field args array before field is initialized.
	 *
	 * This is intended to be used by the subclasses.
	 *
	 * @since     1.0.0
	 * @access    protected
	 *
	 * @param array $field_args Original field args array
	 *
	 * @return array $field_args Optionally modified args array
	 */
	protected function process_field_args( $field_args ) {
		return $field_args;
	}

	/**
	 * Retrieves names of all the registered fields for the form.
	 *
	 * @since     1.0.0
	 * @access    public
	 *
	 * @param string $tab_id Tab ID
	 *
	 * @return array Array of registered field names.
	 */
	public function get_field_names( $tab_id = null ) {
		if ( $tab_id && $this->has_tabs() ) {
			return isset( $this->_fields_by_tab[ $tab_id ] ) ? array_intersect( array_keys( $this->fields ), $this->_fields_by_tab[ $tab_id ] ) : array();
		}

		return array_keys( $this->fields );
	}

	/**
	 * Removes a field from the form.
	 *
	 * @since     1.0.0
	 * @access    public
	 *
	 * @param $field_name
	 */
	public function remove_field( $field_name ) {
		unset( $this->fields[ $field_name ] );
	}

	/**
	 * Checks if the form has tabs.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return bool
	 */
	public function has_tabs() {
		return ! empty( $this->tabs );
	}

}
