# Marcasite Cursos - Sistema de Inscrições

Sistema de inscrições para cursos com integração ao Mercado Pago, área administrativa completa e gestão de alunos.

## Tecnologias

- **PHP 8.2+**
- **Laravel 11+**
- **Vue.js 3**
- **MySQL 8.0**
- **Docker**
- **Mercado Pago (Sandbox)**

## Requisitos

- Docker Desktop
- Git

## Instalação

### 1. Clone o repositório

```bash
git clone https://github.com/leccorside/marcasite-cursos.git
cd marcasite-cursos
```

### 2. Configure o ambiente

Copie o arquivo `.env.example` para `.env`:

```bash
cp .env.example .env
```

### 3. Inicie os containers Docker

```bash
docker-compose up -d
```

### 4. Instale as dependências do PHP

```bash
docker-compose exec app composer install
```

### 5. Gere a chave da aplicação

```bash
docker-compose exec app php artisan key:generate
```

### 6. Instale as dependências do Node.js

```bash
docker-compose exec node npm install
```

### 7. Configure o usuário administrador (opcional)

Antes de executar as migrations, você pode configurar os dados do usuário admin padrão no arquivo `.env`:

```env
ADMIN_NAME="Administrador"
ADMIN_EMAIL="admin@marcasite.com.br"
ADMIN_PASSWORD="senha123456"
```

Se não configurar, será usado o padrão:
- **Email:** `admin@marcasite.com.br`
- **Senha:** `password`

### 8. Execute as migrations e seeders

Execute as migrations junto com os seeders para criar o banco de dados e popular com dados iniciais (incluindo o usuário admin):

```bash
docker-compose exec app php artisan migrate --seed
```

> **Nota:** O seeder é idempotente, ou seja, pode ser executado múltiplas vezes sem criar usuários duplicados. O usuário admin será criado apenas se não existir.

### 9. Configure o Mercado Pago

Adicione suas credenciais do Mercado Pago (Sandbox) no arquivo `.env`:

```
MERCADOPAGO_ACCESS_TOKEN=seu_access_token
MERCADOPAGO_PUBLIC_KEY=sua_public_key
MERCADOPAGO_WEBHOOK_SECRET=seu_webhook_secret
```

## Executando o projeto

### Desenvolvimento

```bash
# Iniciar containers
docker-compose up -d

# Compilar assets (Vue.js)
docker-compose exec node npm run dev

# Acessar a aplicação
http://localhost:8080
```

### Comandos importantes para evitar erros locais

```bash
# Cria o diretório Armazena views compiladas do Blade
New-Item -ItemType Directory -Force -Path "storage\framework\views"

# Cria o diretório base de cache do framework
New-Item -ItemType Directory -Force -Path "storage\framework\cache"

# Cria o diretório de cache de dados (configurações, rotas, etc.)
New-Item -ItemType Directory -Force -Path "storage\framework\cache\data"

# Cria o diretório de sessões de usuários (driver file)
New-Item -ItemType Directory -Force -Path "storage\framework\sessions"

# Cria o diretório de arquivos temporários de testes
New-Item -ItemType Directory -Force -Path "storage\framework\testing"

# Limpa o cache de configuração do Laravel
docker-compose exec app php artisan config:clear

# Limpa o cache de views compiladas do Blade
docker-compose exec app php artisan view:clear
```

### Abrindo a url local

```bash
# Acessar a aplicação
http://localhost:8080
```

### Build de produção

```bash
docker-compose exec node npm run build
```

## Estrutura do Projeto

```
marcasite/
├── app/                    # Aplicação Laravel
├── bootstrap/              # Arquivos de inicialização
├── config/                 # Arquivos de configuração
├── database/              # Migrations, seeders, factories
├── docker/                # Configurações Docker
├── public/                # Arquivos públicos
├── resources/             # Views, CSS, JS (Vue.js)
├── routes/                # Rotas da aplicação
├── storage/               # Arquivos de armazenamento
├── tests/                 # Testes automatizados
└── vendor/                # Dependências Composer
```

## Funcionalidades

- ✅ Sistema de autenticação
- ✅ CRUD de cursos
- ✅ Sistema de inscrições
- ✅ Integração com Mercado Pago
- ✅ Área administrativa
- ✅ Busca avançada de inscritos
- ✅ Exportação em Excel e PDF
- ✅ Interface responsiva

## Gerenciamento de Usuários

### Criar usuário administrador manualmente

Você pode criar um usuário admin manualmente usando o comando Artisan:

**Modo interativo:**
```bash
docker-compose exec app php artisan user:create-admin
```

**Modo com parâmetros:**
```bash
docker-compose exec app php artisan user:create-admin --name="Nome Admin" --email="admin@example.com" --password="senha123456"
```

O comando valida os dados automaticamente e cria o usuário com tipo `admin` e status `ativo`.

### Usuário admin padrão

Ao executar `php artisan migrate --seed`, um usuário administrador é criado automaticamente com as credenciais configuradas no arquivo `.env`:

- **Email:** Valor de `ADMIN_EMAIL` (padrão: `admin@marcasite.com.br`)
- **Senha:** Valor de `ADMIN_PASSWORD` (padrão: `password`)
- **Nome:** Valor de `ADMIN_NAME` (padrão: `Administrador`)

> **Importante:** Altere a senha padrão em produção!

## Desenvolvimento

Este projeto segue o padrão MVC e utiliza:

- **Eloquent ORM** para interação com banco de dados
- **Form Requests** para validação
- **Migrations** para versionamento do banco
- **Vue.js 3** com Composition API
- **Vite** para build de assets

## Licença

MIT
