/* global cfbL10n, tinymce, tinyMCEPreInit, QTags, quicktags */

window.c7FormBuilder = (function ($) {
	'use strict';

	/**
	 * Retrieves all the descendant elements (including current element)
	 * matching the selector.
	 *
	 * @param {string} selector
	 * @returns {jQuery}
	 */
	$.fn.findAll = function (selector) {
		return $(this).find(selector).addBack(selector);
	};

	var cfb = {

		tabs: {
			initialize: function (form) {
				// Bail early if form does not have tabs.
				if (!cfb.hasTabs(form)) {
					return;
				}

				// Add CSS class to fields wrapper for styling purposes.
				form.find('.cfb-form-fields-wrapper').addClass('cfb-tab-content');

				// Activate the default tab.
				this.activateTab(form);

				// Bind event handler for switching between tabs.
				form.on('click', '.cfb-form-tabs', function (e) {
					e.preventDefault();
					cfb.tabs.activateTab(form, $(e.currentTarget).data('tab'));
				});
			},

			/**
			 * Retrieves the default form tab.
			 *
			 * @param {jQuery} form
			 * @returns {string}
			 */
			getDefaultTab: function (form) {
				var defaultTab = form.find('.cfb-form-tabs-wrapper').data('default-tab');

				// Fallback to first tab if default tab is either unavailable or invalid.
				if (form.find('.cfb-form-tabs[data-tab="' + defaultTab + '"]').length === 0) {
					defaultTab = form.find('.cfb-form-tabs').first().data('tab');
				}

				return wp.hooks.applyFilters('cfb.get_default_tab', defaultTab, form);
			},

			/**
			 * Activates a form tab.
			 *
			 * @param {jQuery} form
			 * @param {string} [tab]
			 */
			activateTab: function (form, tab) {
				tab = tab || this.getDefaultTab(form);

				// Add and remove classes from tabs.
				form.find('.cfb-form-tabs[data-tab="' + tab + '"]').addClass('cfb-active-tab').siblings().removeClass('cfb-active-tab');

				// Toggle the field visibility.
				form.find('.cfb-form-fields > .cfb-field-wrapper').each(
					function () {
						if (($(this).data('tab') === tab)) {
							$(this).fadeIn();
						} else {
							$(this).hide();
						}
					}
				);

				wp.hooks.doAction('cfb.activated_tab', form, tab);
			}

		},

		fields: {
			color: {
				initialize: function (selection) {
					selection.find('input[type="text"]').wpColorPicker();
				}
			},
			editor: {
				initialize: function (selection) {
					cfb.getAllFieldControls(selection, {type: 'editor'}).each(function () {
						var textarea = $(this).find('textarea.wp-editor-area'),
							id = textarea.attr('id'),
							oldID = textarea.data('prev-id');

						if (typeof( tinyMCEPreInit.mceInit[id] ) === 'undefined') {
							tinyMCEPreInit.mceInit[id] = $.extend({}, tinyMCEPreInit.mceInit[oldID], {
								selector: '#' + id,
								body_class: id
							});
						}

						if (typeof( tinyMCEPreInit.qtInit[id] ) === 'undefined') {
							tinyMCEPreInit.qtInit[id] = $.extend({}, tinyMCEPreInit.qtInit[oldID], {id: id});
						}

						// Initialize tinyMCE
						if (!tinymce.get(id) && $(this).find('.wp-editor-wrap').hasClass('tmce-active')) {
							try {
								tinymce.init(tinyMCEPreInit.mceInit[id]);
							} catch (e) {
							}
						}

						// Initialize quicktags
						if (typeof quicktags !== 'undefined' && !QTags.getInstance(id)) {
							try {
								quicktags(tinyMCEPreInit.qtInit[id]);
								QTags._buttonsInit();
							} catch (e) {
							}
						}
					});
				},
				destroy: function (selection) {
					cfb.getAllFieldControls(selection, {type: 'editor'}).each(function () {
						var textarea = $(this).find('textarea.wp-editor-area'),
							id = textarea.attr('id'),
							ed = tinymce.get(id);

						// Save the current id for reinitialization.
						if (!textarea.attr('data-prev-id')) {
							textarea.attr('data-prev-id', textarea.attr('id'));
						}

						if (ed) {
							ed.save();
							ed.remove();
						}

						$(this).find('#qt_' + id + '_toolbar').remove();
						delete QTags.instances[id];
					});
				}
			}

		},

		sortable: {
			initialize: function (field) {
				if (!cfb.isRepeatableField(field)) {
					return;
				}
				cfb.getFieldControlWrapper(field).sortable({
					axis: 'y',
					cursor: 'move',
					handle: '.cfb-sort-handle',
					items: ' > .cfb-field-control',
					forcePlaceholderSize: true,
					forceHelperSize: true,
					tolerance: 'pointer',
					placeholder: 'cfb-field-control-placeholder',
					start: function (event, ui) {
						// Cache the original position.
						var index = $(event.target).find('> .cfb-field-control').not('.cfb-empty-control').index(ui.item);
						ui.item.data('sortstart-index', index);

						wp.hooks.doAction('cfb.sortstart', event, ui);
					},
					stop: function (event, ui) {
						wp.hooks.doAction('cfb.sortstop', event, ui);
					},
					update: function (event, ui) {
						wp.hooks.doAction('cfb.sortupdate', event, ui);
					}
				});
			}
		},

		/**
		 * Retrieves the localized data from global cfb_l10n variable.
		 *
		 * @param {string} key Key
		 * @param {*} [val] Default value.
		 * @returns {*} Localized value if available. Otherwise, default value.
		 */
		l18n: function (key, val) {
			return cfbL10n[key] || val || '';
		},

		/**
		 * Retrieves the form name.
		 *
		 * @param {jQuery} form
		 * @returns {string}
		 */
		getFormName: function (form) {
			return form.data('name');
		},

		/**
		 * Retrieves the field name.
		 *
		 * @param {jQuery} field
		 * @returns {string}
		 */
		getFieldName: function (field) {
			return field.data('name');
		},

		/**
		 * Retrieves the form type.
		 *
		 * @param {jQuery} form
		 * @returns {string}
		 */
		getFormType: function (form) {
			return form.data('type');
		},

		/**
		 * Retrieves the field type.
		 *
		 * @param {jQuery} field
		 * @returns {string}
		 */
		getFieldType: function (field) {
			return field.data('type');
		},

		/**
		 * Checks if form has tabs or not.
		 *
		 * @param {jQuery} form
		 * @returns {boolean}
		 */
		hasTabs: function (form) {
			return form.find('.cfb-form-tabs-wrapper').length > 0;
		},

		/**
		 * Checks if field is repeatable or not.
		 *
		 * @param {jQuery} field
		 * @returns {boolean}
		 */
		isRepeatableField: function (field) {
			return field.hasClass('cfb-repeatable');
		},

		/**
		 * Retrieves the field controls wrapper.
		 *
		 * This ensures control wrapper belonging to the current
		 * field only is retrieved excluding any sub field control wrapper.
		 *
		 * @param {jQuery} field
		 * @returns {jQuery}
		 */
		getFieldControlWrapper: function (field) {
			return field.find('.cfb-field-control-wrapper').first();
		},

		/**
		 * Retrieves all the field controls belonging to the provided field.
		 *
		 * @param {jQuery} field
		 * @param {bool} [includeEmpty]
		 * @returns {jQuery}
		 */
		getFieldControls: function (field, includeEmpty) {
			includeEmpty = includeEmpty || false;

			var controls = this.getFieldControlWrapper(field).find('> .cfb-field-control');
			if (includeEmpty) {
				return controls;
			}

			return controls.not('.cfb-empty-control');
		},

		/**
		 * Retrieves all the field controls from the jQuery selection.
		 *
		 * @param {jQuery} selection
		 * @param {object} [args]
		 * @returns {jQuery}
		 */
		getAllFieldControls: function (selection, args) {
			args = $.extend({}, {
				'type': '',
				'includeEmpty': false
			}, args);

			var selector = args.type ? '.cfb-' + args.type + '-field .cfb-field-control' : '.cfb-field-control',
				controls = selection.findAll(selector);

			if (args.includeEmpty) {
				return controls;
			}
			return controls.not(function () {
				return cfb.isEmptyControl($(this));
			});
		},

		/**
		 * Checks if a field control is either empty or belongs
		 * to an empty parent field control.
		 *
		 * @param {jQuery} control
		 * @returns {boolean}
		 */
		isEmptyControl: function (control) {
			if (control.hasClass('cfb-empty-control')) {
				return true;
			} else if (control.parentsUntil('.cfb-form-fields', '.cfb-empty-control').length > 0) {
				return true;
			}
			return false;
		},

		/**
		 * Checks if new control can be added or not.
		 *
		 * @param {jQuery} field
		 * @returns {boolean}
		 */
		canAddControl: function (field) {
			var max = field.data('repeatable-max'),
				count = this.getFieldControls(field).length;
			return max ? count < max : true;
		},

		/**
		 * Checks if an existing control can be safely removed or not.
		 *
		 * @param {jQuery} field
		 * @returns {boolean}
		 */
		canRemoveControl: function (field) {
			var min = field.data('repeatable-min') || 1,
				count = this.getFieldControls(field).length;

			return count > min;
		},

		/**
		 * Add new control after the target field control.
		 *
		 * @param {jQuery} target
		 * @param {jQuery} [field]
		 * @param {jQuery} [form]
		 */
		addControl: function (target, field, form) {
			field = field || target.closest('.cfb-field-wrapper');
			form = form || field.closest('.cfb-form-wrapper');

			if (!this.canAddControl(field)) {
				return;
			}

			var newControl = target.siblings('.cfb-empty-control').clone().removeClass('cfb-empty-control');

			wp.hooks.doAction('cfb.pre_add_control', newControl, field, form);

			target.after(newControl);

			this.updateControls(newControl.nextAll('.cfb-field-control').addBack(), field, form);
			this.toggleHandles(field);

			wp.hooks.doAction('cfb.added_control', newControl, field, form);
		},

		/**
		 * Removes the target field control.
		 *
		 * @param {jQuery} target
		 * @param {jQuery} [field]
		 * @param {jQuery} [form]
		 */
		removeControl: function (target, field, form) {
			field = field || target.closest('.cfb-field-wrapper');
			form = form || field.closest('.cfb-form-wrapper');

			if (!this.canRemoveControl(field)) {
				return;
			}

			var updateControls = target.nextAll('.cfb-field-control');

			wp.hooks.doAction('cfb.pre_remove_control', target, field, form);

			target.remove();

			this.updateControls(updateControls, field, form);
			this.toggleHandles(field);

			wp.hooks.doAction('cfb.removed_control', target, field, form);
		},

		/**
		 * Updates the field controls ids and name attributes.
		 *
		 * @param {jQuery} controls
		 * @param {jQuery} [field]
		 * @param {jQuery} [form]
		 */
		updateControls: function (controls, field, form) {
			controls = controls.not('.cfb-empty-control');

			if (!controls.length) {
				return;
			}

			field = field || controls.closest('.cfb-field-wrapper');
			form = form || field.closest('.cfb-form-wrapper');

			var formName = this.getFormName(form),
				fieldName = this.getFieldName(field),
				idRegex = new RegExp('(cfb-field-' + formName + '-' + fieldName.replace(/:/g, '(?:-\\d+)?-') + ')-(\\d+|x)'),
				nameRegex = new RegExp('(c7_form_builder\\[' + formName + '\\]\\[' + fieldName.replace(/:/g, '\\](?:\\[\\d+\\])?\\[') + '\\])\\[(\\d+|x)\\]'),
				id, name, forAttr, index;

			wp.hooks.doAction('cfb.pre_update_controls', controls, field, form);

			controls.each(function () {
				index = cfb.getFieldControls(field).index($(this));

				$(this).find('label.cfb-field-label').each(function () {
					forAttr = $(this).attr('for').replace(idRegex, '$1-' + index);
					$(this).attr('for', forAttr);
				});
				$(this).find('[id]').each(function () {
					id = $(this).attr('id');
					id = id.replace(idRegex, '$1-' + index);
					$(this).attr('id', id);
				});
				$(this).find('[name]').each(function () {
					name = $(this).attr('name');
					name = name.replace(nameRegex, '$1[' + index + ']');
					$(this).attr('name', name);
				});
			});

			wp.hooks.doAction('cfb.updated_controls', controls, field, form);
		},

		/**
		 * Appends handles to repeatable fields.
		 *
		 * This adds handles for dynamically adding/removing field controls
		 * to the repeatable fields.
		 *
		 * @param {jQuery} field
		 */
		appendHandles: function (field) {
			// Bail early if field is not repeatable.
			if (!this.isRepeatableField(field)) {
				return;
			}

			var repeatableHandles = $([
					'<div class="cfb-repeatable-handles">',
					'<a href="#" class="cfb-add-control" title="' + this.l18n('add_control_button_text') + '"><span class="cfb-icon-add"></span></a>',
					'<a href="#" class="cfb-remove-control" title="' + this.l18n('remove_control_button_text') + '"><span class="cfb-icon-remove"></span></a>',
					'</div>'
				].join('')),
				sortHandle = $('<span class="cfb-sort-handle" title="' + this.l18n('sort_control_button_text') + '"><span class="cfb-icon-sort"></span></span>');

			wp.hooks.doAction('cfb.pre_append_handles', field);

			this.getFieldControls(field, true).prepend(sortHandle).append(repeatableHandles);

			wp.hooks.doAction('cfb.appended_handles', field);

			// Toggle the repeatable field handles after they are appended.
			this.toggleHandles(field);
		},

		/**
		 * Toggles the field repeatable handles visibility.
		 *
		 * @param {jQuery} field
		 */
		toggleHandles: function (field) {
			var self = this;

			// Get the handles that belongs to the current field only.
			var handles = field.find('.cfb-repeatable-handles').filter(function () {
				return self.getFieldName(field) === self.getFieldName($(this).closest('.cfb-field-wrapper'));
			});

			// Toggle the repeatable handles visibility.
			handles.find('.cfb-add-control').toggle(this.canAddControl(field));
			handles.find('.cfb-remove-control').toggle(this.canRemoveControl(field));
		}
	};

	/**
	 * Fires main js action after DOM is fully loaded.
	 */
	$(function () {
		wp.hooks.doAction('cfb.ready', $('body'));
	});

	/**
	 * Fires js action for each form.
	 */
	wp.hooks.addAction('cfb.ready', function (el) {
		el.find('.cfb-form-wrapper').each(function () {
			wp.hooks.doAction('cfb.ready_form', $(this));
			wp.hooks.doAction('cfb.ready_' + cfb.getFormType($(this)) + '_form', $(this));
		});
	});

	/**
	 * Fires js action for each field excluding any sub field belonging
	 * to an empty field control.
	 */
	wp.hooks.addAction('cfb.ready_form', function (form) {
		form.find('.cfb-field-wrapper').not('.cfb-empty-control .cfb-field-wrapper').each(function () {
			wp.hooks.doAction('cfb.ready_field', $(this), form);
			wp.hooks.doAction('cfb.ready_' + cfb.getFieldType($(this)) + '_field', $(this), form);
		});
	});

	/**
	 * Fires js action for each field control excluding any empty field control.
	 */
	wp.hooks.addAction('cfb.ready_field', function (field, form) {
		cfb.getFieldControls(field).each(function () {
			wp.hooks.doAction('cfb.ready_field_control', $(this), field, form);
			wp.hooks.doAction('cfb.ready_' + cfb.getFieldType(field) + '_field_control', $(this), field, form);
		});
	});

	/**
	 * Bind event handler for adding/removing field control.
	 */
	wp.hooks.addAction('cfb.ready_form', function (form) {
		form.on('click', '.cfb-repeatable-handles', function (e) {
			e.preventDefault();
			e.stopPropagation();
			var field = $(e.currentTarget).closest('.cfb-field-wrapper');
			var target = $(e.currentTarget).parentsUntil('.cfb-field-control-wrapper', '.cfb-field-control');
			if (!target.length) {
				target = cfb.getFieldControls(field).last();
			}
			if ($(e.target).closest('.cfb-add-control').length > 0) {
				cfb.addControl(target, field, form);
			} else if ($(e.target).closest('.cfb-remove-control').length > 0) {
				cfb.removeControl(target, field, form);
			}
		});
	});

	// Add repeatable handles to all the repeatable fields.
	wp.hooks.addAction('cfb.ready_field', function (field) {
		cfb.appendHandles(field);
	});

	/**
	 * Fires js action for initializing the newly field control.
	 */
	wp.hooks.addAction('cfb.added_control', function (newControl, field, form) {
		wp.hooks.doAction('cfb.ready_field_control', newControl, field, form);
		wp.hooks.doAction('cfb.ready_' + cfb.getFieldType(field) + '_field_control', newControl, field, form);

		newControl.find('.cfb-field-wrapper').not('.cfb-empty-control .cfb-field-wrapper').each(function () {
			wp.hooks.doAction('cfb.ready_field', $(this), form);
			wp.hooks.doAction('cfb.ready_' + cfb.getFieldType($(this)) + '_field', $(this), form);
		});
	});

	// Setup the form having tabs.
	wp.hooks.addAction('cfb.ready_form', cfb.tabs.initialize, 10, cfb.tabs);

	// Init sortable for repeatable fields.
	wp.hooks.addAction('cfb.ready_field', cfb.sortable.initialize);

	// Always display the submit fields that don't belong to any tab.
	wp.hooks.addAction('cfb.activated_tab', function (form) {
		form.find('.cfb-submit-field:not([data-tab])').fadeIn();
	});

	// Update controls after field control position is changed.
	wp.hooks.addAction('cfb.sortupdate', function (e, ui) {
		// Check if field control is moved downward/upward and act accordingly.
		var index = $(e.target).find('> .cfb-field-control').not('.cfb-empty-control').index(ui.item);

		if (index > ui.item.data('sortstart-index')) {
			cfb.updateControls(ui.item.prevAll('.cfb-field-control').addBack());
		} else {
			cfb.updateControls(ui.item.nextAll('.cfb-field-control').addBack());
		}
	});

	// Setup Color Field
	wp.hooks.addAction('cfb.ready_color_field_control', cfb.fields.color.initialize);

	// Setup Editor Field
	wp.hooks.addAction('cfb.ready_editor_field_control', cfb.fields.editor.initialize);
	wp.hooks.addAction('cfb.pre_remove_control', cfb.fields.editor.destroy);
	wp.hooks.addAction('cfb.pre_update_controls', cfb.fields.editor.destroy);
	wp.hooks.addAction('cfb.updated_controls', cfb.fields.editor.initialize);

	// Destroy editor instance on sortstart.
	wp.hooks.addAction('cfb.sortstart', function (e, ui) {
		cfb.fields.editor.destroy(ui.item);
	});

	// Initialize editor instance on sortstop.
	wp.hooks.addAction('cfb.sortstop', function (e, ui) {
		cfb.fields.editor.initialize(ui.item);
	});

	// Fix: Allow editor field inside sortable meta boxes.
	wp.hooks.addAction('cfb.ready', function (el) {
		el.find('#poststuff').on('sortstart', function (event) {
			cfb.fields.editor.destroy($(event.target));
		}).on('sortstop', function (event) {
			cfb.fields.editor.initialize($(event.target));
		});
	});

	return cfb;

})(jQuery);
