<template>
  <aside 
    :class="[
      'fixed left-0 top-0 h-full bg-gray-200 z-50 transition-transform duration-300',
      isOpen ? 'translate-x-0' : '-translate-x-full',
      'w-64',
      'lg:translate-x-0'
    ]"
  >
    <div class="h-full flex flex-col">
      <!-- Logo/Header -->
      <div class="p-4 flex items-center justify-between border-b border-gray-300">
        <div class="flex items-center gap-2">
          <button 
            @click="emit('close')"
            class="text-gray-600 hover:text-gray-900 lg:hidden"
            aria-label="Fechar menu"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
          <span class="text-xl font-semibold text-gray-700">@ Marcasite</span>
        </div>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 p-4 space-y-2">
        <router-link
          v-for="item in menuItems"
          :key="item.path"
          :to="item.path"
          :class="[
            'flex items-center gap-3 px-4 py-3 rounded-lg transition-colors',
            isActive(item.path) 
              ? 'bg-gray-300 text-gray-900' 
              : 'text-gray-700 hover:bg-gray-250'
          ]"
          @click="handleMenuClick"
        >
          <!-- Dashboard Icon -->
          <svg v-if="item.icon === 'dashboard'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
          <!-- Meus Cursos Icon -->
          <svg v-else-if="item.icon === 'cursos'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          <!-- Vitrine Icon -->
          <svg v-else-if="item.icon === 'vitrine'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
          </svg>
          <!-- Configurações Icon -->
          <svg v-else-if="item.icon === 'configuracoes'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          <span class="font-medium">{{ item.label }}</span>
        </router-link>
      </nav>
    </div>
  </aside>
</template>

<script setup>
import { useRoute } from 'vue-router';

defineProps({
  isOpen: {
    type: Boolean,
    default: true
  }
});

const route = useRoute();

const menuItems = [
  {
    label: 'Dashboard',
    path: '/',
    icon: 'dashboard'
  },
  {
    label: 'Meus Cursos',
    path: '/meus-cursos',
    icon: 'cursos'
  },
  {
    label: 'Vitrine de Cursos',
    path: '/vitrine',
    icon: 'vitrine'
  },
  {
    label: 'Configurações',
    path: '/configuracoes',
    icon: 'configuracoes'
  }
];

const isActive = (path) => {
  return route.path === path || route.path.startsWith(path + '/');
};

// Emit para fechar o menu
const emit = defineEmits(['close']);

// Fechar menu no mobile ao clicar em um link
const handleMenuClick = () => {
  // Fecha o menu apenas em telas mobile (menores que lg)
  if (window.innerWidth < 1024) {
    emit('close');
  }
};
</script>
