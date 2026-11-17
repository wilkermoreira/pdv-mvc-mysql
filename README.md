# Sistema de Ponto de Venda (PDV) — PHP, MySQL e MVC

Projeto acadêmico de **Análise e Desenvolvimento de Sistemas (ADS)**. Parte do PDV fornecido em aula (versão MySQL, com `index.html`, `salvar\_produto.php`, `editar\_produto.php` e `pdv.php`) e o reorganiza em **MVC com Design Patterns**, acrescentando os CRUDs pedidos:

1. Cadastro de clientes
2. Carrinho de vendas
3. Relatórios de vendas

O cadastro de produtos da versão de aula foi mantido (e reescrito em MVC), pois é ele que alimenta o carrinho.

**Tecnologias:** PHP 8.1+, MySQL/MariaDB, PDO, HTML, Bootstrap 5.

\---

## 1\. Arquitetura MVC

|Camada|Pasta|Responsabilidade|
|-|-|-|
|**Model**|`app/Models`|Entidades (`Cliente`, `Produto`, `Venda`, `ItemVenda`) com seus dados e regras de validação.|
|**Model (persistência)**|`app/Repositories`|Todo o SQL do sistema fica aqui (prepared statements via PDO).|
|**View**|`app/Views`|Telas em PHP/HTML. Só exibem dados; não têm SQL nem regra de negócio. Toda saída passa por `e()` (escape contra XSS).|
|**Controller**|`app/Controllers`|Recebe a requisição, chama Models/Repositories/Services e escolhe a View.|

Apoio: `app/Services` (regras de negócio), `app/Core` (roteador, controller base, conexão, helpers), `public/index.php` (ponto de entrada).

### Fluxo de uma requisição (exemplo: listar clientes)

1. O navegador abre `index.php?r=clientes`.
2. `public/index.php` registra as rotas e chama o `Router`.
3. O `Router` instancia `ClienteController` e executa `index()`.
4. O Controller pede os dados ao `ClienteRepository`, que consulta o MySQL e devolve objetos `Cliente`.
5. O Controller entrega os dados à View `clientes/index.php`, que monta a tabela.

\---

## 2\. Design Patterns adotados

|Padrão|Onde|Por quê|
|-|-|-|
|**Singleton**|`app/Core/Database.php`|Uma única conexão PDO por requisição, compartilhada por todos que chamam `Database::connection()`. Construtor privado impede novas instâncias.|
|**Repository**|`app/Repositories/\*`|Isola o SQL. Controllers e Services lidam com objetos (`Cliente`, `Produto`, `Venda`) e não sabem como são gravados. Trocar o banco afetaria só esta camada.|
|**Service Layer**|`app/Services/VendaService.php`, `CarrinhoService.php`|Regras que envolvem vários passos ficam fora do Controller: fechar venda (transação: grava venda, itens e baixa estoque, ou desfaz tudo), cancelar venda (devolve estoque) e controlar o carrinho.|
|**Front Controller**|`public/index.php` + `app/Core/Router.php`|Toda requisição entra por um único ponto; a tabela de rotas despacha para o Controller/método certo e valida o token CSRF dos POSTs.|
|**Injeção de Dependência**|construtores de Repositories e Services|`VendaService` recebe o `PDO`; `CarrinhoService` recebe o `ProdutoRepository`. Facilita testes e reduz acoplamento.|

\---

## 3\. CRUDs

### 3.1 Cadastro de clientes — `?r=clientes`

* **Create:** nome, CPF, e-mail, telefone e endereço.
* **Read:** listagem com busca por nome, CPF, e-mail ou telefone.
* **Update:** edição dos dados.
* **Delete:** exclusão. As vendas antigas permanecem no histórico, sem vínculo com o cliente.
* Validações: nome obrigatório, e-mail válido, **CPF com dígito verificador** e CPF único.

### 3.2 Carrinho de vendas — `?r=vendas`

* **Create:** buscar produto e adicionar ao carrinho (soma se já estiver lá).
* **Read:** itens, subtotais e total.
* **Update:** alterar quantidades (0 remove o item; acima do estoque é ajustado).
* **Delete:** remover um item ou esvaziar o carrinho.
* **Finalizar:** escolhe o cliente (opcional), grava a venda e seus itens e baixa o estoque em uma transação. O estoque é conferido de novo no fechamento.

### 3.3 Relatórios de vendas — `?r=relatorios`

* **Read:** lista de vendas com filtros (período, status, cliente), resumo (nº de vendas, faturamento, ticket médio), produtos mais vendidos e página de detalhes.
* **Update:** trocar o cliente de uma venda e **cancelar** a venda (devolve os itens ao estoque).
* **Delete:** excluir do histórico, somente vendas já canceladas.

### Produtos (da versão de aula) — `?r=produtos`

CRUD completo com busca e **upload de imagem** (JPG/PNG/WEBP/GIF até 2 MB; o tipo é conferido pelo conteúdo do arquivo e o nome é gerado pelo sistema).

\---

## 4\. POP — Procedimento Operacional Padrão de implantação

### 4.1 Objetivo

Instalar e executar o PDV em um computador local.

### 4.2 Requisitos

* PHP 8.1 ou superior com a extensão `pdo\_mysql` habilitada
* MySQL 8 ou MariaDB 10.4+
* XAMPP/WAMP **ou** apenas PHP + MySQL instalados
* Git (para clonar o repositório)

### 4.3 Passo a passo

1. **Obter o projeto**

```bash
   git clone https://github.com/wilkermoreira/pdv-mvc-mysql.git
   cd pdv-mvc-mysql
   ```

