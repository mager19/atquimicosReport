<?php

/**
 * Debug de redirecciones para hosting de producción
 * Agrega este archivo temporalmente para diagnosticar problemas
 */

if (!defined('ABSPATH')) {
    exit;
}

// Agregar logs detallados para debugging en producción
add_action('wp_login', 'debug_login_redirect', 5, 2);
add_action('template_redirect', 'debug_template_redirect', 1);

function debug_login_redirect($user_login, $user)
{
    // Log básico que funciona en producción
    error_log("=== ATQuimicos Debug Login ===");
    error_log("Usuario: $user_login");
    error_log("Roles: " . implode(', ', $user->roles));
    error_log("Headers sent: " . (headers_sent() ? 'SÍ' : 'NO'));
    error_log("Output buffering: " . (ob_get_level() > 0 ? 'SÍ' : 'NO'));
    error_log("Redirect URL esperada: " . home_url('/reportes/'));
    error_log("==============================");
}

function debug_template_redirect()
{
    if (is_user_logged_in() && isset($_GET['debug_atquimicos'])) {
        $current_user = wp_get_current_user();
        echo "<h2>Debug ATQuímicos - Estado del Sistema</h2>";
        echo "<p><strong>Usuario logueado:</strong> " . $current_user->user_login . "</p>";
        echo "<p><strong>Roles:</strong> " . implode(', ', $current_user->roles) . "</p>";
        echo "<p><strong>Headers enviados:</strong> " . (headers_sent() ? 'SÍ' : 'NO') . "</p>";
        echo "<p><strong>URL actual:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";
        echo "<p><strong>URL de reportes esperada:</strong> " . home_url('/reportes/') . "</p>";
        echo "<p><strong>Página de reportes ID:</strong> " . get_option('atquimicos_reporte_page_id') . "</p>";

        $reportes_page_id = get_option('atquimicos_reporte_page_id');
        if ($reportes_page_id) {
            echo "<p><strong>Estado de página reportes:</strong> " . get_post_status($reportes_page_id) . "</p>";
            echo "<p><strong>URL de página reportes:</strong> " . get_permalink($reportes_page_id) . "</p>";
        }

        exit;
    }
}
