<?php

/**
 * Template para mostrar un reporte individual
 * Este template se carga mediante template_include hook
 */

get_header();

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
if (!$post_id && is_singular('atquimicosreports')) {
    $post_id = get_queried_object_id();
}

// Método 5: Como último recurso, intentar obtenerlo de la URL slug
if (!$post_id && is_singular()) {
    global $wp_query;
    if (isset($wp_query->queried_object->ID)) {
        $post_id = $wp_query->queried_object->ID;
    }
}

// Debug: mostrar el ID en desarrollo (COMENTADO PARA PRODUCCIÓN)
// echo "<!-- TEMPLATE DEBUG INFO -->";
// echo "<!-- Post ID: " . $post_id . " -->";
// echo "<!-- Post Object: " . ($post ? 'Available' : 'Not Available') . " -->";
// echo "<!-- Global Post ID: " . (isset($GLOBALS['post']->ID) ? $GLOBALS['post']->ID : 'Not Set') . " -->";
// echo "<!-- Queried Object ID: " . get_queried_object_id() . " -->";
// echo "<!-- Is Singular: " . (is_singular('atquimicosreports') ? 'Yes' : 'No') . " -->";
// echo "<!-- Template Include Method: ACTIVE -->";

// Debug output desactivado para producción

// Verificar que tenemos un ID válido
if (!$post_id || $post_id <= 0) {
    echo '<div class="container__atquimicos__report">';
    echo '<p><strong>Error:</strong> No se pudo obtener el ID del reporte. Por favor, verifica la URL.</p>';
    echo '</div>';
    get_footer();
    return;
}

// Obtener los campos con el ID específico
$fecha = get_field('fecha', $post_id);
$tecnico = get_field('tecnico_atquimicos', $post_id);
$cliente = get_field('cliente', $post_id);
$sede = get_field('sedes', $post_id);
$tipo_reporte = get_field('tipo', $post_id);
$type = get_field('tipo', $post_id);
?>

<div class="container__atquimicos__report">
    <?php
    if (!is_user_logged_in()) {
        echo '<p>Debes iniciar sesión para ver este contenido.</p>';
        return;
    }
    ?>
    <h3>Informe: <?php the_title(); ?></h3>
    <p><span>Fecha: </span><?php echo $fecha ? esc_html($fecha) : 'No disponible'; ?></p>
    <p><span>Técnico ATQuimicos: </span><?php echo ($tecnico && isset($tecnico['display_name'])) ? esc_html($tecnico['display_name']) : 'No disponible'; ?></p>
    <p><span>Cliente: </span><?php echo ($cliente && isset($cliente->display_name)) ? esc_html($cliente->display_name) : 'No disponible'; ?></p>
    <p><span>Sede: </span><?php echo ($sede && is_array($sede) && count($sede) > 0 && isset($sede[0]->post_title)) ? esc_html($sede[0]->post_title) : 'No disponible'; ?></p>


    <?php
    if ($type && $type === 'caldera') {
        $variables = get_field('variables_caldera', $post_id);

        $parametros_fijos = array(
            'dureza_del_suavizador' => '0',
            'ph' => '10.5 - 11.5',
            'dureza_total_ppm' => 'Máximo 20',
            'alcalinidad_p_ppm' => '',
            'alcalinidad_m_ppm' => 'Máximo 700',
            'alcalinidad_oh__ppm' => '100 - 400',
            'solidos_disueltos_ppm' => 'Máximo 2500',
            'fosfatos_ppm' => '30 - 60',
            'sulfitos_ppm' => '30 - 60',
            'hierro_ppm' => 'Máximo 5',
            'silice_ppm' => 'Máximo 150',
            'oxigeno_ppm' => '0',
        );
    } else {
        $variables = get_field('variables_otros', $post_id);
        $parametros_fijos = array(
            'dureza_total' => 'Máximo 250',
            'ph' => 'Máximo 9',
            'alcalinidad_m' => 'Máximo 500',
            'solidos_disueltos' => 'Máximo 1500',
            'fosfatos' => 'Min 5 - Máx 10',
            'silice' => 'Máximo 200',
            'hierro' => 'Máximo 10',
        );
    }


    if ($variables && is_array($variables) && !empty($variables)) {
        echo '<table class="table_component" role="region" tabindex="0">';
        echo '<thead>';
        echo '    <tr>';
        echo '        <th>Variable</th>';
        echo '        <th>Valor</th>';
        echo '        <th>Parámetro</th>';
        echo '    </tr>';
        echo '</thead>';
        echo '<tbody>';

        // Iterar sobre el grupo de campos
        foreach ($variables as $key => $value) {
            // Verificar que tanto key como value sean válidos
            if (!empty($key) && $value !== null && $value !== '') {
                $parametro = isset($parametros_fijos[$key]) ? $parametros_fijos[$key] : 'N/A';
                echo '<tr>';
                echo '    <td>' . esc_html(str_replace('_', ' ', ucfirst($key))) . '</td>';
                echo '    <td>' . esc_html($value) . '</td>';
                echo '    <td>' . esc_html($parametro) . '</td>';
                echo '</tr>';
            }
        }

        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>No hay variables disponibles para este reporte.</p>';

        // Debug desactivado para producción
        // echo '<!-- DEBUG INFO: -->';
        // echo '<!-- Post ID: ' . $post_id . ' -->';
        // echo '<!-- Type: ' . ($type ? $type : 'NULL') . ' -->';
        // echo '<!-- Variables found: ' . ($variables ? 'Yes' : 'No') . ' -->';
        // echo '<!-- Variables type: ' . gettype($variables) . ' -->';
        // if (is_array($variables)) {
        //     echo '<!-- Variables count: ' . count($variables) . ' -->';
        // }

        // if (!$post_id) {
        //     echo '<p><em>Error: No se pudo obtener el ID del post.</em></p>';
        // } elseif (!$type) {
        //     echo '<p><em>Error: No se pudo determinar el tipo de reporte.</em></p>';
        // } else {
        //     echo '<p><em>Tipo de reporte: ' . esc_html($type) . '</em></p>';
        // }
    }



    ?>
</div>

<?php get_footer(); ?>