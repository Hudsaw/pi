drop database pi;

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
    logradouro varchar(250),
    complemento varchar(50),
    cidade varchar(100),
    tipo_chave_pix enum('cpf', 'cnpj', 'email', 'telefone', 'aleatoria') DEFAULT 'cpf',
    chave_pix varchar(250),
    senha varchar(255) NOT NULL,
    banco int NULL DEFAULT 0,
    agencia INT NULL DEFAULT 0,
    conta INT NULL DEFAULT 0,
    especialidade_id INT NOT NULL DEFAULT 2,
    ativo TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
 );

INSERT INTO usuarios (tipo, nome, cpf, email, telefone, cep, logradouro, complemento, cidade, tipo_chave_pix, chave_pix, senha, banco, agencia, conta, especialidade_id, ativo) VALUES
('admin'     , 'Administrador' , '12345678901', 'admin@pi.com'  , '11987654321', '12345678', 'Rua Botuverá'    , 'Apto 101', 'Gaspar'   , 'cpf'     , '12345678901'   , '123'  , 123, 456, 789, 3, 1),
('costureira', 'Maria da Silva', '98765432100', 'costura@pi.com', '11912345678', '87654321', 'Rua São Paulo'   , 'Casa 2'  , 'Blumenau' , 'email'   , 'costura@pi.com', '123', 321, 654, 987, 2, 1),
('costureira', 'Janaina'       , '87439287245', 'catu@piri.com' , '73737373737', '2313'    , 'Av. Das Alegrias', 'Casa 3'  , 'São Paulo', 'telefone', '73737373737'   , '123'  , 321, 654, 987, 1, 1);

-- Tabela de empresas
CREATE TABLE empresas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(255) NOT NULL,
    cnpj VARCHAR(14) NOT NULL UNIQUE,
    email VARCHAR(255),
    telefone VARCHAR(20),
    endereco TEXT,
    cidade VARCHAR(100),
    estado VARCHAR(2),
    cep VARCHAR(8),
    observacao TEXT,
    ativo TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Inserir algumas empresas de exemplo
INSERT INTO empresas (nome, cnpj, email, telefone, endereco, cidade, estado, cep) VALUES 
('Moda Fashion Ltda', '12345678000195', 'contato@modafashion.com', '4733334444', 'Rua das Flores, 123', 'Blumenau', 'SC', '89010000'),
('Confecções Estilo SA', '98765432000187', 'vendas@estiloconfec.com', '4732223333', 'Av. Brasil, 456', 'Gaspar', 'SC', '89110000'),
('Têxtil Qualidade ME', '45678912000134', 'qualidade@textil.com', '4734445555', 'Rua Industrial, 789', 'Indaial', 'SC', '89080000');

-- Tabela de Tipos de Peça (DEVE VIR ANTES DOS LOTES)
CREATE TABLE tipos_peca (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    ativo BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de Cores 
CREATE TABLE cores (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(50) NOT NULL,
    codigo_hex VARCHAR(7),
    ativo BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de Tamanhos 
CREATE TABLE tamanhos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(10) NOT NULL,
    ordem INT DEFAULT 0,
    ativo BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de operações
CREATE TABLE operacoes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(255) NOT NULL,
    valor DECIMAL(10, 2) NOT NULL,
    ativo TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de lotes
CREATE TABLE lotes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    empresa_id VARCHAR(50) NOT NULL,
    colecao VARCHAR(100) NOT NULL,
    nome VARCHAR(100) NOT NULL,
    observacao TEXT,
    data_entrada DATE NOT NULL,
    data_entrega DATE,
    valor_total DECIMAL(12, 2) DEFAULT 0.00,
    status ENUM('Aberto', 'Entregue', 'Cancelado') DEFAULT 'Aberto',
    anexos VARCHAR(500),
    ativo TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de peças
CREATE TABLE pecas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    lote_id INT NOT NULL,
    tipo_peca_id INT NOT NULL,
    cor_id INT NOT NULL,
    tamanho_id INT NOT NULL,
    operacao_id INT NOT NULL,
    quantidade INT NOT NULL,
    valor_unitario DECIMAL(10, 2) NOT NULL,
    valor_total DECIMAL(10, 2) GENERATED ALWAYS AS (quantidade * valor_unitario) STORED,
    observacao TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (lote_id) REFERENCES lotes(id) ON DELETE CASCADE,
    FOREIGN KEY (tipo_peca_id) REFERENCES tipos_peca(id),
    FOREIGN KEY (cor_id) REFERENCES cores(id),
    FOREIGN KEY (tamanho_id) REFERENCES tamanhos(id),
    FOREIGN KEY (operacao_id) REFERENCES operacoes(id)
);

-- Tabela de serviços
CREATE TABLE servicos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    lote_id INT NOT NULL,
    operacao_id INT NOT NULL,
    quantidade_pecas INT NOT NULL,
    valor_operacao DECIMAL(10,2) NOT NULL,
    data_envio DATE NOT NULL,
    data_finalizacao DATE NULL,
    observacao TEXT,
    costureira_id INT NULL,
    status ENUM('Em andamento', 'Finalizado', 'Inativo') DEFAULT 'Em andamento',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (lote_id) REFERENCES lotes(id) ON DELETE RESTRICT,
    FOREIGN KEY (operacao_id) REFERENCES operacoes(id) ON DELETE RESTRICT
);

