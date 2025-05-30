# All Strategy

## Requisitos

- Docker
- Docker Compose

## Instalação

1. Clone o repositório:
```bash
git clone https://github.com/mauricioguim/Teste-Dev-php.git
cd Teste-Dev-php
```

2. Copie o arquivo de ambiente:
```bash
cp .env.example .env
```

3. Crie um schema de banco de dados MySQL e configure as variáveis de ambiente no arquivo `.env`:
- DB_DATABASE
- DB_USERNAME
- DB_PASSWORD

## Executando com Docker

1. Construa e inicie os containers:
```bash
docker compose up -d --build
```

2. Acesse a aplicação:
- API: http://localhost:8000

3. Endpoints disponíveis:
- **Listar Clientes**: `GET /api/clients`
- **Criar Cliente**: `POST /api/clients`
- **Atualizar Cliente**: `PUT /api/clients/{id}`
- **Deletar Cliente**: `DELETE /api/clients/{id}`

Para testar os endpoints, você pode usar ferramentas como Postman.
Na pasta `public` do projeto, você encontrará um arquivo `postman_collection.json` que contém a coleção de requisições para testar a API.'

## Comandos Úteis

- Parar os containers:
```bash
docker compose down
```

## Testes

1. Crie um banco de dados MySQL para os testes e configure as variáveis de ambiente no arquivo `.env.testing`:
- DB_DATABASE
- DB_USERNAME
- DB_PASSWORD


2. Execute os testes:
```bash
docker compose exec -it all-strategy-app php artisan test --env=testing
```
