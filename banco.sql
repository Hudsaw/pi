create database pi;
use pi;

-- Tabela de especialidades
CREATE TABLE especialidade (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(50) NOT NULL UNIQUE
);

-- Inserção de especialidades
INSERT INTO especialidade (nome) VALUES 
('Costura overlock'), 
('Costura reta'), 
('Costura lateral'), 
('Costura gola'); 

-- Tabela de níveis
CREATE TABLE nivel (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome ENUM('Ouro', 'Prata', 'Bronze') NOT NULL,
    percentual int NOT NULL DEFAULT 0
);

-- Inserção de níveis
INSERT INTO nivel (nome, percentual) VALUES 
('Bronze', 95), 
('Prata', 100), 
('Ouro', 105);

-- Tabela de cadastro
CREATE TABLE usuarios (
    id int PRIMARY KEY AUTO_INCREMENT,
    tipo enum('admin', 'costureira') NOT NULL DEFAULT 'costureira',
    nome varchar(50) NOT NULL,
    cpf varchar(11) NOT NULL UNIQUE,
    email varchar(250) NOT NULL UNIQUE,
    telefone varchar(11) NOT NULL,
    cep varchar(8) NOT NULL,
    complemento varchar(50),
    senha varchar(255) NOT NULL,
    banco int NULL DEFAULT 0,
    agencia INT NULL DEFAULT 0,
    conta INT NULL DEFAULT 0,
    saldo DECIMAL(10, 2) DEFAULT 0.00,
    especialidade_id INT NOT NULL DEFAULT 2,
    nivel INT NOT NULL DEFAULT 1,
    ativo TINYINT DEFAULT 1
 );

 INSERT INTO usuarios (tipo, nome, cpf, email, telefone, cep, complemento, senha, banco, agencia, conta, especialidade_id, nivel) VALUES
('admin', 'Administrador', '12345678901', 'admin@pi.com', '11987654321', '12345678', 'Apto 101', '$2y$10$eW5z1Z1Z1Z1Z1Z1Z1Z1Z1.Z1Z1Z1Z1Z1Z1Z1Z1Z1Z1Z1Z1', 123, 456, 789, 2, 2),
('costureira', 'Maria da Silva', '98765432100', 'costura@pi.com', '11912345678', '87654321', 'Casa 2', '$2y$10$eW5z1Z1Z1Z1Z1Z1Z1Z1Z1.Z1Z1Z1Z1Z1Z1Z1Z1Z1Z1Z1', 321, 654, 987, 2, 2);

-- Tabela de Lotes
CREATE TABLE lotes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    empresa_id VARCHAR(50) NOT NULL,
    descricao TEXT NOT NULL,
    quantidade INT NOT NULL,
    valor DECIMAL(10, 2) NOT NULL,
    data_inicio DATE NOT NULL,
    data_prazo DATE NOT NULL,
    ativo TINYINT DEFAULT 1
);

-- Inserção de lotes
INSERT INTO lotes (empresa_id, descricao, quantidade, valor, data_inicio, data_prazo) VALUES 
(1, 'Lote de roupas femininas', 1000, 1500.00, '2025-08-30', '2025-09-30'),
(2, 'Lote de roupas masculinas', 2000, 2500.00, '2025-08-30', '2025-09-30');

-- Tabela de serviços
CREATE TABLE servicos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    lote_id INT REFERENCES lotes(id),
    descricao TEXT NOT NULL,
    especialidade INT REFERENCES especialidade(id),
    quantidade INT NOT NULL,
    valor DECIMAL(10, 2) NOT NULL,
    valor_pecas DECIMAL(10, 2) NOT NULL,
    valor_falhas DECIMAL(10, 2) NOT NULL,
    pecas_entregue INT DEFAULT 0,
    falhas_entregue INT DEFAULT 0,
    valor_total DECIMAL(10, 2) DEFAULT 0.00,
    data_inicio DATE NOT NULL,
    data_prazo DATE NOT NULL,
    ativo TINYINT DEFAULT 1
);

-- Inserção de serviços
INSERT INTO servicos (lote_id, descricao, especialidade, quantidade, valor, valor_pecas, valor_falhas, data_inicio, data_prazo) VALUES 
(1, 'Serviço de costura overlock', 1, 300, 30.00, 0.10, 0.2, '2025-08-30', '2025-09-30'),
(1, 'Serviço de costura reta', 2, 300, 33.00, 0.11, 0.22,'2025-08-30', '2025-09-30'),
(1, 'Serviço de costura lateral', 3, 300, 30.00, 0.10, 0.2,'2025-08-30', '2025-09-30'),
(1, 'Serviço de costura gola', 4, 300, 36.00, 0.12, 0.24,'2025-08-30', '2025-09-30');