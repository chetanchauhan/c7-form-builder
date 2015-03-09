<?php
/**
 * Option Storage Type
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Storage
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
 * Class CFB_Option_Storage
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Storage
 * @author     Chetan Chauhan <chetanchauhan1991@gmail.com>
 */
class CFB_Option_Storage extends CFB_Storage {

	/**
	 * The option group name corresponding to a option key name.
	 *
	 * If the option group is an empty string, all the field values
	 * will be saved separately. Otherwise, all the field values will be
	 * saved in a single option.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @var     string
	 */
	public $option_group = '%s';

	/**
	 * The key by which the field value is saved.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var    string
	 */
	public $storage_key = '%2$s';

	/**
	 * Retrieves 'option' as storage type.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return string
	 */
	public function get_type() {
		return 'option';
	}

	/**
	 * Retrieves the current field value.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return  mixed If no value is found, null is returned.
	 */
	public function get_value() {
		$option_group = $this->get_option_group();
		$storage_key  = $this->get_storage_key();

		if ( ! empty( $option_group ) ) {
			if ( false === ( $options = get_option( $option_group ) ) || ! isset( $options[ $storage_key ] ) ) {
				return null;
			}

			return $options[ $storage_key ];
		}

		return false === ( $options = get_option( $storage_key ) ) ? null : $options;
	}

	/**
	 * Update the current field value with the provided value.
	 *
	 * @since     1.0.0
	 * @access    public
	 *
	 * @param mixed $value
	 */
	public function update_value( $value ) {
		$option_group = $this->get_option_group();
		$storage_key  = $this->get_storage_key();

		if ( ! empty( $option_group ) ) {
			$options = false === ( $options = get_option( $option_group ) ) ? array() : (array) $options;

			// Bail early if previous value is equal to current value.
			if ( isset( $options[ $storage_key ] ) && $options[ $storage_key ] === $value ) {
				return;
			}

			$options[ $storage_key ] = $value;
			update_option( $option_group, $options );
		} else {
			update_option( $storage_key, $value );
		}
	}

	/**
	 * Delete the current field value.
	 *
	 * @since     1.0.0
	 * @access    public
	 */
	public function delete_value() {
		$option_group = $this->get_option_group();
		$storage_key  = $this->get_storage_key();

		if ( ! empty( $option_group ) ) {
			// Bail early if no value exist already.
			if ( false === ( $options = get_option( $option_group ) ) || ! isset( $options[ $storage_key ] ) ) {
				return;
			}
			unset( $options[ $storage_key ] );
			update_option( $option_group, $options );
		} else {
			delete_option( $storage_key );
		}
	}

	/**
	 * Retrieves the formatted option group name.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @return string
	 */
	protected function get_option_group() {
		return sprintf( $this->option_group, $this->field->get_form()->get_name() );
	}

}
