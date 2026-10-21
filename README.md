# Biblioteca

Sistema web de gestión de una biblioteca desarrollado en PHP y MySQL.

## Instalación local con XAMPP

### Requisitos
- XAMPP con Apache y MySQL.
- Git.
- PHP y MySQL compatibles con el proyecto.

### Pasos de instalación

1. Clona el repositorio o descarga el proyecto en `C:\xampp\htdocs\biblioteca`.

   Desde una terminal, puedes ejecutar:

   ```bash
   cd C:\xampp\htdocs
   git clone https://github.com/jhoan9/biblioteca.git
   ```

2. Inicia Apache y MySQL desde el panel de control de XAMPP.
3. Abre phpMyAdmin en http://localhost/phpmyadmin.
4. Crea una base de datos llamada `biblioteca`.
5. Selecciona la base de datos, abre la pestaña **Importar** y elige el archivo:

   `C:\xampp\htdocs\biblioteca\app\sql\biblioteca.sql`

6. Ejecuta la importación.
7. Verifica que la conexión a MySQL esté configurada con los datos de tu instalación local.

### Configuración de MySQL

**Prueba realizada:** se verificó que la conexión con la base de datos `biblioteca` funciona correctamente. También se añadieron dos libros a la biblioteca.

Con Apache y MySQL encendidos, abre:

http://localhost/biblioteca/

El archivo `.htaccess` dirige las solicitudes a `public/`, desde donde se gestionan las rutas de la aplicación.

## Funcionalidades

- **Panel:** resumen de libros, disponibilidad, préstamos activos, usuarios y actividad reciente.
- **Catálogo:** búsqueda por título, autor o ISBN; filtros por disponibilidad; registro, edición y eliminación de libros. El ISBN debe ser único.
- **Usuarios:** registro, edición, búsqueda y eliminación de lectores.
- **Préstamos:** asignación de libros disponibles, devoluciones e historial. Los préstamos vencidos se identifican automáticamente y se destacan en el panel.
- **Interfaz:** diseño adaptable, validación de formularios y protección CSRF.

## Datos de ejemplo (opcional)

Para probar los préstamos sin crear usuarios manualmente, importa el archivo:

`C:\xampp\htdocs\biblioteca\app\sql\datos_demo.sql`

Este archivo crea tres usuarios de ejemplo y no modifica los libros existentes.

## Tecnologías

- PHP
- MySQL
- HTML y CSS
- JavaScript
- Apache y XAMPP
