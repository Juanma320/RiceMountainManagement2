<?php
include('includes/includes.php');
include('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);
include('includes/navbar.php');

// Procesar el formulario de nueva presentación
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['nombrePresentacion']) && !empty($_POST['nombrePresentacion']) && isset($_POST['medida']) && !empty($_POST['medida'])) {
        $nombrePresentacion = $_POST['nombrePresentacion'];
        $medida = $_POST['medida'];

        // Insertar la nueva presentación en la base de datos
        $queryInsertPresentacion = "INSERT INTO presentaciones (NombrePresentacion, Medida) VALUES ('$nombrePresentacion', '$medida')";
        if (mysqli_query($conexion, $queryInsertPresentacion)) {
            echo "<p>Presentación agregada correctamente.</p>";
        } else {
            echo "<p>Error al agregar la presentación.</p>";
        }
    } else {
        echo "<p>Por favor, complete todos los campos.</p>";
    }
}

// Obtener presentaciones
$queryPresentaciones = "SELECT * FROM presentaciones";
$resultPresentaciones = mysqli_query($conexion, $queryPresentaciones);
?>

<h1>Gestión de Presentaciones</h1>

<h2>Presentaciones Existentes</h2>
<table border="1">
    <tr>
        <th>Nombre Presentación</th>
        <th>Medida</th>
        <th>Acciones</th>
    </tr>
    <?php
    while ($rowPresentacion = mysqli_fetch_assoc($resultPresentaciones)) {
        echo "<tr>";
        echo "<td>{$rowPresentacion['NombrePresentacion']}</td>";
        echo "<td>{$rowPresentacion['Medida']}</td>";
        // Verificar si la presentación está siendo usada
        $queryUsada = "SELECT COUNT(*) AS total FROM productos WHERE PresentacionID = {$rowPresentacion['PresentacionID']}";
        $resultUsada = mysqli_query($conexion, $queryUsada);
        $rowUsada = mysqli_fetch_assoc($resultUsada);
        if ($rowUsada['total'] > 0) {
            echo "<td>La presentación aún está siendo usada</td>";
        } else {
            echo "<td><a href='eliminar_presentacion.php?presentacionID={$rowPresentacion['PresentacionID']}'>Eliminar</a></td>";
        }
        echo "</tr>";
    }
    ?>
</table>

<h2>Añadir Nueva Presentación</h2>
<form method="post" action="procesar_presentacion.php">
    <label for="nombrePresentacion">Nombre Presentación:</label>
    <input type="text" id="nombrePresentacion" name="nombrePresentacion" required><br>

    <label for="medida">Medida:</label>
    <input type="number" id="medida" name="medida" required>
    <select name="medicion" id="medicion">
        <option value="ml">ml</option>
        <option value="g">g</option>
        <option value="unidad">Unidad</option>
    </select><br>

    <input type="submit" value="Agregar">
</form>

<script>
function agregarMedida() {
    var medidaSelect = document.getElementById('medidaSelect');
    var medidaInput = document.getElementById('medidaInput');
    
    if (medidaSelect.value === 'g') {
        medidaInput.value += ' g';
    } else {
        medidaInput.value = medidaInput.value.replace(' g', '');
    }
}
</script>

