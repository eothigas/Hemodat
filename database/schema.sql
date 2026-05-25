-- schema.sql — Schema completo do banco de dados Hemodat
-- Execute: mysql -u root -p < schema.sql

CREATE DATABASE IF NOT EXISTS efegduik_gphemodat
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE efegduik_gphemodat;

-- ─── Usuários ────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS usuarios (
    id         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    nome       VARCHAR(50)     NOT NULL,
    email      VARCHAR(255)    NOT NULL,
    senha      VARCHAR(255)    NOT NULL,
    criado_em  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Estoque de bolsas de sangue ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS bolsas_sangue (
    id              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    tipo_sanguineo  VARCHAR(5)      NOT NULL,
    quantidade      DECIMAL(10,2)   NOT NULL,
    data_coleta     DATE            NOT NULL,
    data_validade   DATE            NOT NULL,
    criado_em       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_tipo (tipo_sanguineo),
    KEY idx_validade (data_validade)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Registro de saídas ──────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS saida_bolsas_sangue (
    id              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    email           VARCHAR(255)    NOT NULL,
    tipo_sanguineo  VARCHAR(5)      NOT NULL,
    quantidade      DECIMAL(10,2)   NOT NULL,
    data_saida      DATE            NOT NULL,
    criado_em       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_email (email),
    KEY idx_tipo (tipo_sanguineo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Recuperação de senha ────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS recuperar_senha (
    id         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    usuario    VARCHAR(50)     NOT NULL,
    email      VARCHAR(255)    NOT NULL,
    codigo     VARCHAR(8)      NOT NULL,
    criado_em  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
