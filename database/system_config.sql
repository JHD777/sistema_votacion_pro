CREATE TABLE IF NOT EXISTS `system_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `config_key` varchar(100) NOT NULL,
  `config_value` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `config_key` (`config_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar configuraciones por defecto
INSERT INTO `system_config` (`config_key`, `config_value`) VALUES
('color_primario', '#0d6efd'),
('color_secundario', '#6c757d'),
('color_exito', '#198754'),
('color_peligro', '#dc3545'),
('color_advertencia', '#ffc107'),
('color_info', '#0dcaf0'),
('color_fondo', '#f8f9fa'),
('titulo_sistema', 'Sistema de Votación'),
('subtitulo_sistema', 'Vota de manera segura y transparente'),
('mensaje_bienvenida', 'Bienvenido al sistema de votación'),
('footer_texto', '© 2023 Sistema de Votación');