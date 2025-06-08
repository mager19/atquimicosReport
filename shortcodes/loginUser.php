<?php

if (!class_exists('LoginUser')) {
    class LoginUser
    {
        public function __construct()
        {
            add_shortcode('atquimicos_login_form', array($this, 'render_login_form'));
            add_action('wp_login', array($this, 'redirect_after_login'), 10, 2);
            add_filter('the_content', array($this, 'load_custom_template'));

            // Hook alternativo con mayor prioridad para asegurar la redirección
            add_action('wp_loaded', array($this, 'check_login_redirect'));

            // Hook adicional para capturar redirecciones perdidas
            add_action('template_redirect', array($this, 'handle_login_redirect_fallback'));

            // Hook para manejar redirección después de login exitoso
            add_filter('login_redirect', array($this, 'custom_login_redirect'), 10, 3);
        }

        public function render_login_form()
        {
            if (is_user_logged_in()) {
                $reports_url = $this->get_user_reports_url();
                return '<p>Ya estás logueado. <a href="' . esc_url($reports_url) . '">Ver tus reportes</a></p>';
            }

            // Agregar campo de redirección oculto
            $redirect_to = isset($_GET['redirect_to']) ? esc_url($_GET['redirect_to']) : $this->get_user_reports_url();
            $login_url = add_query_arg('login_redirect', 'atquimicos', wp_login_url());

            ob_start();
?>
            <div class="container__atquimicos__report">
                <form action="<?php echo esc_url($login_url); ?>" method="post" class="userForm">
                    <label for="username">Usuario o Email</label>
                    <input type="text" name="log" id="username" required>

                    <label for="password">Contraseña</label>
                    <input type="password" name="pwd" id="password" required>

                    <input type="hidden" name="redirect_to" value="<?php echo esc_url($redirect_to); ?>">

                    <button class="registerButton" type="submit">Iniciar Sesión</button>
                </form>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Fallback con JavaScript si la redirección del servidor falla
                        const form = document.querySelector('.userForm');
                        if (form) {
                            form.addEventListener('submit', function() {
                                // Guardar URL de redirección en localStorage como backup
                                localStorage.setItem('atquimicos_redirect_url', '<?php echo esc_js($redirect_to); ?>');
                            });
                        }

                        // Verificar si el usuario acaba de hacer login exitoso
                        if (localStorage.getItem('atquimicos_login_attempt') === 'true') {
                            localStorage.removeItem('atquimicos_login_attempt');

                            // Si estamos en la página de login pero el usuario está logueado, redirigir
                            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded',
                                    },
                                    body: 'action=check_login_status'
                                }).then(response => response.json())
                                .then(data => {
                                    if (data.logged_in && data.redirect_url) {
                                        window.location.href = data.redirect_url;
                                    }
                                });
                        }
                    });

                    // Marcar que se está intentando hacer login
                    document.querySelector('.userForm').addEventListener('submit', function() {
                        localStorage.setItem('atquimicos_login_attempt', 'true');
                    });
                </script>
            </div>
