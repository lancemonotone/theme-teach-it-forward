/*
 * Bones Scripts File
 * Author: Eddie Machado
 *
 * This file should contain any js scripts you want to add to the site.
 * Instead of calling it in the header or throwing it inside wp_head()
 * this file will be called automatically in the footer so as not to
 * slow the page load.
 *
 * There are a lot of example functions and tools in here. If you don't
 * need any of it, just remove it. They are meant to be helpers and are
 * not required. It's your world baby, you can do whatever you want.
 */


/*
 * Get Viewport Dimensions
 * returns object with viewport dimensions to match css in width and height properties
 * ( source: http://andylangton.co.uk/blog/development/get-viewport-size-width-and-height-javascript )
 */
function updateViewportDimensions() {
    var w = window, d = document, e = d.documentElement, g = d.getElementsByTagName('body')[0], x = w.innerWidth || e.clientWidth || g.clientWidth, y = w.innerHeight || e.clientHeight || g.clientHeight;
    return {
        width : x,
        height: y
    };
}
// setting the viewport width
var viewport = updateViewportDimensions();

/*
 * Throttle Resize-triggered Events
 * Wrap your actions in this function to throttle the frequency of firing them off, for better performance, esp. on mobile.
 * ( source: http://stackoverflow.com/questions/2854407/javascript-jquery-window-resize-how-to-fire-after-the-resize-is-completed )
 */
var waitForFinalEvent = (function () {
    var timers = {};
    return function (callback, ms, uniqueId) {
        if (!uniqueId) {
            uniqueId = "Don't call this twice without a uniqueId";
        }
        if (timers[uniqueId]) {
            clearTimeout(timers[uniqueId]);
        }
        timers[uniqueId] = setTimeout(callback, ms);
    };
})();

// how long to wait before deciding the resize has stopped, in ms. Around 50-100 should work ok.
var timeToWaitForLast = 100;

/*
 * Here's an example so you can see how we're using the above function
 *
 * This is commented out so it won't work, but you can copy it and
 * remove the comments.
 *
 *
 *
 * If we want to only do it on a certain page, we can setup checks so we do it
 * as efficient as possible.
 *
 * if( typeof is_home === "undefined" ) var is_home = $('body').hasClass('home');
 *
 * This once checks to see if you're on the home page based on the body class
 * We can then use that check to perform actions on the home page only
 *
 * When the window is resized, we perform this function
 * $(window).resize(function () {
 *
 *    // if we're on the home page, we wait the set amount (in function above) then fire the function
 *    if( is_home ) { waitForFinalEvent( function() {
 *
 *	// update the viewport, in case the window size has changed
 *	viewport = updateViewportDimensions();
 *
 *      // if we're above or equal to 768 fire this off
 *      if( viewport.width >= 768 ) {
 *        console.log('On home page and window sized to 768 width or more.');
 *      } else {
 *        // otherwise, let's do this instead
 *        console.log('Not on home page, or window sized to less than 768.');
 *      }
 *
 *    }, timeToWaitForLast, "your-function-identifier-string"); }
 * });
 *
 * Pretty cool huh? You can create functions like this to conditionally load
 * content and other stuff dependent on the viewport.
 * Remember that mobile devices and javascript aren't the best of friends.
 * Keep it light and always make sure the larger viewports are doing the heavy lifting.
 *
 */

// SMOOTH SCROLL TO INTERNAL LINKS
var $scroll_root = jQuery('html, body');
function smoothScrollTo($el) {
    if ($el.length) {
        $scroll_root.animate({
            scrollTop: $el.offset().top
        }, 500, function () {
            // Uncomment next line to add hash to URL
            //window.location.hash = href;
        });
        return false;
    }
}

