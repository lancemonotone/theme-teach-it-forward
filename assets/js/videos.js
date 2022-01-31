(function (window, $) {
    'use strict';

    var iOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;

    // Hotlinked video from URL query string 'v'
    var $hotlinkedVideo;

    // Caching element for performance.
    var $eventsList = $('.events-list');

    if (iOS) {
        $('body').addClass('iOS');
    }

    var caroufredselOptions = {
        responsive: true,
        circular: false,
        infinite: false,
        width: '100%',
        height: 'auto',
        swipe: true,
        prev: {
            button: function () {
                return $(this).parents('.event-video-slider').find('.previous');
            },
            key: "left"
        },
        next: {
            button: function () {
                return $(this).parents('.event-video-slider').find('.next');
            },
            key: "right"
        },
        auto: false,
        scroll: {
            items: 2,
            easing: "quadratic"
        },
        items: {
            width: 150,
            visible: {
                min: 2,
                max: 4
            }
        }
    };

    window.wmsPlayers = [];

// Video player
    function initVideo(config) {
        config.autoplay = config.autoplay || false;
        config.$container = config.$container || false;
        config.home = config.home || false;
        config.iOS = $('body').hasClass('iOS');
        config.loop = config.loop || false;
        config.muted = config.muted || false;
        config.poster = config.poster || '';
        config.ratio = config.ratio || 16 / 9;
        config.splash = config.splash || '';
        config.src = config.src || '';
        config.width = config.$container.width();

        var options = {
            controls: !config.iOS,
            ytcontrols: !!config.iOS,
            preload: 'auto',
            muted: config.muted,
            width: config.$container.width(),
            height: Math.ceil(config.width / config.ratio),
            techOrder: ['youtube', 'html5'],
            poster: config.poster,
            src: config.src,
            type: 'video/youtube'
        };

        // Init player and analytics plugin
        var player = videojs(config.$container.find('video').attr('id'), options);
        player.ga({
            eventsToTrack: ['play', 'pause', 'end'],
            debug: true,
            eventCategory: 'Campaign 2015'
        });

        // Assign player to the DOM element for easy access ($el[0])
        config.$container[0].player = player;
        window.wmsPlayers.push(config.$container[0].player);

        config.$container[0].player.on('loadstart', function () {
            config.$container[0].player.videoHelper(config);
            playerResize();
        });

        $(window).on('resize', function () {
            playerResize();
        });

        function playerResize() {
            var width = config.$container.width(),
                pHeight; // player height, tbd

            // get new player height
            pHeight = Math.ceil(width / config.ratio);
            $(config.$container[0].player.el()).width(width);
            // player height is greater, offset top; reset left
            $(config.$container[0].player.el()).height(pHeight);

        }
    }

    function pauseVideos() {
        for (var i = 0; i < window.wmsPlayers.length; i++) {
            if (!window.wmsPlayers[i].paused()) {
                window.wmsPlayers[i].pause();
            }
        }
    }

    function switchVideo(config) {
        pauseVideos();
        preventAnchorClick();

        config.$container[0].player
            .error(null)
            .src(config.src)
            .poster(config.poster)
            .setTimeout(function () {
                var player = config.$container[0].player;
                if (config.autoplay) {
                    player.play();
                } else {
                    player.pause();
                }
            }, 500);
    }

    function openExpander($eventItem) {
        pauseVideos();

        $eventItem.find('.event-info').removeClass('big-video');

        if ($eventItem.hasClass('active')) {
            $eventItem.removeClass('active');
        } else {
            $('.event-item.active').removeClass('active');
            $eventItem.addClass('active');
            if ($eventItem.hasClass('upcoming') && !$eventItem.hasClass('.video-loaded')) {
                var $container = $eventItem.find('.video-container');
                var config = {
                    $container: $container,
                    src: $container.find('video').data('src'),
                    poster: $container.find('video').data('poster')
                };
                if (config.src) {
                    initVideo(config)
                }
                $eventItem.addClass('video-loaded');
            }

            // GA Tracking
            var eventName = 'Event : ' + $eventItem.find('h4').text() + ' (' + $eventItem.attr('id') + ')';
            _gaq.push(['_trackEvent', 'Campaign 2015', 'click-expand', eventName]);

            // Minimize event date and location if videos present
            //if ($eventItem.find('.video-js').length) {
            if ($eventItem.hasClass('video')) {
                $eventItem.find('.event-info').addClass('big-video');
            }
        }

        smoothScrollTo($eventItem);
    }

    function initVideos() {
        if ($('.page.home').length) {
            initHomeVideo();
        } else if ($('.archive.tax-priorities').length) {
            initPrioritiesVideos();
        } else if ($eventsList.length) {
            initEventVideos();
        }

        preventAnchorClick();
    }

    function initHomeVideo() {
        $('.video-container').each(function () {
            var is_mobile = $('body').hasClass('is-mobile');
            var config = {
                $container: $(this),
                home: true,
                src: $(this).data('src'),
                poster: $(this).data('poster'),
                splash: $(this).data('splash'),
                autoplay: false,
                muted: true
            };
            initVideo(config);
        });
    }

    function initPrioritiesVideos() {
        $('.video-container').each(function () {
            // injected by WP from functions.php
            var config = {
                $container: $(this),
                home: true,
                splash: priorities.overlayText || '',
                src: $(this).data('src'),
                poster: $(this).data('poster')
            };
            initVideo(config);
        });
    }

    function initEventVideos() {

        $eventsList.find('.event-video-slider .video-items').each(function () {
            $(this).caroufredsel(caroufredselOptions);
        });

        // populate event with first video
        $eventsList.find('li.event-item.previous').each(function () {
            // Attach anchor hotlink tools
            $(this).find('.video-item').each(function () {
                attachAnchorLink.call(this);
            });

            // click and play video slider
            $(this).find('.event-video-slider .video-item').click(function () {
                sliderClickEvents.call(this);
            });

            loadFirstVideo.call(this);
        });

        // open expanders
        $eventsList.find('.event-info').click(function () {
            openExpander($(this).parents('.event-item'));
        });

        if (typeof $hotlinkedVideo == 'undefined') {
            gotoHash();
        }
    }

    function gotoHash() {
        if (window.location.hash) {
            openExpander($(window.location.hash));
        }
    }

    function getParameterByName(name) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(location.search);
        return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    }

    function attachAnchorLink() {
        var video_id = $(this).find('.description').data('src').split('?')[1];
        var ampersandPosition = video_id.indexOf('&');
        if (ampersandPosition != -1) {
            video_id = video_id.substring(0, ampersandPosition);
        }
        var url = window.location.href.split('?')[0];
        var completeUrl = url + '?' + video_id;
        var anchor = $('<a class="anchor" title="Right-click to copy link" href="' + completeUrl + '" aria-hidden="true"></a>');
        $(this).append(anchor);
    }

    function loadFirstVideo() {
        var $firstVideo;
        var hotlinkedVideoID = getParameterByName('v');

        if (hotlinkedVideoID) {
            $firstVideo = $hotlinkedVideo = $(this).find('.description[data-src*="' + hotlinkedVideoID + '"]').parents('.video-item');
        }

        if (typeof $firstVideo === 'undefined') {
            $firstVideo = $(this).find('.event-video-slider .video-item').first();
        }

        // place .video-item html inside .feature
        $(this).find('.feature').html($firstVideo.html()).promise().done(function () {
            // initialize player with first video
            var config = {
                $container: $(this).siblings('.video-container'),
                src: $(this).find('.description').data('src'),
                poster: $(this).find('.image img').attr('src')
            };
            initVideo(config);

            $(this).addClass('video-loaded');

            if (typeof $hotlinkedVideo != 'undefined' && $hotlinkedVideo.length) {
                openExpander($hotlinkedVideo.parents('.event-item'));
                $hotlinkedVideo.click();
            }
        });
        // set blurred background image
        $(this).find('.item-background-image').attr('src', $firstVideo.find('.image img').attr('src'));
    }

    function sliderClickEvents() {
        var autoplay = typeof $hotlinkedVideo === 'undefined';
        $hotlinkedVideo = undefined;
        // place .video-item html inside .feature
        $(this).parents('li.event-item.active').find('.feature').html($(this).html()).promise().done(function () {
            // switch video src and play
            var config = {
                $container: $(this).siblings('.video-container'),
                src: $(this).find('.description').data('src'),
                poster: $(this).find('.image img').attr('src'),
                autoplay: autoplay
            };
            switchVideo(config);
        });
        // set blurred background image
        $(this).parents('.event-item').find('.item-background-image').attr('src', $(this).find('.image img').attr('src'));
    }

    function preventAnchorClick() {
        $('a.anchor').click(function (e) {
            e.preventDefault();
        });
    }

    initVideos();

})(window, jQuery);

