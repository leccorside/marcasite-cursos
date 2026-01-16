import { createRouter, createWebHistory } from 'vue-router';
import AdminLayout from '@/layouts/AdminLayout.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { useAuth } from '@/composables/useAuth';
import { useLoading } from '@/composables/useLoading';

const routes = [
  // Rotas de Autenticação (sem layout)
  {
    path: '/login',
    name: 'login',
    component: () => import('@/views/auth/Login.vue'),
    meta: { requiresGuest: true }
  },
  {
    path: '/register',
    name: 'register',
    component: () => import('@/views/auth/Register.vue'),
    meta: { requiresGuest: true }
  },
  {
    path: '/forgot-password',
    name: 'forgot-password',
    component: () => import('@/views/auth/ForgotPassword.vue'),
    meta: { requiresGuest: true }
  },
  {
    path: '/reset-password',
    name: 'reset-password',
    component: () => import('@/views/auth/ResetPassword.vue'),
    meta: { requiresGuest: true }
  },
  // Rotas Públicas (todos os usuários autenticados podem acessar)
  {
    path: '/',
    component: PublicLayout,
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        name: 'home',
        component: () => import('@/views/public/Home.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: 'vitrine',
        name: 'vitrine',
        component: () => import('@/views/public/Vitrine.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: 'inscricao/:id',
        name: 'public.inscricao',
        component: () => import('@/views/public/Inscricao.vue'),
        meta: { requiresAuth: true },
        props: true
      },
      {
        path: 'meus-cursos',
        name: 'meus-cursos',
        component: () => import('@/views/public/MeusCursos.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: 'configuracoes',
        name: 'configuracoes',
        component: () => import('@/views/public/Configuracoes.vue'),
        meta: { requiresAuth: true }
      }
    ]
  },
  // Rotas Administrativas
  {
    path: '/admin',
    component: AdminLayout,
    meta: { requiresAuth: true, requiresAdmin: true },
    children: [
      {
        path: '',
        name: 'admin.dashboard',
        component: () => import('@/views/admin/Dashboard.vue')
      },
      {
        path: 'cursos',
        name: 'admin.cursos',
        component: () => import('@/views/admin/Cursos.vue')
      },
      {
        path: 'usuarios',
        name: 'admin.usuarios',
        component: () => import('@/views/admin/Usuarios.vue')
      },
      {
        path: 'configuracoes',
        name: 'admin.configuracoes',
        component: () => import('@/views/admin/Configuracoes.vue')
      }
    ]
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Instância do loading para uso nos guards
const { startLoading, stopLoading } = useLoading();

// Navigation guards
router.beforeEach(async (to, from, next) => {
  try {
    const auth = useAuth();

    // Iniciar loading apenas se estiver mudando de rota
    // A verificação funciona mesmo na primeira navegação (from.name será undefined)
    if (!from.name || to.path !== from.path) {
      startLoading();
    }

    // Verificar autenticação apenas se necessário
    // Se já está autenticado e não precisa verificar admin, usa cache
    // Se não está autenticado ou precisa verificar admin, força verificação
    if (!auth.isAuthenticated.value || to.meta.requiresAdmin) {
      await auth.checkAuth();
    } else {
      // Verificação leve usando cache (sem fazer requisição HTTP)
      await auth.checkAuth(false);
    }

    // Verificar se a rota é apenas para convidados (não autenticados)
    if (to.meta.requiresGuest) {
      // Se já estiver autenticado, redirecionar conforme tipo
      if (auth.isAuthenticated.value) {
        if (auth.user.value?.tipo === 'admin') {
          next({ name: 'admin.dashboard' });
        } else {
          next({ name: 'home' });
        }
        return;
      }
      // Se não estiver autenticado, permitir acesso
      next();
      return;
    }

    // POR PADRÃO: Todas as rotas que não são de guest exigem autenticação
    // Verificar se precisa estar autenticado (padrão para todas as rotas)
    if (!auth.isAuthenticated.value) {
      // Redirecionar para login, mantendo a rota desejada para redirecionamento após login
      next({ name: 'login', query: { redirect: to.fullPath } });
      return;
    }

    // Verificar se precisa ser admin (para rotas administrativas)
    if (to.meta.requiresAdmin && !auth.isAdmin.value) {
      // Se não for admin, redirecionar para home
      next({ name: 'home' });
      return;
    }

    // Todas as verificações passaram, permitir acesso
    next();
  } catch (error) {
    // Em caso de erro, parar o loading e rejeitar a navegação
    stopLoading();
    console.error('Erro na navegação:', error);
    next(false);
  }
});

// Guard para parar o loading após a transição
router.afterEach(() => {
  // Aguardar um pequeno delay para garantir que o componente foi montado
  // Delay reduzido para resposta mais rápida
  setTimeout(() => {
    stopLoading();
  }, 50);
});

// Guard para parar o loading em caso de erro na navegação
router.onError((error) => {
  stopLoading();
  console.error('Erro na navegação da rota:', error);
});

export default router;
