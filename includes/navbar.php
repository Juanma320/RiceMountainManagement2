<style>
    .gradient-custom {
        background: radial-gradient(circle at 0% -29.6%, rgb(144, 17, 105) 0%, rgb(51, 0, 131) 100.2%);
    }

    .text-shadowed {
        color: #F9F6EE;
        text-shadow: 1px 1px 1px #000;
    }
</style>

<nav class="navbar navbar-expand-lg navbar-light bg-body-tertiary gradient-custom">

    <div class="container-fluid">
        <div class="collapse navbar-collapse" id="navbarNav">

            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link disabled" href="#" style="color: #F9F6EE; text-shadow: 1px 1px 1px #000;">
                        <?php
                        switch ($row['RolID']) {
                            case 1: // Administrador
                                echo '<li class="nav-item"><a class="navbar-brand" href="indexadmin.php" style="color: #F9F6EE">RiceMountainDB</a></li>';
                                break;
                            case 2: // Coordinador
                                echo '<li class="nav-item"><a class="navbar-brand" href="indexcoordinador.php" style="color: #F9F6EE">RiceMountainDB</a></li>';
                                break;
                            case 3: // Financiero
                                echo '<li class="nav-item"><a class="navbar-brand" href="indexfinanciero.php" style="color: #F9F6EE">RiceMountainDB</a></li>';
                                break;
                        }
                        ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" style="color: #F9F6EE; text-shadow: 1px 1px 1px #000;">
                        <?php echo 'Sesión iniciada como: ', $row['NombreUsuario']; ?>
                    </a>
                </li>
            </ul>

            <div class="user-info" style="color: #F9F6EE">
                <?php
                // Mostrar el nombre del rol según el RolID del usuario
                $rolNombre = "";
                switch ($row['RolID']) {
                    case 1:
                        $rolNombre = "Rol: Administrador";
                        break;
                    case 2:
                        $rolNombre = "Rol: Coordinador";
                        break;
                    case 3:
                        $rolNombre = "Rol: Financiero";
                        break;
                }
                echo $rolNombre ?>
            </div>

            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="perfil.php"
                        style="color: #F9F6EE; text-shadow: 1px 1px 1px #000;">Perfil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="includes/logout.php"
                        style="color: #F9F6EE; text-shadow: 1px 1px 1px #000;">Cerrar Sesión</a>
                </li>
            </ul>
        </div>
    </div>
</nav>