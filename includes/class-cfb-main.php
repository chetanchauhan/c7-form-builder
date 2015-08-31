<?php
/**
 * Main C7 Form Builder Class
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Main
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
 * Class CFB_Main
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Main
 * @author     Chetan Chauhan <chetanchauhan1991@gmail.com>
 */
final class CFB_Main {

	/**
	 * Holds the registered forms.
	 *
	 * @since     1.0.0
	 * @access    private
	 * @var CFB_Form[]
	 */
	private $forms = array();

	/**
	 * Registered form types.
	 *
	 * @since     1.0.0
	 * @access    private
	 * @var array
	 */
	private $_form_types = array(
		'admin'    => 'CFB_Admin_Form',
		'post'     => 'CFB_Post_Form',
		'taxonomy' => 'CFB_Taxonomy_Form',
		'theme'    => 'CFB_Theme_Form',
		'user'     => 'CFB_User_Form',
	);

	/**
	 * Registered form field types.
	 *
	 * @since     1.0.0
	 * @access    private
	 * @var array
	 */
	private $_field_types = array(
		'color'    => 'CFB_Color_Field',
		'editor'   => 'CFB_Editor_Field',
		'email'    => 'CFB_Email_Field',
		'group'    => 'CFB_Group_Field',
		'hidden'   => 'CFB_Hidden_Field',
		'number'   => 'CFB_Number_Field',
		'password' => 'CFB_Password_Field',
		'select'   => 'CFB_Select_Field',
		'submit'   => 'CFB_Submit_Field',
		'text'     => 'CFB_Text_Field',
		'textarea' => 'CFB_Textarea_Field',
		'url'      => 'CFB_URL_Field',
	);

	/**
	 * Registered storage types.
	 *
	 * @since     1.0.0
	 * @access    private
	 * @var array
	 */
	private $_storage_types = array(
		'meta'     => 'CFB_Meta_Storage',
		'option'   => 'CFB_Option_Storage',
		'taxonomy' => 'CFB_Taxonomy_Storage',
	);

	/**
	 * Registered form view types.
	 *
	 * @since     1.0.0
	 * @access    private
	 * @var array
	 */
	private $_form_view_types = array(
		'default' => 'CFB_Default_Form_View',
		'partial' => 'CFB_Partial_Form_View',
	);


	/**
	 * Registered form field view types.
	 *
	 * @since     1.0.0
	 * @access    private
	 * @var array
	 */
	private $_field_view_types = array(
		'default' => 'CFB_Default_Field_View',
		'hidden'  => 'CFB_Hidden_Field_View',
	);

