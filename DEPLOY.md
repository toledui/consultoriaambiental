# Manual de Deploy — Consultoría Ambiental

## Requisitos del servidor

- **PHP** 8.0 o superior
- **MySQL** 5.7+ / MariaDB 10.3+
- **Apache** con `mod_rewrite` habilitado (o nginx)
- **Extensiones PHP**: `pdo_mysql`, `mbstring`, `gd` (para upload de imágenes)
- **Node.js** 18+ y **npm** (para compilar assets localmente antes del deploy)

---

## 1. Preparación local (compilar assets)

Antes de subir archivos al servidor, compila los assets localmente:

```bash
# 1. Instalar dependencias (solo la primera vez)
npm install

# 2. Compilar Tailwind CSS (genera public/css/tailwind.css)
npm run build:css

# 3. (Opcional) Modo watch para desarrollo — recompila automáticamente
npm run watch:css
```

> **Nota**: Los assets compilados (`public/css/tailwind.css`, `public/js/gsap.min.js`, `public/js/ScrollTrigger.min.js`) deben subirse al servidor. No es necesario tener Node.js en el servidor de producción.

---

## 2. Subir archivos al servidor

Sube todo el contenido del proyecto a la raíz de tu dominio (ej: `/public_html/`).

La estructura debe quedar así:

```
/
├── index.php              ← Front controller (punto de entrada)
├── .htaccess              ← Reescribe URLs a index.php
├── package.json           ← Dependencias (solo referencia, no se ejecuta en prod)
├── config/
│   ├── database.php       ← Configuración de BD
│   ├── database.local.example.php ← Plantilla para credenciales del servidor
│   └── app.php            ← Constantes de la app
├── app/                   ← Código de la aplicación
├── migrations/            ← Migraciones SQL
├── migrate                ← Ejecutor de migraciones (CLI: php migrate)
├── public/
│   ├── images/            ← Imágenes subidas
│   ├── js/
│   │   ├── gsap.min.js          ← GSAP (local)
│   │   └── ScrollTrigger.min.js ← ScrollTrigger (local)
│   └── css/
│       ├── app.css              ← Fuente Tailwind (no necesaria en prod)
│       └── tailwind.css         ← Tailwind compilado (¡importante!)
├── storage/
│   └── logs/
└── setup.php              ← Alias CLI de compatibilidad para migrate
```

> **Importante**: El `index.php` debe estar en la raíz del dominio, NO dentro de `public/`.
>
> **Importante**: No subas `node_modules/` al servidor. Solo se necesita el CSS/JS compilado en `public/`.

---

## 3. Configurar la base de datos

### 3.1 Crear la base de datos

Accede a tu panel de control (cPanel, phpMyAdmin, etc.) y crea una base de datos vacía.

### 3.2 Configurar credenciales

No uses el usuario `root` de MySQL para la aplicación: en muchos VPS solo permite iniciar sesión desde la cuenta administradora del sistema. Si todavía no tienes un usuario MySQL para el sitio, créalo desde la consola administrativa de MySQL (`sudo mysql`; en MariaDB puede ser `sudo mariadb`). Sustituye `nombre_bd` por la base real creada en el paso anterior:

```sql
CREATE USER 'consultoria_app'@'localhost' IDENTIFIED BY 'una_contraseña_larga_y_unica';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, INDEX, REFERENCES
ON `nombre_bd`.* TO 'consultoria_app'@'localhost';
```

Si ya existe un usuario con acceso a esa base, úsalo sin crearlo de nuevo. En el servidor, copia [`config/database.local.example.php`](config/database.local.example.php), completa el nombre de la base, usuario y contraseña reales y permite que PHP lea el archivo:

```bash
cp config/database.local.example.php config/database.local.php
nano config/database.local.php
sudo chgrp www-data config/database.local.php
chmod 640 config/database.local.php
```

`config/database.local.php` está ignorado por Git y sirve tanto para la web como para `php migrate`, por lo que no generará conflictos en futuros `git pull`. Ajusta `www-data` si PHP usa otro grupo. También puedes definir `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER` y `DB_PASS` como variables de entorno; estas tienen prioridad sobre el archivo local.

### 3.3 Ejecutar migraciones

```bash
php migrate
```

