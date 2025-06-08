<?php

if (!class_exists('ATQuimicosDiagnostics')) {
    class ATQuimicosDiagnostics
    {
        public function __construct()
        {
            add_shortcode('atquimicos_diagnostics', array($this, 'render_diagnostics'));
        }

        public function render_diagnostics()
        {
            // Solo permitir a administradores ver esta información
            if (!current_user_can('manage_options')) {
                return '<p>Acceso denegado. Solo administradores pueden ver esta información.</p>';
            }

            ob_start();
?>
            <div class="atquimicos-diagnostics">
                <h2>Diagnóstico ATQuímicos Reports</h2>

                <h3>🔧 Configuración de Páginas</h3>
                <?php $this->check_pages_configuration(); ?>

                <h3>👥 Configuración de Roles</h3>
                <?php $this->check_roles_configuration(); ?>

                <h3>🔗 URLs y Redirecciones</h3>
                <?php $this->check_urls_configuration(); ?>

                <h3>📁 Archivos del Plugin</h3>
                <?php $this->check_plugin_files(); ?>

                <h3>⚙️ Configuración de WordPress</h3>
                <?php $this->check_wp_configuration(); ?>
            </div>

            <style>
                .atquimicos-diagnostics {
                    background: #f9f9f9;
                    padding: 20px;
                    border-radius: 8px;
                    font-family: monospace;
                }

                .diagnostic-item {
                    margin: 10px 0;
                    padding: 10px;
                    border-left: 4px solid #ccc;
                    background: white;
                }

                .diagnostic-ok {
                    border-left-color: #46b450;
                }

                .diagnostic-error {
                    border-left-color: #dc3232;
                }

                .diagnostic-warning {
                    border-left-color: #ffb900;
                }
            </style>
<?php
            return ob_get_clean();
        }

        private function check_pages_configuration()
        {
            $pages_config = array(
                'atquimicos_reporte_page_id' => 'Reportes',
                'atquimicos_login_page_id' => 'Login ATQuimicos Clientes',
                'atquimicos_reports_page_id' => 'Crear Reporte',
                'atquimicos_sede_page_id' => 'Crear Sede'
            );

            foreach ($pages_config as $option_name => $page_title) {
                $page_id = get_option($option_name);
                $page_status = $page_id ? get_post_status($page_id) : false;
                $page_url = $page_id ? get_permalink($page_id) : 'N/A';

                $class = ($page_status === 'publish') ? 'diagnostic-ok' : 'diagnostic-error';
                $status = ($page_status === 'publish') ? '✅ OK' : '❌ ERROR';

                echo "<div class='diagnostic-item {$class}'>";
                echo "<strong>{$page_title}</strong> {$status}<br>";
                echo "ID: {$page_id} | Estado: {$page_status} | URL: <a href='{$page_url}' target='_blank'>{$page_url}</a>";
                echo "</div>";
            }
        }

        private function check_roles_configuration()
        {
            $roles_to_check = array('cliente', 'tecnico');

            foreach ($roles_to_check as $role_name) {
                $role = get_role($role_name);
                $class = $role ? 'diagnostic-ok' : 'diagnostic-error';
                $status = $role ? '✅ Existe' : '❌ No existe';

                echo "<div class='diagnostic-item {$class}'>";
                echo "<strong>Rol: {$role_name}</strong> {$status}";
                if ($role) {
                    echo "<br>Capacidades: " . implode(', ', array_keys($role->capabilities));
                }
                echo "</div>";
            }
        }

        private function check_urls_configuration()
        {
            // URL base del sitio
            echo "<div class='diagnostic-item diagnostic-ok'>";
            echo "<strong>URL del sitio:</strong> " . home_url();
            echo "</div>";

            // URL de login
            echo "<div class='diagnostic-item diagnostic-ok'>";
            echo "<strong>URL de login:</strong> " . wp_login_url();
            echo "</div>";

            // Verificar .htaccess y permalinks
            $permalink_structure = get_option('permalink_structure');
            $class = $permalink_structure ? 'diagnostic-ok' : 'diagnostic-warning';
            $status = $permalink_structure ? '✅ Configurado' : '⚠️ URLs simples';

            echo "<div class='diagnostic-item {$class}'>";
            echo "<strong>Estructura de permalinks:</strong> {$status}<br>";
            echo "Estructura: {$permalink_structure}";
            echo "</div>";
        }

        private function check_plugin_files()
        {
            $files_to_check = array(
                'templates/page-reportes.php',
                'shortcodes/loginUser.php',
                'assets/css/styles.css',
                'utils/createPage.php'
            );

            foreach ($files_to_check as $file) {
                $full_path = ATQUIMICOS_REPORTS_PATH . $file;
                $exists = file_exists($full_path);
                $class = $exists ? 'diagnostic-ok' : 'diagnostic-error';
                $status = $exists ? '✅ Existe' : '❌ No encontrado';

                echo "<div class='diagnostic-item {$class}'>";
                echo "<strong>{$file}</strong> {$status}<br>";
                echo "Ruta: {$full_path}";
                echo "</div>";
            }
        }

        private function check_wp_configuration()
        {
            // Debug mode
            $debug_status = WP_DEBUG ? '✅ Activado' : '❌ Desactivado';
            $debug_class = WP_DEBUG ? 'diagnostic-warning' : 'diagnostic-ok';

            echo "<div class='diagnostic-item {$debug_class}'>";
            echo "<strong>WP_DEBUG:</strong> {$debug_status}";
            echo "</div>";

            // WordPress version
            echo "<div class='diagnostic-item diagnostic-ok'>";
            echo "<strong>Versión de WordPress:</strong> " . get_bloginfo('version');
            echo "</div>";

            // PHP version
            echo "<div class='diagnostic-item diagnostic-ok'>";
            echo "<strong>Versión de PHP:</strong> " . PHP_VERSION;
            echo "</div>";

            // Memory limit
            echo "<div class='diagnostic-item diagnostic-ok'>";
            echo "<strong>Límite de memoria:</strong> " . ini_get('memory_limit');
            echo "</div>";
        }
    }
}
