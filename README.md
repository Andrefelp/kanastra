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

2. Construa e inicie os conteiner's:

   ```bash
   docker compose up -d
    ```

3. Acesse o contêiner do aplicativo:

   ```bash
   docker compose exec app bash
    ```

    3.1 Execute as migrations do banco de dados:
    
    ```bash
    php artisan migrate
    ```
       
    3.2 Faça a instalação das dependências
    
    ```bash
    composer install
    ```
    
    3.3 Para os testes unitários/feature:
   ```bash
    php artisan test
    ```