-- Tabela de pagamentos
CREATE TABLE pagamentos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    costureira_id INT NOT NULL,
    periodo_referencia DATE NOT NULL,
    valor_bruto DECIMAL(10, 2) NOT NULL,
    valor_desconto DECIMAL(10, 2) DEFAULT 0.00,
    valor_liquido DECIMAL(10, 2) GENERATED ALWAYS AS (valor_bruto - valor_desconto) STORED,
    motivo_desconto TEXT,
    data_pagamento DATE,
    status ENUM('Pendente', 'Pago', 'Cancelado') DEFAULT 'Pendente',
    comprovante VARCHAR(500),
    observacao TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (costureira_id) REFERENCES usuarios(id)
);

-- Tabela de itens de pagamento 
CREATE TABLE pagamento_itens (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pagamento_id INT NOT NULL,
    servico_id INT NOT NULL,
    valor_calculado DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (pagamento_id) REFERENCES pagamentos(id) ON DELETE CASCADE,
    FOREIGN KEY (servico_id) REFERENCES servicos(id)
);

-- Tabela de mensagens 
CREATE TABLE mensagens (
    id INT PRIMARY KEY AUTO_INCREMENT,
    remetente_id INT NOT NULL,
    destinatario_id INT,
    mensagem TEXT NOT NULL,
    lida TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (remetente_id) REFERENCES usuarios(id),
    FOREIGN KEY (destinatario_id) REFERENCES usuarios(id)
);

-- Tabela de logs do sistema
CREATE TABLE logs_sistema (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    acao VARCHAR(100) NOT NULL,
    entidade VARCHAR(50) NOT NULL,
    entidade_id INT,
    dados_anteriores JSON,
    dados_novos JSON,
    ip VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Tabela de Reset de Senha
CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX (token)
);

-- =============================================
-- INSERÇÃO DE DADOS INICIAIS
-- =============================================

-- Inserir cores
INSERT INTO cores (nome, codigo_hex) VALUES 
('Branco', '#FFFFFF'),
('Preto', '#000000'),
('Azul', '#0000FF'),
('Vermelho', '#FF0000'),
('Verde', '#008000'),
('Amarelo', '#FFFF00'),
('Cinza', '#808080'),
('Rosa', '#FFC0CB'),
('Laranja', '#FFA500'),
('Roxo', '#800080');

-- Inserir tamanhos
INSERT INTO tamanhos (nome, ordem) VALUES 
('PP', 10),
('P', 20),
('M', 30),
('G', 40),
('GG', 50),
('XG', 60),
('XXG', 70);

