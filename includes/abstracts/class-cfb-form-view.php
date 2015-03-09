<?php
/**
 * Abstract Form View Class
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
 * Class CFB_Form_View
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Abstracts
 * @author     Chetan Chauhan <chetanchauhan1991@gmail.com>
 */
abstract class CFB_Form_View extends CFB_View {

	/**
	 * Form being rendered.
	 *
	 * @since     1.0.0
	 * @access    protected
	 * @var       CFB_Form
	 */
	protected $form;

	/**
	 * Set form as view group.
	 *
	 * @since     1.0.0
	 * @access    protected
	 * @var        string
	 */
	protected $_view_group = 'form';

	/**
	 * Available form view helpers.
	 *
	 * @since     1.0.0
	 * @access    protected
	 * @var       array
	 */
	protected $_available_helpers = array(
		'title'       => 'CFB_Title_Form_View_Helper',
		'description' => 'CFB_Description_Form_View_Helper',
		'tabs'        => 'CFB_Tabs_Form_View_Helper',
		'fields'      => 'CFB_Fields_Form_View_Helper',
	);

	/**
	 * Template to be used for rendering the complete form.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var       string
	 */
	public $template = '{title}{description}{tabs}{fields}';

	/**
	 * Overrides the parent method to output nonce field.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return    string
	 */
	public function render() {
		$html = $this->render_template() . $this->form->get_nonce_field();

		return CFB_Html::tag( $this->get_wrapper_tag(), $this->get_wrapper_attributes(), $html );
	}

	/**
	 * Returns the HTML attributes array for the wrapper.
	 *
	 * @since     1.0.0
	 * @access    protected
	 * @return    array
	 */
	protected function get_wrapper_attributes() {
		return CFB_Html::merge_attributes(
			$this->wrapper_attributes, array(
				'class'     => "cfb-form-wrapper cfb-{$this->get_type()}-form-view cfb-{$this->form->get_type()}-form",
				'id'        => 'cfb-form-' . $this->form->get_name() . '-wrapper',
				'data-name' => $this->form->get_name(),
				'data-type' => $this->form->get_type(),
			)
		);
	}

}
