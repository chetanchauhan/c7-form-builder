<?php
/**
 * Abstract View Helper Class
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
 * Class CFB_View_Helper
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Abstracts
 * @author     Chetan Chauhan <chetanchauhan1991@gmail.com>
 */
abstract class CFB_View_Helper extends CFB_Core {

	/**
	 * The type of helper.
	 *
	 * @since     1.0.0
	 * @access    protected
	 * @var        string
	 */
	protected $helper_type = 'unspecified';

	/**
	 * @var mixed
	 */
	protected $iterator = null;

	/**
	 * HTML tag name for wrapper.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var string
	 */
	public $wrapper_tag = 'div';

	/**
	 * HTML attributes array for wrapper.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var array
	 */
	public $wrapper_attributes = array();

	/**
	 * HTML tag to be used for rendering helper.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var string
	 */
	public $tag = 'div';

	/**
	 * HTML attributes to be applied to HTML tag.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var array
	 */
	public $tag_attributes = array();

	/**
	 * Renders the helper.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return string
	 */
	public function render_helper() {
		$html_content = $this->get_html_content();
		if ( ! $html_content ) {
			return '';
		}

		if ( is_array( $html_content ) ) {
			$html = array();
			foreach ( $html_content as $this->iterator => $content ) {
				$html[] = CFB_Html::tag( $this->tag, $this->get_attributes(), $content );
			}
			$html = implode( "\n", $html );

		} else {
			$html = CFB_Html::tag( $this->tag, $this->get_attributes(), $html_content );
		}

		// Applies the wrapper.
		return CFB_Html::tag( $this->wrapper_tag, $this->get_wrapper_attributes(), $html );
	}

	/**
	 * Helper content that should be rendered.
	 *
	 * Intended to be used by subclasses.
	 *
	 * @since     1.0.0
	 * @access    protected
	 * @return false|string
	 */
	protected function get_html_content() {
		return false;
	}

	/**
	 * Return attributes array after safely merging with the
	 * user specified attributes.
	 *
	 * @since     1.0.0
	 * @access    protected
	 * @return array
	 */
	protected function get_attributes() {
		return $this->tag_attributes;
	}

	/**
	 * Returns HTML attributes for the wrapper after safely merging
	 * with the user supplied wrapper attributes.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return array
	 */
	protected function get_wrapper_attributes() {
		return $this->wrapper_attributes;
	}

}

