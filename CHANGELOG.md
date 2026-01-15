# Changelog - Marcasite Cursos

## Fase 1: Configuração Inicial e Ambiente ✅

### Concluído

- ✅ Configuração do Docker (docker-compose.yml)
  - PHP 8.2-fpm
  - MySQL 8.0
  - Nginx
  - Node.js 18

- ✅ Dockerfile configurado com extensões PHP necessárias
- ✅ Configurações do Nginx, PHP e MySQL
- ✅ Estrutura básica do Laravel 11 criada
- ✅ Vue.js 3 configurado com Vite
- ✅ Arquivos .env e .env.example criados
- ✅ Estrutura de pastas criada
- ✅ Git inicializado e repositório remoto configurado
- ✅ Middlewares básicos criados
- ✅ README.md e INSTALL.md criados

## Fase 2: Estrutura de Banco de Dados ✅

### Concluído

- ✅ Migrations criadas:
  - `users` - Tabela de usuários (admin/aluno)
  - `password_reset_tokens` - Tokens de recuperação de senha
  - `sessions` - Sessões de usuários
  - `cursos` - Tabela de cursos com vagas, valores e período de inscrição
  - `alunos` - Dados completos dos alunos
  - `inscricoes` - Inscrições de alunos em cursos
  - `pagamentos` - Registros de pagamentos do Mercado Pago
  - `curso_materiais` - Materiais/arquivos dos cursos

- ✅ Models criados com relacionamentos Eloquent:
  - `User` - Com relacionamento para Aluno e métodos auxiliares
  - `Curso` - Com relacionamentos para Inscrições e Materiais, scopes e métodos de negócio
  - `Aluno` - Com relacionamentos para User e Inscrições
  - `Inscricao` - Com relacionamentos para Aluno, Curso e Pagamentos
  - `Pagamento` - Com relacionamento para Inscricao
  - `CursoMaterial` - Com relacionamento para Curso

- ✅ Seeders criados:
  - `DatabaseSeeder` - Orquestra todos os seeders
  - `UserSeeder` - Cria usuários admin e alunos
  - `CursoSeeder` - Cria cursos de exemplo (UX, PHP, Photoshop, Illustrator, Laravel, Vue.js)
  - `AlunoSeeder` - Cria alunos associados aos usuários
  - `InscricaoSeeder` - Cria inscrições com diferentes status e pagamentos

- ✅ Factories criadas:
  - `UserFactory` - Para gerar usuários de teste
  - `AlunoFactory` - Para gerar alunos de teste com dados realistas

## Fase 3: Área Administrativa - Layout e Visual (Em Andamento) ✅

### Concluído

- ✅ Layout Administrativo criado:
  - `AdminLayout.vue` - Layout base com sidebar e header
  - `Sidebar.vue` - Menu lateral com navegação
  - `Header.vue` - Cabeçalho com busca, notificações e perfil
  - Design responsivo para mobile e desktop

- ✅ Layout Público criado:
  - `PublicLayout.vue` - Layout para área pública
  - `PublicSidebar.vue` - Menu lateral público
  - `PublicHeader.vue` - Cabeçalho público

- ✅ Componentes Vue.js:
  - Estrutura completa de componentes baseada nos mockups
  - Ícones SVG inline
  - Navegação ativa destacada

- ✅ Páginas criadas:
  - Dashboard administrativo
  - Listagem de Cursos (admin)
  - Listagem de Usuários (admin)
  - Vitrine de Cursos (pública)
  - Meus Cursos (pública)
  - Páginas de configurações

- ✅ Router configurado:
  - Rotas públicas e administrativas
  - Lazy loading de componentes
  - Nested routes com layouts

- ✅ Estilos:
  - Tailwind CSS configurado
  - Design fiel aos mockups fornecidos
  - Cores e espaçamentos consistentes

### Próximos Passos

- Fase 3 (Continuação): Autenticação
  - Implementar sistema de autenticação Laravel Sanctum
  - Criar páginas de login e registro
  - Configurar middleware de autenticação para rotas protegidas
