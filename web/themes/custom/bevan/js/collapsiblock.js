(function ($) {

    Drupal.Collapsiblock = Drupal.Collapsiblock || {};

    Drupal.behaviors.collapsiblock = {

      attach: function (context, settings) {
        var cookieData = Drupal.Collapsiblock.getCookieData();
        var slidetype = settings.collapsiblock.slide_type;
        var activePages = settings.collapsiblock.active_pages;
        var slidespeed = parseInt(settings.collapsiblock.slide_speed, 10);
        // Override animation speed, as collapsiblock doesn't allow you to remove it
        slidespeed = 0;
        $('.collapsiblock').once('collapsiblock').each(function () {
          var id = this.id.split("-").pop();
          var titleElt = $(this)[0];
          var $button;
          if (titleElt.children.length > 0) {
            // Status values: 1 = not collapsible, 2 = collapsible and expanded,
            // 3 = collapsible and collapsed, 4 = always collapsed,
            // 5 = always expanded
            var stat = $(this).data('collapsiblock-action');
            if (stat == 1) {
              return;
            }

            titleElt.target = $(this).siblings().not('[data-contextual-id]');
            var targetId = 'collapse-' + id;
            titleElt.target.attr('id', targetId);
            $(titleElt).find('.js-facet-heading').wrapInner('<button type="button" class="govuk-accordion__section-button" aria-controls="' + targetId + '" />');
            $button = $(titleElt).find('.js-facet-heading').find('button');
            $button.append('<span class="govuk-accordion__icon" aria-hidden="true"></span>');
            $button.click(function (e) {
                var $titleElt =  $(titleElt);
                // If collapsed, expand
                if ($titleElt.is('.collapsiblockCollapsed')) {
                    $titleElt.removeClass('collapsiblockCollapsed');
                    $button.attr('aria-expanded', true);
                    if (slidetype == 1) {
                        $(titleElt.target).slideDown(slidespeed).attr('aria-hidden', false);
                    }
                    else {
                        $(titleElt.target).animate({
                            height: 'show',
                        opacity: 'show'
                        }, slidespeed);
                    }

                    // Don't save cookie data if the block is always collapsed.
                    if (stat != 4 && stat != 5) {
                        cookieData[id] = 1;
                    }
                }
                // If expanded, collapse
                else {
                    $titleElt.addClass('collapsiblockCollapsed');
                    $button.attr('aria-expanded', false);
                    if (slidetype == 1) {
                    $(titleElt.target).slideUp(slidespeed).attr('aria-hidden', true);
                    }
                    else {
                    $(titleElt.target).animate({
                        height: 'hide',
                        opacity: 'hide'
                    }, slidespeed);
                    }

                    // Don't save cookie data if the block is always collapsed.
                    if (stat != 4 && stat != 5) {
                    cookieData[id] = 0;
                    }
                }
                // Stringify the object in JSON format for saving in the cookie.
                cookieString = JSON.stringify(cookieData);
                $.cookie('collapsiblock', cookieString, {
                path: settings.basePath
            });
        });
        // Leave active blocks if Remember collapsed on active pages is false.
        // If the block is expanded, do nothing.
        if (stat == 4 || (cookieData[id] == 0 || (stat == 3 && cookieData[id] == undefined))) {
            if (!$(this).find('a.active').length || activePages === 1) {
                // Allow block content to assign class 'collapsiblock-force-open'
                // to it's content to force itself to stay open. E.g. useful if
                // block contains a form that was just ajaxly updated and should
                // be visible
                if (titleElt.target.hasClass('collapsiblock-force-open') || titleElt.target.find('.collapsiblock-force-open').length > 0) {
                    return;
                }
                $(titleElt).addClass('collapsiblockCollapsed');
                $button.attr('aria-expanded', false);
                $(titleElt.target).hide();
            }
        }
    }
    });
    }
    };

    Drupal.Collapsiblock.getCookieData = function () {
      if ($.cookie) {
        var cookieString = $.cookie('collapsiblock');
        return cookieString ? $.parseJSON(cookieString) : {};
      }
      else {
        return '';
      }
    };

  })(jQuery);
