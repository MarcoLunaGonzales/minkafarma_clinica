# Guía para Evitar Errores de Mixed Content en HTTPS
## Proyecto: Minka Farma Clínica

---

## ✅ Cambios Realizados

### 1. **CDN de jQuery actualizados a HTTPS**
Se actualizaron los siguientes archivos para cargar jQuery desde HTTPS:
- indexVentas.php
- indexVentasAlmacen.php
- indexSoloCaja.php
- indexSecond.php
- indexGerencia.php
- indexIngresos.php
- indexCotizaciones.php
- indexConta.php
- indexAlmacenRegPE.php
- indexCaja.php
- indexAlmacenReg.php
- ofertas/verDescuento.php

**Cambio:** `http://code.jquery.com` → `https://code.jquery.com`

### 2. **CDN de Font Awesome actualizados a HTTPS**
**Cambio:** `http://maxcdn.bootstrapcdn.com` → `https://maxcdn.bootstrapcdn.com`

### 3. **URLs externas actualizadas**
- `enviar_correo/php/PHPMailer/email_template_bk.html`: Logo de farmaciasbolivia.com.bo
- `dFacturaElectronicaAll.php`: Enlace a sitio web

### 4. **Archivo de ayuda creado: `helpers_https.php`**
Funciones útiles para manejar HTTPS dinámicamente en PHP.

---

## 🔧 Configuración del Servidor

### En el archivo `.htaccess` (crear en la raíz del proyecto):

```apache
# Forzar HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Headers de seguridad adicionales
<IfModule mod_headers.c>
    # Forzar HTTPS en subdominios y dominios relacionados
    Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"
    
    # Prevenir que el navegador interprete archivos de forma incorrecta
    Header set X-Content-Type-Options "nosniff"
    
    # Habilitar protección XSS
    Header set X-XSS-Protection "1; mode=block"
    
    # Controlar cómo se puede embeber el sitio en iframes
    Header set X-Frame-Options "SAMEORIGIN"
</IfModule>
```

### En PHP (alternativa o complemento al .htaccess):

Agregar al inicio de archivos principales (después de `<?php`):

```php
// Incluir el archivo de ayuda
require_once 'helpers_https.php';

// Forzar redirección a HTTPS
forceHttps();
```

---

## 📋 Checklist Pre-Producción

### Antes de subir a hosting.com:

- [ ] **Verificar certificado SSL**: Asegurarse de que hosting.com tiene instalado un certificado SSL válido
- [ ] **Probar el sitio en local con HTTPS**: Si es posible, configurar HTTPS en el entorno local
- [ ] **Revisar rutas absolutas**: Buscar en el código cualquier URL hardcodeada con `http://`
- [ ] **Actualizar archivo .environment**: Verificar que DATABASE_HOST y otras URLs usen el dominio correcto
- [ ] **Configurar CORS**: Si hay llamadas AJAX entre subdominios, configurar headers CORS apropiados

### Después de subir:

- [ ] **Probar todas las páginas**: Verificar que no aparezcan warnings de mixed content en la consola
- [ ] **Verificar formularios**: Asegurarse de que todos los formularios funcionen correctamente
- [ ] **Probar subida de archivos**: Verificar que imágenes y archivos se carguen correctamente
- [ ] **Revisar API/AJAX**: Confirmar que todas las llamadas AJAX funcionen
- [ ] **Verificar llamadas externas**: APIs de pago, servicios SIAT, etc.

---

## 🔍 Cómo Detectar Errores de Mixed Content

### En el navegador:

1. Abrir **DevTools** (F12)
2. Ir a la pestaña **Console**
3. Buscar mensajes que digan "Mixed Content" o "blocked loading"
4. Buscar warnings sobre recursos cargados por HTTP en página HTTPS

### Tipos de errores comunes:

#### Error activo (bloquea el recurso):
```
Mixed Content: The page at 'https://ejemplo.com' was loaded over HTTPS, 
but requested an insecure script 'http://ejemplo.com/script.js'. 
This request has been blocked; the content must be served over HTTPS.
```

