(function ($, Drupal) {
  'use strict';

  Drupal.behaviors.nlcRegisterInterest = {
    attach: function(context, settings) {

      $('#close-registered').once().click(function() {
        var $target = $('#block-registerinterestblock');
        if ($target) {
          $target.hide('slow', function() {
            $target.remove();
          });
        }
      });

      $('#nlc-register-interest-show-more').once().click(function(event) {
        $('#register-interest-form').addClass('expanded');
        event.preventDefault();
      });
      $('#nlc-register-interest-not-now').once().click(function(event) {
        $('#register-interest-form').removeClass('expanded');
        event.preventDefault();
      });
    }
  }

})(jQuery, Drupal);