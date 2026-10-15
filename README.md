# Biblioteca

## Instalación local con XAMPP

1. Instala e inicia XAMPP. En el panel de control, enciende **Apache** y **MySQL**.
2. Clona este repositorio dentro de la carpeta `htdocs` de XAMPP. En Windows, la ruta del proyecto debe quedar así:

   ```text
   C:\xampp\htdocs\biblioteca
   ```

   Por ejemplo, desde una terminal:

   ```bash
   cd C:\xampp\htdocs
   git clone https://github.com/jhoan9/biblioteca.git
   ```

3. Abre phpMyAdmin en [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
4. Crea una base de datos llamada `biblioteca`.
5. Selecciona la base de datos `biblioteca`, abre la pestaña **Importar** y elige el archivo SQL del proyecto:

   ```text
   C:\xampp\htdocs\biblioteca\app\sql\biblioteca.sql
   ```

6. Pulsa **Importar** o **Continuar** para ejecutar el archivo.

Con Apache y MySQL encendidos, abre el proyecto desde [http://localhost/biblioteca/](http://localhost/biblioteca/). El archivo `.htaccess` dirige las solicitudes a `public/`; desde allí, las rutas se despachan en el formato `controlador/metodo/parametro`.

Para consultar el catálogo, visita [http://localhost/biblioteca/libros/index](http://localhost/biblioteca/libros/index) o selecciona **Ver catálogo de libros** en la página inicial.

Desde el catálogo puedes registrar libros indicando nombre, título, autor e ISBN. El ISBN debe ser único.
