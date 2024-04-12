<?php
// logout.php
session_start();

// Destruir la sesión
session_destroy();

// Redireccionar al inicio de sesión o a la página principal
header("Location: ../login.php");
exit();
?>
