-- Dados de exemplo (opcional). Importe depois do schema.sql.
USE pdv_faculdade;

INSERT INTO produtos (cod, nome, categoria, validade, quantidade, preco_unitario, preco_pacote, imagem) VALUES
('P001', 'Produto de exemplo 1', 'Geral',      '2027-12-31', 50, 5.50, 50.00, 'produto-exemplo-1.jpg'),
('P002', 'Produto de exemplo 2', 'Geral',      '2027-12-31', 30, 8.90, 80.00, 'produto-exemplo-2.jpg'),
('P003', 'Água mineral 500 ml',  'Bebidas',    '2027-06-30', 100, 2.50, 24.00, NULL),
('P004', 'Pão francês (un.)',    'Padaria',    NULL,         200, 0.80, 20.00, NULL);

INSERT INTO clientes (nome, cpf, email, telefone, endereco) VALUES
('Maria da Silva', '529.982.247-25', 'maria@example.com', '(65) 99999-0001', 'Rua A, 100 - Cuiabá/MT'),
('João Souza',     '111.444.777-35', 'joao@example.com',  '(65) 99999-0002', 'Av. B, 200 - Cuiabá/MT');
