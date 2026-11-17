-- PDV MVC (MySQL / MariaDB)
CREATE DATABASE IF NOT EXISTS pdv_faculdade CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pdv_faculdade;

CREATE TABLE IF NOT EXISTS produtos (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  cod             VARCHAR(40)  NOT NULL UNIQUE,
  nome            VARCHAR(150) NOT NULL,
  categoria       VARCHAR(100) NULL,
  validade        DATE NULL,
  quantidade      INT NOT NULL DEFAULT 0,
  preco_unitario  DECIMAL(10,2) NOT NULL DEFAULT 0,
  preco_pacote    DECIMAL(10,2) NOT NULL DEFAULT 0,
  imagem          VARCHAR(255) NULL,
  CONSTRAINT chk_produto_qtd   CHECK (quantidade >= 0),
  CONSTRAINT chk_produto_preco CHECK (preco_unitario >= 0 AND preco_pacote >= 0)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS clientes (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  nome      VARCHAR(150) NOT NULL,
  cpf       VARCHAR(14)  NULL UNIQUE,
  email     VARCHAR(150) NULL,
  telefone  VARCHAR(30)  NULL,
  endereco  VARCHAR(255) NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS vendas (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  cliente_id INT NULL,
  total      DECIMAL(10,2) NOT NULL DEFAULT 0,
  status     ENUM('concluida','cancelada') NOT NULL DEFAULT 'concluida',
  criado_em  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_vendas_criado_em (criado_em),
  CONSTRAINT fk_venda_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS venda_itens (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  venda_id       INT NOT NULL,
  produto_id     INT NULL,
  nome_produto   VARCHAR(150) NOT NULL,
  quantidade     INT NOT NULL,
  preco_unitario DECIMAL(10,2) NOT NULL,
  subtotal       DECIMAL(10,2) NOT NULL,
  CONSTRAINT fk_item_venda   FOREIGN KEY (venda_id)   REFERENCES vendas(id)   ON DELETE CASCADE,
  CONSTRAINT fk_item_produto FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE SET NULL
) ENGINE=InnoDB;
