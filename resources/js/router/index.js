import { createRouter, createWebHistory } from 'vue-router';
import AdminLayout from '@/layouts/AdminLayout.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { useAuth } from '@/composables/useAuth';

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
  // Rotas Públicas
  {
    path: '/',
    component: PublicLayout,
    children: [
      {
        path: '',
        name: 'home',
        component: () => import('@/views/public/Home.vue')
      },
      {
        path: 'vitrine',
        name: 'vitrine',
        component: () => import('@/views/public/Vitrine.vue')
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

// Navigation guards
router.beforeEach(async (to, from, next) => {
  const auth = useAuth();

  // Verificar autenticação se necessário
  if (to.meta.requiresAuth || to.meta.requiresAdmin) {
    await auth.checkAuth();
  }

  // Verificar se precisa estar autenticado
  if (to.meta.requiresAuth && !auth.isAuthenticated.value) {
    next({ name: 'login', query: { redirect: to.fullPath } });
    return;
  }

  // Verificar se precisa ser admin
  if (to.meta.requiresAdmin && !auth.isAdmin.value) {
    next({ name: 'home' });
    return;
  }

  // Verificar se precisa estar desautenticado (guest)
  if (to.meta.requiresGuest && auth.isAuthenticated.value) {
    // Se já estiver autenticado, redirecionar conforme tipo
    if (auth.user.value?.tipo === 'admin') {
      next({ name: 'admin.dashboard' });
    } else {
      next({ name: 'home' });
    }
    return;
  }

  next();
});

export default router;