Ejecuta el comando desde la raíz del proyecto **en cada despliegue**, tanto en instalaciones nuevas como existentes. Usa la base indicada por `DB_NAME` o `config/database.php`; la base debe existir antes de ejecutar el comando.

En la primera ejecución, `migrate` crea `schema_migrations` y aplica los archivos `.sql` por nombre. En una instalación existente sin historial, comprueba las tablas, columnas, índices y restricciones ya presentes, y completa lo que falte. Los datos iniciales solo se insertan cuando no existen; los valores configurados por el administrador se conservan. Las migraciones 017 y 018 del importador también se aplican por este comando, sin pasos manuales en phpMyAdmin.

Después, cada archivo aplicado queda registrado con su checksum y las siguientes ejecuciones lo omiten. Si una migración falla, el comando termina con error; corrige la causa y vuelve a ejecutar `php migrate`. El ejecutor retomará las operaciones pendientes de las migraciones actuales. Haz un respaldo antes de desplegar cambios de esquema en producción.

Para futuras migraciones, agrega un nuevo archivo SQL con prefijo numérico creciente, por ejemplo `migrations/019_nuevo_cambio.sql`. No edites archivos ya registrados: el ejecutor detecta cambios por checksum y se detiene. `php setup.php` sigue disponible como alias de compatibilidad, pero el comando recomendado es `php migrate`.

---

## 4. Configurar Apache (.htaccess)

El archivo [`index.php`](index.php) es el front controller. El [`.htaccess`](.htaccess) ya está incluido en el proyecto y reescribe todas las peticiones a `index.php`.