(function ($, vjs) {
    var iOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    // plugin initializer
    function videoHelper(config) {
        var player = this, $nav, $overlay;
        player.config = config || {};
        player.so = player.so || {};

        // Before play begins show splash title screen
        player.so._init = function (options) {
            if (player.config.iOS) {
                var title;
                if (title = config.$container.data('title')) {
                    config.$container.prepend('<h1 class="archive-title">' + title + '</h2>');
                }
            } else {
                $overlay = $('<div class="screen-overlay"></div>')
                    .click(function (e) {
                        if (player.so._isInit()) {
                            player.muted(false);
                            player.volume(0.5);
                            $(player.el()).removeClass('init');
                            player.currentTime(0);
                        } else if (!$overlay.find('ul').length) {
                            if (player.paused()) {
                                player.play();
                            } else {
                                player.pause();
                            }
                        }
                    });

                $(player.el()).append($overlay);

                player.so._displaySplash();

                player.on('play', function () {
                    player.so._hideOverlay();
                });

                // Show nav menu upon video end or pause
                if (options.home) {
                    player.on('ended', function () {
                        player.so._displayNavigation();
                    });

                    player.on('pause', function () {
                        if (!player.so._isSeeking()) {
                            player.so._displayNavigation();
                        }
                    });
                }
            }

            if (options.home) {
                player.so._getMainNav();
            }

            if (options.autoplay) {
                if (options.loop.hasOwnProperty('start')) {
                    player.currentTime(options.loop.start);
                } else {
                    player.currentTime(0);
                }
            }

            player.so._loopPlay();
        };

        player.on('mouseup', function (e) {
            console.log(e);
        });

        player.so._loopPlay = function () {
            if (config.loop.hasOwnProperty('start') && config.loop.hasOwnProperty('end')) {
                var loopInterval = setInterval(function () {
                    if (player.so._isInit()) {
                        if (player.currentTime() >= config.loop.end) {
                            player.currentTime(config.loop.start);
                        }
                    } else {
                        clearInterval(loopInterval);
                    }
                }, 200);
            }
        };

        player.so._isSeeking = function () {
            return $(player.el()).hasClass('vjs-seeking') || false;
        };

        player.so._isInit = function () {
            return $(player.el()).hasClass('init') || false;

        };

        player.so._hideOverlay = function () {
            if (!player.so._isInit()) {
                $overlay.addClass('no-display');
            }
        };

        player.so._showOverlay = function () {
            if (!player.so._isInit()) {
                $overlay.removeClass('no-display');
            }
        };

        player.so._getMainNav = function () {
            var $navOrig = $('#menu-primary-navigation');
            if ($navOrig.length) {
                $nav = $navOrig.clone();
                $nav.find('.menu-home').remove();
            }
        };

        player.so._displaySplash = function () {
            if (!iOS) {
                player.so._setInnerHtml('<h1 class="archive-title h2">' + config.splash + '</h1>');
                player.so._showOverlay();
            }
        };

        player.so._displayNavigation = function () {
            if (!iOS) {
                player.so._setInnerHtml($nav.get(0).outerHTML);
                player.so._showOverlay();
            }
        };

        player.so._setInnerHtml = function (text) {
            $overlay.find('.splash-click').html(text);
        };

        player.so._init(config);

        return this;
    }

    // register the plugin with video.js
    vjs.plugin('videoHelper', videoHelper);
})(jQuery, window.videojs);
