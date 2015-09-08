<?php
/**
 * Post Form Type
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
 * Class CFB_Post_Form
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Forms
 * @author     Chetan Chauhan <chetanchauhan1991@gmail.com>
 */
class CFB_Post_Form extends CFB_Form {

	/**
	 * Registered post types where to show the meta box.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var array
	 */
	public $pages = array();

	/**
	 * The context ( 'normal', 'advanced', 'after_title' or 'side')
	 * where the meta box should be displayed.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var string
	 */
	public $context = 'normal';

	/**
	 * The priority ('high', 'core', 'default' or 'low') within the
	 * context where the boxes should show.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var string
	 */
	public $priority = 'default';

	/**
	 * @since  1.0.0
	 * @access protected
	 */
	protected function initialize() {
		add_action( 'add_meta_boxes', array( $this, 'add_post_forms' ) );
		add_action( 'save_post', array( $this, 'save_form' ) );

		// Do not remove.
		parent::initialize();
	}

	/**
	 * @since  1.0.0
	 * @access protected
	 * @return array
	 */
	protected function get_default_args() {
		return array(
			'view' => array(
				'type'     => 'partial',
				'template' => '{description}{tabs}{fields}',
			)
		);
	}

	/**
	 * Callback to add meta boxes to registered post types.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function add_post_forms() {
		foreach ( $this->pages as $page ) {
			add_meta_box(
				$this->get_name(),
				$this->title,
				array( $this, 'the_form' ),
				$page,
				$this->context,
				$this->priority
			);
		}
	}

	/**
	 * Add 'meta' as default storage to all fields of post form type.
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
				'type'      => 'meta',
				'meta_type' => 'post',
			),
		);

		return array_replace_recursive( $defaults, $field_args );
	}

	/**
	 * Ensure form don't get saved on auto save routine.
	 *
	 * @since     1.0.0
	 * @access    protected
	 *
	 * @param array $submitted_data
	 *
	 * @return bool
	 */
	protected function is_submission_valid( $submitted_data ) {
		return parent::is_submission_valid( $submitted_data ) && ! ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE );
	}

	/**
	 * Renders the meta boxes after the post title.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public static function _do_after_title_meta_boxes() {
		global $post, $wp_meta_boxes;
		do_meta_boxes( get_current_screen(), 'after_title', $post );
		unset( $wp_meta_boxes['post']['after_title'] );
	}

	/**
	 * Retrieves 'post' as form type.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return string
	 */
	public function get_type() {
		return 'post';
	}

}
