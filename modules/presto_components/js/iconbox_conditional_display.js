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

      function conditionalDisplay(elt) {

        let id = jQuery(elt).attr('id');
        let selected = $('#' + id + ' option:selected').val();

        let imageField = $('[data-drupal-selector*="edit-field-body-paragraphs-"][data-drupal-selector*="-subform-field-media-wrapper"]');
        let iconField = $('[data-drupal-selector*="edit-field-body-paragraphs-"][data-drupal-selector*="-subform-field-icon-wrapper"]');

        switch (selected.toLowerCase()) {
          case '_none':
            imageField.hide();
            iconField.hide();
            break;

          case 'image':
            imageField.show();
            iconField.hide();
            break;

          case 'icon':
            iconField.show();
            imageField.hide();
            break;

          default:
            // Case default or _none.
            // Hide both.
            imageField.hide();
            iconField.hide();
            break;
        }

      }

      $('[name*="field_body_paragraphs"][name*="subform"][name*="field_icon_box_type"]').each(function() {

        $(this).load(conditionalDisplay(this));

        $(this).change(function () {
          conditionalDisplay(this);
        });

      });

    }

  };
}(jQuery, Drupal));
