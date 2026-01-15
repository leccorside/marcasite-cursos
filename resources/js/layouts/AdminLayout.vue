<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Sidebar -->
    <Sidebar :is-open="sidebarOpen" @close="sidebarOpen = false" />
    
    <!-- Main Content -->
    <div :class="[
      'transition-all duration-300 min-h-screen',
      sidebarOpen ? 'ml-64 lg:ml-64' : 'ml-0'
    ]">
      <!-- Header -->
      <Header @toggle-sidebar="sidebarOpen = !sidebarOpen" />
      
      <!-- Page Content -->
      <main class="p-6">
        <slot />
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
import { ref } from 'vue';
import Sidebar from '@/components/admin/Sidebar.vue';
import Header from '@/components/admin/Header.vue';

const sidebarOpen = ref(true);
</script>
