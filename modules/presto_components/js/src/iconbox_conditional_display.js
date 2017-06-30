/**
 * @file
 * Defines the behavior of paragraph Icon box form display.
 */

(function ($, Drupal) {

  /**
   * Attaches the behavior of the media entity browser view.
   */
  Drupal.behaviors.paragraphIconBox = {
    attach(context, settings) {
      let conditionalDisplay = (elt) => {
        let id = $(elt).attr('id');
        let selected = $(`#${id}`).find('option:selected').val();

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
      };

      $('[name*="field_body_paragraphs"][name*="subform"][name*="field_icon_box_type"]').each(function () {
        $(this).load(conditionalDisplay(this));
        $(this).change(function () {
          conditionalDisplay(this);
        });
      });
    }
  };

}(jQuery, window.Drupal));