**Verifica que [`mod_rewrite`](https://httpd.apache.org/docs/2.4/mod/mod_rewrite.html) esté habilitado** en Apache. Si usas cPanel, generalmente ya lo está.

Si tu servidor usa **nginx**, agrega esta regla en el bloque `server`:

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ^~ /config/ {
    deny all;
}

location ~ \.php$ {
    include fastcgi_params;
    fastcgi_pass unix:/var/run/php/php8.x-fpm.sock;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
}
```

---

## 5. Configurar la aplicación

Define `BASE_URL` con la URL pública del sitio, preferentemente como variable de entorno:

```bash
BASE_URL=https://tudominio.com
APP_DEBUG=false
```

Si `BASE_URL` no está definida, [`config/app.php`](config/app.php) la detecta a partir de la petición. Mantén `APP_DEBUG=false` en producción.

---

## 6. Permisos de archivos

Asegúrate de que el servidor web tenga permisos de escritura en:

| Ruta | Permiso | Propósito |
|------|---------|-----------|
| `public/images/` | `755` o `775` | Upload de logos e imágenes |
| `public/uploads/` | `755` o `775` | Upload de medios (blog, etc.) |
| `storage/logs/` | `755` o `775` | Logs de errores |

```bash
chmod -R 755 public/images
chmod -R 755 public/uploads
chmod -R 755 storage/logs
```

---

## 7. Verificar la instalación

1. Abre `https://tudominio.com/` — Deberías ver el sitio funcionando.
2. Abre `https://tudominio.com/admin/login` — Deberías ver el formulario de login.
3. Solo en una instalación nueva, inicia sesión con:
   - **Usuario**: `admin`
   - **Contraseña**: `admin123`

> **⚠️ Cambia la contraseña del admin inmediatamente después del primer inicio de sesión.**

---

## 8. Resumen de comandos

```bash
# === LOCAL (antes del deploy) ===

# 1. Instalar dependencias (solo la primera vez)
npm install

# 2. Compilar Tailwind CSS
npm run build:css

# 3. (Opcional) Modo desarrollo con auto-recompila
npm run watch:css

# === SERVIDOR (producción) ===

# 4. Subir archivos (vía FTP, Git, etc.) — sin node_modules/

# 5. Configurar base de datos (config/database.local.php)

# 6. Ejecutar migraciones
php migrate

# 7. Configurar permisos
chmod -R 755 public/images
chmod -R 755 public/uploads
chmod -R 755 storage/logs

# 8. ¡Listo! El sitio ya debería funcionar.
```

---

## Notas importantes

- **Node.js no es necesario en el servidor de producción**. Los assets se compilan localmente y se suben ya compilados.
- **GSAP y ScrollTrigger** están instalados localmente via npm y copiados a `public/js/`. No dependen de CDN externo.
- **Tailwind CSS** se compila a un archivo estático `public/css/tailwind.css`. No se usa el CDN de Tailwind en producción.
- Después de modificar clases Tailwind en las vistas, ejecuta `npm run build:css` para regenerar el CSS.
- **Caching**: El archivo [`.htaccess`](.htaccess) incluye reglas de cacheo para assets estáticos (1 año) y compresión Gzip. Verifica que los módulos `mod_expires`, `mod_deflate` y `mod_headers` estén habilitados en Apache.
- **GSAP se carga bajo demanda**: Solo se descarga si hay elementos con clase `.gsap-reveal` o `.gsap-stagger` en la página. Esto reduce JS no utilizado en páginas sin animaciones.

## SEO - Configuración implementada

La home page incluye las siguientes optimizaciones SEO:

### Etiquetas técnicas
- **Title SEO**: Se usa la variable `$seoTitle` en el controlador para títulos descriptivos. Si no existe, usa `$title | APP_NAME` como fallback.
- **Meta description**: Texto optimizado con keywords principales: consultoría ambiental, MIA, residuos, emisiones, COA, LAU, PROEPA/PROFEPA.
- **Canonical URL**: `<link rel="canonical">` generado dinámicamente desde `BASE_URL` + `REQUEST_URI`.
- **Open Graph**: `og:title`, `og:description`, `og:type`, `og:url`, `og:image`, `og:locale` para redes sociales.
- **Twitter Card**: `summary_large_image` con título y descripción.

### Datos estructurados (Schema.org)
- **ProfessionalService** con nombre, descripción, URL, teléfono, email, área de servicio y tipos de servicio.
- Ubicado en [`app/views/layouts/main.php`](app/views/layouts/main.php) dentro del `<head>`.

### Contenido de la home
- **H1**: "Consultoría Ambiental para Empresas e Industrias en México" — incluye keyword principal.
- **Subtítulo hero**: Describe servicios específicos (MIA, COA, LAU, PROEPA/PROFEPA).
- **CTAs**: Enlaces a `/servicios` y `/contacto` (sin `href="#"`).
- **Sección "Por qué elegirnos"**: Incluye datos de autoridad (+10 años, +60 MIA, +140 inspecciones).
- **Servicios**: Descripciones con intención comercial y keywords específicas.

### Personalización por página
Para agregar SEO personalizado a cualquier página, pasa estas variables al renderizar la vista:

```php
$this->view('ruta/vista', [
    'title'       => 'Título corto',           // Fallback para <title>
    'seoTitle'    => 'Título SEO optimizado',   // Opcional: reemplaza <title> y OG
    'metaDesc'    => 'Descripción optimizada',  // Opcional: reemplaza meta description
    'currentPage' => 'nombre',                  // Para navegación activa
]);
```

---

## Solución de problemas

| Problema | Posible causa | Solución |
|----------|---------------|----------|
| Error 500 | Permisos incorrectos | Verifica permisos de archivos y carpetas |
| Error 404 en todas las rutas | `mod_rewrite` deshabilitado | Habilita `mod_rewrite` en Apache |
| "Database connection failed" | Credenciales incorrectas | Revisa `config/database.php` |
| Imágenes no se ven | Ruta incorrecta | Verifica que las imágenes estén en `public/images/` |
| Login no funciona | Usuario o contraseña incorrectos | Revisa el usuario en la base de datos y restablece su contraseña mediante un procedimiento administrativo; `php migrate` no cambia contraseñas existentes |
| Estilos no se ven | `tailwind.css` no compilado | Ejecuta `npm run build:css` localmente y vuelve a subir |
| Animaciones no funcionan | GSAP no encontrado | Verifica que `public/js/gsap.min.js` y `ScrollTrigger.min.js` existan |
| Caching no funciona | Módulos Apache deshabilitados | Habilita `mod_expires`, `mod_deflate`, `mod_headers` en Apache |
| Compresión Gzip no activa | `mod_deflate` deshabilitado | Habilita `mod_deflate` en Apache o configura Brotli si está disponible |
