<template>
  <div class="p-4 md:p-8">
    <!-- Topo: Busca e Botão Novo -->
    <div class="flex flex-col gap-4 mb-10">
      <div class="flex justify-between items-center w-full">
        <!-- Lupa (Mobile) e Busca (Desktop) -->
        <div class="flex items-center">
          <button 
            @click="showMobileSearch = !showMobileSearch"
            class="md:hidden p-2.5 text-gray-600 hover:text-gray-900 focus:outline-none border border-gray-300 rounded-lg bg-white shadow-sm"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </button>
          
          <!-- Busca Desktop -->
          <div class="hidden md:block relative w-full max-w-sm">
            <input
              v-model="search"
              type="text"
              placeholder="Buscar curso..."
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

        <!-- Botão Novo -->
        <button
          @click="abrirModalCriar"
          class="bg-[#333] text-white px-5 md:px-8 py-2.5 rounded-lg font-bold hover:bg-black transition-colors whitespace-nowrap text-sm md:text-base shadow-sm"
        >
          Novo curso
        </button>
      </div>

      <!-- Busca Mobile (Aparece abaixo quando clicado) -->
      <transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0 -translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-2"
      >
        <div v-if="showMobileSearch" class="md:hidden relative w-full">
          <input
            ref="mobileSearchInput"
            v-model="search"
            type="text"
            placeholder="O que você procura?"
            class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-gray-400 bg-white shadow-md"
            @input="debounceSearch"
          />
          <div class="absolute left-3 top-3.5 text-gray-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
          <button 
            @click="showMobileSearch = false"
            class="absolute right-3 top-3.5 text-gray-400 hover:text-gray-600"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </transition>
    </div>

    <!-- Feedback -->
    <div v-if="mensagem" :class="['mb-6 p-4 rounded-lg font-medium text-sm', mensagemTipo === 'sucesso' ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-red-50 text-red-800 border border-red-200']">
      {{ mensagem }}
    </div>

    <!-- Tabela -->
    <div v-if="loading" class="flex justify-center py-20">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-800"></div>
    </div>

    <div v-else-if="cursos.length === 0 && !search" class="text-center py-20 bg-white rounded-xl shadow-sm border border-gray-100">
      <p class="text-gray-400 text-lg md:text-xl font-medium px-4">Nenhum curso cadastrado ainda.</p>
    </div>

    <div v-else class="overflow-x-auto border border-gray-100 rounded-xl shadow-sm bg-white -mx-1 md:mx-0">
      <table class="min-w-full">
        <thead>
          <tr class="text-left bg-gray-50/50 border-b border-gray-100">
            <th class="p-4 md:p-6 text-[10px] md:text-sm font-black text-black uppercase tracking-widest border-r border-gray-100">Nome</th>
            <th class="p-4 md:p-6 text-[10px] md:text-sm font-black text-black uppercase tracking-widest border-r border-gray-100">Valor</th>
            <th class="hidden sm:table-cell p-4 md:p-6 text-[10px] md:text-sm font-black text-black uppercase tracking-widest text-center border-r border-gray-100">Ativo</th>
            <th class="hidden lg:table-cell p-4 md:p-6 text-[10px] md:text-sm font-black text-black uppercase tracking-widest text-center border-r border-gray-100">Inscrição</th>
            <th class="hidden sm:table-cell p-4 md:p-6 text-[10px] md:text-sm font-black text-black uppercase tracking-widest text-center border-r border-gray-100">Vagas</th>
            <th class="p-4 md:p-6 text-[10px] md:text-sm font-black text-black uppercase tracking-widest text-right">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <tr v-for="curso in cursos" :key="curso.id" class="hover:bg-gray-50/50 transition-colors">
            <td class="p-4 md:p-6 text-xs md:text-sm font-bold text-gray-900 border-r border-gray-50">{{ curso.nome }}</td>
            <td class="p-4 md:p-6 text-xs md:text-sm font-bold text-gray-900 border-r border-gray-50 whitespace-nowrap">{{ formatCurrency(curso.valor) }}</td>
            <td class="hidden sm:table-cell p-4 md:p-6 text-xs md:text-sm font-medium text-gray-900 text-center border-r border-gray-50">{{ curso.ativo ? 'Sim' : 'Não' }}</td>
            <td class="hidden lg:table-cell p-4 md:p-6 text-xs md:text-sm font-medium text-gray-900 text-center border-r border-gray-50">
              {{ formatPeriodo(curso.data_inicio_inscricoes, curso.data_fim_inscricoes) }}
            </td>
            <td class="hidden sm:table-cell p-4 md:p-6 text-xs md:text-sm font-medium text-gray-900 text-center border-r border-gray-50">{{ curso.vagas_disponiveis }}</td>
            <td class="p-4 md:p-6 text-right">
              <div class="flex justify-end gap-2 md:gap-4">
                <!-- Ver inscritos -->
                <button @click="abrirModalInscritos(curso)" class="text-black hover:opacity-60 transition-opacity" title="Ver inscritos">
                  <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                  </svg>
                </button>
                <!-- Editar -->
                <button @click="abrirModalEditar(curso.id)" class="text-black hover:opacity-60 transition-opacity" title="Editar curso">
                  <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                </button>
                <!-- Excluir -->
                <button @click="confirmarExclusao(curso)" class="text-black hover:opacity-60 transition-opacity" title="Excluir">
                  <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
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
        v-for="page in paginasVisiveis"
        :key="page"
        @click="irParaPagina(page)"
        :class="[
          'w-10 h-10 flex items-center justify-center rounded-lg font-bold text-sm transition-colors',
          page === currentPage ? 'bg-[#333] text-white' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'
        ]"
      >
        {{ page }}
      </button>
      <template v-if="totalPages > 5 && currentPage < totalPages - 2">
        <span class="text-gray-400 px-1">...</span>
        <button
          @click="irParaPagina(totalPages)"
          class="w-10 h-10 flex items-center justify-center rounded-lg bg-gray-100 text-gray-500 font-bold text-sm hover:bg-gray-200"
        >
          {{ totalPages }}
        </button>
      </template>
    </div>

    <!-- Modais -->
    <ModalCurso v-if="mostrarModal" :curso="cursoEditando" @fechar="fecharModal" @salvar="salvarCurso" />
    <ModalInscritos v-if="mostrarInscritos" :curso-id="cursoSelecionado?.id" :curso-nome="cursoSelecionado?.nome" @fechar="mostrarInscritos = false" />
    <ModalConfirmacao v-if="cursoExcluir" :mensagem="'Deseja realmente excluir este curso?'" @confirmar="excluirCurso" @cancelar="cursoExcluir = null" />
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch, nextTick } from 'vue';
import { cursoService } from '@/services/curso';
import ModalCurso from '@/components/admin/ModalCurso.vue';
import ModalInscritos from '@/components/admin/ModalInscritos.vue';
import ModalConfirmacao from '@/components/admin/ModalConfirmacao.vue';

