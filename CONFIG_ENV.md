# Configuração do Arquivo .env

## Estrutura Completa do .env

O arquivo `.env` foi configurado com as seguintes variáveis:

### Aplicação
```
APP_NAME="Marcasite Cursos"
APP_ENV=local
APP_KEY=                    # Será gerado automaticamente com: php artisan key:generate
APP_DEBUG=true
APP_TIMEZONE=America/Sao_Paulo
APP_URL=http://localhost:8080
APP_LOCALE=pt_BR
APP_FALLBACK_LOCALE=pt_BR
APP_FAKER_LOCALE=pt_BR
```

### Manutenção
```
APP_MAINTENANCE_DRIVER=file
APP_MAINTENANCE_STORE=database
```

### Segurança
```
BCRYPT_ROUNDS=12
```

### Logs
```
LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug
```

### Banco de Dados (MySQL - Docker)
```
DB_CONNECTION=mysql
DB_HOST=db                  # Nome do container Docker
DB_PORT=3306
DB_DATABASE=marcasite_cursos
DB_USERNAME=marcasite
DB_PASSWORD=root
```

### Sessões
```
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
```

### Filas e Cache
```
BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
CACHE_STORE=database
CACHE_PREFIX=
```

### Redis (Opcional)
```
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### Email
```
MAIL_MAILER=log            # Para desenvolvimento (salva em storage/logs)
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@marcasite.com.br"
MAIL_FROM_NAME="${APP_NAME}"
```

### Mercado Pago (Sandbox)
```
MERCADOPAGO_ACCESS_TOKEN=   # Obtenha em: https://www.mercadopago.com.br/developers/panel/credentials
MERCADOPAGO_PUBLIC_KEY=     # Obtenha em: https://www.mercadopago.com.br/developers/panel/credentials
MERCADOPAGO_WEBHOOK_SECRET= # Configure no painel do Mercado Pago
```

## Como Reconfigurar

Se precisar reconfigurar o arquivo `.env`, execute:

```powershell
.\setup-env.ps1
```

Ou copie o conteúdo do arquivo `ENV_TEMPLATE.txt` para o `.env`.

## Importante

1. **APP_KEY**: Será gerado automaticamente quando você executar:
   ```bash
   docker-compose exec app php artisan key:generate
   ```

2. **Mercado Pago**: Você precisa preencher as credenciais do Mercado Pago Sandbox manualmente no arquivo `.env`.

3. **Banco de Dados**: As configurações estão prontas para funcionar com Docker. Se estiver usando outro ambiente, ajuste `DB_HOST` e `DB_PORT`.
