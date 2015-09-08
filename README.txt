=== C7 Form Builder ===
Contributors: chetanchauhan
Tags: form, custom fields, meta boxes, meta, repeatable, fields, contact form, form creator, form builder, metabox
Requires at least: 3.8.0
Tested up to: 4.3
Stable tag: 1.0.0-beta.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Provides an easy to use and powerful API for building forms that can be displayed, customized and saved any way you want.

== Description ==
C7 Form Builder provides an easy to use and powerful API allowing you to build even complex forms with ease. Using this plugin, you can create custom meta boxes, custom user profile fields, custom taxonomy fields,  setting pages or even contact forms. With C7 Form Builder, you are not limited to either frontend forms or admin forms as with other WordPress form builder plugins.

= Features =
* Redirect to a custom WordPress page or a URL after successful submission.
* Break large forms into tabs.
* Inbuilt repeatable field support. Dynamically add or remove all the bundled field types excluding hidden, select and submit field types as repeatable field support is removed intentionally from them.
* Sortable Fields - drag and drop all the repeatable fields to change the order.
* Compatible with any CSS framework like Bootstrap, Foundation, etc.
* Using field storage types, save form fields anywhere you want.
* Create and register new form types, field types, form view types, field view types, and storage types.
* Easy to extend and customize.
* and much more.

= Available Form Types =
* Admin
* Post
* Taxonomy
* Theme
* User

= Available Field Types =
* Color
* Editor
* Email
* Group
* Hidden
* Number
* Password
* Select
* Submit
* Textarea
* Text
* URL

= Important Links =
* [Documentation →](https://github.com/chetanchauhan/c7-form-builder/wiki/)
* [Github →](https://github.com/chetanchauhan/c7-form-builder/)

== Installation ==
1. Upload `c7-form-builder` to the `/wp-content/plugins/` directory
1. Activate the plugin through the \'Plugins\' menu in WordPress

Read the [documentation](https://github.com/chetanchauhan/c7-form-builder/wiki/) for basic usage instructions.

== Frequently Asked Questions ==
= Q. Does this plugin has GUI for building forms? =
A. No. You will need very basic programming skills to build forms with C7 Form Builder. But, if you can register custom post type or taxonomy, you can also create any form using this plugin.

== Screenshots ==
1. All the currently supported field types.
2. An example of meta box created using C7 Form Builder.
3. A simple contact form built with C7 Form Builder.

== Changelog ==
= 1.0.0-beta.2 - 2015-09-08 =
* Tweak: Improved default styling of forms and fields.
* Tweak: Remove backslashes from the submitted data.
* Tweak: Forms and fields descriptions are no longer passed through `wpautop()` before displaying.
* Tweak: Use `wp_kses_post()` instead of `esc_textarea()` for sanitizing textarea field values.
* Tweak: Add a handle for sorting repeatable field controls.
* Tweak: Allow repeatable field controls to be dragged either horizontally or vertically.
* Tweak: Add cfb-field-control class to repeatable field control placeholder.
* Tweak: Append asterisk after required fields label.
* Fix: Sanitization filters should only be applied recursively for repeatable field values not whenever the value is an array.
* Fix: Setting `required` field arg to `true` don't make it required.
* Fix: If the position of empty control gets changed after sorting repeatable field control indexes are calculated incorrectly.
* Fix: Child fields are hidden always in tabbed forms.
* Fix: Switching between visual and text mode in editor field not working after upgrade to WordPress 4.3.
* Fix: Unable to insert media in dynamically added editor field controls.
* Fix: Repeatable editor fields are initialized with incorrect settings in dynamically added group field controls.
* Fix: Double Quicktags toolbar in dynamically added editors.
* Fix: After switching editor field to fullscreen mode, admin bar and menu don't hide automatically.
* Fix: `c7FormBuilder.fields.editor.destroy` is hooked to incorrect JS action.

= 1.0.0-beta - 2015-03-14 =
* Initial public release.
