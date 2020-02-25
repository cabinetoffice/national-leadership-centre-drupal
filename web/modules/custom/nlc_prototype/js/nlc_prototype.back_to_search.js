(function ($, Drupal, drupalSettings) {
  'use strict';

  Drupal.behaviors.nlc_prototype_back_to_search = {
    attach: function(context, settings) {
      if (document.referrer.length > 0) {
        $('#back-to-search-link').click(function(event) {
          event.preventDefault();
          window.location.href = document.referrer;
          return false;
        });
      }
      else {
        const wrapper = $('#back-to-search-link').parent();
        $(wrapper).remove();
      }
    }

  };

})(jQuery, Drupal, drupalSettings);