2. **Iniciar o MySQL** (painel do XAMPP/WAMP ou serviço do sistema).
3. **Criar o banco e as tabelas**

```bash
   mysql -u root -p < database/schema.sql
   ```

   Alternativa: importar `database/schema.sql` pelo phpMyAdmin (aba *Importar*).

4. **(Opcional) Carregar dados de exemplo**

```bash
   mysql -u root -p < database/seed.sql
   ```

5. **Configurar o ambiente**

```bash
   cp .env.example .env      # no Windows (CMD): copy .env.example .env
   ```

   Edite o `.env` e ajuste `DB\_USER` e `DB\_PASS` conforme o seu MySQL (no XAMPP padrão: usuário `root`, senha vazia). O `.env` não vai para o GitHub.

6. **Subir o servidor**

```bash
   php -S localhost:8000 -t public
   ```

   Alternativa XAMPP: copiar a pasta para `htdocs` e abrir `http://localhost/pdv-mvc-mysql/`.

7. **Acessar** `http://localhost:8000`.

### 4.4 Roteiro de conferência

1. Em **Clientes**, cadastre um cliente (use um CPF válido).
2. Em **Produtos**, cadastre um produto com estoque maior que zero e uma imagem.
3. Em **Carrinho de vendas**, adicione o produto, altere a quantidade e finalize escolhendo o cliente.
4. Em **Produtos**, confirme que o estoque diminuiu.
5. Em **Relatórios**, confira a venda, abra os detalhes e cancele-a; confirme que o estoque voltou.
6. Exclua a venda cancelada do histórico.

### 4.5 Problemas comuns

|Sintoma|Causa provável / solução|
|-|-|
|"Erro ao carregar o sistema"|MySQL desligado ou `.env` com usuário/senha errados. Coloque `APP\_DEBUG=true` no `.env` para ver a mensagem e consulte `storage/logs/app.log`.|
|`could not find driver`|Habilite `extension=pdo\_mysql` no `php.ini`.|
|Banco não encontrado|O passo 3 não foi executado (importe `database/schema.sql`).|
|Imagem não envia|Arquivo maior que 2 MB, formato diferente de JPG/PNG/WEBP/GIF, ou limite `upload\_max\_filesize` do PHP muito baixo.|
|Porta 8000 ocupada|Use outra, ex.: `php -S localhost:8080 -t public`.|

\---

## 5\. Estrutura de pastas

```text
.
├── app/
│   ├── Controllers/    # Home, Produto, Cliente, Venda, Relatorio, Error
│   ├── Core/           # Router, Controller base, Database (Singleton), helpers
│   ├── Models/         # Cliente, Produto, Venda, ItemVenda
│   ├── Repositories/   # ClienteRepository, ProdutoRepository, VendaRepository
│   ├── Services/       # CarrinhoService, VendaService, ImageUploader
│   └── Views/          # layouts, produtos, clientes, vendas, relatorios, errors
├── config/database.php # leitura das variáveis do .env
├── database/           # schema.sql (estrutura) e seed.sql (dados de exemplo)
├── public/             # index.php (Front Controller) e assets (css, imagens)
├── storage/logs/       # app.log (erros da aplicação)
├── bootstrap.php       # .env, sessão, autoload
├── .env.example
└── README.md
```

### Rotas

|Rota (`?r=`)|Método|Ação|
|-|-|-|
|`produtos`, `clientes`|GET|listar/buscar|
|`produtos/novo`, `clientes/novo`|GET|formulário de cadastro|
|`produtos/editar`, `clientes/editar`|GET|formulário de edição (`\&id=`)|
|`produtos/salvar`, `clientes/salvar`|POST|criar ou atualizar|
|`produtos/excluir`, `clientes/excluir`|POST|excluir|
|`vendas`|GET|busca de produtos + carrinho|
|`vendas/adicionar`, `atualizar`, `remover`, `limpar`, `finalizar`|POST|operações do carrinho|
|`relatorios`, `relatorios/detalhe`|GET|listagem com filtros / detalhes|
|`relatorios/cliente`, `cancelar`, `excluir`|POST|alterar cliente, cancelar, excluir venda|

\---

## 6\. Segurança e decisões técnicas

* **Sem credenciais no código:** a conexão usa o `.env`, ignorado pelo Git. A versão de aula tinha usuário e senha escritos em `db.php`; eles não aparecem neste projeto.
* **SQL Injection:** todas as consultas usam prepared statements (a versão de aula concatenava valores no `INSERT`).
* **XSS:** toda saída nas Views passa por `htmlspecialchars` (`e()`).
* **CSRF:** todo formulário POST leva um token validado pelo `Router`.
* **Integridade:** vendas usam transações e bloqueio de linha (`FOR UPDATE`) ao baixar o estoque; o preço e o nome do produto ficam gravados no item da venda, então o histórico não muda se o produto for editado ou excluído.
* **Upload:** tipo conferido pelo conteúdo, limite de 2 MB, nome gerado pelo servidor.
* **Fora do escopo (projeto didático):** autenticação e perfis de usuário, paginação, testes automatizados e suporte a PostgreSQL (a versão de aula em PostgreSQL/Neon não foi portada).

## 7\. Verificação realizada

O sistema foi executado com PHP 8.3 e MariaDB 10.11, exercitando por requisições HTTP: criação, edição, busca e exclusão de clientes e produtos (incluindo CPF inválido/duplicado, código duplicado e upload inválido), carrinho (adicionar, estoque insuficiente, ajustar, remover), finalização, baixa de estoque, relatórios com filtros, troca de cliente, cancelamento com devolução de estoque e exclusão de venda cancelada.

\---

**Trabalho acadêmico — Análise e Desenvolvimento de Sistemas (ADS)**

