<?php
/**
 *
 */
function print_events() {
    $permalink = get_permalink();
    $events    = get_events();
    $out       = '';
    foreach ($events as $event) {
        $description    = apply_filters('the_content', $event->post_content);
        $location_date  = get_location_date($event);
        $event_previous = get_field('event_previous', $event->ID);
        $event_class    = $event_previous ? 'previous' : 'upcoming';
        $event_rows     = get_field('event_schedule', $event->ID);
        $event_schedule = get_event_sched($event_rows);

        // Show edit link for admins
        $edit_link      = get_edit_post_link($event->ID);
        $edit_link_html = $edit_link ? '<a class="edit-link" href="' . $edit_link . '">Edit</a>' : '';

        $title_attribute = the_title_attribute(array(
            'post' => $event->ID,
            'echo' => false
        ));
        $addthis         = display_social_sharing($title_attribute, $permalink . '#event_' . $event->ID);

        switch ($event_class) {
            case 'upcoming':
                // Upcoming
                $speakers        = get_speakers($event);
                $promo_video_url = get_field('event_promo_video_url', $event->ID);
                $promo           = '';
                $background      = '';
                if ($img_arr = get_field('promo_video_poster_image', $event->ID)) {
                    $promo      = '<img src="' . $img_arr['url'] . '" alt="' . $img_arr['alt'] . '">';
                    $background = '<img class="item-background-image" src="' . $img_arr['url'] . '" alt="' . $img_arr['alt'] . '">';
                }
                if ($promo_video_url) {
                    $promo = <<<EOD
						<video id="event_video_{$event->ID}" data-poster="{$img_arr['url']}" data-src="{$promo_video_url}" class="video-js vjs-wms-skin vjs-big-play-centered">
							<p class="vjs-no-js">To view this video please enable JavaScript, and consider upgrading to a web browser that
								<a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a></p>
						</video>
EOD;
                }
                if ($rsvp_link = get_field('event_rsvp_link', $event->ID)) {
                    $rsvp_link = '<a href="' . $rsvp_link . '" class="button" target="_blank">RSVP</a>';
                }

                $out .= <<<EOD
				<li id="event_{$event->ID}" class="event-item {$event_class}">
				        {$rsvp_link}
						<div class="event-info expander"></div>
						{$background}
					<div class="wrap cf">
						{$edit_link_html}
						<div class="location-date">
							{$location_date}
						</div>
						<div class="event-details feature cf">
							<div class="video-container m-all t-2of5 d-2of5">
								{$promo}
							</div>
							<div class="details m-all t-3of5 d-3of5">
								<div class="description">
									{$description}
									{$event_schedule}
									
								</div>
								{$speakers}
							</div>
						</div>
						{$addthis}
					
					</div>
				</li>
EOD;
                break;
            case 'previous':
                // Previous
                $videos       = get_field('event_videos', $event->ID);
                $video_slider = get_video_slider($videos);
                $recap        = get_field('event_recap', $event->ID);

                $out .= <<<EOD
				<li id="event_{$event->ID}" class="event-item {$event_class} video">
					<div class="event-info expander"></div>
					<img class="item-background-image" src="" alt="Event background image">
					<div class="wrap cf">
						{$edit_link_html}
						<div class="location-date">
							{$location_date}
						</div>
						<div class="video-container">
							<video id="event_video_{$event->ID}" class="video-js vjs-wms-skin vjs-big-play-centered">
								<p class="vjs-no-js">To view this video please enable JavaScript, and consider upgrading to a web browser that
									<a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a></p>
							</video>
						</div>
						<div class="event-details feature"></div>
						<div class="event-video-slider-container">
							{$video_slider}
						</div>
						<div class="event-details">{$recap}</div>
						{$addthis}
					</div>
				</li>
EOD;
                break;
        }
    }

    if ($out) {
        echo <<<EOD
    <ul class="events-list">
        $out
    </ul>
EOD;
    }
}

/**
 * @return WP_Query
 */
