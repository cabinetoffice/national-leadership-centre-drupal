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
      // If we set the focus earlier, it gets reset on the next load.
      if ($(context).data("drupal-facets-summary-id") == "facet_summary") {
        // If we have any applied facets, we want to show the reset link.
        var facetsCount = $("#facet-summary-wrapper").data(
          "facets-facets-count"
        );
        var resetLink = $("#search-reset-link");
        if (facetsCount == 0) {
          resetLink.addClass("hidden");
        } else {
          resetLink.removeClass("hidden");
        }
        // Only update the count if the text has actually changed, prevent screeen readers reading it twice.
        var countText =
          $("#facet-summary-wrapper").data("facets-summary-count") + " results";
        if (countText != $("#result-count").text()) {
          $("#result-count").text(countText);
        }
      }
    },
  };
})(jQuery, Drupal, drupalSettings);
