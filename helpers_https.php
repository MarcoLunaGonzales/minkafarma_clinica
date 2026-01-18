<?php
/**
 * Archivo de ayuda para garantizar URLs seguras en HTTPS
 * 
 * Este archivo contiene funciones útiles para evitar mixed content
 * al migrar el sitio a HTTPS
 */

/**
 * Obtiene el protocolo actual del sitio (http o https)
 * @return string 'https' o 'http'
 */
function getProtocol() {
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        return 'https';
    }
    if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
        return 'https';
    }
    if (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on') {
        return 'https';
    }
    if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) {
        return 'https';
    }
    return 'http';
}

/**
 * Obtiene la URL base del sitio con el protocolo correcto
 * @return string URL base completa (ej: https://www.ejemplo.com)
 */
function getBaseUrl() {
    $protocol = getProtocol();
    $host = $_SERVER['HTTP_HOST'];
    return $protocol . '://' . $host;
}

/**
 * Genera una URL completa con el protocolo correcto
 * @param string $path Ruta relativa (ej: '/images/logo.png')
 * @return string URL completa con protocolo
 */
function secureUrl($path) {
    $baseUrl = getBaseUrl();
    // Eliminar barra inicial si existe para evitar doble barra
    $path = ltrim($path, '/');
    return $baseUrl . '/' . $path;
}

/**
 * Fuerza la redirección a HTTPS si el sitio se accede por HTTP
 * Incluir al inicio de los archivos principales
 */
function forceHttps() {
    if (getProtocol() === 'http') {
        $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        header('Location: ' . $redirect, true, 301);
        exit();
    }
}

/**
 * Verifica si una URL externa usa HTTPS
 * @param string $url URL a verificar
 * @return bool true si usa HTTPS, false si no
 */
function isSecureUrl($url) {
    return strpos($url, 'https://') === 0;
}

/**
 * Convierte una URL HTTP a HTTPS
 * @param string $url URL a convertir
 * @return string URL con HTTPS
 */
function toHttps($url) {
    return str_replace('http://', 'https://', $url);
}
?>
