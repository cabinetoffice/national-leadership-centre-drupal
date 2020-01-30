(function ($, Drupal, drupalSettings) {
  'use strict';
  
  Drupal.behaviors.bevantheme = {
    attach: function (context, settings) {
      $('.eu-cookie-compliance__close').click(function() {
        Drupal.eu_cookie_compliance.toggleWithdrawBanner();
      });

      // Once the facet summary block has loaded.
      $('#result-count').text($('#facet-summary-wrapper').data('facets-summary-count') + ' results');
    }
  };
})(jQuery, Drupal, drupalSettings);
