# 🔧 Solução: Página em Branco

## ⚠️ Problema
A página aparece em branco ao acessar `http://localhost:8080`

## ✅ Solução: Rodar o Vite Manualmente

### Passo 1: Abra um novo terminal PowerShell

Não feche o terminal atual. Abra um **novo terminal PowerShell** separado.

### Passo 2: Execute o comando

```powershell
docker-compose exec node npm run dev
```

**IMPORTANTE:** 
- Deixe este terminal **aberto e rodando**
- Você verá mensagens como:
  ```
  VITE v5.x.x  ready in xxx ms
  ➜  Local:   http://localhost:5173/
  ```

### Passo 3: Acesse o navegador

Com o Vite rodando, acesse:
```
http://localhost:8080
```

## 🔍 Verificar Erros no Console do Navegador

1. Abra o navegador (Chrome, Firefox, Edge)
2. Pressione **F12** para abrir o DevTools
3. Vá na aba **Console**
4. Veja se há erros em vermelho

### Erros Comuns e Soluções

#### Erro: "Failed to load module script"
- **Causa:** Vite não está rodando
- **Solução:** Execute `docker-compose exec node npm run dev`

#### Erro: "Cannot find module '@/...'"
- **Causa:** Problema com alias do Vite
- **Solução:** Verifique o `vite.config.js`

#### Erro: "Cannot GET /"
- **Causa:** Problema no servidor Nginx
- **Solução:** Verifique se o container nginx está rodando:
  ```powershell
  docker-compose ps
  ```

## 📋 Checklist Completo

Execute na ordem:

1. ✅ Verificar containers rodando:
   ```powershell
   docker-compose ps
   ```
   Todos devem estar com status "Up"

2. ✅ Verificar se Vite está rodando:
   - Abra novo terminal
   - Execute: `docker-compose exec node npm run dev`
   - Deixe rodando

3. ✅ Limpar cache do navegador:
   - Pressione **Ctrl + Shift + R** (hard refresh)
   - Ou **Ctrl + F5**

4. ✅ Verificar console do navegador:
   - Pressione **F12**
   - Veja a aba **Console** para erros

5. ✅ Acessar a aplicação:
   ```
   http://localhost:8080
   ```

## 🛠️ Comandos Úteis

### Parar o Vite
No terminal onde está rodando, pressione: **Ctrl + C**

### Reiniciar tudo
```powershell
# Parar containers
docker-compose down

# Iniciar containers
docker-compose up -d

# Rodar Vite novamente
docker-compose exec node npm run dev
```

### Ver logs do Vite
```powershell
docker-compose logs node -f
```

### Reinstalar dependências
```powershell
docker-compose exec node npm install
docker-compose exec node npm run dev
```

## 🎯 O Que Esperar Quando Funcionar

Quando tudo estiver correto, você verá:

1. **No terminal do Vite:**
   ```
   VITE v5.x.x  ready in xxx ms
   ➜  Local:   http://localhost:5173/
   ```

2. **No navegador (`http://localhost:8080`):**
   - Layout com sidebar cinza à esquerda
   - Header branco no topo
   - Conteúdo da página carregando

3. **No Console do Navegador (F12):**
   - Nenhum erro em vermelho
   - Apenas mensagens informativas (se houver)

## ❓ Ainda com Problemas?

Se mesmo após seguir todos os passos a página continua em branco:

1. **Verifique o código-fonte da página:**
   - Clique com botão direito na página → "Ver código-fonte"
   - Veja se há algum erro PHP visível

2. **Verifique os logs do Laravel:**
   ```powershell
   docker-compose logs app -f
   ```

3. **Verifique os logs do Nginx:**
   ```powershell
   docker-compose logs nginx -f
   ```

## 📝 Nota Importante

O **Vite precisa estar rodando** durante todo o desenvolvimento. Sem ele, os arquivos Vue.js não são compilados e a página fica em branco.

**Sempre deixe o terminal com `npm run dev` aberto enquanto desenvolve!**