	/**
	 * Initializes the object instance.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function __construct() {

		// i18n
		$this->load_textdomain();

		// Enqueue scripts and styles.
		add_action( 'admin_enqueue_scripts', array( $this, '_enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, '_enqueue_scripts' ) );

		// Add action that handles registration of forms.
		add_action( 'init', array( $this, '_register' ), 30 );

		add_filter( 'cfb_maybe_init_admin_form', 'is_admin' );
		add_filter( 'cfb_maybe_init_post_form', 'cfb_is_post_edit_screen' );
		add_filter( 'cfb_maybe_init_taxonomy_form', 'cfb_is_term_edit_screen' );
		add_filter( 'cfb_maybe_init_theme_form', 'cfb_is_frontend' );
		add_filter( 'cfb_maybe_init_user_form', 'cfb_is_user_edit_screen' );

		add_filter( 'cfb_do_redirect_post_form_type', '__return_false' );
		add_filter( 'cfb_do_redirect_user_form_type', '__return_false' );
		add_filter( 'cfb_do_redirect_taxonomy_form_type', '__return_false' );

		add_shortcode( 'cfb_form', array( $this, 'shortcode_cb' ) );

		if ( is_admin() ) {
			add_action( 'edit_form_after_title', array( 'CFB_Post_Form', '_do_after_title_meta_boxes' ), 1 );
		}

		// Remove all the saved data when a term is deleted.
		add_action( 'delete_term', array( 'CFB_Taxonomy_Storage', '_garbage_collect' ), 10, 2 );

		do_action( 'cfb_loaded', $this );
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @since     1.0.0
	 * @access    public
	 */
	public function _enqueue_scripts() {
		// Bail early if no form has been initialized already.
		if ( array() === ( $forms = array_filter( $this->forms, 'is_object' ) ) ) {
			return;
		}

		// Use minified scripts if SCRIPT_DEBUG is turned off.
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Enqueue common scripts and styles.
		wp_enqueue_script( 'cfb-event-manager', C7_FORM_BUILDER_URL . "assets/js/event-manager{$suffix}.js", array(), C7_FORM_BUILDER_VERSION );
		wp_enqueue_script( 'c7-form-builder', C7_FORM_BUILDER_URL . "assets/js/cfb{$suffix}.js", array(
			'jquery',
			'cfb-event-manager',
		), C7_FORM_BUILDER_VERSION );
		wp_enqueue_style( 'c7-form-builder', C7_FORM_BUILDER_URL . "assets/css/cfb{$suffix}.css", array(), C7_FORM_BUILDER_VERSION );

		wp_localize_script( 'c7-form-builder', 'cfbL10n', apply_filters( 'cfb_script_localized_data', array(
					'add_control_button_text'    => __( 'Add', 'c7-form-builder' ),
					'remove_control_button_text' => __( 'Remove', 'c7-form-builder' ),
					'sort_control_button_text'   => __( 'Drag to reorder', 'c7-form-builder' ),
				)
			)
		);

		foreach ( $forms as $form ) {
			// Enqueue scripts for each unique form type exactly once.
			if ( ! isset( $enqueued['forms'][ $form->get_type() ] ) ) {
				$form->enqueue();
				$enqueued['forms'][ $form->get_type() ] = true;
			}

			foreach ( $form->get_field_names() as $field_name ) {
				$field = $form->get_field( $field_name );

				// Enqueue jquery ui sortable script if field is repeatable.
				if ( $field->is_repeatable() ) {
					wp_enqueue_script( 'jquery-ui-sortable' );
				}

				// Enqueue scripts for all registered field type exactly once.
				if ( ! isset( $enqueued['fields'][ $field->get_type() ] ) ) {
					$field->enqueue();
					$enqueued['field'][ $field->get_type() ] = true;
				}
			}
		}
	}

	/**
	 * Fires the registration hook.
	 *
	 * @since     1.0.0
	 * @access    public
	 */
	public function _register() {
		do_action( 'cfb_register', $this );
	}

	/**
	 * Renders the form via shortcode.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public function shortcode_cb( $atts ) {
		$atts = shortcode_atts( array(
			'name' => '',
		), $atts, 'cfb_form' );

		if ( $form = $this->get_form( $atts['name'] ) ) {
			return $form->render();
		}

		return '';
	}


	/**
	 * Load the text domain for translation.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function load_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'c7-form-builder' );
		load_textdomain( 'c7-form-builder', WP_LANG_DIR . '/c7-form-builder/c7-form-builder-' . $locale . '.mo' );
		load_plugin_textdomain( 'c7-form-builder', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Registers a form.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param string $form_name Unique name of the form(required).
	 * @param string $form_type Type of the form.
	 * @param array $form_args Form args.
	 *
	 * @return bool
	 */
	public function register_form( $form_name, $form_type, $form_args = array() ) {

		$form_name = sanitize_key( $form_name );

		// Bail early if form is already registered or form type is invalid.
		if ( $this->has_form( $form_name ) || ! $this->has_form_type( $form_type ) ) {
			return false;
		}

		// Merge form type in the form args so that it can be initialized later on.
		$form_args['type'] = $form_type;

		// Register the form.
		$this->forms[ $form_name ] = $form_args;

		// Filters whether to initialize the form right at the time of registration.
		$maybe_init = apply_filters( 'cfb_maybe_init_form', false, $form_name, $form_args );
		$maybe_init = apply_filters( "cfb_maybe_init_{$form_args['type']}_form", $maybe_init, $form_name, $form_args );

		if ( $maybe_init ) {
			$this->get_form( $form_name );
		}

		// Form is registered successfully.
		return true;
	}

