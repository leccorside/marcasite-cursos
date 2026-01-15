# 🔧 Correção: Erro de CORS do Vite

## ⚠️ Problema
Erros no console:
- `Requisição cross-origin bloqueada`
- `http://[::1]:5174/@vite/client` (IPv6 não está funcionando)

## ✅ Solução Aplicada

O arquivo `vite.config.js` foi atualizado para:
- Usar IPv4 (`0.0.0.0`) em vez de IPv6
- Configurar HMR (Hot Module Replacement) corretamente
- Permitir conexões do Laravel

## 🔄 Próximos Passos

### 1. Reinicie o Vite

**Pare o Vite atual** (se estiver rodando):
- No terminal onde o Vite está rodando, pressione: **Ctrl + C**

**Inicie novamente:**
```powershell
docker-compose exec node npm run dev
```

### 2. Aguarde a mensagem de sucesso

Você deve ver:
```
VITE v5.x.x  ready in xxx ms

➜  Local:   http://localhost:5173/
➜  Network: use --host to expose
➜  press h + enter to show help
```

### 3. Limpe o cache do navegador

Pressione: **Ctrl + Shift + R** (hard refresh)

### 4. Acesse novamente

```
http://localhost:8080
```

## 🔍 Verificar se Funcionou

1. Abra o console do navegador (F12)
2. Vá na aba **Network** (Rede)
3. Recarregue a página (F5)
4. Procure por requisições para:
   - `@vite/client`
   - `app.js`
   - `app.css`

Todos devem ter status **200 OK** (verde).

## 📝 Notas

- O Vite agora está configurado para aceitar conexões do Laravel
- O servidor está escutando em `0.0.0.0:5173` (IPv4)
- HMR (Hot Module Replacement) está ativo para desenvolvimento

## ❓ Ainda com Problemas?

Se ainda houver erros:

1. **Verifique se o Vite está realmente rodando:**
   ```powershell
   docker-compose logs node -f
   ```

2. **Verifique a porta 5173:**
   O Vite deve estar rodando na porta 5173

3. **Reinstale dependências (se necessário):**
   ```powershell
   docker-compose exec node npm install
   docker-compose exec node npm run dev
   ```
