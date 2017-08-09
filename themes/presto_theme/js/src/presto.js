import collapse from 'bootstrap-sass/assets/javascripts/bootstrap/collapse.js';
import transition from 'bootstrap-sass/assets/javascripts/bootstrap/transition.js';

(function ($, window, document) {

  let addDependency = (dep) => dep;

  // Add dependencies here to include them in the compiled output.
  addDependency(collapse);
  addDependency(transition);

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
