<?php
/**
 * Group Field Type
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
 * Class CFB_Group_Field
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Fields
 * @author     Chetan Chauhan <chetanchauhan1991@gmail.com>
 */
class CFB_Group_Field extends CFB_Field {

	/**
	 * Holds the registered children fields.
	 *
	 * @since     1.0.0
	 * @access    protected
	 * @var array
	 */
	protected $children = array();

	/**
	 * @since  1.0.0
	 * @access protected
	 */
	protected function initialize() {
		// Prepare child fields.
		$children = array();

		foreach ( $this->children as $child_name => $child_args ) {
			// Skip the current child field, if type is either not specified or invalid.
			if ( ! isset( $child_args['type'] ) || ! c7_form_builder()->has_field_type( $child_args['type'] ) ) {
				continue;
			}
			// Child field cannot belong to a particular tab.
			$child_args['tab']                       = false;
			$children[ sanitize_key( $child_name ) ] = $child_args;
		}

		$this->children = $children;

		// Sort child fields by priority.
		$this->children = array_reverse( $this->children );
		uasort( $this->children, '_cfb_cmp_priority' );

		// Do not remove.
		parent::initialize();
	}

	/**
	 * @since 1.0.0
	 * @access protected
	 * @return array
	 */
	protected function get_default_args() {
		return array(
			'view' => array(
				'type'    => 'default',
				'helpers' => array(
					'label' => array(
						'tag' => 'div',
					),
				),
			)
		);
	}

	/**
	 * @since   1.0.0
	 * @access  public
	 *
	 * @param string $child_name
	 *
	 * @return false|CFB_Field
	 */
	public function get_child_field( $child_name ) {
		if ( ! isset( $this->children[ $child_name ] ) ) {
			return false;
		}

		if ( ! $this->children[ $child_name ] instanceof CFB_Field ) {
			$child_field                   = $this->children[ $child_name ];
			$child_field['parent']         = $this;
			$this->children[ $child_name ] = c7_form_builder()->build_field( $child_field['type'], $this->form, $child_name, $child_field );
		}

		return $this->children[ $child_name ];
	}

	/**
	 * @param mixed  $value
	 * @param string $html_name
	 * @param string $html_id
	 *
	 * @return string
	 */
	public function single_control_html( $value, $html_name, $html_id ) {

		$html = array();
		foreach ( $this->children as $child_name => $child ) {
			$child = $this->get_child_field( $child_name );
			$child->set_value( $value[ $child_name ] );
			$html[] = $child->render();
		}

		return implode( "\n", $html );
	}

	/**
	 * @since     1.0.0
	 * @access    protected
	 * @return  array
	 */
	protected function get_default_value() {
		$default = array();
		foreach ( $this->children as $child_name => $child ) {
			$child                  = $this->get_child_field( $child_name );
			$default[ $child_name ] = $child->get_default_value();
		}

		return $default;
	}

	/**
	 * @since     1.0.0
	 * @access    protected
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	protected function prepare_value( $value ) {
		$value = parent::prepare_value( $value );
		if ( $this->is_repeatable() ) {
			return array_map( array( $this, 'prepare_children_value' ), $value );
		}

		return $this->prepare_children_value( $value );
	}

	/**
	 * @since     1.0.0
	 * @access    protected
	 *
	 * @param mixed $value
	 *
	 * @return array
	 */
	protected function prepare_children_value( $value ) {
		$prepared_value = array();
		foreach ( $this->children as $child_name => $child ) {
			$child = $this->get_child_field( $child_name );
			if ( ! isset( $value[ $child_name ] ) ) {
				$value[ $child_name ] = null;
			}
			$child->set_value( $value[ $child_name ] );
			$prepared_value[ $child_name ] = $child->get_raw_value();
		}

		return $prepared_value;
	}

	/**
	 * @since     1.0.0
	 * @access    protected
	 *
	 * @param mixed $value Un-sanitized value
	 *
	 * @return mixed $value Sanitized value
	 */
	protected function apply_filters( $value ) {
		foreach ( $value as $child_name => $val ) {
			$child = $this->get_child_field( $child_name );
			$child->set_value( $val );
			$value[ $child_name ] = $child->get_value();
		}

		return $value;
	}

	/**
	 * Retrieves 'group' as field type.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return string
	 */
	public function get_type() {
		return 'group';
	}

}
