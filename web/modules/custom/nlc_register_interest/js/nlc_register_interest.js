(function ($, Drupal) {
  'use strict';

  Drupal.behaviors.nlcRegisterInterest = {
    attach: function(context, settings) {

      $('#close-registered').once().click(function() {
        var $target = $('#block-registerinterestblock');
        if ($target) {
          $target.hide('fast', function() {
            $target.remove();
          });
        }
      });

      $('#nlc-register-interest-show-more').once().click(function(event) {
        $('.nlc-register-interest__full').addClass('expanded');
        $(this).attr('aria-expanded', true);
        $('#nlc-register-interest-not-now').attr('aria-expanded', true);
        event.preventDefault();
      });
      $('#nlc-register-interest-not-now').once().click(function(event) {
        $('.nlc-register-interest__full').removeClass('expanded');
        $(this).attr('aria-expanded', false);
        $('#nlc-register-interest-show-more').attr('aria-expanded', false);
        event.preventDefault();
      });
    }
  }

})(jQuery, Drupal);