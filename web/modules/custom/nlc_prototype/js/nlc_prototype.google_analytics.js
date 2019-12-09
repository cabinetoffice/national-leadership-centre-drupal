(function ($, Drupal, drupalSettings) {
  'use strict';

  Drupal.behaviors.nlc_prototype_ga = {
    attach: function(context, settings) {
      $("a[href^='tel:'],a[href^='mailto:']").bind({
        copy: function() {
          gtag('event', 'Copy', {
            event_category: this.href.includes('tel:') ? 'Telephone' : 'Mails',
            transport_type: 'beacon'
          });
        }
      });

      $("a[href^='tel:']").bind( {
        click: function () {
          gtag('event', 'Click', {
            event_category: 'Telephone',
            event_label: this.href.substring(4),
            transport_type: 'beacon'
          });
        }
      });
    }
  }

})(jQuery, Drupal, drupalSettings);