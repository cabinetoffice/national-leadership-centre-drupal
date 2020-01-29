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

      $('#nlc-register-interest-show-more').once().click(function() {
        $('.nlc-register-interest__full').addClass('expanded');
        return false;
      });
      $('#nlc-register-interest-not-now').once().click(function() {
        $('.nlc-register-interest__full').removeClass('expanded');
        return false;
      });
    }
  }

})(jQuery, Drupal);