<?php
/**
 * Sistema de registro de errores
 * Este archivo permite registrar errores para su posterior análisis
 */

class ErrorLogger {
    private static $logFile = __DIR__ . '/errors.log';
    
    /**
     * Registra un error en el archivo de log
     * 
     * @param string $message Mensaje de error
     * @param string $file Archivo donde ocurrió el error
     * @param int $line Línea donde ocurrió el error
     * @param array $context Contexto adicional (opcional)
     * @return bool True si se registró correctamente
     */
    public static function log($message, $file = '', $line = 0, $context = []) {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] ERROR: {$message}" . PHP_EOL;
        
        if (!empty($file)) {
            $logMessage .= "Archivo: {$file}" . PHP_EOL;
        }
        
        if ($line > 0) {
            $logMessage .= "Línea: {$line}" . PHP_EOL;
        }
        
        if (!empty($context)) {
            $logMessage .= "Contexto: " . print_r($context, true) . PHP_EOL;
        }
        
        $logMessage .= "------------------------------" . PHP_EOL;
        
        return file_put_contents(self::$logFile, $logMessage, FILE_APPEND);
    }
    
    /**
     * Obtiene todos los errores registrados
     * 
     * @return string Contenido del archivo de log
     */
    public static function getErrors() {
        if (file_exists(self::$logFile)) {
            return file_get_contents(self::$logFile);
        }
        return "No hay errores registrados.";
    }
    
    /**
     * Limpia el archivo de log
     * 
     * @return bool True si se limpió correctamente
     */
    public static function clearLog() {
        return file_put_contents(self::$logFile, '');
    }
}
?>