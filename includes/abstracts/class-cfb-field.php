<?php
/**
 * Abstract Field Class
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Abstracts
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
 * Class CFB_Field
 *
 * Provides base functionality to all field types.
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Abstracts
 * @author     Chetan Chauhan <chetanchauhan1991@gmail.com>
 */
abstract class CFB_Field extends CFB_Core {

	/**
	 * Name of the field.
	 *
	 * @since     1.0.0
	 * @access    protected
	 * @var          string
	 */
	protected $name;

	/**
	 * Form to which this field is attached.
	 *
	 * @since     1.0.0
	 * @access    protected
	 * @var CFB_Form
	 */
	protected $form;

	/**
	 * Parent field to which this field is attached, if applicable.
	 *
	 * Currently, CFB_Field_Group is the only field to support this.
	 *
	 * @since     1.0.0
	 * @access    protected
	 * @var CFB_Field
	 */
	protected $parent = null;

	/**
	 * Holds the field view to be used for rendering.
	 *
	 * @since     1.0.0
	 * @access    protected
	 * @var array|CFB_Field_View
	 */
	protected $view;

	/**
	 * Holds the storage to be used for saving field values.
	 *
	 * @since     1.0.0
	 * @access    protected
	 * @var array|CFB_Storage
	 */
	protected $storage;

	/**
	 * Current value of the field.
	 *
	 * @since     1.0.0
	 * @access    protected
	 * @var        null|mixed
	 */
	protected $_value = null;

	/**
	 * Input type, mainly to support HTML5 input types.
	 *
	 * Subclasses like password may override this to change type attribute
	 * for input tag.
	 *
	 * @since     1.0.0
	 * @access    protected
	 * @var      string
	 */
	protected $_input_type = 'text';

	/**
	 * CSS classes to apply to the individual field control.
	 *
	 * @since     1.0.0
	 * @access    protected
	 * @var         string
	 */
	protected $_input_class = 'regular-text';

	/**
	 * Are we in the middle of rendering current field.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var bool
	 */
	protected $_doing_render = false;

	/**
	 * Default value of the field.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var mixed
	 */
	public $default = null;

	/**
	 * Holds the valid tab id, whenever applicable.
	 *
	 * @var string|false
	 */
	public $tab = false;

	/**
	 * Label for the field control.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var    string
	 */
	public $label = '';

	/**
	 * Description for the field control.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var string
	 */
	public $description = '';

	/**
	 * Tooltip description for the field control.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var string
	 */
	public $tooltip = '';

	/**
	 * Placeholder text for the field control.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var string
	 */
	public $placeholder = '';

	/**
	 * Makes a field required.
	 *
	 * This ensures that the field value is always submitted.
	 *
	 * Note: Currently, this only adds a required boolean HTML attribute to
	 * the supported field controls, and thus, doesn't work in browsers
	 * not supporting it.
	 *
	 * @since     1.0.0
	 * @access    public
	 *
	 * @var bool
	 */
	public $required = false;

	/**
	 * Makes a field as read only.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var bool
	 */
	public $readonly = false;

	/**
	 * Adds disabled boolean HTML attribute to the supported field controls.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var bool
	 */
	public $disabled = false;

	/**
	 * Allows a field to have more than one input control.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var bool|int
	 */
	public $repeatable = false;

	/**
	 * Maximum number of control inputs to display for repeatable field.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var int
	 */
	public $repeatable_max = 0;

	/**
	 * Extra HTML attributes to apply to the individual field control.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var array
	 */
	public $attributes = array();

	/**
	 * @var mixed
	 */
	public $index = null;

	/**
	 * Determines whether value needs to be prepared or not.
	 *
	 * This is generally used internally so as to avoid preparing
	 * field values every time it gets retrieved.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var bool
	 */
	public $prepared = false;

	/**
	 * @return array
	 */
	protected function get_default_args() {
		return array( 'view' => array( 'type' => 'default' ) );
	}

	/**
	 * Retrieves the field type.
	 *
	 * This helps in determining field type once the field object
	 * has been instantiated.
	 *
	 * Subclasses should implement this method to return the field
	 * type alias with which it is registered.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return string
	 */
	abstract public function get_type();

