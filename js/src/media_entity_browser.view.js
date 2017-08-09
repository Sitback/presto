/**
 * @file
 * Defines the behavior of the media entity browser view.
 *
 * Based on code by Burda in BurdaMagazinOrg/thunder, copyright (c) 2017.
 * Distributed under the GNU GPL v2 or higher. For full terms see the LICENSE
 * file.
 */

/* global Drupal drupalSettings */

(function ($) {

  /**
   * Attaches the behavior of the media entity browser view.
   */
  Drupal.behaviors.mediaEntityBrowserView = {
    attach(context/* , settings */) {
      $('.views-row', context).each(function () {
        let $row = $(this);
        let $input = $row.find('.views-field-entity-browser-select input');

        // When Auto Select functionality is enabled, then select entity
        // on click, without marking it as selected.
        if (drupalSettings.entity_browser_widget.auto_select) {
          $row.once('register-row-click').click(function (event) {
            event.preventDefault();

            $row.parents('form')
              .find('.entities-list')
              .trigger('add-entities', [[$input.val()]]);
          });
        }
        else {
          $row[$input.prop('checked') ? 'addClass' : 'removeClass']('checked');

          $row.once('register-row-click').click(function () {
            $input.prop('checked', !$input.prop('checked'));
            $row[$input.prop('checked') ? 'addClass' : 'removeClass']('checked');
          });
        }
      });
    }
  };

}(jQuery, Drupal));
