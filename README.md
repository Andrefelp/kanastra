# Kanastra

Este é o hiring challenge Kanastra - candidato André Felipe Machado.

Foi utilizado PHP/Laravel para o desenvolvimento.

## Pré-requisitos

Certifique-se de ter os seguintes programas instalados:

- [Docker](https://www.docker.com/get-started) (inclui o Docker Compose)
- [Docker Compose](https://docs.docker.com/compose/install/) (obrigatório para gerenciar contêineres)

## Instalação

1. Clone este repositório:

   ```bash
   git clone https://github.com/Andrefelp/kanastra
   cd kanastra
    ```

2. Copiar o .env:
    ```bash
    cp .env.example .env
    ```
   
3. Construa e inicie os conteiner's:

   ```bash
   docker compose up -d
    ```

4. Acesse o contêiner do aplicativo:

   ```bash
   docker compose exec app bash
    ```

    4.1 Execute as migrations do banco de dados:
    
    ```bash
    php artisan migrate
    ```
       
    4.2 Faça a instalação das dependências
    
    ```bash
    composer install
    ```
    
    4.3 Para os testes unitários/feature:
   ```bash
    php artisan test
    ```
   
    4.4 Para gerar o token de chamada na API:
   ```bash
    php artisan create:user
    ```
   
    Copiar a chave da linha "Token de API".
    Exemplo: 333|fXRgBaVThLnST2JOKquDwKpp1wtjsImTBE5J9veLad174e1e


5. Para testar a chamada de API, use o software de sua escolha (postman, insomnia, etc)
    
    5.1 Rota para acessar:
        POST: http://localhost:8000/api/importacao-boletos-csv
        
        Inserir o header:
        Accept, com o value application/json

        No Auth:
        inserir o Bearer Token, com o token gerado no passo anterior
    
        No Body:
        inserir uma linha com o nome file, e anexar ao lado o .csv desejado
