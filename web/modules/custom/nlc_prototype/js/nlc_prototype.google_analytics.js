(function ($, Drupal, drupalSettings) {
  'use strict';

  Drupal.behaviors.nlc_prototype_ga = {
    attach: function(context, settings) {
      $("a[href^='tel:'],a[href^='mailto:']").bind({
        copy: function() {
          const value = this.href.includes('tel:') ? this.href.substring(4) : this.href.substring(7);
          gtag('event', 'Connection', {
            event_category: this.href.includes('tel:') ? 'tel' : 'mailto',
            event_label: $("#block-bevan-page-title h1").text() + ' | ' + $(this).siblings('strong').text(),
            value: value,
            transport_type: 'beacon'
          });
        }
      });

      $("a[href^='tel:']").bind( {
        click: function () {
          gtag('event', 'Click', {
            event_category: 'Telephone',
            event_label: $("#block-bevan-page-title h1").text() + ' | ' + $(this).siblings('strong').text(),
            value: this.href.substring(4),
            transport_type: 'beacon'
          });
        }
      });
    }
  }

})(jQuery, Drupal, drupalSettings);