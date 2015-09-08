<?php
/**
 * Taxonomy Form Type
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Forms
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
 * Class CFB_Taxonomy_Form
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Forms
 * @author     Chetan Chauhan <chetanchauhan1991@gmail.com>
 */
class CFB_Taxonomy_Form extends CFB_Form {

	/**
	 * Registered taxonomies where to show the form.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var array
	 */
	public $taxonomies = array();

	/**
	 * Term taxonomy ID.
	 *
	 * This is used internally to initialize taxonomy
	 * storage with the current taxonomy term id while
	 * saving taxonomy term fields.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @var     int
	 */
	protected $tt_id;

	/**
	 * @since  1.0.0
	 * @access protected
	 */
	protected function initialize() {
		foreach ( $this->taxonomies as $taxonomy ) {
			add_action( $taxonomy . '_add_form_fields', array( $this, 'the_form' ) );
			add_action( $taxonomy . '_edit_form', array( $this, 'the_form' ) );

			add_action( 'created_term', array( $this, 'save_taxonomy_form' ), 10, 2 );
			add_action( 'edited_term', array( $this, 'save_taxonomy_form' ), 10, 2 );
		}

		// Do not remove.
		parent::initialize();
	}

	/**
	 * @since  1.0.0
	 * @access protected
	 * @return array
	 */
	protected function get_default_args() {
		$default_args['view']['type'] = 'partial';

		if ( cfb_is_term_edit_screen() == 'edit' ) {
			$default_args['view']['helpers'] = array(
				'fields' => array(
					'wrapper_tag'        => 'table',
					'wrapper_attributes' => array(
						'class' => 'form-table',
					),
					'tag'                => 'tbody',
				),
			);
		}

		return $default_args;
	}

	/**
	 * Add default args to all the fields of taxonomy form type.
	 *
	 * This currently adds 'taxonomy' as default storage and custom template
	 * args to display the fields using table markup on taxonomy edit page.
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
			'storage' => array(
				'type'  => 'taxonomy',
				'tt_id' => $this->tt_id,
			),
		);
		if ( cfb_is_term_edit_screen() == 'edit' ) {
			$defaults['view'] = array(
				'wrapper_tag' => 'tr',
				'template'    => '<th scope="row">{label}</th><td>{control}{description}</td>',
			);
		}

		return array_replace_recursive( $defaults, $field_args );
	}

	/**
	 * Handles saving of taxonomy term fields.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param int $term_id Term ID.
	 * @param int $tt_id   Term taxonomy ID.
	 *
	 * @return bool
	 */
	public function save_taxonomy_form( $term_id, $tt_id ) {
		$this->tt_id = $tt_id;

		return $this->save_form();
	}

	/**
	 * Retrieves 'taxonomy' as form type.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return string
	 */
	public function get_type() {
		return 'taxonomy';
	}

}
