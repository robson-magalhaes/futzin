-- Futzin Database Setup Script
-- Execute com: mysql -u root -p < create_database.sql

-- Criar banco de dados
CREATE DATABASE IF NOT EXISTS futzin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Usar o banco
USE futzin;

-- Criar usuário (comentado, edite se quiser)
-- CREATE USER IF NOT EXISTS 'futzin_user'@'localhost' IDENTIFIED BY 'sua_senha_aqui';
-- GRANT ALL PRIVILEGES ON futzin.* TO 'futzin_user'@'localhost';
-- FLUSH PRIVILEGES;

-- Pronto!
SELECT 'Banco de dados criado com sucesso!' as status;
