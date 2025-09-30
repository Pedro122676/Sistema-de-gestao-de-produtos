# Sistema-de-gestao-de-produtos


**Sistema de Gestão de Produtos e Cestas (PHP + MySQL)**

Este projeto é um sistema web básico para a **Gestão de Produtos**, **Fornecedores** e o gerenciamento de **Cestas de Produtos** associadas a usuários logados. É desenvolvido utilizando **PHP puro (sem frameworks)** com persistência de dados em MySQL (via PDO).

**Funcionalidades**

O sistema oferece as seguintes funcionalidades principais:

* **Autenticação e Cadastro**
    * **Cadastro de Usuário:** Tela dedicada para novos usuários (). Utiliza `password_hash()` para armazenamento seguro de senhas.
    * **Login:** Acesso ao sistema com validação de credenciais ().
    * **Logout Seguro:** Encerramento da sessão ().
* **Gestão de Fornecedores (CRUD)**
    * **Cadastro:** Registro de novos fornecedores com **Nome** e **CNPJ** ().
    * **Listagem:** Visualização de todos os fornecedores cadastrados.
* **Gestão de Produtos (CRUD)**
    * **Cadastro:** Adição de novos produtos com **Nome**, **Preço** e associação a um **Fornecedor** ().
    * **Listagem:** Visualização dos produtos, exibindo nome e preço.
* **Gestão de Cestas**
    * **Criação de Cesta:** Seleção de múltiplos produtos na listagem de produtos e criação de uma nova cesta para o usuário logado ().
        * **Segurança:** Implementa **transações SQL** (`beginTransaction`, `commit`, `rollback`) para garantir a integridade dos dados na criação da cesta.
    * **Visualização:** Exibição da última cesta ativa do usuário, listando os produtos e calculando o **valor total** ().
    * **Esvaziar/Excluir Cesta:** Função para remover a cesta ativa do usuário (), também utilizando transações SQL.

---

**Tecnologias e Arquivos-Chave**

| Categoria | Tecnologia | Arquivo de Referência |
| :--- | :--- | :--- |
| **Backend** | PHP (Puro) | Todos os arquivos `.php` |
| **Banco de Dados** | MySQL/MariaDB | |
| **Conexão DB** | PDO (PHP Data Objects) | |
| **Estrutura** | HTML, Bootstrap 5.3 (CDN) | |
| **Utilitário DB** | Script de criação de tabelas | |
| **Segurança** | PHP Sessions, `password_hash()` | |

---

**Instalação e Configuração**

Siga os passos abaixo para rodar o projeto no seu ambiente local (ex: XAMPP, WAMP, MAMP).

**Pré-requisitos**

1.  Um servidor web (Apache, Nginx).
2.  **PHP** (versão 8.x recomendada).
3.  Servidor **MySQL** ou **MariaDB**.

**1. Configuração do Banco de Dados**

Você tem duas opções para configurar o banco de dados:

**Opção A: Usando o Arquivo SQL (Recomendado)**

1.  Acesse seu gerenciador de banco de dados (phpMyAdmin, MySQL Workbench, etc.).
2.  Crie um banco de dados chamado **`gestao_produtos`**.
3.  Importe o script **`gestao_produtos.sql`** para criar todas as tabelas e popular com dados iniciais (incluindo usuários de teste).

**Opção B: Usando o Script PHP de Setup**

1.  Altere as credenciais de conexão no arquivo **`setup.php`**.
2.  Execute o script no seu navegador (ex: `http://localhost/seu-diretorio/setup.php`). Isso criará as tabelas, mas você precisará cadastrar os primeiros usuários manualmente.

**2. Configuração da Conexão**

1.  Abra o arquivo **`db.php`**.
2.  Altere as variáveis `$host`, `$user` e `$pass` na função estática `getConnection()` para corresponderem às suas credenciais do servidor de banco de dados:

    ```php
    // Exemplo em db.php
    private static function getConnection() {
        // ...
        $host = 'localhost';
        $db   = 'gestao_produtos';
        $user = 'seu_usuario_db'; // <--- ATUALIZE
        $pass = 'sua_senha_db';     // <--- ATUALIZE
        // ...
    ```

**3. Execução do Projeto**

1.  Coloque todos os arquivos na pasta raiz do seu servidor web (ex: `htdocs` ou `www`).
2.  Acesse a página inicial de login pelo navegador: `http://localhost/seu-diretorio/index.html`.

---

**Credenciais de Teste**

Se você importou o arquivo `gestao_produtos.sql`, você pode usar as seguintes credenciais para acesso:

| Email | Senha |

| `peter.@exemplo.com` | `123456789` |
| `joao@exemplo.com` | `123` |

Se preferir, utilize a página de **Cadastro** () para criar um novo usuário.