function get_events() {
    global $post;

    if (get_field('events_hide_earlier_than_today', $post->ID)) {
        $hide_earlier = date('Ymd');
    } else {
        $hide_earlier = get_field('events_hide_earlier', $post->ID);
    }

    $args = array(
        'posts_per_page' => -1,
        'post_type'      => 'event',
        'post_status'    => 'publish',
        'meta_key'       => 'event_date',
        'orderby'        => 'meta_value_num',
        'order'          => 'ASC'
    );

    $meta_query = $hide_earlier ?
        array(
            'meta_query' => array(
                array(
                    'key'     => 'event_date',
                    'compare' => '>=',
                    'value'   => $hide_earlier
                )
            )
        )
        : array();
    $args       = array_merge($args, $meta_query);

    return get_posts($args);
}

/**
 * @param $event
 *
 * @return string
 */
function get_speakers($event) {
    $speaker_html = '';
    if ($speakers = get_field('event_speakers', $event->ID)) {
        $speaker_html .= '<ul class="event-speakers">';
        foreach ($speakers as $speaker) {
            $speaker_html .= <<<EOD
				<li>
					<p class="name">{$speaker['event_speaker_name']}</p>
					<span class="info">{$speaker['event_speaker_bio']}</span>
				</li>
EOD;
        }
        $speaker_html .= '</ul>';
    }

    return $speaker_html;
}

function get_video_slider($videos) {
    $videos_html = '';
    foreach ($videos as $video) {
        $videos_html .= get_video($video);
    }

    return <<<EOD
		<div class="gallery event-video-slider">
			<div class="video-items cf">
				{$videos_html}
			</div>
			<div class="previous"></div>
			<div class="next"></div>
		</div>
EOD;
}

/**
 * @param $video
 *
 * @return string
 */
function get_video($video) {
    return <<<EOD
	<div class="video-item">
		<div class="image">
			<img src="{$video['video_poster_image']['url']}" alt={$video['video_poster_image']['alt']}/>
		</div>
		<div class="details">
			<p class="title">{$video['video_title']}</p>
			<div class="description" data-src="{$video['video_url']}">
			    {$video['video_description']}
			</div>
		</div>
	</div>
EOD;
}

/**
 * @param $event
 *
 * @return string
 */
function get_location_date($event) {
    $location   = get_field('event_location', $event->ID);
    $event_type = get_field('event_type', $event->ID);
    $event_type = $event_type == "NA" || $event_type == "" ? "" : "<span class='separator'>|</span><div class='event-type'>TIF " . $event_type . "</div>";
    $timestamp  = strtotime(get_field('event_date', $event->ID));
    $date       = date('F j, Y', $timestamp);
    $title      = $event->post_title;

    return <<<EOD
	<div>
		<span class="location">{$location}</span>
		<span class="separator">|</span>
		<span class="date">{$date}{$event_type}</span>
		<h4 class="title">{$title}</h4>
	</div>
EOD;
}

/**
 * @param $rows -- repeater events
 *
 * @return html string
 *
 * build a schedule table from ACF repeater field row
 */
function get_event_sched($rows) {
    $html = "";
    if ($rows) {
        $html .= "<p><strong>Event Schedule</strong></p>";
        $html .= "<ul class='Rtable Rtable--3cols Rtable--collapse js-RtableAccordions'>";

        foreach ($rows as &$row) {
            $html .= "<li>";
            $html .= "<a href='#/' class='Accordion' role='tab' >" . $row["time"] . "</a>";
            $html .= "<div class='Rtable-cell alpha' style='order:0'>";
            $html .= $row["time"];
            $html .= "</div>";
            $html .= "<div class='Rtable-cell' style='order:1'>";
            $html .= $row["segment_title"];
            $html .= "</div>";
            $html .= "<div class='Rtable-cell omega ' style='order:2'>";
            $html .= $row["segment_description"];
            $html .= "</div>";
            $html .= "</li>";
        }

        $html .= "</ul>";
    }

    return $html;
}
