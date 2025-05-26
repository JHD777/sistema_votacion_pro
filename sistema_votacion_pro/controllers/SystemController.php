<?php
// Verificamos si la sesión ya está iniciada para evitar el warning
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/SystemConfig.php';

class SystemController {
    private $configModel;
    
    public function __construct() {
        $this->configModel = new Models\SystemConfig();
    }
    
    // Obtener todas las configuraciones
    public function getAllConfigs() {
        return $this->configModel->getAllConfigs();
    }
    
    // Actualizar configuración
    public function updateConfig($key, $value) {
        return $this->configModel->updateConfig($key, $value);
    }
    
    // Actualizar múltiples configuraciones
    public function updateConfigs($configs) {
        $results = [
            'success' => true,
            'message' => 'Todas las configuraciones se actualizaron correctamente'
        ];
        
        foreach ($configs as $key => $value) {
            $result = $this->configModel->updateConfig($key, $value);
            if (!$result['success']) {
                $results['success'] = false;
                $results['message'] = 'Error al actualizar algunas configuraciones';
                break;
            }
        }
        
        return $results;
    }
    
    // Obtener una configuración específica
    public function getConfig($key, $default = '') {
        return $this->configModel->getConfig($key, $default);
    }
}