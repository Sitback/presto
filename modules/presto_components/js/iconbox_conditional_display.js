/**
 * @file
 * Defines the behavior of the media entity browser view.
 *
 * Based on code by Burda in BurdaMagazinOrg/thunder, copyright (c) 2017.
 * Distributed under the GNU GPL v2 or higher. For full terms see the LICENSE
 * file.
 */

/*global Drupal drupalSettings*/

(function ($) {

  'use strict';

  /**
   * Attaches the behavior of the media entity browser view.
   */
  Drupal.behaviors.paragraphIconBox = {
    attach: function (context, settings) {

      function conditional_display(elt) {

        let id = jQuery(elt).attr('id');
        let selected = $("#" + id + ' option:selected').val();

        let image_field = $('[data-drupal-selector*="edit-field-body-paragraphs-"][data-drupal-selector*="-subform-field-media-wrapper"]');
        let icon_field = $('[data-drupal-selector*="edit-field-body-paragraphs-"][data-drupal-selector*="-subform-field-icon-wrapper"]');

        switch (selected.toLowerCase()) {
          case '_none':
            image_field.hide();
            icon_field.hide();
            break;

          case 'image':
            image_field.show();
            icon_field.hide();
            break;

          case 'icon':
            icon_field.show();
            image_field.hide();
            break;

          default:
            // Case default or _none.
            // Hide both.
            image_field.hide();
            icon_field.hide();
            break
        }

      }

      $('[name*="field_body_paragraphs"][name*="subform"][name*="field_icon_box_type"]').each(function() {

        $(this).load(conditional_display(this));

        $(this).change(function () {
          conditional_display(this);
        });

      });

    }

  };
}(jQuery, Drupal));
