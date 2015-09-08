<?php
/**
 * Taxonomy Storage Type
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Storage
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
 * Class CFB_Taxonomy_Storage
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Storage
 * @author     Chetan Chauhan <chetanchauhan1991@gmail.com>
 */
class CFB_Taxonomy_Storage extends CFB_Storage {

	/**
	 * Term taxonomy ID.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @var     int
	 */
	public $tt_id;

	/**
	 * Retrieves 'taxonomy' as storage type.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return string
	 */
	public function get_type() {
		return 'taxonomy';
	}

	/**
	 * Retrieves taxonomy term field value.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return  mixed If no value is found, null is returned.
	 */
	public function get_value() {
		if ( ! $tt_id = $this->get_taxonomy_term_id() ) {
			return null;
		}

		$storage_key = $this->get_storage_key();
		$term_meta   = (array) get_option( 'c7_form_builder_term_meta', null );

		return isset( $term_meta[ $tt_id ][ $storage_key ] ) ? $term_meta[ $tt_id ][ $storage_key ] : null;
	}

	/**
	 * Update taxonomy term field value.
	 *
	 * @since     1.0.0
	 * @access    public
	 *
	 * @param mixed $value
	 */
	public function update_value( $value ) {
		if ( ! $tt_id = $this->get_taxonomy_term_id() ) {
			return;
		}

		$storage_key = $this->get_storage_key();
		$term_meta   = false === ( $term_meta = get_option( 'c7_form_builder_term_meta' ) ) ? array() : (array) $term_meta;

		// No need to update if previous value is equal to the current value.
		if ( isset( $term_meta[ $tt_id ][ $storage_key ] ) && $term_meta[ $tt_id ][ $storage_key ] === $value ) {
			return;
		}

		// Update the field value with the current value.
		$term_meta[ $tt_id ][ $storage_key ] = $value;
		update_option( 'c7_form_builder_term_meta', $term_meta );
	}

	/**
	 * Delete the current field value.
	 *
	 * @since     1.0.0
	 * @access    public
	 */
	public function delete_value() {
		if ( ! $tt_id = $this->get_taxonomy_term_id() ) {
			return;
		}

		$storage_key = $this->get_storage_key();

		// Bail early if no value exist for the current field.
		if ( false === ( $term_meta = get_option( 'c7_form_builder_term_meta' ) ) || ! isset( $term_meta[ $tt_id ][ $storage_key ] ) ) {
			return;
		}

		// Delete the field value.
		unset( $term_meta[ $tt_id ][ $storage_key ] );
		update_option( 'c7_form_builder_term_meta', $term_meta );
	}

	/**
	 * Delete all the saved data for the taxonomy term being deleted.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param int $term  Term ID.
	 * @param int $tt_id Term taxonomy ID.
	 */
	public static function _garbage_collect( $term, $tt_id ) {
		if ( false === ( $term_meta = get_option( 'c7_form_builder_term_meta' ) ) || ! isset( $term_meta[ $tt_id ] ) ) {
			return;
		}

		unset( $term_meta[ $tt_id ] );
		update_option( 'c7_form_builder_term_meta', $term_meta );
	}

	/**
	 * Retrieves the taxonomy term id.
	 *
	 * If it is not available, it is retrieved from the global scope.
	 * This is useful when taxonomy form gets displayed on term edit
	 * screen.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @return integer
	 */
	protected function get_taxonomy_term_id() {
		if ( ! isset( $this->tt_id ) ) {
			$this->tt_id = 0;
			if ( isset( $_GET['tag_ID'] ) && isset( $_GET['taxonomy'] ) ) {
				$term = get_term( $_GET['tag_ID'], $_GET['taxonomy'] );
				if ( $term && ! is_wp_error( $term ) ) {
					$this->tt_id = $term->term_taxonomy_id;
				}
			}
		}

		return absint( $this->tt_id );
	}

}