	/**
	 * Retrieves the field name.
	 *
	 * If the field belongs to a parent field, then a colon separated
	 * unique name for the field e.g. grandparent:parent:this is returned.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return string
	 */
	public function get_name() {
		if ( $this->parent ) {
			return "{$this->parent->get_name()}:{$this->name}";
		}

		return $this->name;
	}

	/**
	 * Get the form to which this field is registered.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return CFB_Form
	 */
	public function get_form() {
		return $this->form;
	}

	/**
	 * Enqueue all scripts and styles required by the field.
	 *
	 * This is intended to be used by subclasses.
	 *
	 * @since     1.0.0
	 * @access    public
	 */
	public function enqueue() {
	}

	/**
	 * Renders the individual field control input.
	 *
	 * @since     1.0.0
	 * @access    public
	 *
	 * @param  mixed $value
	 * @param  string $html_name
	 * @param  string $html_id
	 *
	 * @return string
	 */
	public function single_control_html( $value, $html_name, $html_id ) {

		// Merge field control attributes with the user supplied attributes.
		$html_attributes = CFB_Html::merge_attributes(
			$this->attributes,
			array(
				'id'          => $html_id,
				'name'        => $html_name,
				'type'        => $this->_input_type,
				'class'       => "{$this->_input_class}",
				'value'       => $value,
				'placeholder' => $this->placeholder,
				'readonly'    => $this->readonly,
				'disabled'    => $this->disabled,
				'required'    => $this->required,
			)
		);

		return CFB_Html::tag( 'input', $html_attributes );
	}

	/**
	 * Renders the field.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return string
	 */
	public function render() {
		$this->_doing_render = true;
		$output              = '';
		if ( $view = $this->get_view() ) {
			$output = $view->render();
		}
		$this->_doing_render = false;

		return $output;
	}

	/**
	 * Set the field view, if the field view type exists.
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
	 * Retrieves the field view object, if available.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return false|CFB_Field_View
	 */
	public function get_view() {
		if ( $this->view && ! $this->view instanceof CFB_Field_View ) {
			$this->view         = (array) $this->view;
			$this->view['type'] = isset( $this->view['type'] ) ? $this->view['type'] : 'default';
			$this->view         = c7_form_builder()->build_field_view( $this->view['type'], $this, $this->view );
		}

		return $this->view;
	}

	/**
	 * Set the field storage, if the storage type exists.
	 *
	 * @since     1.0.0
	 * @access    public
	 *
	 * @param array $storage_args
	 */
	public function set_storage( $storage_args ) {
		$this->storage = $storage_args;
	}

	/**
	 * Retrieves the storage object, if available.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return    false|CFB_Storage
	 */
	public function get_storage() {
		// Bail early, if either this field belongs to a parent field, or the storage cannot be initialized.
		if ( isset( $this->parent ) || ! $this->storage ) {
			return false;
		}

		if ( ! $this->storage instanceof CFB_Storage ) {
			$this->storage         = (array) $this->storage;
			$this->storage['type'] = isset( $this->storage['type'] ) ? $this->storage['type'] : false;
			$this->storage         = c7_form_builder()->build_storage( $this->storage['type'], $this, $this->storage );
		}

		return $this->storage;
	}

	/**
	 * Set the field value.
	 *
	 * This does not save the field value in the storage.
	 *
	 * @since     1.0.0
	 * @access    public
	 *
	 * @param mixed $value
	 */
	public function set_value( $value ) {
		$this->_value   = $value;
		$this->prepared = false;
	}

	/**
	 * Returns the raw field value.
	 *
	 * If the value is not available, this attempts to load
	 * the value from the storage object.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return mixed
	 */
	public function get_raw_value() {
		// If value is not available, try loading from the storage.
		if ( is_null( $this->_value ) && $storage = $this->get_storage() ) {
			$this->set_value( $storage->get_value() );
		}

		// If value is not prepared, prepare it now.
		if ( ! $this->prepared ) {
			$this->_value   = $this->prepare_value( $this->_value );
			$this->prepared = true;
		}

		return $this->_value;
	}

