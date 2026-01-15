import { createRouter, createWebHistory } from 'vue-router';
import AdminLayout from '@/layouts/AdminLayout.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';

const routes = [
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
        component: () => import('@/views/public/MeusCursos.vue')
      },
      {
        path: 'configuracoes',
        name: 'configuracoes',
        component: () => import('@/views/public/Configuracoes.vue')
      }
    ]
  },
  // Rotas Administrativas
  {
    path: '/admin',
    component: AdminLayout,
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

export default router;
