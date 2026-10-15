<?php
$baseUrl = rtrim(dirname(dirname($_SERVER["SCRIPT_NAME"])), "/\\");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca</title>
</head>
<body>
    <main>
        <h1>Bienvenido al sistema de Biblioteca</h1>
        <p>Consulta los libros registrados en la colección.</p>
        <p><a href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, "UTF-8") ?>/libros/index">Ver catálogo de libros</a></p>
    </main>
</body>
</html>