```markdown
# Logística Pro

Aplicación web desarrollada con **CodeIgniter 4** para la gestión de almacén, productos, existencias, pedidos, rutas, entregas, facturas y comunicación interna.

El sistema centraliza el flujo completo desde la creación de un pedido por parte del cliente hasta su preparación, asignación logística, reparto y entrega final.

## Características principales

- Gestión de usuarios por roles.
- Panel separado para cliente, administrador, logística y conductor.
- Gestión de productos, categorías, imágenes y existencias.
- Creación y seguimiento de pedidos.
- Confirmación de pedidos y control de stock.
- Generación de facturas.
- Planificación de rutas de reparto.
- Gestión de entregas e incidencias.
- Mensajería interna relacionada con pedidos.

## Tecnologías utilizadas

- PHP 8.2+
- CodeIgniter 4
- MySQL / MariaDB
- XAMPP
- phpMyAdmin
- HTML5
- CSS3
- JavaScript
- Composer
- Dompdf
- PHPUnit
- Git / GitHub

## Instalación en local

### 1. Clonar o descargar el proyecto

Clonar el repositorio:

```bash
git clone https://github.com/anouaramh97/almacen-logistica-ci4.git
```

O descargar el proyecto en formato ZIP desde GitHub y descomprimirlo dentro de:

```text
C:\xampp\htdocs\almacen-logistica-ci4
```

### 2. Dependencias del proyecto

El proyecto utiliza Composer para gestionar las dependencias de CodeIgniter 4.

Si el proyecto ya incluye la carpeta:

```text
vendor/
```

no es necesario ejecutar ningún comando adicional.

Si la carpeta `vendor/` no está incluida, entrar en la carpeta del proyecto:

```bash
cd C:\xampp\htdocs\almacen-logistica-ci4
```

y ejecutar:

```bash
composer install
```

### 3. Configurar el archivo `.env`

Copiar el archivo `env` y renombrarlo como `.env`.

Configurar la base de datos:

```env
CI_ENVIRONMENT = development

app.baseURL = 'http://localhost/almacen-logistica-ci4/public/'

database.default.hostname = 127.0.0.1
database.default.database = almacen_logistica_ci4
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.port = 3306
```

Si MySQL usa otro puerto, modificar `database.default.port`.

### 4. Crear e importar la base de datos

Abrir phpMyAdmin:

```text
http://localhost/phpmyadmin
```

Crear una base de datos llamada:

```text
almacen_logistica_ci4
```

Importar el archivo:

```text
almacen_logistica_ci4.sql
```

### 5. Ejecutar el proyecto

Iniciar Apache y MySQL desde XAMPP.

Abrir en el navegador:

```text
http://localhost/almacen-logistica-ci4/public/
```

## Instalación en hosting

### 1. Subir archivos al servidor

Subir el proyecto al hosting mediante FTP, administrador de archivos o Git.

En CodeIgniter 4, el punto de entrada público está dentro de:

```text
public/index.php
```

Por seguridad, lo recomendable es que el dominio apunte directamente a la carpeta:

```text
public/
```

### 2. Dependencias en hosting

Si se sube al hosting el proyecto completo incluyendo la carpeta:

```text
vendor/
```

no es necesario ejecutar Composer en el servidor.

Si no se sube la carpeta `vendor/` y el hosting permite Composer, ejecutar:

```bash
composer install --no-dev
```

Si el hosting no permite Composer, ejecutar `composer install` en local y subir después la carpeta `vendor/`.

### 3. Crear la base de datos en el hosting

Desde el panel del hosting, crear una base de datos MySQL/MariaDB.

Después importar el archivo:

```text
almacen_logistica_ci4.sql
```

### 4. Configurar `.env` en el hosting

Editar el archivo `.env` con los datos reales del servidor:

```env
CI_ENVIRONMENT = production

app.baseURL = 'https://tudominio.com/'

database.default.hostname = servidor_mysql
database.default.database = nombre_base_datos
database.default.username = usuario_base_datos
database.default.password = contraseña_base_datos
database.default.DBDriver = MySQLi
database.default.port = 3306
```

### 5. Permisos de carpetas

Asegurar permisos de escritura para:

```text
writable/
```

Esta carpeta se usa para logs, caché y archivos generados por la aplicación.

## Usuarios de prueba

| Perfil | Correo |
|---|---|
| Administrador | admin@almacen.com |
| Logística | logistica@almacen.com |
| Conductor | conductor@almacen.com |
| Cliente | cliente@almacen.com |

## Estructura general

```text
app/
├── Controllers/
├── Models/
├── Views/
├── Filters/
├── Config/
├── Database/

public/
├── index.php

writable/
├── logs/
├── cache/

vendor/
├── dependencias de Composer
```

## Base de datos

La base de datos está formada por entidades relacionadas para gestionar usuarios, catálogo, existencias, pedidos, facturas, rutas, entregas y mensajes.

Tablas principales:

- roles_sistema
- usuarios
- categorias
- productos
- imagenes_producto
- almacenes
- existencias
- pedidos
- detalles_pedido
- facturas
- rutas
- entregas
- conversaciones
- mensajes

## Flujo principal

```text
Cliente crea pedido
→ Administrador revisa y confirma
→ Se descuenta stock
→ Se genera factura
→ Logística crea ruta
→ Conductor realiza entrega
→ Pedido entregado o incidencia registrada
```

## Pruebas

Para ejecutar las pruebas del proyecto:

```bash
vendor\bin\phpunit
```

## Repositorio

```text
https://github.com/anouaramh97/almacen-logistica-ci4
```
```
