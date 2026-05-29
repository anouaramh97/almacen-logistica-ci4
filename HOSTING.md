# Subir este proyecto a hosting `htdocs`

Este proyecto ya esta preparado para funcionar:

- en local como `http://localhost/almacen-logistica-ci4/`
- en hosting cuando el dominio apunta directamente a `htdocs`

## Como subirlo

1. Sube todo el contenido del proyecto dentro de `htdocs`.
2. Asegurate de subir tambien:
   - `app`
   - `vendor`
   - `writable`
   - `uploads`
   - `.env`
   - `index.php`
   - `.htaccess`
3. Importa la base de datos en el hosting.
4. Edita `.env` con los datos reales del hosting:

```ini
CI_ENVIRONMENT = production
app.baseURL = ''
app.forceGlobalSecureRequests = false

database.default.hostname = TU_HOST
database.default.database = TU_BASE
database.default.username = TU_USUARIO
database.default.password = TU_PASSWORD
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = 3306
app.indexPage = ''
```

## Notas

- `app.baseURL = ''` deja que el proyecto detecte solo la URL base.
- Si el hosting usa HTTPS, puedes poner:

```ini
app.forceGlobalSecureRequests = true
```

- La carpeta `writable` debe tener permisos de escritura.
- Si el hosting no trae dependencias, necesitas subir `vendor` ya instalado.
