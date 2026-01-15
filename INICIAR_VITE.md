# 🚀 Como Iniciar o Vite Corretamente

## ⚠️ Problema Atual
O Vite não está rodando, causando erro `NS_ERROR_CONNECTION_REFUSED` no navegador.

## ✅ Solução Passo a Passo

### 1. Verifique se o docker-compose.yml foi atualizado

O arquivo foi atualizado para expor a porta 5173. Se você modificou o docker-compose.yml, pode precisar recriar o container:

```powershell
docker-compose up -d node
```

### 2. Abra um NOVO terminal PowerShell

**IMPORTANTE:** Não use o terminal onde você já está executando comandos. Abra um **novo terminal**.

### 3. Execute este comando no novo terminal:

```powershell
docker-compose exec node npm run dev
```

### 4. Aguarde a mensagem de sucesso

Você deve ver algo como:

```
VITE v5.x.x  ready in xxx ms

➜  Local:   http://localhost:5173/
➜  Network: http://0.0.0.0:5173/
➜  press h + enter to show help
```

**MANTENHA ESTE TERMINAL ABERTO E RODANDO!**

### 5. Limpe o cache do navegador

No navegador:
- Pressione **Ctrl + Shift + R** (hard refresh)
- Ou feche e abra a aba novamente

### 6. Acesse a aplicação

```
http://localhost:8080
```

## 🔍 Como Verificar se Está Funcionando

### No Terminal do Vite:
Deve mostrar:
- `VITE v5.x.x ready`
- Porta `5173` ativa

### No Console do Navegador (F12):
- **Não deve ter** erros de `NS_ERROR_CONNECTION_REFUSED`
- **Não deve ter** erros de CORS

### Na aba Network (Rede) do DevTools:
- `@vite/client` → Status **200** (verde)
- `app.js` → Status **200** (verde)
- `app.css` → Status **200** (verde)

## ⚠️ Erros Comuns

### Erro: "Cannot find module"
```powershell
# Reinstale as dependências
docker-compose exec node npm install
docker-compose exec node npm run dev
```

### Erro: "Port already in use"
A porta 5173 já está em uso. Pare o processo anterior:
```powershell
# No terminal do Vite, pressione: Ctrl + C
# Depois rode novamente:
docker-compose exec node npm run dev
```

### Erro: "container not running"
```powershell
# Certifique-se que o container está rodando
docker-compose ps
# Se não estiver, inicie:
docker-compose up -d node
```

## 📝 Comandos Úteis

### Ver logs do Vite (se houver erro)
```powershell
docker-compose logs node -f
```

### Reiniciar tudo do zero
```powershell
# Pare tudo
docker-compose down

# Inicie novamente
docker-compose up -d

# Em um NOVO terminal, rode o Vite
docker-compose exec node npm run dev
```

### Verificar se a porta 5173 está aberta
```powershell
# No Windows PowerShell
Test-NetConnection -ComputerName localhost -Port 5173
```

## ✅ Checklist Final

- [ ] Container node está rodando (`docker-compose ps`)
- [ ] Vite está rodando em um terminal separado (`npm run dev`)
- [ ] Porta 5173 está exposta no docker-compose.yml
- [ ] Navegador foi atualizado (Ctrl + Shift + R)
- [ ] Console do navegador não mostra erros de conexão

## 🎯 Resultado Esperado

Quando tudo estiver correto:
- ✅ Página carrega normalmente
- ✅ Layout aparece (sidebar, header)
- ✅ Componentes Vue.js funcionam
- ✅ Sem erros no console
- ✅ Hot reload funciona (mudanças aparecem automaticamente)
