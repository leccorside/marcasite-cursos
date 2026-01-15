<template>
  <div class="p-6">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Vitrine de Cursos</h1>
    
    <!-- Search Bar -->
    <div class="mb-6 max-w-md">
      <div class="relative">
        <input
          type="text"
          placeholder="Buscar cursos..."
          class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400"
        />
        <svg
          class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
          />
        </svg>
      </div>
    </div>

    <!-- Cursos Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <!-- Card de Curso -->
      <div
        v-for="curso in cursos"
        :key="curso.id"
        class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow"
      >
        <!-- Thumbnail -->
        <div class="h-48 bg-gray-200 flex items-center justify-center">
          <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
        </div>
        
        <!-- Content -->
        <div class="p-4">
          <h3 class="text-xl font-bold text-gray-900 mb-2">{{ curso.nome }}</h3>
          <p class="text-2xl font-bold text-gray-900 mb-4">{{ formatCurrency(curso.valor) }}</p>
          <button class="w-full bg-gray-800 text-white py-2 px-4 rounded-lg hover:bg-gray-900 transition-colors">
            Comprar
          </button>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div class="mt-8 flex justify-center gap-2">
      <button
        v-for="page in 5"
        :key="page"
        :class="[
          'px-4 py-2 rounded-lg transition-colors',
          page === 2
            ? 'bg-gray-800 text-white'
            : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
        ]"
      >
        {{ page }}
      </button>
      <span class="px-4 py-2 text-gray-700">...</span>
      <button class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
        20
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const cursos = ref([
  { id: 1, nome: 'Curso de PHP', valor: 1250.00 },
  { id: 2, nome: 'Curso de Illustrator', valor: 2400.00 },
  { id: 3, nome: 'Curso de UX', valor: 1250.00 },
  { id: 4, nome: 'Curso de Photoshop', valor: 1099.00 },
  { id: 5, nome: 'Curso de Laravel', valor: 1899.00 },
  { id: 6, nome: 'Curso de Vue.js', valor: 1599.00 }
]);

const formatCurrency = (value) => {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  }).format(value);
};
</script>
