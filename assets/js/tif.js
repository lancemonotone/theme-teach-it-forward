(function ($) {
    'use strict';
    var $tif_storyline_container = $('#tif-storyline-container');
    var $tif_comment_container = $('#tif-comment-container');
    var $tif_active_story, $tif_story_nav;
    var $loader = $('<div class="loader"></div>');
    var ajax_get_tif_posts, ajax_get_tif_comments;
    tif.post_parent = 0;

    get_tif_posts();
    acf_form_actions();


    function get_tif_posts() {
        ajax_get_tif_posts = $.ajax({
            url: tif.ajaxurl,
            type: 'post',
            data: {
                action: 'get_tif_posts',
                query_vars: tif.query_vars,
                post_parent: tif.post_parent
            },
            beforeSend: function () {
                emptyPostsContainer();
                emptyCommentContainer();
            },
            success: function (html) {
                populatePostsContainer(html);
                setupPostsEvents();
            }
        });
    }

    function get_tif_comments(post_id){
        ajax_get_tif_comments = $.ajax({
            url: tif.ajaxurl,
            type: 'post',
            data: {
                action: 'get_tif_comments',
                post_id: post_id
            },
            beforeSend: function () {
                emptyCommentContainer();
                $loader.clone().appendTo($tif_comment_container);
            },
            success: function (html) {
                populateCommentContainer(html);
            }
        });
    }

    function emptyCommentContainer(){
        $tif_comment_container.empty();
    }

    function populateCommentContainer(html){
        $tif_comment_container.find('.loader').remove();
        $tif_comment_container.append(html);
    }

    function emptyPostsContainer() {
        $tif_storyline_container.empty();
        $loader.clone().appendTo($tif_storyline_container);
    }

    function populatePostsContainer(html) {
        $tif_storyline_container.find('.loader').remove();
        $tif_storyline_container.append(html);
        $tif_active_story = $('#tif-active-story');
        $tif_story_nav = $('#tif-story-nav');
        load_active_story($tif_story_nav.find('article').eq(0));
    }

    function load_active_story($story) {
        $tif_active_story.fadeOut('fast', function () {
            $(this).find('article').remove();
            var $article = $story.clone().appendTo($tif_active_story);
            $article.find('.post-content').html($article.find('.post-content').data('post_content'));
            $(this).fadeIn('fast', function(){
                get_tif_comments($article.data('post_id'));
            });
        });
    }

    function cancel_ajax(){
        ajax_get_tif_posts.abort();
        ajax_get_tif_comments.abort();
    }

    function setupPostsEvents() {
        $tif_story_nav.on('click', 'article', function (e) {
            cancel_ajax();
            e.preventDefault();
            $tif_story_nav.find('article.active').removeClass('active');
            var $active_link = $(this);
            $active_link.addClass('active');
            load_active_story($active_link);
        });
        $tif_active_story.on('click', '.expand-storyline', function (e) {
            cancel_ajax();
            e.preventDefault();
            tif.post_parent = $(this).data('post_parent');
            get_tif_posts();
        });
        $tif_active_story.on('click', '.back-link', function (e) {
            cancel_ajax();
            e.preventDefault();
            tif.post_parent = $(this).data('post_parent');
            get_tif_posts();
        });
    }

    function acf_form_actions(){
        $('#tif-add-story-form').find('select[data-filter="taxonomy"]').find('option:eq(0)').each(function(){
           $(this).text('Select Storyline');
        });
    }

})
(jQuery);
