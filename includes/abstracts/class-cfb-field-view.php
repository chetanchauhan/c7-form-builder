<?php
/**
 * Abstract Field View Class
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
 * Class CFB_Field_View
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Abstracts
 * @author     Chetan Chauhan <chetanchauhan1991@gmail.com>
 */
abstract class CFB_Field_View extends CFB_View {

	/**
	 * Field being rendered.
	 *
	 * @since     1.0.0
	 * @access    protected
	 * @var       CFB_Field
	 */
	protected $field;

	/**
	 * Set field as view group.
	 *
	 * @since     1.0.0
	 * @access    protected
	 * @var        string
	 */
	protected $_view_group = 'field';

	/**
	 * Field view helpers.
	 *
	 * @since     1.0.0
	 * @access    protected
	 * @var        array
	 */
	protected $_available_helpers = array(
		'label'       => 'CFB_Label_Field_View_Helper',
		'control'     => 'CFB_Control_Field_View_Helper',
		'description' => 'CFB_Description_Field_View_Helper',
	);

	/**
	 * Template to be used for rendering field.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var        string
	 */
	public $template = '{label}{control}{description}';

	/**
	 * Returns the HTML attributes array for the wrapper.
	 *
	 * @since     1.0.0
	 * @access    protected
	 * @return    array
	 */
	protected function get_wrapper_attributes() {
		$wrapper_attributes = array(
			'class'     => "cfb-field-wrapper cfb-{$this->get_type()}-field-view cfb-{$this->field->get_type()}-field",
			'id'        => $this->field->get_html_id() . '-wrapper',
			'data-tab'  => $this->field->tab,
			'data-name' => $this->field->get_name(),
			'data-type' => $this->field->get_type(),
		);

		if ( $this->field->is_repeatable() ) {
			$wrapper_attributes['class']               = $wrapper_attributes['class'] . ' cfb-repeatable';
			$wrapper_attributes['data-repeatable-min'] = absint( $this->field->repeatable );
			$wrapper_attributes['data-repeatable-max'] = absint( $this->field->repeatable_max );
		}

		return CFB_Html::merge_attributes( $this->wrapper_attributes, $wrapper_attributes );
	}

}
