(function ($, Drupal, drupalSettings) {
  'use strict';
  
  Drupal.behaviors.bevantheme = {
    focusFacetId: null,
    attach: function (context, settings) {
      $('.eu-cookie-compliance__close').click(function() {
        Drupal.eu_cookie_compliance.toggleWithdrawBanner();
        $('.eu-cookie-compliance-banner').trigger('eu_cookie_compliance_popup_close');
      });

      $('.eu-cookie-compliance-banner').on('eu_cookie_compliance_popup_close', function() {
        var wrapper = this.parentElement;
        window.setTimeout(function() {
          $(wrapper).remove();
        }, drupalSettings.eu_cookie_compliance.popup_delay);
      })

      // Track facet clicks so we can restore focus.
      $('.facet-item a').click(function(ev) {
        Drupal.behaviors.bevantheme.focusFacetId = $(ev.target).data('drupal-facet-item-id');
      });

      // The summary block loads last so if it's the current AJAX context the check boxes exist.
      // If we set the focus earlier, it gets reset on the next load.
      if ($(context).data('drupal-facets-summary-id') == 'facet_summary') {

        // Only update the count if the text has actually changed, prevent screeen readers reading it twice.
        var countText = $('#facet-summary-wrapper').data('facets-summary-count') + ' results';
        if (countText != $('#result-count').text()) {
          $('#result-count').text(countText);
        }

        if (Drupal.behaviors.bevantheme.focusFacetId) {
          var el = document.querySelector('[data-drupal-facet-item-id="' + Drupal.behaviors.bevantheme.focusFacetId + '"]');
          if (el) {
            el.focus();
            // Clean up so we don't trigger this in unexpected AJAX calls.
            Drupal.behaviors.bevantheme.focusFacetId = null;
          }
        }
      }
    }
  };
})(jQuery, Drupal, drupalSettings);
