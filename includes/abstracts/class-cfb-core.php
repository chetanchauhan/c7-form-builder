<?php
/**
 * Abstract Core Class
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
 * Class CFB_Core
 *
 * Provides args handling functionality to other abstract classes such as
 * CFB_Form, CFB_Field, etc.
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Abstracts
 * @author     Chetan Chauhan <chetanchauhan1991@gmail.com>
 */
class CFB_Core {

	/**
	 * Initializes the object instance.
	 *
	 * @since     1.0.0
	 * @access    public
	 *
	 * @param    array $args
	 */
	public function __construct( $args = array() ) {
		$args = array_replace_recursive( $this->get_default_args(), $args );
		$args = $this->pre_assign_args( $args );
		$this->assign_args( $args );
		$this->initialize();
	}

	/**
	 * Retrieves the default args.
	 *
	 * The resulting array is replaced with the elements from the
	 * supplied args array.
	 *
	 * This is intended to be used by the subclasses.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @return array
	 */
	protected function get_default_args(){
		return array();
	}

	/**
	 * Allows to modify the $args array.
	 *
	 * This is intended to be used by the subclasses for any
	 * normalization of form args array before it gets assigned.
	 *
	 * @since     1.0.0
	 * @access    protected
	 *
	 * @param   array $args
	 *
	 * @return    array    $args
	 */
	protected function pre_assign_args( $args ) {
		return $args;
	}

	/**
	 * Assign the values in the $args array to the class properties.
	 *
	 * @since     1.0.0
	 * @access    protected
	 *
	 * @param    array $args
	 *
	 * @return    void
	 */
	protected function assign_args( $args ) {

		foreach ( $args as $name => $value ) {
			if ( 0 === strpos( $name, '_' ) ) {
				continue;
			} elseif ( method_exists( $this, "set_{$name}" ) && ! property_exists( $this, "_{$name}" ) ) {
				call_user_func( array( $this, "set_{$name}" ), $value );
			} elseif ( property_exists( $this, $name ) ) {
				$this->{$name} = $value;
			}
		}

	}

	/**
	 * Invoked after assigning the properties.
	 *
	 * Subclasses should override this to add actions, hooks,
	 * override/initialize class properties, etc.
	 *
	 * @since     1.0.0
	 * @access    protected
	 */
	protected function initialize() {
	}

}
