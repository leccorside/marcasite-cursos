<template>
  <aside 
    :class="[
      'fixed left-0 top-0 h-full bg-gray-200 z-50 transition-transform duration-300',
      isOpen ? 'translate-x-0' : '-translate-x-full',
      'w-64 lg:translate-x-0'
    ]"
  >
    <div class="h-full flex flex-col">
      <!-- Logo/Header -->
      <div class="p-4 flex items-center justify-between border-b border-gray-300">
        <div class="flex items-center gap-2">
          <button 
            @click="$emit('close')"
            class="text-gray-600 hover:text-gray-900"
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
        >
          <!-- Dashboard Icon -->
          <svg v-if="item.icon === 'dashboard'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
          <!-- Cursos Icon -->
          <svg v-else-if="item.icon === 'cursos'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          <!-- Usuários Icon -->
          <svg v-else-if="item.icon === 'usuarios'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
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
import { computed } from 'vue';
import { useRoute } from 'vue-router';

defineProps({
  isOpen: {
    type: Boolean,
    default: true
  }
});

defineEmits(['close']);

const route = useRoute();

const menuItems = [
  {
    label: 'Dashboard',
    path: '/admin',
    icon: 'dashboard'
  },
  {
    label: 'Cursos',
    path: '/admin/cursos',
    icon: 'cursos'
  },
  {
    label: 'Usuários',
    path: '/admin/usuarios',
    icon: 'usuarios'
  },
  {
    label: 'Configurações',
    path: '/admin/configuracoes',
    icon: 'configuracoes'
  }
];

const isActive = (path) => {
  return route.path === path || route.path.startsWith(path + '/');
};
</script>
