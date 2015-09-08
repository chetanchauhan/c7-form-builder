<?php
/**
 * Helps rendering HTML Elements
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Helpers
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
 * Class CFB_Html
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Helpers
 * @author     Chetan Chauhan <chetanchauhan1991@gmail.com>
 */
class CFB_Html {

	/**
	 * List of self closing HTML tags.
	 *
	 * @since     1.0.0
	 * @access    protected
	 * @var array
	 */
	protected static $_empty_tags = array(
		'area'      => true,
		'abstracts' => true,
		'br'        => true,
		'col'       => true,
		'command'   => true,
		'embed'     => true,
		'hr'        => true,
		'img'       => true,
		'input'     => true,
		'keygen'    => true,
		'link'      => true,
		'meta'      => true,
		'param'     => true,
		'source'    => true,
		'track'     => true,
		'wbr'       => true,
	);

	/**
	 * Return valid HTML markup for a HTML tag.
	 *
	 * @since     1.0.0
	 * @access    protected
	 *
	 * @param string $tag_name A valid HTML tag name.
	 * @param array $attributes HTML attributes array.
	 * @param bool|string $content Content to be wrapped within the HTML opening and closing tag.
	 * @param bool $tidy_html If true, prevents returning output like '<div></div>'.
	 *
	 * @return string
	 */
	public static function tag( $tag_name, $attributes = array(), $content = false, $tidy_html = true ) {

		$tag_name = tag_escape( $tag_name );

		if ( self::is_empty_tag( $tag_name ) ) {
			return "<{$tag_name} " . self::stringify_attributes( $attributes ) . '>';
		}

		if ( ! $content && $tidy_html ) {
			return '';
		}

		return "<{$tag_name} " . self::stringify_attributes( $attributes ) . '>' . $content . "</$tag_name>";

	}

	/**
	 * Converts the key-value pair of attributes array into string.
	 *
	 * @since     1.0.0
	 * @access    public
	 *
	 * @param array $attributes
	 *
	 * @return string
	 */
	public static function stringify_attributes( $attributes = array() ) {

		// Remove all attributes having null, false or empty string as values.
		$attributes = array_filter( $attributes, 'strlen' );

		$html = array();

		foreach ( $attributes as $name => $value ) {
			// Handle boolean attributes.
			if ( true === $value ) {
				$html[] = esc_attr( $name );
				continue;
			}
			$html[] = sprintf( '%s="%s"', esc_attr( $name ), esc_attr( $value ) );
		}

		return implode( ' ', $html );

	}

	/**
	 * Safely merge HTML attributes array.
	 *
	 * @since     1.0.0
	 * @access    public
	 *
	 * @param array $attributes_1 Initial attributes array to merge.
	 * @param array $attributes_2 Attributes array to be merged into initial attributes array.
	 *
	 * @return array    Resulting attributes array after merging
	 */
	public static function merge_attributes( $attributes_1, $attributes_2 ) {

		$attributes_1 = (array) $attributes_1;
		$attributes_2 = (array) $attributes_2;

		// Holds the attribute names whose values are to be appended instead of replacing from the second attributes array.
		$append_attributes = array( 'id', 'class' );

		foreach ( $append_attributes as $append_attribute ) {
			// Check if we need to do merging preserving the attribute values from both the supplied attributes array.
			if ( isset( $attributes_1[ $append_attribute ] ) && isset( $attributes_2[ $append_attribute ] ) ) {
				// Merge the attribute values.
				$attributes_1[ $append_attribute ] = $attributes_1[ $append_attribute ] . ' ' . $attributes_2[ $append_attribute ];
				$attributes_1[ $append_attribute ] = implode( ' ', array_unique( explode( ' ', $attributes_1[ $append_attribute ] ) ) );

				// Remove the attribute name from the attributes array to be merged as merging is already done.
				unset( $attributes_2[ $append_attribute ] );
			}
		}

		return array_merge( $attributes_1, $attributes_2 );

	}

	/**
	 * Handles outputting a "select" element.
	 *
	 * @since     1.0.0
	 * @access    public
	 *
	 * @param array $attributes
	 * @param array $options
	 * @param array $value
	 * @param bool $tidy_html
	 *
	 * @return string
	 */
	public static function select( $attributes = array(), $options = array(), $value = array(), $tidy_html = true ) {
		$value        = (array) $value;
		$options_html = array();
		foreach ( $options as $option => $name ) {
			$options_html[] = self::tag( 'option', array(
				'value'    => $option,
				'selected' => in_array( $option, $value ),
			), $name );
		}

		$options_html = implode( "\n", $options_html );

		return self::tag( 'select', $attributes, $options_html, $tidy_html );
	}

	/**
	 * Checks if an HTML tag is self enclosing or not.
	 *
	 * @since     1.0.0
	 * @access    public
	 *
	 * @param string $tag_name HTML tag to be checked.
	 *
	 * @return bool
	 */
	public static function is_empty_tag( $tag_name ) {
		return isset( self::$_empty_tags[ $tag_name ] );
	}
}
