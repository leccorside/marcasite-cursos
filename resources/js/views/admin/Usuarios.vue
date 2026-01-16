<template>
  <div class="p-4 md:p-8">
    <!-- Topo: Busca e Título -->
    <div class="flex flex-col gap-4 mb-10">
      <div class="flex justify-between items-center w-full">
        <h1 class="text-2xl md:text-3xl font-black text-black uppercase tracking-tighter">Usuários</h1>
        
        <!-- Botão Novo (Mockup por enquanto) -->
        <button
          class="bg-[#333] text-white px-5 md:px-8 py-2.5 rounded-lg font-bold hover:bg-black transition-colors whitespace-nowrap text-sm md:text-base shadow-sm"
        >
          Novo usuário
        </button>
      </div>

      <!-- Busca -->
      <div class="relative w-full max-w-sm">
        <input
          v-model="search"
          type="text"
          placeholder="Buscar por nome ou email..."
          class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-gray-400"
          @input="debounceSearch"
        />
        <div class="absolute left-3 top-2.5 text-gray-400">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
      </div>
    </div>

    <!-- Tabela -->
    <div v-if="loading" class="flex justify-center py-20">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-800"></div>
    </div>

    <div v-else-if="usuarios.length === 0" class="text-center py-20 bg-white rounded-xl shadow-sm border border-gray-100">
      <p class="text-gray-400 text-lg md:text-xl font-medium px-4">Nenhum usuário encontrado.</p>
    </div>

    <div v-else class="overflow-x-auto border border-gray-100 rounded-xl shadow-sm bg-white">
      <table class="min-w-full">
        <thead>
          <tr class="text-left bg-gray-50/50 border-b border-gray-100">
            <th class="p-4 md:p-6 text-[10px] md:text-sm font-black text-black uppercase tracking-widest border-r border-gray-100">Nome</th>
            <th class="p-4 md:p-6 text-[10px] md:text-sm font-black text-black uppercase tracking-widest border-r border-gray-100">Email</th>
            <th class="p-4 md:p-6 text-[10px] md:text-sm font-black text-black uppercase tracking-widest border-r border-gray-100 text-center">Tipo</th>
            <th class="p-4 md:p-6 text-[10px] md:text-sm font-black text-black uppercase tracking-widest border-r border-gray-100 text-center">Ativo</th>
            <th class="p-4 md:p-6 text-[10px] md:text-sm font-black text-black uppercase tracking-widest text-right">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <tr v-for="usuario in usuarios" :key="usuario.id" class="hover:bg-gray-50/50 transition-colors">
            <td class="p-4 md:p-6 text-xs md:text-sm font-bold text-gray-900 border-r border-gray-50">{{ usuario.name }}</td>
            <td class="p-4 md:p-6 text-xs md:text-sm font-medium text-gray-500 border-r border-gray-50">{{ usuario.email }}</td>
            <td class="p-4 md:p-6 text-xs md:text-sm font-medium text-gray-900 text-center border-r border-gray-50">
              <span :class="[
                'px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider',
                usuario.tipo === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700'
              ]">
                {{ usuario.tipo }}
              </span>
            </td>
            <td class="p-4 md:p-6 text-xs md:text-sm font-medium text-gray-900 text-center border-r border-gray-50">{{ usuario.ativo ? 'Sim' : 'Não' }}</td>
            <td class="p-4 md:p-6 text-right">
              <div class="flex justify-end gap-4">
                <button class="text-black hover:opacity-60 transition-opacity" title="Editar">
                  <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Paginação -->
    <div v-if="totalPages > 1" class="mt-16 flex justify-center items-center gap-2">
      <button
        v-for="page in totalPages"
        :key="page"
        @click="irParaPagina(page)"
        :class="[
          'w-10 h-10 flex items-center justify-center rounded-lg font-bold text-sm transition-colors',
          page === currentPage ? 'bg-[#333] text-white' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'
        ]"
      >
        {{ page }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { userService } from '@/services/user';

const usuarios = ref([]);
const loading = ref(true);
const search = ref('');
const currentPage = ref(1);
const total = ref(0);
const perPage = 9;
const totalPages = ref(1);
let searchTimeout = null;

const carregarUsuarios = async () => {
  loading.value = true;
  try {
    const result = await userService.listar({
      page: currentPage.value,
      search: search.value,
      per_page: perPage
    });
    if (result.success) {
      usuarios.value = result.data.data;
      total.value = result.data.total;
      totalPages.value = result.data.last_page;
    }
  } finally {
    loading.value = false;
  }
};

const debounceSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    currentPage.value = 1;
    carregarUsuarios();
  }, 500);
};

const irParaPagina = (p) => {
  currentPage.value = p;
  carregarUsuarios();
};

onMounted(carregarUsuarios);
</script>
