<?php

if (!class_exists('LoginUser')) {
    class LoginUser
    {
        public function __construct()
        {
            add_shortcode('atquimicos_login_form', array($this, 'render_login_form'));
            add_action('wp_login', array($this, 'redirect_after_login'), 10, 2);
            add_filter('the_content', array($this, 'load_custom_template'));
        }

        public function render_login_form()
        {
            if (is_user_logged_in()) {
                return '<p>Ya estás logueado. <a href="#">Ver tus reportes</a></p>';
            }

            ob_start();
?>
            <div class="container__atquimicos__report">
                <form action="<?php echo esc_url(wp_login_url()); ?>" method="post" class="userForm">
                    <label for="username">Usuario o Email</label>
                    <input type="text" name="log" id="username" required>

                    <label for="password">Contraseña</label>
                    <input type="password" name="pwd" id="password" required>

                    <button class="registerButton" type="submit">Iniciar Sesión</button>
                </form>
            </div>
<?php
            return ob_get_clean();
        }

        public function redirect_after_login($user_login, $user)
        {
            $redirect_url = $this->get_user_reports_url($user->ID);
            wp_safe_redirect($redirect_url);
            exit;
        }

        private function get_user_reports_url($user_id = null)
        {
            $user_id = $user_id ? $user_id : get_current_user_id();
            $reportes_slug = 'reportes'; // Asegúrate de que el slug de la página es 'reportes'
            return add_query_arg('user_id', $user_id, home_url("/{$reportes_slug}/"));
        }

        public function load_custom_template($content)
        {
            if (is_page('reportes')) {

                ob_start();
                require_once ATQUIMICOS_REPORTS_PATH . 'templates/page-reportes.php';

                return ob_get_clean();
            }

            return $content; // Retorna el template original si no es el del CPT
        }
    }
}