	/**
	 * Retrieves a registered form.
	 *
	 * @since     1.0.0
	 * @access    public
	 *
	 * @param string $form_name Name of the registered form.
	 *
	 * @return bool|CFB_Form    Form object or false if form is not registered.
	 */
	public function get_form( $form_name ) {
		if ( ! $this->has_form( $form_name ) ) {
			return false;
		}

		if ( ! $this->forms[ $form_name ] instanceof CFB_Form ) {
			$this->forms[ $form_name ] = $this->build_form( $this->forms[ $form_name ]['type'], $form_name, $this->forms[ $form_name ] );
		}

		return $this->forms[ $form_name ];
	}

	/**
	 * Checks if a form is registered or not.
	 *
	 * @param string $form_name Name of the form to be checked.
	 *
	 * @return bool
	 */
	public function has_form( $form_name ) {
		return isset( $this->forms[ $form_name ] );
	}

	/**
	 * Registers a custom form type.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param      $form_type
	 * @param      $class_name
	 * @param bool $force
	 *
	 * @return bool
	 */
	public function register_form_type( $form_type, $class_name, $force = false ) {
		$form_type = sanitize_key( $form_type );

		if ( ( ! $this->has_form_type( $form_type ) || $force ) && $this->maybe_register_type( 'CFB_Form', $class_name ) ) {
			$this->_form_types[ $form_type ] = $class_name;

			return true;
		}

		return false;
	}

	/**
	 * Checks if a form type is registered or not.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param $form_type
	 *
	 * @return bool
	 */
	public function has_form_type( $form_type ) {
		return isset( $this->_form_types[ $form_type ] );
	}

	/**
	 * Build a form object.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param       $form_type
	 * @param       $form_name
	 * @param array $form_args
	 *
	 * @return false|CFB_Form
	 */
	public function build_form( $form_type, $form_name, $form_args = array() ) {
		$class_name        = $this->has_form_type( $form_type ) ? $this->_form_types[ $form_type ] : false;
		$form_args['name'] = $form_name;

		return $class_name ? new $class_name( $form_args ) : false;
	}

	/**
	 * Registers a custom field type.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param      $field_type
	 * @param      $class_name
	 * @param bool $force
	 *
	 * @return bool
	 */
	public function register_field_type( $field_type, $class_name, $force = false ) {
		$field_type = sanitize_key( $field_type );

		if ( ( ! $this->has_field_type( $field_type ) || $force ) && $this->maybe_register_type( 'CFB_Field', $class_name ) ) {
			$this->_field_types[ $field_type ] = $class_name;

			return true;
		}

		return false;
	}

	/**
	 * Checks if a field type is registered or not.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param $field_type
	 *
	 * @return bool
	 */
	public function has_field_type( $field_type ) {
		return isset( $this->_field_types[ $field_type ] );
	}

	/**
	 * Build a field object.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param string $field_type
	 * @param CFB_Form $form
	 * @param string $field_name
	 * @param array $field_args
	 *
	 * @return false|CFB_Field
	 */
	public function build_field( $field_type, CFB_Form $form, $field_name, $field_args = array() ) {
		$class_name         = $this->has_field_type( $field_type ) ? $this->_field_types[ $field_type ] : false;
		$field_args['name'] = $field_name;
		$field_args['form'] = $form;

		return $class_name ? new $class_name( $field_args ) : false;
	}

