(function ($, Drupal, drupalSettings) {
  'use strict';

  Drupal.behaviors.nlc_prototype_ga = {
    attach: function(context, settings) {
      $("a[href^='tel:'],a[href^='mailto:']").bind({
        copy: function() {
          gtag('event', 'Copy', {
            event_category: this.href.includes('tel:') ? 'Telephone' : 'Mails',
            event_label: 'UID: ' + drupalSettings.user.uid + ' | Page: ' + drupalSettings.path.currentPath,
            transport_type: 'beacon'
          });
        }
      });

      $("a[href^='tel:']").bind( {
        click: function () {
          gtag('event', 'Click', {
            event_category: 'Telephone',
            event_label: 'UID: ' + drupalSettings.user.uid + ' | Page: ' + drupalSettings.path.currentPath,
            transport_type: 'beacon'
          });
        }
      });

      $("a[href^='mailto:'],area[href^='mailto:']", context).bind( {
        click: function () {
          gtag('event', 'Click', {
            event_category: 'Mails',
            event_label: 'UID: ' + drupalSettings.user.uid + ' | Page: ' + drupalSettings.path.currentPath + ' | Context: ' + $(this).prev().text(),
            transport_type: 'beacon'
          });
        }
      });

      if ($('h3.search-no-results', context).length) {
        let query = location.pathname + location.search;
        gtag('event', 'Search', {
            event_category: 'ZeroResults',
            event_label: query,
            transport_type: 'beacon'
          }
        );
      }
    }
  };

})(jQuery, Drupal, drupalSettings);
