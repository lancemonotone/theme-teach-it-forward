<?php

/*********************
Add details expando shortcode
 *********************/
function details_shortcode( $atts, $content = null ) {
    extract( shortcode_atts( array(
                                 'button' => 'button'
                             ), $atts ) );
    return '<details class="cf"><summary><p>' . esc_attr($button) . '</p></summary>' . $content . '</details>';
}
add_shortcode('details', 'details_shortcode');

/*********************
Progress circle shortcode
 *********************/
function progress_shortcode( $atts, $content = null ) {
    extract( shortcode_atts( array(
                                 'percentage' =>  0,
                                 'label' => 'Percent',
                                 'color' => '#744F99',
                                 'radius'  => '90',
                                 'link' =>'#'
                             ), $atts ) );
            // cut 15% off to make it so it doesn't wrap past label
            $percentage_label = $percentage;
            $percentage = $percentage;

    $circle = <<<EOD

                <a class="donut donut-$radius" href="$link">
                  <svg width="240" height="240" xmlns="http://www.w3.org/2000/svg" class="donut-svg">
                    <circle id="donut-graph-x" class="donut-svg__scrim" r="$radius" cy="120" cx="120" stroke-width="3" stroke="#000" fill="none"/>
                    <circle id="donut-graph" class="donut-svg__circle--r$radius donut-svg__circle--$percentage" r="$radius" cy="120" cx="120" stroke-width="3" stroke="$color" stroke-linejoin="round" stroke-linecap="round" fill="none"/>
                  </svg>
                  <div class="donut-copy">
                    <span class="donut-title">$percentage_label<span class="donut-spic">%</span><br/></span>
                  </div>
                  <span class="donut-label">$label</span>
                </a>
EOD;

    return $circle;
}
add_shortcode('progress', 'progress_shortcode');