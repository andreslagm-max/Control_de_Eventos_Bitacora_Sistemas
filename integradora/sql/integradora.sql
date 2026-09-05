-- =====================================================================
-- SysTrace · Control de Eventos y Bitácora de Sistemas
-- Script de base de datos para la Actividad Integradora 3.
-- Autor: Luis Andrés García Mendoza
--
-- Importar con phpMyAdmin o desde consola:
--   mysql -u root < sql/integradora.sql
-- =====================================================================

CREATE DATABASE IF NOT EXISTS integradora
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE integradora;

-- ---------------------------------------------------------------------
-- Tabla principal: eventos ocurridos en los sistemas
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS eventos (
    id                    INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    sistema               VARCHAR(60)   NOT NULL,
    tipo                  ENUM('Incidente','Mantenimiento','Alerta','Cambio','Respaldo') NOT NULL,
    severidad             TINYINT UNSIGNED NOT NULL,
    descripcion           VARCHAR(500)  NOT NULL,
    responsable           VARCHAR(80)   NOT NULL,
    correo_responsable    VARCHAR(120)  NOT NULL,
    tiempo_resolucion_min INT UNSIGNED  NOT NULL DEFAULT 0,
    estado                ENUM('Abierto','En proceso','Resuelto','Cerrado') NOT NULL DEFAULT 'Abierto',
    fecha_evento          DATETIME      NOT NULL,
    creado_en             TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_eventos_sistema (sistema),
    INDEX idx_eventos_estado  (estado),
    INDEX idx_eventos_fecha   (fecha_evento),
    CONSTRAINT chk_eventos_severidad CHECK (severidad BETWEEN 1 AND 5),
    CONSTRAINT chk_eventos_tiempo    CHECK (tiempo_resolucion_min <= 10080)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Bitácora: rastro automático de cada acción realizada en la aplicación
-- ON DELETE SET NULL: si se borra un evento, la bitácora conserva el
-- registro del borrado en lugar de perderlo.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS bitacora (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    accion     ENUM('REGISTRAR','CONSULTAR','BUSCAR','ELIMINAR') NOT NULL,
    evento_id  INT UNSIGNED NULL,
    detalle    VARCHAR(255) NOT NULL,
    ip_origen  VARCHAR(45)  NOT NULL,
    agente     VARCHAR(255) NOT NULL DEFAULT '',
    fecha_hora TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_bitacora_fecha  (fecha_hora),
    INDEX idx_bitacora_accion (accion),
    CONSTRAINT fk_bitacora_evento FOREIGN KEY (evento_id)
        REFERENCES eventos (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Datos de ejemplo
-- ---------------------------------------------------------------------
INSERT INTO eventos (sistema, tipo, severidad, descripcion, responsable, correo_responsable, tiempo_resolucion_min, estado, fecha_evento) VALUES
('Servidor de base de datos', 'Incidente', 5, 'Caída del servicio MySQL por disco lleno en la partición de datos. Se liberó espacio, se rotaron los binlogs y se reinició el servicio.', 'Luis Andrés García', 'andres.lagm@gmail.com', 45, 'Resuelto', '2026-09-01 08:30:00'),
('Firewall perimetral', 'Alerta', 4, 'Múltiples intentos de acceso SSH fallidos desde una misma IP externa. Se bloqueó la IP y se reforzó la regla de rate limiting.', 'María Fernanda Rojas', 'mrojas@empresa.com', 20, 'Cerrado', '2026-09-02 02:15:00'),
('Portal web institucional', 'Mantenimiento', 2, 'Actualización programada de PHP 8.2 a 8.3 y parches de seguridad del sistema operativo. Ventana de mantenimiento sin afectación.', 'Carlos Peña', 'cpena@empresa.com', 90, 'Cerrado', '2026-09-02 22:00:00'),
('Servidor de respaldos', 'Respaldo', 1, 'Respaldo completo semanal de bases de datos y archivos compartidos ejecutado correctamente. Verificada la integridad del archivo.', 'Luis Andrés García', 'andres.lagm@gmail.com', 0, 'Cerrado', '2026-09-03 01:00:00'),
('Active Directory', 'Cambio', 3, 'Cambio de política de contraseñas: longitud mínima 12 caracteres y caducidad de 90 días. Pendiente comunicar a los usuarios.', 'Ana Lucía Torres', 'atorres@empresa.com', 0, 'En proceso', '2026-09-04 10:00:00'),
('Servidor de correo', 'Incidente', 4, 'Cola de correo saliente detenida por certificado TLS expirado. Se renovó el certificado; aún se monitorea la entrega pendiente.', 'Carlos Peña', 'cpena@empresa.com', 0, 'Abierto', '2026-09-05 07:45:00');

INSERT INTO bitacora (accion, evento_id, detalle, ip_origen, agente) VALUES
('REGISTRAR', 1, 'Evento #1 registrado: Incidente en Servidor de base de datos', '127.0.0.1', 'Carga inicial del script SQL'),
('REGISTRAR', 2, 'Evento #2 registrado: Alerta en Firewall perimetral', '127.0.0.1', 'Carga inicial del script SQL'),
('REGISTRAR', 3, 'Evento #3 registrado: Mantenimiento en Portal web institucional', '127.0.0.1', 'Carga inicial del script SQL'),
('REGISTRAR', 4, 'Evento #4 registrado: Respaldo en Servidor de respaldos', '127.0.0.1', 'Carga inicial del script SQL'),
('REGISTRAR', 5, 'Evento #5 registrado: Cambio en Active Directory', '127.0.0.1', 'Carga inicial del script SQL'),
('REGISTRAR', 6, 'Evento #6 registrado: Incidente en Servidor de correo', '127.0.0.1', 'Carga inicial del script SQL');
