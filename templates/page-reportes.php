<?php


?>

<div class="container__atquimicos__report">
    <?php


    if (!is_user_logged_in()) {
        echo '<p>Debes iniciar sesión para ver este contenido.</p>';
        return;
    }

    $current_user = wp_get_current_user();

    if (!in_array('cliente', $current_user->roles)) {
        echo "<p>Esta acción solo esta disponible para clientes, si usted es administrador o cliente puede usar.</p>";

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
        )
    );

    $reports = new WP_Query($args);

    if ($reports->have_posts()) : while ($reports->have_posts()) : $reports->the_post(); ?>
            <div class="report">
                <a href="<?php the_permalink(); ?>" target="_blank"><?php the_title(); ?></a>
            </div>
        <?php endwhile; ?>
        <!-- post navigation -->
    <?php else: ?>
        <!-- no posts found -->
    <?php endif; ?>


</div>