-- Inserir tipos de peça
INSERT INTO tipos_peca (nome, descricao) VALUES 
('Camiseta', 'Camiseta básica'),
('Moletom', 'Moletom com capuz'),
('Regata', 'Camiseta sem manga'),
('Manga Comprida', 'Camiseta de manga longa'),
('Calça', 'Calça de malha'),
('Short', 'Short de malha'),
('Body', 'Body infantil'),
('Macacão', 'Macacão infantil');

-- Inserir operações
INSERT INTO operacoes (nome, valor) VALUES 
('Costura Overlock', 0.50),
('Costura Reta', 0.45),
('Costura Lateral', 0.40),
('Costura Gola', 0.60),
('Arrematar Manga', 0.35),
('Arrematar Bainha', 0.30),
('Costurar Etiqueta', 0.25),
('Pregar Botão', 0.20);

-- Inserir lotes de exemplo
INSERT INTO lotes (empresa_id, colecao, nome, observacao, data_entrada, data_entrega, valor_total, status) VALUES 
('1', 'Coleção Verão 2025', 'Lote Feminino V25', 'Lote de roupas femininas para verão', '2025-08-30', '2025-09-30', 1500.00, 'Aberto'),
('2', 'Coleção Inverno 2025', 'Lote Masculino I25', 'Lote de roupas masculinas para inverno', '2025-08-30', '2025-10-15', 2500.00, 'Aberto'),
('1', 'Coleção Primavera 2025', 'Lote Infantil P25', 'Lote de roupas infantis para primavera', '2025-09-01', NULL, 0.00, 'Aberto');

INSERT INTO servicos (lote_id, operacao_id, quantidade_pecas, valor_operacao, data_envio, observacao, status) VALUES
(1, 1, 100, 2.50, '2024-01-15', 'Serviço de costura de mangas', 'Em andamento'),
(2, 2, 150, 1.80, '2024-01-10', 'Arremate de bainhas', 'Finalizado'),
(3, 3, 80, 3.20, '2024-01-20', 'Costura de golas', 'Em andamento');

-- =============================================
-- ÍNDICES PARA MELHOR PERFORMANCE
-- =============================================

-- Índices para empresas
CREATE INDEX idx_empresas_nome ON empresas(nome);
CREATE INDEX idx_empresas_cnpj ON empresas(cnpj);
CREATE INDEX idx_empresas_ativo ON empresas(ativo);

-- Índices para lotes
CREATE INDEX idx_lotes_status ON lotes(status);
CREATE INDEX idx_lotes_empresa ON lotes(empresa_id);
CREATE INDEX idx_lotes_data_entrada ON lotes(data_entrada);

-- Índices para peças
CREATE INDEX idx_pecas_lote_id ON pecas(lote_id);
CREATE INDEX idx_pecas_tipo ON pecas(tipo_peca_id);
CREATE INDEX idx_pecas_cor ON pecas(cor_id);
CREATE INDEX idx_pecas_tamanho ON pecas(tamanho_id);

-- Índices para serviços
CREATE INDEX idx_servicos_lote_id ON servicos(lote_id);
CREATE INDEX idx_servicos_costureira_id ON servicos(costureira_id);
CREATE INDEX idx_servicos_status ON servicos(status);
CREATE INDEX idx_servicos_data_inicio ON servicos(data_inicio);
CREATE INDEX idx_servicos_operacao_id ON servicos(operacao_id);

-- Índices para pagamentos
CREATE INDEX idx_pagamentos_costureira_id ON pagamentos(costureira_id);
CREATE INDEX idx_pagamentos_periodo ON pagamentos(periodo_referencia);
CREATE INDEX idx_pagamentos_status ON pagamentos(status);

-- Índices para mensagens
CREATE INDEX idx_mensagens_remetente ON mensagens(remetente_id);
CREATE INDEX idx_mensagens_destinatario ON mensagens(destinatario_id);
CREATE INDEX idx_mensagens_created_at ON mensagens(created_at);

-- Índices para logs
CREATE INDEX idx_logs_usuario ON logs_sistema(usuario_id);
CREATE INDEX idx_logs_entidade ON logs_sistema(entidade, entidade_id);
CREATE INDEX idx_logs_created_at ON logs_sistema(created_at);