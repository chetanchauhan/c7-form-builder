<?php
/**
 * Functions
 *
 * @package    C7_Form_Builder
 * @subpackage C7_Form_Builder/Functions
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

if ( ! function_exists( 'array_replace_recursive' ) ) {
	/**
	 * Replace elements from the second array into the first array recursively.
	 *
	 * @since     1.0.0
	 *
	 * @param array $base The array in which elements are replaced.
	 * @param array $replacements The array from which elements will be extracted.
	 *
	 * @return array
	 */
	function array_replace_recursive( $base = array(), $replacements = array() ) {
		// http://php.net/manual/en/function.array-replace-recursive.php#109390
		foreach ( array_slice( func_get_args(), 1 ) as $replacements ) {
			$bref_stack = array( &$base );
			$head_stack = array( $replacements );

			do {
				end( $bref_stack );

				$bref = &$bref_stack[ key( $bref_stack ) ];
				$head = array_pop( $head_stack );

				unset( $bref_stack[ key( $bref_stack ) ] );

				foreach ( array_keys( $head ) as $key ) {
					if ( isset( $key, $bref ) && is_array( $bref[ $key ] ) && is_array( $head[ $key ] ) ) {
						$bref_stack[] = &$bref[ $key ];
						$head_stack[] = $head[ $key ];
					} else {
						$bref[ $key ] = $head[ $key ];
					}
				}
			} while ( count( $head_stack ) );
		}

		return $base;
	}
}

/**
 * Whether the current request is for post edit screen.
 *
 * @since  1.0.0
 * @return bool
 */
function cfb_is_post_edit_screen() {
	global $pagenow;

	return 'post.php' === $pagenow || 'post-new.php' === $pagenow;
}

/**
 * Whether the current request is for user profile edit screen.
 *
 * @since  1.0.0
 * @return bool
 */
function cfb_is_user_edit_screen() {
	global $pagenow;

	return 'profile.php' === $pagenow || 'user-edit.php' === $pagenow;
}

/**
 * Whether the current request is for taxonomy term add/edit screen.
 *
 * @since   1.0.0
 * @return  bool|string
 */
function cfb_is_term_edit_screen() {
	global $pagenow;
	if ( 'edit-tags.php' === $pagenow ) {
		return isset( $_REQUEST['action'] ) && 'edit' === $_REQUEST['action'] ? 'edit' : 'add';
	}

	return false;
}

/**
 * Whether the current request is for frontend page.
 *
 * @since   1.0.0
 * @return  bool
 */
function cfb_is_frontend() {
	return ! is_admin();
}

/**
 * Retrieves the current page URL including query strings.
 *
 * @since  1.0.0
 * @return string
 */
function cfb_get_current_url() {
	$protocol = ( ( ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] != 'off' ) || $_SERVER['SERVER_PORT'] == 443 ) ? "https://" : "http://";

	return $protocol . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
}

/**
 * Helper function to compare two arrays by priority.
 *
 * @since     1.0.0
 *
 * @param array $a Array A.
 * @param array $b Array B.
 *
 * @return int
 */
function _cfb_cmp_priority( $a, $b ) {
	$ap = isset( $a['priority'] ) ? $a['priority'] : 10;
	$bp = isset( $b['priority'] ) ? $b['priority'] : 10;

	if ( $ap === $bp ) {
		return 0;
	}

	return ( $ap > $bp ) ? 1 : - 1;
}

/**
 * Retrieves the object id from the global scope for object types
 * such as post, user and comment.
 *
 * @since   1.0.0
 *
 * @param string $object_type Object type (such as post, user and comment).
 *
 * @return int  $object_id  Object id for the given object id.
 */
function cfb_get_object_id( $object_type ) {
	$object_id = 0;
	switch ( $object_type ) {
		case 'post':
			global $post;
			if ( isset( $_GET['post'] ) ) {
				$object_id = $_GET['post'];
			} elseif ( isset( $_POST['post_ID'] ) ) {
				$object_id = $_POST['post_ID'];
			} elseif ( ! empty( $post->ID ) ) {
				$object_id = $post->ID;
			}
			break;
		case 'user':
			if ( defined( 'IS_PROFILE_PAGE' ) && IS_PROFILE_PAGE ) {
				$object_id = get_current_user_id();
			} elseif ( isset( $_REQUEST['user_id'] ) ) {
				$object_id = $_REQUEST['user_id'];
			}
			break;
	}

	return absint( $object_id );
}

/**
 * Finds whether an array is associative or not.
 *
 * @since   1.0.0
 *
 * @param array $array Array to be checked.
 *
 * @return bool
 */
function cfb_is_array_assoc( $array ) {
	$keys = array_keys( $array );

	return array_keys( $keys ) !== $keys;
}