const cursos = ref([]);
const loading = ref(true);
const search = ref('');
const showMobileSearch = ref(false);
const mobileSearchInput = ref(null);
const currentPage = ref(1);

watch(showMobileSearch, (val) => {
  if (val) {
    nextTick(() => {
      mobileSearchInput.value?.focus();
    });
  }
});

const total = ref(0);
const perPage = 9;
const mensagem = ref('');
const mensagemTipo = ref('sucesso');
const mostrarModal = ref(false);
const mostrarInscritos = ref(false);
const cursoSelecionado = ref(null);
const cursoEditando = ref(null);
const cursoExcluir = ref(null);
let searchTimeout = null;

const totalPages = computed(() => Math.ceil(total.value / perPage));

const paginasVisiveis = computed(() => {
  const pages = [];
  const max = 5;
  let start = Math.max(1, currentPage.value - 2);
  let end = Math.min(totalPages.value, start + max - 1);
  if (end - start < max - 1) start = Math.max(1, end - max + 1);
  for (let i = start; i <= end; i++) {
    if (i > 0) pages.push(i);
  }
  return pages;
});

const carregarCursos = async () => {
  loading.value = true;
  try {
    const result = await cursoService.listar({
      page: currentPage.value,
      search: search.value,
      per_page: perPage
    });
    if (result.success) {
      cursos.value = result.data.data || [];
      total.value = result.data.total || 0;
    }
  } finally {
    loading.value = false;
  }
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
};

const abrirModalCriar = () => { cursoEditando.value = null; mostrarModal.value = true; };
const fecharModal = () => { mostrarModal.value = false; };
const abrirModalEditar = async (id) => {
  const result = await cursoService.buscar(id);
  if (result.success) {
    cursoEditando.value = result.data;
    mostrarModal.value = true;
  }
};

const abrirModalInscritos = (curso) => {
  cursoSelecionado.value = curso;
  mostrarInscritos.value = true;
};

const salvarCurso = async (dados, callback) => {
  const result = cursoEditando.value 
    ? await cursoService.atualizar(cursoEditando.value.id, dados) 
    : await cursoService.criar(dados);
    
  if (result.success) {
    fecharModal();
    carregarCursos();
    mostrarMensagem(result.message, 'sucesso');
    if (callback) callback({});
  } else if (callback) {
    callback(result.errors || {});
  }
};

const confirmarExclusao = (curso) => { cursoExcluir.value = curso; };
const excluirCurso = async () => {
  if (!cursoExcluir.value) return;
  const result = await cursoService.excluir(cursoExcluir.value.id);
  if (result.success) {
    mostrarMensagem(result.message, 'sucesso');
  } else {
    mostrarMensagem(result.message, 'erro');
  }
  cursoExcluir.value = null;
  carregarCursos();
};

const mostrarMensagem = (m, t) => {
  mensagem.value = m; mensagemTipo.value = t;
  setTimeout(() => { mensagem.value = ''; }, 5000);
};

const formatCurrency = (v) => new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v);
const formatPeriodo = (inicio, fim) => {
  if (!inicio || !fim) return '-';
  const d1 = new Date(inicio).toLocaleDateString('pt-BR');
  const d2 = new Date(fim).toLocaleDateString('pt-BR');
  return `${d1} - ${d2}`;
};

onMounted(carregarCursos);
</script>