	/**
	 * Registers a storage type.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param      $storage_type
	 * @param      $class_name
	 * @param bool $force
	 *
	 * @return bool
	 */
	public function register_storage_type( $storage_type, $class_name, $force = false ) {
		$storage_type = sanitize_key( $storage_type );

		if ( ( ! $this->has_field_type( $storage_type ) || $force ) && $this->maybe_register_type( 'CFB_Storage', $class_name ) ) {
			$this->_storage_types[ $storage_type ] = $class_name;

			return true;
		}

		return false;
	}

	/**
	 * Checks if a storage type is registered or not.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param $storage_type
	 *
	 * @return bool
	 */
	public function has_storage_type( $storage_type ) {
		return isset( $this->_storage_types[ $storage_type ] );
	}

	/**
	 * Build a field object.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $storage_type
	 * @param  CFB_Field $field
	 * @param  array $storage_args
	 *
	 * @return false|CFB_Storage
	 */
	public function build_storage( $storage_type, CFB_Field $field, $storage_args = array() ) {
		$class_name            = $this->has_storage_type( $storage_type ) ? $this->_storage_types[ $storage_type ] : false;
		$storage_args['field'] = $field;

		return $class_name ? new $class_name( $storage_args ) : false;
	}

	/**
	 * Registers a form view type.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param string $view_type
	 * @param string $class_name
	 * @param bool $force
	 *
	 * @return bool
	 */
	public function register_form_view( $view_type, $class_name, $force = false ) {
		$view_type = sanitize_key( $view_type );

		if ( ( ! $this->has_form_view( $view_type ) || $force ) && $this->maybe_register_type( 'CFB_Form_View', $class_name ) ) {
			$this->_form_view_types[ $view_type ] = $class_name;

			return true;
		}

		return false;
	}

	/**
	 * Checks if a form view type exists.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param $form_view_type
	 *
	 * @return bool
	 */
	public function has_form_view( $form_view_type ) {
		return isset( $this->_form_view_types[ $form_view_type ] );
	}

	/**
	 * Build a form view object.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param          $view_type
	 * @param CFB_Form $form
	 * @param array $view_args
	 *
	 * @return false|CFB_Form_View
	 */
	public function build_form_view( $view_type, CFB_Form $form, $view_args = array() ) {
		$class_name        = $this->has_form_view( $view_type ) ? $this->_form_view_types[ $view_type ] : false;
		$view_args['form'] = $form;

		return $class_name ? new $class_name( $view_args ) : false;
	}

	/**
	 * Registers a field view type.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param      $view_type
	 * @param      $class_name
	 * @param bool $force
	 *
	 * @return bool
	 */
	public function register_field_view( $view_type, $class_name, $force = false ) {
		$view_type = sanitize_key( $view_type );

		if ( ( ! $this->has_field_view( $view_type ) || $force ) && $this->maybe_register_type( 'CFB_Field_View', $class_name ) ) {
			$this->_field_view_types[ $view_type ] = $class_name;

			return true;
		}

		return false;
	}

	/**
	 * Checks if a field view type exists.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param $field_view_type
	 *
	 * @return bool
	 */
	public function has_field_view( $field_view_type ) {
		return isset( $this->_field_view_types[ $field_view_type ] );
	}

	/**
	 * Build a form view object.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param           $view_type
	 * @param CFB_Field $field
	 * @param array $view_args
	 *
	 * @return false|CFB_Field_View
	 */
	public function build_field_view( $view_type, CFB_Field $field, $view_args = array() ) {
		$class_name         = $this->has_field_view( $view_type ) ? $this->_field_view_types[ $view_type ] : false;
		$view_args['field'] = $field;

		return $class_name ? new $class_name( $view_args ) : false;
	}

	/**
	 * Validates if class exists and extends from a base class.
	 *
	 * @since  1.0.0
	 * @access private
	 *
	 * @param $abstract_class
	 * @param $class_name
	 *
	 * @return bool
	 */
	private function maybe_register_type( $abstract_class, $class_name ) {
		if ( class_exists( $class_name ) && is_subclass_of( $class_name, $abstract_class ) ) {
			return true;
		}

		return false;
	}

}
