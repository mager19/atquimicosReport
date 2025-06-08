<?php

add_action('wp_ajax_filter_reports', 'filter_reports');
add_action('wp_ajax_nopriv_filter_reports', 'filter_reports');

function filter_reports()
{
    $year = isset($_POST['year']) ? intval($_POST['year']) : '';
    $month = isset($_POST['month']) ? intval($_POST['month']) : '';

    $args = array(
        'post_type' => 'atquimicosreports',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'cliente',
                'value' => get_current_user_id(),
                'compare' => '='
            )
        ),
        'orderby' => 'post_date',
        'order'   => 'DESC',
    );

    if ($year || $month) {
        $date_query = array('relation' => 'AND');

        if ($year) {
            $date_query[] = array(
                'year' => $year,
            );
        }

        if ($month) {
            $date_query[] = array(
                'month' => $month,
            );
        }

        $args['date_query'] = $date_query;
    }

    $reports = new WP_Query($args);

    if ($reports->have_posts()) :
        $grouped_reports = [];

        while ($reports->have_posts()) : $reports->the_post();
            $month = get_the_date('F Y');
            $grouped_reports[$month][] = [
                'title' => get_the_title(),
                'link'  => get_permalink(),
            ];
        endwhile;

        foreach ($grouped_reports as $month => $reports) :
            echo "<h2>" . esc_html($month) . "</h2>";
            echo "<ul>";
            foreach ($reports as $report) :
                echo "<li><a href='" . esc_url($report['link']) . "' target='_blank'>" . esc_html($report['title']) . "</a></li>";
            endforeach;
            echo "</ul>";
        endforeach;
    else :
        echo '<p>No se encontraron reportes para este cliente.</p>';
    endif;

    wp_reset_postdata();
    wp_die();
}
