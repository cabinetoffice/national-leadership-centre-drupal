(function ($, Drupal, drupalSettings) {
  'use strict';
  
  Drupal.behaviors.bevantheme = {
    focusFacetId: null,
    attach: function (context, settings) {
      $('.eu-cookie-compliance__close').click(function() {
        Drupal.eu_cookie_compliance.toggleWithdrawBanner();
      });

      // Get the result count once the facet results have loaded.
      $('#result-count').text($('#facet-summary-wrapper').data('facets-summary-count') + ' results');

      // Track facet clicks so we can restore focus.
      $('.facet-item a').click(function(ev) {
        Drupal.behaviors.bevantheme.focusFacetId = $(ev.target).data('drupal-facet-item-id');
      });

      // The summary block loads last so if it's the current AJAX context the check boxes exist.
      // If we set the focus earlier, it gets reset on the next load.
      if (Drupal.behaviors.bevantheme.focusFacetId && $(context).data('drupal-facets-summary-id') == 'facet_summary') {
        var el = document.querySelector('[data-drupal-facet-item-id="' + Drupal.behaviors.bevantheme.focusFacetId + '"]');
        if (el) {
          el.focus();
          // Clean up so we don't trigger this in unexpected AJAX calls.
          Drupal.behaviors.bevantheme.focusFacetId = null;
        }
      }
    }
  };
})(jQuery, Drupal, drupalSettings);
