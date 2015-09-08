<?php
/**
 * Abstract Storage Class
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
 * Class CFB_Storage
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Abstracts
 * @author     Chetan Chauhan <chetanchauhan1991@gmail.com>
 */
abstract class CFB_Storage extends CFB_Core {

	/**
	 * The field object to which this storage is attached.
	 *
	 * @since     1.0.0
	 * @access    protected
	 * @var    CFB_Field
	 */
	protected $field;

	/**
	 * The storage key by which the field value is saved.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var    string
	 */
	public $storage_key = '%1$s_%2$s';

	/**
	 * Retrieves the storage type.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return string
	 */
	abstract public function get_type();

	/**
	 * Return the field value from the storage.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return    mixed    If no value is found in the storage, null is returned.
	 */
	abstract public function get_value();

	/**
	 * Update the field value in the storage.
	 *
	 * @since     1.0.0
	 * @access    public
	 *
	 * @param $value
	 */
	abstract function update_value( $value );

	/**
	 * Deletes the value from the storage.
	 *
	 * @since     1.0.0
	 * @access    public
	 */
	abstract public function delete_value();

	/**
	 * Returns the name used for saving the field value, e.g. meta key.
	 *
	 * @since     1.0.0
	 * @access    protected
	 * @return string
	 */
	protected function get_storage_key() {
		return sprintf( $this->storage_key, $this->field->get_form()->get_name(), $this->field->get_name() );
	}

}
