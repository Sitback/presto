/**
 * @file
 * Defines the behavior of paragraph Icon box form display.
 */

(($, Drupal) => {
  /**
   * Attaches the behavior of the media entity browser view.
   */
  Drupal.behaviors.paragraphIconBox = {
    attach(context/* , settings */) {
      setTimeout(() => {
        $('[name*="field_body_paragraphs"][name*="subform"][name*="field_icon_box_type"]', context).triggerHandler('change');
      }, 1000);

      const conditionalDisplay = (event) => {
        const $target = $(event.target);
        const selected = $target.find('option:selected').val();
        if (selected === 'undefined') {
          return;
        }
        const $parentForm = $target.parents('.paragraphs-subform').first();
        const $imageField = $parentForm.find('[data-drupal-selector*="-subform-field-media-wrapper"]');
        const $iconField = $parentForm.find('[data-drupal-selector*="-subform-field-icon-wrapper"]');

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

      $('[name*="field_body_paragraphs"][name*="subform"][name*="field_icon_box_type"]', context).once('paragraphIconBox').each((i, el) => {
        const $this = $(el);
        $this.on('change', conditionalDisplay);
        $this.triggerHandler('change');
      });
    },
  };
})(jQuery, window.Drupal);
