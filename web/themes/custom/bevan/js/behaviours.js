(function ($, Drupal, drupalSettings) {
  "use strict";

  Drupal.behaviors.bevantheme = {
    focusFacetId: null,
    respondToCookieBannerHeight: function () {
      // Set padding to the bottom of the page based on the height of the cookie banner
      var $popup = $("#sliding-popup");
      var $body = $("body");
      var cookieBannerHeight = 0;
      if ($popup.length !== 0) {
        cookieBannerHeight = $popup.css("height");
      }
      if (cookieBannerHeight == $body.css("padding-bottom")) {
        return false;
      }

      $body.css({
        "padding-bottom": cookieBannerHeight,
      });
    },
    attach: function (context, settings) {
      var bevan = this;
      this.respondToCookieBannerHeight();
      window.addEventListener("resize", this.respondToCookieBannerHeight);

      // Track when the popup closes so we can remove the body padding
      $("#sliding-popup").on("eu_cookie_compliance_popup_close", function () {
        var wrapper = this;
        window.setTimeout(function () {
          $(wrapper).remove();
          bevan.respondToCookieBannerHeight();
        }, drupalSettings.eu_cookie_compliance.popup_delay);
      });

      // Track facet clicks so we can restore focus.
      $(".facet-item a").click(function (ev) {
        Drupal.behaviors.bevantheme.focusFacetId = $(this).data(
          "drupal-facet-item-id"
        );
      });

      if (Drupal.behaviors.bevantheme.focusFacetId) {
        var el = $(context).find(
          '[data-drupal-facet-item-id="' +
            Drupal.behaviors.bevantheme.focusFacetId +
            '"]'
        );
        if (el.length > 0) {
          el.focus();
          // Clean up so we don't trigger this in unexpected AJAX calls.
          Drupal.behaviors.bevantheme.focusFacetId = null;
        }
      }

      // The summary block loads last so if it's the current AJAX context the check boxes exist.
      if ($(context).data("drupal-facets-summary-id") == "facet_summary") {
        // If we have any applied facets, we want to show the reset link.
        var facetsCount = $("#facet-summary-info").data("facets-facets-count");
        var resetLink = $("#search-reset-link");
        if (facetsCount == 0) {
          resetLink.addClass("hidden");
        } else {
          resetLink.removeClass("hidden");
        }
        // Only update the count if the text has actually changed, prevent screeen readers reading it twice.
        var count = 0;
        var updatedCount = $("#facet-summary-info").data(
          "facets-summary-count"
        );
        if (updatedCount) {
          count = updatedCount;
        }
        var countText = count + " results";
        if (countText != $("#result-count").text()) {
          $("#result-count").text(countText);
        }
      }

      // Annouce the number of items on the library page
      if ($(context).hasClass("connect-library")) {
        var message = "";
        var $itemCount = $(context).find(".nlc-item-count");
        var $noResults = $(context).find(".search-no-results");
        if ($itemCount.length > 0) {
          message = $itemCount.text();
        } else if ($noResults.length > 0) {
          message = $noResults.text();
        }
        Drupal.announce(Drupal.t(message));
      }
    },
  };
})(jQuery, Drupal, drupalSettings);