<?php
            return ob_get_clean();
        }

        public function redirect_after_login($user_login, $user)
        {
            // Log para debugging (solo en desarrollo)
            if (WP_DEBUG) {
                error_log("ATQuimicos Login - Usuario: {$user_login}, Roles: " . implode(', ', $user->roles));
            }

            // Verificar que el usuario tenga el rol de cliente
            if (!in_array('cliente', $user->roles) && !in_array('administrator', $user->roles)) {
                if (WP_DEBUG) {
                    error_log("ATQuimicos Login - Usuario sin permisos de cliente");
                }
                return; // No redirigir si no es cliente o admin
            }

            // Obtener la URL de redirección del formulario
            $redirect_to = isset($_POST['redirect_to']) ? esc_url_raw($_POST['redirect_to']) : '';

            // Si no hay URL de redirección, generar la URL de reportes
            if (empty($redirect_to)) {
                $redirect_to = $this->get_user_reports_url($user->ID);
            }

            if (WP_DEBUG) {
                error_log("ATQuimicos Login - URL de redirección: {$redirect_to}");
            }

            // Verificar que la URL de redirección sea válida y dentro del sitio
            if ($this->is_valid_redirect_url($redirect_to)) {
                // Usar wp_redirect en lugar de wp_safe_redirect para más control
                wp_redirect($redirect_to);
                exit;
            } else {
                if (WP_DEBUG) {
                    error_log("ATQuimicos Login - URL de redirección inválida");
                }
            }
        }

        // todo: logout redirect

        public function get_user_reports_url($user_id = null)
        {
            $user_id = $user_id ? $user_id : get_current_user_id();

            // Verificar si la página de reportes existe
            $reportes_page_id = get_option('atquimicos_reporte_page_id');

            if ($reportes_page_id && get_post_status($reportes_page_id) === 'publish') {
                // Usar la página creada por el plugin
                $page_url = get_permalink($reportes_page_id);
            } else {
                // Fallback al slug por defecto
                $page_url = home_url('/reportes/');
            }

            return add_query_arg('user_id', $user_id, $page_url);
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

        public function check_login_redirect()
        {
            // Verificar si el usuario acaba de hacer login y necesita redirección
            if (is_user_logged_in() && isset($_GET['login_redirect']) && $_GET['login_redirect'] === 'atquimicos') {
                $current_user = wp_get_current_user();

                if (in_array('cliente', $current_user->roles) || in_array('administrator', $current_user->roles)) {
                    $redirect_url = $this->get_user_reports_url($current_user->ID);
                    wp_redirect($redirect_url);
                    exit;
                }
            }
        }

        public function handle_login_redirect_fallback()
        {
            // Solo ejecutar en la página de login y si hay una sesión activa
            if (is_user_logged_in() && is_page() && get_the_title() === 'Login ATQuimicos Clientes') {
                $current_user = wp_get_current_user();

                if (in_array('cliente', $current_user->roles) || in_array('administrator', $current_user->roles)) {
                    $redirect_url = $this->get_user_reports_url($current_user->ID);
                    wp_redirect($redirect_url);
                    exit;
                }
            }
        }

        public function custom_login_redirect($redirect_to, $request, $user)
        {
            // Verificar que sea nuestro formulario de login
            if (isset($_POST['redirect_to']) && strpos($_POST['redirect_to'], 'reportes') !== false) {

                // Verificar que el usuario tenga permisos
                if (isset($user->roles) && (in_array('cliente', $user->roles) || in_array('administrator', $user->roles))) {
                    return $this->get_user_reports_url($user->ID);
                }
            }

            return $redirect_to; // Mantener comportamiento por defecto
        }

        public function load_custom_template($content)
        {
            global $post;

            // Verificar múltiples formas de identificar la página de reportes
            $is_reports_page = false;

            // Método 1: Por slug
            if (is_page('reportes')) {
                $is_reports_page = true;
            }

            // Método 2: Por ID de página guardado en opciones
            $reportes_page_id = get_option('atquimicos_reporte_page_id');
            if ($reportes_page_id && is_page($reportes_page_id)) {
                $is_reports_page = true;
            }

            // Método 3: Por título de página
            if ($post && $post->post_title === 'Reportes') {
                $is_reports_page = true;
            }

            if ($is_reports_page) {
                // Verificar que el archivo del template existe
                $template_path = ATQUIMICOS_REPORTS_PATH . 'templates/page-reportes.php';

                if (file_exists($template_path)) {
                    ob_start();
                    require_once $template_path;
                    return ob_get_clean();
                } else {
                    return '<p>Error: Template de reportes no encontrado.</p>';
                }
            }

            return $content; // Retorna el contenido original si no es la página de reportes
        }
    }
}
