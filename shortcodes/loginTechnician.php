<?php

if (!class_exists('LoginTechnician')) {
    class LoginTechnician
    {
        public function __construct()
        {
            add_shortcode('atquimicos_technician_login_form', array($this, 'render_technician_login_form'));
            add_action('wp_login', array($this, 'redirect_technician_after_login'), 10, 2);

            // Hook para manejar redirección después de login exitoso
            add_filter('login_redirect', array($this, 'custom_technician_login_redirect'), 10, 3);
        }

        public function render_technician_login_form()
        {
            if (is_user_logged_in()) {
                $current_user = wp_get_current_user();
                if (in_array('tecnico', $current_user->roles) || in_array('administrator', $current_user->roles)) {
                    $create_reports_url = home_url('/crear-reporte/');
                    return '<p>Ya estás logueado como técnico. <a href="' . esc_url($create_reports_url) . '">Ir a crear reportes</a></p>';
                } else {
                    return '<p>Ya estás logueado, pero no tienes permisos de técnico.</p>';
                }
            }

            // Agregar campo de redirección oculto
            $redirect_to = isset($_GET['redirect_to']) ? esc_url($_GET['redirect_to']) : home_url('/crear-reporte/');
            $login_url = add_query_arg('login_redirect', 'atquimicos_technician', wp_login_url());

            ob_start();
?>
            <div class="container__atquimicos__report__form">
                <h2 style="text-align: center; margin-bottom: 30px; color: #333; font-weight: 600;">Iniciar Sesión - Técnicos</h2>
                <form action="<?php echo esc_url($login_url); ?>" method="post" class="userForm">
                    <label for="username">Usuario:</label>
                    <input type="text" name="log" id="username" required>

                    <label for="password">Contraseña:</label>
                    <input type="password" name="pwd" id="password" required>

                    <input type="hidden" name="redirect_to" value="<?php echo esc_url($redirect_to); ?>">

                    <button class="registerButton" type="submit">Iniciar Sesión</button>
                </form>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const form = document.querySelector('.userForm');
                        if (form) {
                            form.addEventListener('submit', function() {
                                // Guardar URL de redirección en localStorage como backup
                                localStorage.setItem('atquimicos_technician_redirect_url', '<?php echo esc_js($redirect_to); ?>');
                                localStorage.setItem('atquimicos_technician_login_attempt', 'true');
                            });
                        }

                        // Verificar si el usuario acaba de hacer login exitoso
                        if (localStorage.getItem('atquimicos_technician_login_attempt') === 'true') {
                            localStorage.removeItem('atquimicos_technician_login_attempt');

                            // Verificar estado de login con timeout más largo para hosting lento
                            setTimeout(function() {
                                fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/x-www-form-urlencoded',
                                        },
                                        body: 'action=check_technician_login_status'
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.logged_in && data.is_technician && data.redirect_url) {
                                            window.location.href = data.redirect_url;
                                        } else if (data.logged_in && !data.is_technician) {
                                            alert('No tienes permisos de técnico para acceder a esta área.');
                                        } else {
                                            // Usar URL de localStorage como fallback final
                                            const fallbackUrl = localStorage.getItem('atquimicos_technician_redirect_url');
                                            if (fallbackUrl) {
                                                localStorage.removeItem('atquimicos_technician_redirect_url');
                                                window.location.href = fallbackUrl;
                                            }
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error al verificar login:', error);
                                        // Usar URL de localStorage como fallback
                                        const fallbackUrl = localStorage.getItem('atquimicos_technician_redirect_url');
                                        if (fallbackUrl) {
                                            localStorage.removeItem('atquimicos_technician_redirect_url');
                                            window.location.href = fallbackUrl;
                                        }
                                    });
                            }, 1000); // Esperar 1 segundo para que el servidor procese el login
                        }
                    });
                </script>
            </div>
<?php
            return ob_get_clean();
        }

        public function redirect_technician_after_login($user_login, $user)
        {
            // Log para debugging
            error_log("ATQuimicos Technician Login - Usuario: {$user_login}, Roles: " . implode(', ', $user->roles));

            // Verificar que el usuario tenga el rol de técnico
            if (!in_array('tecnico', $user->roles) && !in_array('administrator', $user->roles)) {
                error_log("ATQuimicos Technician Login - Usuario sin permisos de técnico");
                return; // No redirigir si no es técnico o admin
            }

            // Verificar si ya se enviaron headers
            if (headers_sent()) {
                error_log("ATQuimicos Technician Login - Headers ya enviados, usando JavaScript redirect");
                // Usar JavaScript como fallback
                echo '<script type="text/javascript">window.location.href="' . esc_js(home_url('/crear-reporte/')) . '";</script>';
                return;
            }

            // Obtener la URL de redirección del formulario
            $redirect_to = isset($_POST['redirect_to']) ? esc_url_raw($_POST['redirect_to']) : '';

            // Si no hay URL de redirección, usar la URL de crear reportes
            if (empty($redirect_to)) {
                $redirect_to = home_url('/crear-reporte/');
            }

            error_log("ATQuimicos Technician Login - URL de redirección: {$redirect_to}");

            // Verificar que la URL de redirección sea válida y dentro del sitio
            if ($this->is_valid_redirect_url($redirect_to)) {
                // Limpiar cualquier output buffer antes de la redirección
                while (ob_get_level()) {
                    ob_end_clean();
                }

                // Usar wp_redirect con nocache_headers para evitar problemas de caché
                nocache_headers();
                wp_redirect($redirect_to, 302);
                exit;
            } else {
                error_log("ATQuimicos Technician Login - URL de redirección inválida");
            }
        }

        private function is_valid_redirect_url($url)
        {
            // Verificar que la URL no esté vacía
            if (empty($url)) {
                return false;
            }

            // Verificar que la URL sea del mismo dominio
            $parsed_url = parse_url($url);
            $site_url = parse_url(home_url());

            if (isset($parsed_url['host']) && $parsed_url['host'] !== $site_url['host']) {
                return false;
            }

            return true;
        }

        public function custom_technician_login_redirect($redirect_to, $request, $user)
        {
            // Verificar que sea nuestro formulario de login para técnicos
            if (isset($_POST['redirect_to']) && strpos($_POST['redirect_to'], 'crear-reporte') !== false) {

                // Verificar que el usuario tenga permisos de técnico
                if (isset($user->roles) && (in_array('tecnico', $user->roles) || in_array('administrator', $user->roles))) {
                    return home_url('/crear-reporte/');
                }
            }

            return $redirect_to; // Mantener comportamiento por defecto
        }
    }
}