	/**
	 * Returns the field value after applying sanitization filters.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return mixed
	 */
	public function get_value() {
		$value = $this->get_raw_value();
		if ( $this->is_repeatable() ) {
			return array_map( array( $this, 'apply_filters' ), $value );
		}

		return $this->apply_filters( $value );
	}

	/**
	 * Returns the default value without applying filters.
	 *
	 * @since     1.0.0
	 * @access    protected
	 * @return mixed
	 */
	protected function get_default_value() {
		return $this->default;
	}

	/**
	 * Ensure value is in proper format for rendering/retrieval.
	 *
	 * This effectively takes care of converting values of fields
	 * that was previously repeatable but now is not repeatable,
	 * and vice versa.
	 *
	 * @since     1.0.0
	 * @access    protected
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	protected function prepare_value( $value ) {
		$default_value = $this->get_default_value();

		if ( $this->is_repeatable() ) {

			// Value has to be an array for repeatable fields.
			$value = (array) $value;

			// No numeric key? It is likely to be a request to convert from non repeatable to repeatable.
			if ( ! empty( $value ) && ! count( array_filter( array_keys( $value ), 'is_numeric' ) ) ) {
				$value = array( $value );
			}

			// Remove value of empty row.
			unset( $value['x'] );

			// If there are less values than minimum, then use default value to populate extra values.
			for ( $i = 0; $i < $this->repeatable; $i ++ ) {
				if ( ! array_key_exists( $i, $value ) ) {
					$value[ $i ] = $default_value;
				}
			}

			// If there are more values than allowed, then remove them.
			if ( $this->repeatable_max ) {
				for ( $i = 0; $i < count( $value ); $i ++ ) {
					if ( $i >= $this->repeatable_max ) {
						unset( $value[ $i ] );
					}
				}
			}

			// Add empty row value if we are in middle of rendering the field.
			if ( $this->_doing_render === true ) {
				$value = array_merge( array( 'x' => $default_value ), $value );
			}

			return $value;
		}

		// Convert from repeatable to non repeatable stripping any extra values.
		if ( $this->can_repeat() ) {
			$value = is_array( $value ) && array_key_exists( 0, $value ) ? $value[0] : $value;
		}

		// Use default value if provided, and value is still unavailable.
		$value = is_null( $value ) ? $default_value : $value;

		return $value;
	}

	/**
	 * Apply filters to the passed value.
	 *
	 * @since     1.0.0
	 * @access    protected
	 *
	 * @param mixed $value Un-sanitized value
	 *
	 * @return mixed $value Sanitized value
	 */
	protected function apply_filters( $value ) {
		// Applies a general filter to all the fields.
		$value = apply_filters( 'cfb_filter_field_value', $value, $this );

		// Applies a field type specific filter.
		$value = apply_filters( "cfb_filter_{$this->get_type()}_field_value", $value, $this );

		return $value;
	}

	/**
	 * Return the HTML name attribute value for the field.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return string
	 */
	public function get_html_name() {
		$suffix = '';
		if ( $this->is_repeatable() ) {
			$suffix = '[' . $this->index . ']';
		}
		if ( $this->parent ) {
			return $this->parent->get_html_name() . '[' . $this->name . ']' . $suffix;
		}

		return 'c7_form_builder[' . $this->form->get_name() . '][' . $this->name . ']' . $suffix;
	}

	/**
	 * Return a unique HTML ID for the field.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return string
	 */
	public function get_html_id() {
		if ( $this->parent ) {
			$html_id = $this->parent->get_html_id() . '-' . $this->name;
		} else {
			$html_id = 'cfb-field-' . $this->form->get_name() . '-' . $this->name;
		}
		if ( $this->is_repeatable() && isset( $this->index ) ) {
			$html_id .= '-' . $this->index;
		}

		return $html_id;
	}

	/**
	 * Checks if a field is repeatable or not.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return  bool
	 */
	public function is_repeatable() {
		return (bool) ( $this->repeatable && $this->can_repeat() );
	}

	/**
	 * Retrieves whether this field type has support for repeatable inputs.
	 *
	 * Subclasses can override this to remove inbuilt support for
	 * repeatable fields.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return      bool
	 */
	public function can_repeat() {
		return true;
	}

}
