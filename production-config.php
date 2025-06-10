<?php

/**
 * Configuraciones específicas para hosting de producción
 * Optimizaciones para cPanel y hosting compartido
 */

if (!defined('ABSPATH')) {
    exit;
}

// Configuraciones para mejorar redirecciones en hosting compartido
add_action('init', 'atquimicos_production_optimizations', 1);

function atquimicos_production_optimizations()
{
    // Configurar output buffering si no está activo
    if (!ob_get_level()) {
        ob_start();
    }

    // Configurar headers para evitar problemas de caché
    if (!headers_sent()) {
        header('Cache-Control: no-cache, must-revalidate, max-age=0');
    }
}

// Hook alternativo para redirección después de login (mayor prioridad)
add_action('wp_login', 'atquimicos_production_redirect', 1, 2);

function atquimicos_production_redirect($user_login, $user)
{
    // Solo actuar si es un usuario cliente o admin
    if (!in_array('cliente', $user->roles) && !in_array('administrator', $user->roles)) {
        return;
    }

    // Verificar si es nuestro formulario de login
    if (!isset($_POST['redirect_to']) || strpos($_POST['redirect_to'], 'reportes') === false) {
        return;
    }

    // Configurar redirección temporal en transient para hosting lento
    set_transient('atquimicos_pending_redirect_' . $user->ID, $_POST['redirect_to'], 300); // 5 minutos
}

// Hook para verificar redirecciones pendientes al cargar cualquier página
add_action('template_redirect', 'atquimicos_check_pending_redirects', 1);

function atquimicos_check_pending_redirects()
{
    if (!is_user_logged_in()) {
        return;
    }

    $user_id = get_current_user_id();
    $pending_redirect = get_transient('atquimicos_pending_redirect_' . $user_id);

    if ($pending_redirect) {
        delete_transient('atquimicos_pending_redirect_' . $user_id);

        // Verificar que no estemos ya en la página correcta
        $current_url = $_SERVER['REQUEST_URI'];
        if (strpos($current_url, 'reportes') === false) {
            if (!headers_sent()) {
                nocache_headers();
                wp_redirect($pending_redirect, 302);
                exit;
            }
        }
    }
}

// Función para generar URL de redirección con parámetros de depuración
function atquimicos_get_debug_redirect_url($user_id = null)
{
    $user_id = $user_id ? $user_id : get_current_user_id();
    $reportes_page_id = get_option('atquimicos_reporte_page_id');

    if ($reportes_page_id && get_post_status($reportes_page_id) === 'publish') {
        $page_url = get_permalink($reportes_page_id);
    } else {
        $page_url = home_url('/reportes/');
    }

    // Agregar parámetros de debug temporalmente
    $debug_params = array(
        'user_id' => $user_id,
        'timestamp' => time(),
        'debug' => 'redirect'
    );

    return add_query_arg($debug_params, $page_url);
}
