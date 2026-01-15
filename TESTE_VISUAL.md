# 🎨 Guia de Teste Visual - Marcasite Cursos

## ⚡ Início Rápido

### 1. Certifique-se que o Docker está rodando
```powershell
# Verificar se Docker Desktop está ativo
```

### 2. Inicie os containers (se ainda não iniciou)
```powershell
docker-compose up -d
```

### 3. Instale as dependências do Node.js
```powershell
docker-compose exec node npm install
```

### 4. Compile os assets em modo desenvolvimento
```powershell
docker-compose exec node npm run dev
```

**Importante:** Deixe este comando rodando em um terminal separado. Ele irá compilar os arquivos Vue.js e CSS em tempo real.

### 5. Acesse no navegador
```
http://localhost:8080
```

## 📍 Páginas para Testar

### Área Pública
- **Dashboard:** `http://localhost:8080/`
- **Vitrine de Cursos:** `http://localhost:8080/vitrine`
- **Meus Cursos:** `http://localhost:8080/meus-cursos`
- **Configurações:** `http://localhost:8080/configuracoes`

### Área Administrativa
- **Dashboard Admin:** `http://localhost:8080/admin`
- **Gerenciar Cursos:** `http://localhost:8080/admin/cursos`
- **Gerenciar Usuários:** `http://localhost:8080/admin/usuarios`
- **Configurações Admin:** `http://localhost:8080/admin/configuracoes`

## ✅ Checklist de Teste Visual

### Layout Geral
- [ ] Sidebar aparece corretamente no lado esquerdo
- [ ] Header aparece no topo com busca e perfil
- [ ] Layout é responsivo (teste redimensionando a janela)
- [ ] Menu lateral fecha em mobile ao clicar no X
- [ ] Overlay escuro aparece em mobile quando sidebar está aberta

### Navegação
- [ ] Links do menu funcionam (mudam de página)
- [ ] Item ativo no menu é destacado (fundo cinza)
- [ ] Ícones aparecem corretamente ao lado de cada item do menu

### Área Administrativa - Cursos
- [ ] Tabela de cursos aparece com dados
- [ ] Botão "Novo curso" aparece no topo direito
- [ ] Ações (ver inscritos, editar, excluir) aparecem na última coluna
- [ ] Paginação aparece na parte inferior

### Área Administrativa - Usuários
- [ ] Tabela de usuários aparece
- [ ] Barra de busca aparece acima da tabela
- [ ] Botão "Novo usuário" aparece
- [ ] Ações (editar, excluir) funcionam visualmente

### Área Pública - Vitrine
- [ ] Grid de cards de cursos aparece
- [ ] Cada card tem thumbnail, nome, preço e botão "Comprar"
- [ ] Paginação aparece na parte inferior
- [ ] Barra de busca aparece no topo

### Área Pública - Meus Cursos
- [ ] Tabela de inscrições aparece
- [ ] Status aparece com cores (verde para "Pago", vermelho para "Cancelado")
- [ ] Ícone de "ver" aparece na última coluna
- [ ] Paginação aparece

### Cores e Estilos
- [ ] Background cinza claro (#f9fafb)
- [ ] Sidebar cinza médio (#e5e7eb)
- [ ] Item ativo do menu com fundo cinza escuro (#d1d5db)
- [ ] Botões com fundo cinza escuro (#1f2937)
- [ ] Hover nos botões funciona

## 🔧 Solução de Problemas

### Erro: "Cannot find module"
```powershell
# Reinstale as dependências
docker-compose exec node npm install
```

### Erro: "Vite connection lost"
```powershell
# Pare o processo atual (Ctrl+C) e rode novamente
docker-compose exec node npm run dev
```

### Página em branco
1. Verifique se o `npm run dev` está rodando
2. Verifique o console do navegador (F12) para erros
3. Limpe o cache do navegador (Ctrl+Shift+R)

### Estilos não aparecem
```powershell
# Force a recompilação
docker-compose exec node npm run build
# Depois rode novamente em dev
docker-compose exec node npm run dev
```

### Porta 8080 já em uso
Edite o `docker-compose.yml` e mude a porta:
```yaml
nginx:
  ports:
    - "8081:80"  # Mude de 8080 para 8081
```
E atualize o `APP_URL` no `.env`:
```
APP_URL=http://localhost:8081
```

## 📝 Notas

- Os dados exibidos são **mockados** (falsos) apenas para visualização
- As funcionalidades de CRUD ainda não estão conectadas ao backend
- A autenticação será implementada na próxima fase
- Todos os botões e links de ação são apenas visuais por enquanto

## 🎯 Próximos Passos Após Testar

1. Implementar autenticação (login/registro)
2. Conectar APIs do backend às páginas
3. Implementar CRUD completo
4. Adicionar validações e mensagens de erro
5. Integrar Mercado Pago
