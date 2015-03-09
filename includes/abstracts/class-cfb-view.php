<?php
/**
 * Abstract View Class
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
 * Class CFB_View
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Abstracts
 * @author     Chetan Chauhan <chetanchauhan1991@gmail.com>
 */
abstract class CFB_View extends CFB_Core {

	/**
	 * View group (either field or form).
	 *
	 * @since     1.0.0
	 * @access    protected
	 * @var        string
	 */
	protected $_view_group = 'unspecified';

	/**
	 * Registered helpers that can be used for rendering.
	 *
	 * Subclasses can easily add or remove the support for helpers
	 * by overriding this property.
	 *
	 * @since     1.0.0
	 * @access    protected
	 * @var        array
	 */
	protected $_available_helpers = array();

	/**
	 * The template to be used for rendering.
	 *
	 * This can be used to append or prepend any extra HTML to the
	 * helper output, and control the order in which helpers output
	 * gets generated. The final output is then wrapped into another
	 * HTML element.
	 *
	 * All the available helpers can be used in the template
	 * as "{helper_type}".
	 *
	 * @since      1.0.0
	 * @access     public
	 * @var        string
	 */
	public $template = '';

	/**
	 * Holds the view helpers object instances.
	 *
	 * This also collects the args for helper classes
	 * as helpers[helper_type] which are delegated to the
	 * helper class at the time of instantiation.
	 *
	 * @since     1.0.0
	 * @access    protected
	 * @var        array
	 */
	protected $helpers = array();

	/**
	 * HTML tag to be used for wrapper.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var        string
	 */
	public $wrapper_tag = 'div';

	/**
	 * HTML attributes array for the wrapper.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var        array
	 */
	public $wrapper_attributes = array();

	/**
	 * The main method that renders the form or field.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return    string
	 */
	public function render() {
		return CFB_Html::tag( $this->get_wrapper_tag(), $this->get_wrapper_attributes(), $this->render_template() );
	}

	/**
	 * Renders the template using the available helpers.
	 *
	 * @since     1.0.0
	 * @access    protected
	 * @return    string
	 */
	protected function render_template() {
		if ( ! preg_match_all( "/\\{([^}]*)\\}/", $this->template, $template_tags ) ) {
			return $this->template;
		}

		foreach ( $template_tags[1] as &$template_tag ) {
			if ( $helper = $this->get_helper( $template_tag ) ) {
				$template_tag = $helper->render_helper();
				continue;
			}
			$template_tag = '';
		}

		return str_replace( $template_tags[0], $template_tags[1], $this->template );
	}

	/**
	 * Load a helper object from the helper type name.
	 *
	 * @since     1.0.0
	 * @access    public
	 *
	 * @param    string $helper_type
	 *
	 * @return   false|CFB_View_Helper
	 */
	public function get_helper( $helper_type ) {
		if ( ! isset( $this->_available_helpers[ $helper_type ] ) ) {
			return false;
		}

		$helper = isset( $this->helpers[ $helper_type ] ) ? $this->helpers[ $helper_type ] : array();

		if ( ! $helper instanceof CFB_View_Helper ) {
			$helper                       = (array) $helper;
			$helper['helper_type']        = $helper_type;
			$helper[ $this->_view_group ] = $this->{$this->_view_group};

			$class_name                    = $this->_available_helpers[ $helper_type ];
			$this->helpers[ $helper_type ] = new $class_name( $helper );
		}

		return $this->helpers[ $helper_type ];
	}

	/**
	 * Returns the wrapper tag name.
	 *
	 * @since     1.0.0
	 * @access    protected
	 * @return    string
	 */
	protected function get_wrapper_tag() {
		return $this->wrapper_tag;
	}

	/**
	 * Returns the HTML attributes array for the wrapper.
	 *
	 * @since     1.0.0
	 * @access    protected
	 * @return    array
	 */
	protected function get_wrapper_attributes() {
		return $this->wrapper_attributes;
	}

	/**
	 * Get the current view type.
	 *
	 * Allows to dynamically generate view type specific CSS classes.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return    string
	 */
	abstract function get_type();

}
