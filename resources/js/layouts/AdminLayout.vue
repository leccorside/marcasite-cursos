<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Sidebar -->
    <Sidebar :is-open="sidebarOpen" @close="sidebarOpen = false" />
    
    <!-- Main Content -->
    <div :class="[
      'transition-all duration-300 min-h-screen',
      sidebarOpen ? 'ml-0 lg:ml-64' : 'ml-0'
    ]">
      <!-- Header -->
      <Header @toggle-sidebar="sidebarOpen = !sidebarOpen" />
      
      <!-- Page Content -->
      <main class="p-1">
        <router-view />
      </main>
    </div>
    
    <!-- Overlay para mobile -->
    <div
      v-if="sidebarOpen"
      class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"
      @click="sidebarOpen = false"
    ></div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import Sidebar from '@/components/admin/Sidebar.vue';
import Header from '@/components/admin/Header.vue';

// No mobile, começa fechado. No desktop (lg), começa aberto
const sidebarOpen = ref(window.innerWidth >= 1024);

// Atualizar estado baseado no tamanho da tela
const handleResize = () => {
  if (window.innerWidth >= 1024) {
    sidebarOpen.value = true;
  } else {
    sidebarOpen.value = false;
  }
};

onMounted(() => {
  window.addEventListener('resize', handleResize);
  handleResize(); // Chamar uma vez para definir o estado inicial
});

onUnmounted(() => {
  window.removeEventListener('resize', handleResize);
});
</script>
