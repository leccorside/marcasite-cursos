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

### 7. Execute as migrations

```bash
docker-compose exec app php artisan migrate
```

### 8. Configure o Mercado Pago

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

## Desenvolvimento

Este projeto segue o padrão MVC e utiliza:

- **Eloquent ORM** para interação com banco de dados
- **Form Requests** para validação
- **Migrations** para versionamento do banco
- **Vue.js 3** com Composition API
- **Vite** para build de assets

## Licença

MIT
