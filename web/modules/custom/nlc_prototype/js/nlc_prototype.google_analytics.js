(function ($, Drupal, drupalSettings) {
  "use strict";

  Drupal.behaviors.nlc_prototype_ga = {
    attach: function (context, settings) {
      // Track instances of no results
      if ($(".search-no-results", context).length) {
        let query = location.pathname + location.search;
        gtag("event", "Search", {
          event_category: "ZeroResults",
          event_label: query,
          transport_type: "beacon",
        });
      }
    },
  };

  $(document).ready(function () {
    // Track copy events on phone and email links
    $(document.body).on("copy", function (event) {
      var link = event.target.closest("a");
      if (!link) {
        return;
      }
      var href = link.href;
      if (!href) {
        return;
      }
      var category = "?";
      if (href.includes("tel:")) {
        category = "Telephone";
      } else if (href.includes("mailto:")) {
        category = "Mails";
      } else if (link.classList.contains("js-library-link")) {
        category = "Library links";
      } else if (href.match(/^\w+:\/\//i)) {
        category = "Outbound links";
      }
      gtag("event", "Copy", {
        event_category: category,
        event_label:
          "UID: " +
          drupalSettings.user.uid +
          " | Page: " +
          drupalSettings.path.currentPath,
        transport_type: "beacon",
      });
    });

    // Track clicks on phone links
    $(document.body).on(
      "mousedown keyup touchstart",
      "a[href^='tel:']",
      function (event) {
        gtag("event", "Click", {
          event_category: "Telephone",
          event_label:
            "UID: " +
            drupalSettings.user.uid +
            " | Page: " +
            drupalSettings.path.currentPath,
          transport_type: "beacon",
        });
      }
    );
    // Track clicks on mail links
    $(document.body).on(
      "mousedown keyup touchstart",
      "a[href^='mailto:'],area[href^='mailto:']",
      function (event) {
        gtag("event", "Click", {
          event_category: "Mails",
          event_label:
            "UID: " +
            drupalSettings.user.uid +
            " | Page: " +
            drupalSettings.path.currentPath +
            " | Context: " +
            $(event.target).prev().text(),
          transport_type: "beacon",
        });
      }
    );
    // Track library link clicks
    $(document.body).on(
      "mousedown keyup touchstart",
      ".js-library-link",
      function (event) {
        var link = event.target.closest("a");
        if (link.href.match(/^\w+:\/\//i)) {
          gtag("event", "Click", {
            event_category: "Library links",
            event_label: link.href,
            transport_type: "beacon",
          });
          gtag("event", "Click", {
            event_category: "Library topics",
            event_label: link.dataset.topic,
            transport_type: "beacon",
          });
          gtag("event", "Click", {
            event_category: "Library types",
            event_label: link.dataset.label,
            transport_type: "beacon",
          });
          gtag("event", "Click", {
            event_category: "Library read times",
            event_label: link.dataset.readTime,
            transport_type: "beacon",
          });
        }
      }
    );
    // Track when a user views their own profile page
    $("body")
      .once("narcissus")
      .each(function () {
        if (
          typeof drupalSettings.path.currentPathIsSelfProfile !== "undefined" &&
          drupalSettings.path.currentPathIsSelfProfile === true
        ) {
          gtag("event", "PageView", {
            event_category: "Own profile page",
            non_interaction: true,
            page_location: "/user/XXX",
            page_title: "User profile page",
          });
        }
      });
  });
})(jQuery, Drupal, drupalSettings);
