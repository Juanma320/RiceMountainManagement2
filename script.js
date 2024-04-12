// scripts.js

function mostrarModalAgregarProducto() {
    document.getElementById('agregarProductoModal').style.display = 'block';
}

function cerrarModalAgregarProducto() {
    document.getElementById('agregarProductoModal').style.display = 'none';
}

function cargarProductosDisponibles() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var selectProductos = document.getElementById('productosDisponibles');
            selectProductos.innerHTML = this.responseText;
        }
    };
    xhttp.open('GET', 'getproductosnodisponibles.php?proveedorID=' + obtenerParametroURL('proveedorID'), true);
    xhttp.send();
}

function agregarProducto() {
    var form = document.getElementById('formAgregarProducto');
    var formData = new FormData(form);

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            // Recargar la página para mostrar el producto recién agregado
            location.reload();
        }
    };
    xhttp.open('POST', 'agregarproducto.php', true);
    xhttp.send(formData);
}

function retirarProducto(productoID, proveedorID) {
    var confirmacion = confirm("¿Estás seguro de retirar este producto?");
    if (confirmacion) {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                location.reload();
            }
        };
        xhttp.open('GET', 'retirarproducto.php?productoID=' + productoID + '&proveedorID=' + proveedorID, true);
        xhttp.send();
    }
}




// Función para obtener un parámetro de la URL por nombre
function obtenerParametroURL(nombre) {
    var urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(nombre);
}
function confirmarCambiarEstado(proveedorID) {
    var confirmacion = confirm("¿Estás seguro de cambiar el estado de este proveedor?");
    if (confirmacion) {
        window.location.href = `cambiar_estado_proveedor.php?proveedor_id=${proveedorID}`;
    }
}


