<!-- navbar.php -->
<nav>
    <div class="user-info">
        <p><?php echo $row['NombreUsuario']; ?></p>
        <?php
            // Mostrar el nombre del rol según el RolID del usuario
            $rolNombre = "";
            switch ($row['RolID']) {
                case 1:
                    $rolNombre = "Admin";
                    break;
                case 2:
                    $rolNombre = "Coordinador";
                    break;
                case 3:
                    $rolNombre = "Financiero";
                    break;
                // Agregar más casos según sea necesario
            }
            echo '<p>' . $rolNombre . '</p>';
        ?>
    </div>
    <ul>
        <?php
            // Mostrar enlaces según el RolID del usuario
            switch ($row['RolID']) {
                case 1:
                    echo '<li><a href="indexadmin.php">Inicio (Admin)</a></li>';
                    break;
                case 2:
                    echo '<li><a href="indexcoordinador.php">Inicio (Coordinador)</a></li>';
                    break;
                case 3:
                    echo '<li><a href="indexfinanciero.php">Inicio (Financiero)</a></li>';
                    break;
                // Agregar más casos según sea necesario
            }

            // Agregar enlaces comunes a todos los roles
            echo '<li><a href="perfil.php">Perfil</a></li>';
            echo '<li><a href="includes/logout.php">Cerrar Sesión</a></li>';
            // Agregar el botón de retroceso
            echo '<li><a href="javascript:history.back()">Retroceder</a></li>';
        ?>
    </ul>
</nav>
