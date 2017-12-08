
const $ = require('jquery');

window.jquery = $;
window.jQuery = $;
window.$ = $;

// popper is a pre-requisite for bootstrap 4 dropdowns.
const popper = require('popper.js');

window.Popper = popper;

require('bootstrap');
require('lity');

((window, document) => {
  const f = {
    addToCartInList: (el) => {
      const $el = $(el.delegateTarget);
      const $form = $el.parents('li.product-item').find('form').get(0);
      if ($form) {
        $form.submit();
      }
    },
  };

  $(document).ready(() => {
    $('[data-action="cart"]').on('click', (el) => {
      f.addToCartInList(el);
    });
  });
})(window, document);
