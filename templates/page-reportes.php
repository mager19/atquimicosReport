<div class="container__atquimicos__report">
    <?php
    if (!is_user_logged_in()) {
        echo '<p>Debes iniciar sesión para ver este contenido.</p>';
        return;
    }

    $current_user = wp_get_current_user();

    if (!in_array('cliente', $current_user->roles)) {
        echo "<p>Esta acción solo está disponible para clientes. Si usted es administrador o cliente puede usar.</p>";
        return;
    }

    $args = array(
        'post_type' => 'atquimicosreports',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'cliente',
                'value' => $current_user->ID,
                'compare' => '='
            )
        ),
        'orderby' => 'post_date',
        'order'   => 'DESC',
    );

    $reports = new WP_Query($args);

    if ($reports->have_posts()) :
        $grouped_reports = [];

        // Organizar los reportes por mes
        while ($reports->have_posts()) : $reports->the_post();
            $month = get_the_date('F Y'); // Obtener el mes y año del post
            $grouped_reports[$month][] = [
                'title' => get_the_title(),
                'link'  => get_permalink(),
            ];
        endwhile;

        // Mostrar los reportes agrupados
        foreach ($grouped_reports as $month => $reports) : ?>
            <h2><?php echo esc_html($month); ?></h2>
            <ul>
                <?php foreach ($reports as $report) : ?>
                    <li><a href="<?php echo esc_url($report['link']); ?>" target="_blank"><?php echo esc_html($report['title']); ?></a></li>
                <?php endforeach; ?>
            </ul>
    <?php endforeach;
    else :
        echo '<p>No se encontraron reportes para este cliente.</p>';
    endif;

    wp_reset_postdata();
    ?>
</div>