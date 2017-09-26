

let $ = require('jquery');
window.jquery = $;
window.jQuery = $;
window.$ = $;

// popper is a pre-requisite for bootstrap 4 dropdowns.
let popper = require('popper.js');
window.Popper = popper;

require('bootstrap');
require('lity');

(function ($, window, document) {
  let f = {
    addToCartInList: (el) => {
      let $el = $(el.delegateTarget);
      let $form = $el.parents('li.product-item').find('form').get(0);
      if ($form) {
        $form.submit();
      }
    }
  };

  $(document).ready(() => {
    $('[data-action="cart"]').on('click', (el) => {
      f.addToCartInList(el);
    });
  });
}(jQuery, window, document));
