<?php
// app.php

// Ruta al archivo index.html
$file = 'index.html';

// Verifica que el archivo exista
if (file_exists($file)) {
    // Lee y muestra el contenido del archivo
    echo file_get_contents($file);
} else {
    // Mensaje si no se encuentra el archivo
    echo "Error: No se encontró el archivo index.html";
}
?>