jQuery(document).ready(function ($) {
    'use strict';

    // Track link clicks with GA
    $('.give-now').click(function (e) {
        var eventName = $(this).text() + ' | from: ' + window.location.href;
        _gaq.push(['_trackEvent', 'Campaign 2015', 'click-give', eventName]);
    });

    var $footer        = $('.footer');
    var $footerButtons = $('.footer-buttons');
    var $window        = $(window);
    var wpos, wheight, footertop, fheight;

    function pause() {
        setTimeout(pause, 3000);
    }

    function stickyFooter() {
        wpos      = $window.scrollTop();
        wheight   = $window.height();
        fheight   = $footer.height() - $footerButtons.height();
        footertop = $footer.offset().top;

        if ((wpos + wheight - fheight) < footertop) {
            $footerButtons.addClass('fixed');
            pause();
        } else {
            $footerButtons.removeClass('fixed');
            pause();
        }
    }

    $window
        .ready(stickyFooter)
        .resize(stickyFooter)
        .scroll(stickyFooter);

    function gotoHash() {
        var hash = window.location.hash;
        if (hash) {
            $(hash).addClass('active queued');
            openActiveBox();
        }
    }

    if ($('.page-template-page-pwp').length ||
        $('.page-template-page-tiftoday').length ||
        $('.page-template-page-tif_stories').length) {
        gotoHash();
    }

    $('a.sharing').click(function (e) {
        e.stopPropagation();
    });

    $('.button.back-to-top').click(function () {
        smoothScrollTo($('body'));
    });

    // mobile menu activation
    var $mobileMenu     = $('.mobilemenu');
    var $videoContainer = $('.video-container');
    $mobileMenu.click(function () {
        $('.header nav').toggleClass('active');
        $mobileMenu.toggleClass('active');
        if (!$mobileMenu.hasClass('active')) {
            $videoContainer.css('z-index', -1);
        } else {
            $videoContainer.css('z-index', 1);
        }
    });

    // Priorities Page
    // get subnav and put it in nav
    var mpsub = $('#menu-priorities-subnavigation').html();
    //var mpsub = '<ul class="sub-menu">' + $('#menu-priorities-subnavigation').html() + '</ul>';
    if (mpsub) {
        $('li#menu-item-270').append('<ul class="sub-menu">' + mpsub + '</ul>');
    }

    $('.priority-posts .priority').click(function (e) {
        if (!$(this).hasClass('active')) {
            // Only track open event
            var eventName = 'Priority : ' + $(this).find('h5').first().text() + ' (' + $(this).attr('id') + ')';
            _gaq.push(['_trackEvent', 'Campaign 2015', 'click-expand', eventName]);
        }
        $(this).toggleClass('active');
    });

    /*
     * TIF Today and PwP expandos
     */
    $('.expands').hover(
        function (e) {
            if (!$('#child-of-' + $(this).attr('id')).length) {
                // Build expando
                constructExpando($(this), true);
            }
        }
    );

    function constructExpando(source, hideIt) {
        // If this is a single column of posts we want every post to open underneath.
        var isSingleCol = source.parents().hasClass('single-col');

        // Is box odd-numbered?
        var isLeft = source.parent().children('.expands').index(source) % 2 === 0;

        // Add left-col class if box is odd.
        source.addClass(isLeft ? 'left-col' : '');

        // Check for hidden div or the expandoData object (defined in template library for page).
        var expandoContent = expandoData[source.attr('id')];
        var style          = hideIt ? 'style="display:none;" ' : '';
        var boxContent     = '<div ' + style + 'id="child-of-' + source.attr('id') + '" class="featured-content" data-postid="' + source.attr('id') + '">'
            + expandoContent + '<div class="close-box"><a href="javascript:void(0);">Close</a>'
            + '</div></div>';

        if (isSingleCol) {
            source.after(boxContent);
        } else {
            var viewWidth = $(window).width();
            if (viewWidth >= 768 && isLeft) {
                source.next().after(boxContent);
            } else {
                source.after(boxContent);
            }
        }

        if (typeof instgrm === 'object' && source.hasClass('icon-instagram')) {
            // Process instragram embeds added after initial
            // load of instagram js file.
            instgrm.Embeds.process()
        }
    }

    var $socialList = $('.social-list');

    $socialList.on('click', '.close-box', function (e) {
        e.preventDefault();
        $(this).parent().slideUp();
        $('#' + $(this).parent().data('postid')).removeClass('active');
        smoothScrollTo($(this).parent().prev('.social-item'));
    });

    // social item expand, close
    $socialList.on('click', '.expands > div', function (e) {
        e.preventDefault();
        var $activeBox = $(this).closest('.expands');

        if ($activeBox.hasClass('active')) {
            $activeBox.removeClass('active');
        } else {
            // Deactivate all boxes to ensure only the clicked is active
            $activeBox.addClass('active queued');
            // track event
            doGaTracking($activeBox);
        }
        closeOpenBoxes($activeBox);
    });

    function closeOpenBoxes($activeBox) {
        // Close any open box if it will be replaced by a box
        var $childBox = $('#child-of-' + $activeBox.attr('id'));
        if ($activeBox.hasClass('active')) {
            // Make sure adjacent expandos are closed before opening
            // $activeBox's expando.
            var $activeBoxes = $('.featured-content.active');
            if ($activeBoxes.length) {
                $($activeBoxes).slideUp(400, function () {
                    $(this).removeClass('active');
                    $('#' + $(this).data('postid')).removeClass('active');
                }).promise().done(openActiveBox);
            } else {
                openActiveBox();
            }
        } else {
            // Close $activeBox's expando
            $childBox.slideUp(400, function () {
                $(this).removeClass('active');
            });
        }
    }

    function openActiveBox() {
        var $activeBox  = $('.active.queued');
        var isSingleCol = $activeBox.parents().hasClass('single-col');

        smoothScrollTo($activeBox);

        if ($activeBox.length) {
            var $child = $('#child-of-' + $activeBox.attr('id'));
            $child.find('a').has('img').featherlight();
            if (isSingleCol) {
                // If this is a single column of posts we want every post to open underneath.
                $child.detach().insertAfter($activeBox);
            } else {
                if (
                    $activeBox.hasClass('left-col')
                    && $activeBox.next().first().attr('id') != 'child-of-' + $activeBox.attr('id')
                ) {
                    if ($(window).width() < 768) {
                        $child.detach().insertAfter($activeBox);
                    } else {
                        // Layout has changed from 1- to 2-column, move box;
                        $child.detach().insertAfter($activeBox.nextAll('.social-item').first());
                    }
                }
            }

            if ($child.length) {
                $child.addClass('active').slideDown(600);
                $activeBox.removeClass('queued');
            } else {
                // constructExpando($activeBox, hideIt = true/false);
                constructExpando($activeBox, false);
                openActiveBox();
            }
        }
        $activeBox.removeClass('queued');
    }

    function doGaTracking(activeBox) {
        var siteSection;
        if ($('body.page-template-page-pwp').length) {
            siteSection = 'PwP';
        } else if ($('body.page-template-page-tiftoday').length) {
            siteSection = 'TIF Today'
        } else if ($('body.page-template-page-tif_stories').length) {
            siteSection = 'TIF Stories'
        }
        var eventName = siteSection + ' : ' + activeBox.find('h3').first().text() + ' (' + activeBox.attr('id') + ')';
        _gaq.push(['_trackEvent', 'Campaign 2015', 'click-expand', eventName]);
    }
});
///bottom scroll arrow removal
jQuery(window).scroll(function() {

    if (jQuery(this).scrollTop()>0)
    {
        jQuery('#scroll_arrow').fadeOut();
    }
    else
    {
        jQuery('#scroll_arrow').fadeIn();
    }
});
