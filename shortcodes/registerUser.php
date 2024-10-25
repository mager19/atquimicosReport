<?php

if (!class_exists('RegisterUser')) {
    class RegisterUser
    {
        public function __construct()
        {
            add_shortcode('atquimicos_register_user', array($this, 'custom_user_registration_form'));
        }

        function custom_user_registration_form()
        {
            if ($_POST['custom_user_registration'] == '1') {
                // Datos del formulario
                $username = sanitize_text_field($_POST['username']);
                $email = sanitize_email($_POST['email']);
                $role = sanitize_text_field($_POST['role']); // Nuevo campo para el rol

                $errors = [];
                if (empty($username) || empty($email) || empty($role)) {
                    $errors[] = "Todos los campos son obligatorios.";
                }
                if (!is_email($email)) {
                    $errors[] = "Correo electrónico inválido.";
                }
                if (username_exists($username) || email_exists($email)) {
                    $errors[] = "El nombre de usuario o el correo ya existen.";
                }
                if (!in_array($role, ['cliente', 'tecnico'])) {
                    $errors[] = "El rol seleccionado no es válido.";
                }

                if (empty($errors)) {
                    // Generar una contraseña aleatoria
                    $password = wp_generate_password(12, false);

                    // Crear el usuario
                    $user_id = wp_create_user($username, $password, $email);
                    if (is_wp_error($user_id)) {
                        $errors[] = $user_id->get_error_message();
                    } else {
                        // Asignar el rol seleccionado
                        $user = new WP_User($user_id);
                        $user->set_role($role);

                        // Preparar y enviar el correo electrónico al usuario
                        $subject = "Bienvenido al portal clientes ATQuimicos";
                        $message = "Hola $username,\n\nGracias por registrarte. Aquí están tus datos de acceso:\n\n";
                        $message .= "Nombre de usuario: $username\n";
                        $message .= "Contraseña: $password\n\n";
                        $message .= "Puedes iniciar sesión aquí: " . wp_login_url() . "\n\nSaludos,\nEl Equipo";

                        wp_mail($email, $subject, $message);

                        echo "<p>¡Registro exitoso! Revisa tu correo electrónico para los datos de inicio de sesión.</p>";
                    }
                } else {
                    foreach ($errors as $error) {
                        echo "<p style='color:red;'>$error</p>";
                    }
                }
            }

            ob_start();
?>
            <div class="container__atquimicos__report">
                <p>Los datos de acceso serán enviados al usuario directamente a su email</p>
                <form method="post" class="userForm">
                    <label for="username">Nombre de usuario</label>
                    <input type="text" name="username" required>

                    <label for="email">Correo electrónico</label>
                    <input type="email" name="email" required>

                    <label for="role">Rol de usuario</label>
                    <select name="role" required>
                        <option value="">Selecciona un rol</option>
                        <option value="cliente">Cliente</option>
                        <option value="tecnico">Técnico</option>
                    </select>

                    <input type="hidden" name="custom_user_registration" value="1">
                    <button class="registerButton" type="submit">Registrar Usuario</button>
                </form>
            </div>
<?php
            return ob_get_clean();
        }
    }
}
