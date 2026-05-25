-- migrate.sql — Migrações de banco de dados necessárias para as melhorias implementadas.
-- Execute no phpMyAdmin ou via CLI: mysql -u <user> -p <dbname> < migrate.sql

-- 1. Adiciona coluna de timestamp para expiração do código de recuperação de senha
--    (necessário para o TTL de 15 minutos implementado em codigo.php)
ALTER TABLE recuperar_senha
    ADD COLUMN IF NOT EXISTS criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;

-- 2. Índice para acelerar buscas por email na tabela de recuperação
CREATE INDEX IF NOT EXISTS idx_recuperar_email ON recuperar_senha (email);

-- 3. Garante que bolsas_sangue tem coluna id (caso não exista)
--    Normalmente já existe se criado com AUTO_INCREMENT, mas verifica.
-- ALTER TABLE bolsas_sangue ADD COLUMN IF NOT EXISTS id INT AUTO_INCREMENT PRIMARY KEY FIRST;

-- 4. Índice para acelerar buscas de estoque por tipo sanguíneo
CREATE INDEX IF NOT EXISTS idx_bolsas_tipo ON bolsas_sangue (tipo_sanguineo);

-- Verificação final: estrutura esperada das tabelas
-- DESCRIBE usuarios;
-- DESCRIBE bolsas_sangue;
-- DESCRIBE saida_bolsas_sangue;
-- DESCRIBE recuperar_senha;
