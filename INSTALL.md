# Instruções de Instalação - Marcasite Cursos

## Pré-requisitos

1. Docker Desktop instalado e rodando
2. Git instalado

## Passo a Passo

### 1. Certifique-se de que o Docker está rodando

Abra o Docker Desktop e aguarde até que esteja totalmente iniciado.

### 2. Inicie os containers

```bash
docker-compose up -d
```

Este comando irá:
- Baixar as imagens necessárias (PHP 8.2, MySQL 8.0, Nginx, Node.js 18)
- Criar e iniciar os containers
- Configurar a rede entre os containers

### 3. Instale as dependências do PHP (Composer)

```bash
docker-compose exec app composer install
```

### 4. Gere a chave da aplicação

```bash
docker-compose exec app php artisan key:generate
```

### 5. Instale as dependências do Node.js

```bash
docker-compose exec node npm install
```

### 6. Configure o arquivo .env

Copie o arquivo `.env.example` para `.env` (se ainda não tiver feito):

```bash
# No Windows PowerShell
Copy-Item .env.example .env

# No Linux/Mac
cp .env.example .env
```

Edite o arquivo `.env` e configure:
- As credenciais do banco de dados (já estão configuradas para Docker)
- As credenciais do Mercado Pago (Sandbox)

### 7. Execute as migrations

```bash
docker-compose exec app php artisan migrate
```

### 8. Compile os assets (Vue.js)

Para desenvolvimento:

```bash
docker-compose exec node npm run dev
```

Para produção:

```bash
docker-compose exec node npm run build
```

### 9. Acesse a aplicação

Abra seu navegador e acesse:

```
http://localhost:8080
```

## Comandos Úteis

### Parar os containers

```bash
docker-compose down
```

### Ver logs

```bash
docker-compose logs -f app
docker-compose logs -f nginx
docker-compose logs -f db
```

### Acessar o container PHP

```bash
docker-compose exec app bash
```

### Acessar o container Node

```bash
docker-compose exec node sh
```

### Limpar tudo e recomeçar

```bash
docker-compose down -v
docker-compose up -d
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec node npm install
docker-compose exec app php artisan migrate
```

## Troubleshooting

### Erro: "Docker não está rodando"

Certifique-se de que o Docker Desktop está iniciado e aguarde alguns segundos antes de executar os comandos.

### Erro: "Porta 8080 já está em uso"

Altere a porta no arquivo `docker-compose.yml`:

```yaml
nginx:
  ports:
    - "8081:80"  # Altere 8080 para 8081
```

E atualize o `APP_URL` no `.env`:

```
APP_URL=http://localhost:8081
```

### Erro: "Composer install falhou"

Tente executar novamente ou limpe o cache:

```bash
docker-compose exec app composer clear-cache
docker-compose exec app composer install
```

### Erro: "npm install falhou"

Tente executar novamente ou limpe o cache:

```bash
docker-compose exec node npm cache clean --force
docker-compose exec node npm install
```
