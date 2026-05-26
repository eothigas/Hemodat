-- =============================================================
-- Hemodat P2 Migration
-- Execute via phpMyAdmin ou: mysql -u root efegduik_gphemodat < p2_features.sql
-- =============================================================

-- 1. Role em usuarios
ALTER TABLE usuarios
    ADD COLUMN role ENUM('admin','operador') NOT NULL DEFAULT 'operador';

-- Primeiro usuário vira admin automaticamente (ajuste o id se necessário)
-- UPDATE usuarios SET role = 'admin' WHERE id = 1;

-- 2. Log de entradas (histórico real)
CREATE TABLE IF NOT EXISTS entradas_log (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    tipo_sanguineo  VARCHAR(5)     NOT NULL,
    quantidade      DECIMAL(10,2)  NOT NULL,
    data_coleta     DATE           NOT NULL,
    data_validade   DATE           NOT NULL,
    criado_em       TIMESTAMP      DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Estoque mínimo por tipo
CREATE TABLE IF NOT EXISTS estoque_minimo (
    tipo_sanguineo  VARCHAR(5)    NOT NULL PRIMARY KEY,
    minimo_litros   DECIMAL(10,2) NOT NULL DEFAULT 2.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO estoque_minimo (tipo_sanguineo, minimo_litros) VALUES
    ('A+',  2.00), ('A-',  2.00),
    ('B+',  2.00), ('B-',  2.00),
    ('AB+', 2.00), ('AB-', 2.00),
    ('O+',  2.00), ('O-',  2.00);
