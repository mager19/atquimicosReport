<?php
// Asegurar que tenemos el ID del post actual
global $post;
$post_id = null;

// Método 1: Intentar get_the_ID() (función de WordPress)
if (function_exists('get_the_ID')) {
    $post_id = get_the_ID();
}

// Método 2: Si no funciona, usar el post global
if (!$post_id && isset($post->ID)) {
    $post_id = $post->ID;
}

// Método 3: Si aún no tenemos ID, intentar obtenerlo de la URL
if (!$post_id && isset($_GET['p'])) {
    $post_id = intval($_GET['p']);
}

// Método 4: Si estamos en una página de post type, usar queried_object_id
if (!$post_id && is_singular('atquimicossedes')) {
    $post_id = get_queried_object_id();
}

// Verificar que tenemos un ID válido
if (!$post_id || $post_id <= 0) {
    echo '<div class="container__atquimicos__report">';
    echo '<p><strong>Error:</strong> No se pudo obtener el ID de la sede. Por favor, verifica la URL.</p>';
    echo '</div>';
    return;
}

// Obtener los campos con el ID específico
$cliente = get_field('cliente', $post_id);
$contacto = get_field('contacto_en_la_sede', $post_id);
?>

<div class="container__atquimicos__report">
    <?php
    if (!is_user_logged_in()) {
        echo '<p>Debes iniciar sesión para ver este contenido.</p>';
        return;
    }
    ?>
    <h3>Sede: <?php the_title(); ?></h3>
    <p><span>Cliente: </span><?php echo ($cliente && isset($cliente->display_name)) ? esc_html($cliente->display_name) : 'No disponible'; ?></p>
    <p><span>Contacto en la sede: </span><?php echo $contacto ? esc_html($contacto) : 'No disponible'; ?></p>
</div>