<template>
  <div class="p-4 md:p-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
      <h1 class="text-2xl md:text-3xl font-black text-black uppercase tracking-tighter">Vitrine de Cursos</h1>
      
      <div class="flex flex-col sm:flex-row gap-4 w-full md:max-w-2xl">
        <!-- Filtro Categoria -->
        <div class="relative w-full sm:w-48 flex-shrink-0">
          <select
            v-model="categoriaSelecionada"
            class="w-full pl-4 pr-10 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-1 focus:ring-gray-400 bg-white appearance-none font-bold text-sm text-gray-700"
            @change="filtrarPorCategoria"
          >
            <option value="">Todas Categorias</option>
            <option v-for="cat in categorias" :key="cat" :value="cat">{{ cat }}</option>
          </select>
          <div class="absolute right-3 top-3.5 pointer-events-none text-gray-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </div>
        </div>

        <!-- Search Bar -->
        <div class="relative flex-1">
          <input
            v-model="search"
            type="text"
            placeholder="O que você deseja aprender?"
            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-1 focus:ring-gray-400 bg-white"
            @input="debounceSearch"
          />
          <svg
            class="absolute left-3 top-3 w-5 h-5 text-gray-400"
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
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-20">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-800"></div>
    </div>

    <!-- Empty State -->
    <div v-else-if="cursos.length === 0" class="text-center py-20 bg-white rounded-2xl shadow-sm border border-gray-100">
      <svg class="mx-auto h-16 w-16 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
      </svg>
      <p class="text-gray-400 text-xl font-medium">Nenhum curso encontrado no momento.</p>
    </div>

    <!-- Cursos Grid -->
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="curso in cursos"
        :key="curso.id"
        class="bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 flex flex-col group"
      >
        <!-- Thumbnail -->
        <div class="relative h-48 bg-gray-50 overflow-hidden">
          <img 
            v-if="curso.thumbnail" 
            :src="`/storage/${curso.thumbnail}`" 
            :alt="curso.nome"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
          />
          <div v-else class="w-full h-full flex items-center justify-center">
            <svg class="w-16 h-16 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </div>
          <!-- Categoria Badge -->
          <div class="absolute top-3 left-3">
            <span class="px-3 py-1 bg-black/70 backdrop-blur-sm text-white text-[10px] font-black uppercase tracking-widest rounded-full">
              {{ curso.categoria }}
            </span>
          </div>
        </div>
        
        <!-- Content -->
        <div class="p-5 flex-1 flex flex-col">
          <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2 min-h-[3.5rem]">{{ curso.nome }}</h3>
          
          <div class="mt-auto">
            <p class="text-xs text-gray-500 mb-1">Investimento</p>
            <p class="text-2xl font-black text-black tracking-tighter">{{ formatCurrency(curso.valor) }}</p>
            
            <router-link 
              v-if="isInscricoesAbertas(curso)"
              :to="{ name: 'public.inscricao', params: { id: curso.id } }"
              class="mt-4 w-full bg-black text-white py-3 px-4 rounded-xl font-bold hover:bg-gray-800 transition-colors flex items-center justify-center gap-2"
            >
              <span>Inscrever-se</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
              </svg>
            </router-link>
            
            <div 
              v-else
              class="mt-4 w-full bg-gray-100 text-gray-400 py-3 px-4 rounded-xl font-bold text-center cursor-not-allowed uppercase text-xs tracking-widest border border-gray-200"
            >
              Inscrições encerradas
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="totalPages > 1" class="mt-12 flex justify-center items-center gap-2">
      <button
        v-for="page in totalPages"
        :key="page"
        @click="irParaPagina(page)"
        :class="[
          'w-10 h-10 flex items-center justify-center rounded-xl font-bold text-sm transition-all',
          page === currentPage ? 'bg-black text-white' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'
        ]"
      >
        {{ page }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { cursoService } from '@/services/curso';

const cursos = ref([]);
const categorias = ref([]);
const categoriaSelecionada = ref('');
const loading = ref(true);
const search = ref('');
const currentPage = ref(1);
const totalPages = ref(1);
let searchTimeout = null;

const carregarCursos = async () => {
  loading.value = true;
  try {
    const result = await cursoService.listarPublicos({
      page: currentPage.value,
      search: search.value,
      categoria: categoriaSelecionada.value,
      per_page: 9
    });
    if (result.success) {
      cursos.value = result.data.data;
      totalPages.value = result.data.last_page;
    }
  } finally {
    loading.value = false;
  }
};

const carregarCategorias = async () => {
  const result = await cursoService.listarCategorias();
  if (result.success) {
    categorias.value = result.data;
  }
};

const filtrarPorCategoria = () => {
  currentPage.value = 1;
  carregarCursos();
};

const debounceSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    currentPage.value = 1;
    carregarCursos();
  }, 500);
};

const irParaPagina = (p) => {
  currentPage.value = p;
  carregarCursos();
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

const isInscricoesAbertas = (curso) => {
  if (curso.vagas_disponiveis <= 0) return false;
  if (!curso.data_fim_inscricoes) return true;

  const hoje = new Date();
  hoje.setHours(0, 0, 0, 0);

  // A data do curso vem como YYYY-MM-DD
  // Criamos o objeto Date e ajustamos para o final do dia
  const dataFim = new Date(curso.data_fim_inscricoes + 'T23:59:59');
  
  return hoje <= dataFim;
};

const formatCurrency = (value) => {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  }).format(value);
};

onMounted(() => {
  carregarCursos();
  carregarCategorias();
});
</script>
