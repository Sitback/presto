/**
 * @file
 * Defines the behavior of paragraph Icon box form display.
 */

(function ($, Drupal) {

  /**
   * Attaches the behavior of the media entity browser view.
   */
  Drupal.behaviors.paragraphIconBox = {
    attach(context/* , settings */) {
      let conditionalDisplay = (event) => {
        let $target = $(event.target);
        let selected = $target.find('option:selected').val();
        let $parentForm = $target.parents('.paragraphs-subform').first();
        let $imageField = $parentForm.find(
          '[data-drupal-selector*="-subform-field-media-wrapper"]'
        );
        let $iconField = $parentForm.find(
          '[data-drupal-selector*="-subform-field-icon-wrapper"]'
        );

        switch (selected.toLowerCase()) {
          case '_none':
            $imageField.hide();
            $iconField.hide();
            break;

          case 'image':
            $imageField.show();
            $iconField.hide();
            break;

          case 'icon':
            $iconField.show();
            $imageField.hide();
            break;

          default:
            // Case default or _none: hide both.
            $imageField.hide();
            $iconField.hide();
            break;
        }
      };

      // Trigger on each 'icon box' component form.
      $(
        '[name*="field_body_paragraphs"][name*="subform"][name*="field_icon_box_type"]',
        context
      ).once('paragraphIconBox').each(function () {
        let $this = $(this);
        $this.on('change', conditionalDisplay);
        $this.trigger('change');
      });
    }
  };

}(jQuery, window.Drupal));
