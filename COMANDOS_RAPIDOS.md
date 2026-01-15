# 🚀 Comandos Rápidos para Testar o Visual

## ⚡ Início Rápido (Copie e Cole)

### 1. Iniciar containers Docker
```powershell
docker-compose up -d
```

### 2. Instalar dependências Node.js
```powershell
docker-compose exec node npm install
```

### 3. Rodar Vite em modo desenvolvimento
```powershell
docker-compose exec node npm run dev
```
**⚠️ IMPORTANTE:** Deixe este terminal aberto! Ele precisa ficar rodando.

### 4. Abrir no navegador
```
http://localhost:8080
```

## 📋 Ordem Completa de Comandos

```powershell
# 1. Subir containers
docker-compose up -d

# 2. Instalar dependências Node
docker-compose exec node npm install

# 3. Rodar Vite (deixar rodando em um terminal)
docker-compose exec node npm run dev

# Em outro terminal, se precisar instalar composer:
docker-compose exec app composer install

# Gerar chave da aplicação (se ainda não fez):
docker-compose exec app php artisan key:generate
```

## 🔍 Verificar se está tudo OK

### Verificar containers rodando
```powershell
docker-compose ps
```

### Ver logs do Vite (se houver erro)
```powershell
docker-compose logs node
```

### Ver logs do Laravel
```powershell
docker-compose logs app
```

## 🛠️ Comandos Úteis

### Parar tudo
```powershell
docker-compose down
```

### Rebuild completo (se houver problemas)
```powershell
docker-compose down
docker-compose up -d --build
docker-compose exec node npm install
docker-compose exec node npm run dev
```

### Limpar cache do npm
```powershell
docker-compose exec node npm cache clean --force
docker-compose exec node npm install
```

## 📱 URLs para Testar

- Home: http://localhost:8080
- Vitrine: http://localhost:8080/vitrine
- Meus Cursos: http://localhost:8080/meus-cursos
- Admin Dashboard: http://localhost:8080/admin
- Admin Cursos: http://localhost:8080/admin/cursos
- Admin Usuários: http://localhost:8080/admin/usuarios
