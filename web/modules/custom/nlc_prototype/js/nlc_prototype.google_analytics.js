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
        let query = Drupal.nlc_prototype_ga.getQueryParameterValues();
        ga('send', 'event', 'Search','ZeroResults', query);
      }
    }
  };

  Drupal.nlc_prototype_ga = {};

  Drupal.nlc_prototype_ga.getQueryParameterValues = function() {
    let query = {};
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.forEach(function(value, key) {
      if (key.includes('directory[')) {
        let item = value.split(':');
        if (typeof query[item[0]] === 'undefined') {
          query[item[0]] = [];
        }
        query[item[0]].push(item[1]);
      }
      else {
        query[key] = value;
      }
    });
    return query;
  };


})(jQuery, Drupal, drupalSettings);
