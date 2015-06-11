<?php
/**
 * Meta Storage Type
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
 * Class CFB_Meta_Storage
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Storage
 * @author     Chetan Chauhan <chetanchauhan1991@gmail.com>
 */
class CFB_Meta_Storage extends CFB_Storage {

	/**
	 * Meta type such as 'post', 'user' and 'comment'.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var string
	 */
	public $meta_type = 'post';

	/**
	 * Object ID for the meta type.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @var     int
	 */
	public $object_id;

	/**
	 * Whether retrieve only the first value of the specified storage key.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @var bool
	 */
	public $single = false;

	/**
	 * Metadata key by which the field value is saved.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var    string
	 */
	public $storage_key = '_%1$s_%2$s';

	/**
	 * Retrieves 'meta' as storage type.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return string
	 */
	public function get_type() {
		return 'meta';
	}

	/**
	 * Retrieves field value from the metadata.
	 *
	 * @since     1.0.0
	 * @access    public
	 *
	 * @see       get_metadata()
	 *
	 * @return  mixed If no metadata is found, null is returned.
	 */
	public function get_value() {
		$value = get_metadata( $this->meta_type, $this->get_object_id(), $this->get_storage_key(), $this->single );

		// Value not found?
		if ( $value === array() || $value === false ) {
			return null;
		}

		return $value;
	}

	/**
	 * Update metadata with the field value.
	 *
	 * @since     1.0.0
	 * @access    public
	 *
	 * @see       update_metadata()
	 * @see       add_metadata()
	 *
	 * @param $value
	 */
	public function update_value( $value ) {
		$value = wp_slash( $value );

		// If the value is an associative array, save it as a single metadata entry.
		if ( is_array( $value ) && cfb_is_array_assoc( $value ) ) {
			update_metadata( $this->meta_type, $this->get_object_id(), $this->get_storage_key(), $value );
		} else {
			// Delete current values.
			$this->delete_value();

			// Save each value separately under the same meta key, thereby
			// allowing querying posts, users, etc. by meta key value.
			foreach ( (array) $value as $val ) {
				add_metadata( $this->meta_type, $this->get_object_id(), $this->get_storage_key(), $val );
			}
		}
	}

	/**
	 * Delete all metadata entries with the specified storage key.
	 *
	 * @since     1.0.0
	 * @access    public
	 *
	 * @see       delete_metadata()
	 */
	public function delete_value() {
		delete_metadata( $this->meta_type, $this->get_object_id(), $this->get_storage_key() );
	}

	/**
	 * Retrieves the object id.
	 *
	 * If object id is not available, it is retrieved from global scope.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @return integer $object_id Object ID
	 */
	protected function get_object_id() {
		if ( ! isset( $this->object_id ) ) {
			$this->object_id = cfb_get_object_id( $this->meta_type );
		}

		return $this->object_id;
	}

}
