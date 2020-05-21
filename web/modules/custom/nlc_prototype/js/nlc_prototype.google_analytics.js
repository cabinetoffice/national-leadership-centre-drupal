(function ($, Drupal, drupalSettings) {
  "use strict";

  Drupal.behaviors.nlc_prototype_ga = {
    attach: function (context, settings) {
      // Track copy events on phone and email links
      $("a[href^='tel:'],a[href^='mailto:']").bind({
        copy: function () {
          gtag("event", "Copy", {
            event_category: this.href.includes("tel:") ? "Telephone" : "Mails",
            event_label:
              "UID: " +
              drupalSettings.user.uid +
              " | Page: " +
              drupalSettings.path.currentPath,
            transport_type: "beacon",
          });
        },
      });

      // Track clicks on phone links
      $("a[href^='tel:']").bind({
        click: function () {
          gtag("event", "Click", {
            event_category: "Telephone",
            event_label:
              "UID: " +
              drupalSettings.user.uid +
              " | Page: " +
              drupalSettings.path.currentPath,
            transport_type: "beacon",
          });
        },
      });

      // Track clicks on mail links
      $("a[href^='mailto:'],area[href^='mailto:']", context).bind({
        click: function () {
          gtag("event", "Click", {
            event_category: "Mails",
            event_label:
              "UID: " +
              drupalSettings.user.uid +
              " | Page: " +
              drupalSettings.path.currentPath +
              " | Context: " +
              $(this).prev().text(),
            transport_type: "beacon",
          });
        },
      });

      // Track instances of no results
      if ($(".search-no-results", context).length) {
        let query = location.pathname + location.search;
        gtag("event", "Search", {
          event_category: "ZeroResults",
          event_label: query,
          transport_type: "beacon",
        });
      }

      // Track when a user views their own profile page
      $("body")
        .once("narcissus")
        .each(function () {
          if (
            typeof settings.path.currentPathIsSelfProfile !== "undefined" &&
            settings.path.currentPathIsSelfProfile === true
          ) {
            gtag("event", "PageView", {
              event_category: "Own profile page",
              non_interaction: true,
              page_location: "/user/XXX",
              page_title: "User profile page",
            });
          }
        });

      $(".connect-library").on("mousedown keyup touchstart", "a", function (
        event
      ) {
        var $link = event.target.closest("a");
        if ($link.href.match(/^\w+:\/\//i)) {
          gtag("event", "Click", {
            event_category: "Library links",
            event_label: $link.href,
            transport_type: "beacon",
          });
        }
      });
    },
  };
})(jQuery, Drupal, drupalSettings);