#### Warning pasivo (permite el recurso pero advierte):
```
Mixed Content: The page at 'https://ejemplo.com' was loaded over HTTPS, 
but requested an insecure image 'http://ejemplo.com/image.jpg'. 
This content should also be served over HTTPS.
```

---

## 🛠️ Soluciones a Problemas Comunes

### 1. **Imágenes externas que no funcionan**
Si alguna imagen externa no carga después del cambio:
- Verificar que el servidor externo soporte HTTPS
- Si no soporta HTTPS, descargar la imagen y alojarla localmente

### 2. **APIs externas**
Para APIs de terceros (SIAT, pasarelas de pago):
- Verificar que sus endpoints usen HTTPS
- Actualizar URLs en archivos de configuración

### 3. **Recursos en subdominios**
Si usas subdominios (ej: api.minkafarma.com):
- Asegurarse de que todos tengan certificado SSL
- Configurar CORS apropiadamente

### 4. **CDNs bloqueados**
Si hosting.com bloquea CDNs externos:
- Descargar jQuery y Font Awesome localmente
- Colocar en carpeta `lib/` del proyecto
- Actualizar rutas en archivos HTML/PHP

---

## 📝 Código de Ejemplo

### Uso de helpers_https.php:

```php
<?php
require_once 'helpers_https.php';

// Forzar HTTPS
forceHttps();

// Generar URL segura
$logoUrl = secureUrl('images/logo.png');
echo '<img src="' . $logoUrl . '">';

// Verificar protocolo actual
$protocol = getProtocol();
echo "Sitio corriendo en: " . $protocol;

// URL base completa
$baseUrl = getBaseUrl();
echo "URL base: " . $baseUrl;
?>
```

### En JavaScript (para AJAX):

```javascript
// Usar rutas relativas (recomendado)
$.ajax({
    url: '/ajax_endpoint.php',  // Sin protocolo, se usa el actual
    method: 'POST',
    data: { ... }
});

// O construir URL dinámicamente
var protocol = window.location.protocol;  // 'https:' o 'http:'
var host = window.location.host;          // 'www.ejemplo.com'
var url = protocol + '//' + host + '/api/endpoint';
```

---

## ⚠️ Notas Importantes

1. **Archivos de prueba/documentación**: Los archivos en carpetas como `doc/`, `tutorial/`, `fpdf186/` contienen ejemplos y documentación. No es crítico cambiarlos pero tampoco afecta hacerlo.

2. **Archivos XML**: El archivo `factura.xml` contiene datos de ejemplo. Los atributos `xmlns:xsi` son parte del esquema XML y no causan mixed content.

3. **Archivos minificados**: Librerías como jQuery minificadas (`jquery-*.min.js`) contienen código comprimido. Las referencias a "ajax" dentro son parte del código de la librería y no URLs externas.

4. **Conexión a base de datos**: La conexión a MySQL (localhost o dominio) no se ve afectada por HTTPS, pero asegúrate de que las credenciales en `.environment` sean correctas para producción.

---

## 🚀 Pasos Finales en Hosting.com

1. **Subir archivos** vía FTP/SFTP
2. **Importar base de datos**
3. **Actualizar .environment** con credenciales de producción
4. **Crear/editar .htaccess** con las reglas de redirección
5. **Verificar permisos** de carpetas (uploads, cache, etc.)
6. **Probar el sitio** completamente
7. **Monitorear logs** de errores del servidor

---

## 📞 Soporte Adicional

Si encuentras problemas específicos de mixed content después de la migración:

1. Revisa la consola del navegador (F12)
2. Identifica el recurso problemático
3. Busca en el código dónde se referencia
4. Actualiza a HTTPS o ruta relativa
5. Si es un recurso externo que no soporta HTTPS, considera alternativas

---

**Fecha de creación:** 18 de enero de 2026
**Última actualización:** 18 de enero de 2026
