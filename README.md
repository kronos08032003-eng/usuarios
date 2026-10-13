# API de Usuários

## 1. Identificação

- **Nome completo do aluno:** Felipe de Oliveira Dias
- **Curso:** Técnico em Informática
- **Unidade Curricular:** Desenvolver Serviços Web

## 2. Como Instalar as Dependências

### Requisitos

- PHP instalado no computador;
- Composer instalado;
- Postman para testar as requisições da API.

### Passos para instalação

1. Baixe ou clone o repositório do projeto para o computador.
2. Abra o terminal na pasta raiz do projeto, onde está localizado o arquivo `composer.json`.
3. Execute o comando abaixo para instalar as dependências:

   ```bash
   composer install
   ```

4. Caso as dependências do Slim ainda não estejam instaladas, execute:

   ```bash
   composer require slim/slim:"4.*" slim/psr7
   ```

5. Inicie o servidor PHP utilizando o arquivo principal da API. Ajuste o caminho conforme a localização do arquivo no projeto.

6. Abra o Postman e envie uma requisição GET para:

   `http://localhost:8080/status`

Se a instalação e a execução estiverem corretas, a API retornará uma mensagem informando que está funcionando.

## 3. Decisões Técnicas

A API foi desenvolvida utilizando PHP e Slim Framework 4, permitindo a criação de rotas para realizar operações relacionadas ao gerenciamento de usuários por meio de requisições HTTP.

Os dados são retornados no formato JSON, facilitando a comunicação entre a API e ferramentas de teste, como o Postman.

Nesta versão, os usuários são armazenados temporariamente em um array PHP, sem conexão com um banco de dados. Dessa forma, as alterações realizadas durante a execução não são mantidas após o encerramento ou reinício da aplicação.

As funcionalidades implementadas são:

- **GET /status:** verifica se a API está funcionando.
- **GET /usuarios:** lista todos os usuários cadastrados.
- **GET /usuarios/{id}:** consulta um usuário específico pelo identificador.
- **POST /usuarios:** cadastra um novo usuário.
- **PUT /usuarios/{id}:** atualiza os dados de um usuário.
- **DELETE /usuarios/{id}:** remove um usuário.
- **POST /login:** verifica as credenciais informadas para autenticação.
- **PUT /usuarios/{id}/senha:** permite alterar a senha de um usuário após a validação da senha atual.

O projeto também realiza validações dos campos obrigatórios e utiliza códigos de status HTTP para indicar o resultado das operações, como 200 para sucesso, 201 para criação, 400 para requisições inválidas, 401 para falhas de autenticação e 404 quando um usuário não é encontrado.

Para evitar a exposição direta das senhas nas respostas de consulta, os valores são substituídos por asteriscos. Entretanto, em uma aplicação real, é necessário armazenar as senhas de maneira segura, utilizando funções como `password_hash()` e `password_verify()`.

## 4. Prints


