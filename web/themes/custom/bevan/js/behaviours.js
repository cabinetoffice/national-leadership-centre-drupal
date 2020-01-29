(function ($, Drupal, drupalSettings) {
  'use strict';
  
  Drupal.behaviors.bevantheme = {
    attach: function (context, settings) {
      $('.eu-cookie-compliance__close').click(function() {
        Drupal.eu_cookie_compliance.toggleWithdrawBanner();
      });
    }
  };
})(jQuery, Drupal, drupalSettings);
