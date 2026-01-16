<template>
  <div class="p-4 md:p-8">
    <!-- Topo: Busca e Título -->
    <div class="flex flex-col gap-4 mb-10">
      <div class="flex justify-between items-center w-full flex-wrap gap-4">
        <h1 class="text-2xl md:text-3xl font-black text-black uppercase tracking-tighter">Usuários</h1>
        
        <div class="flex items-center gap-3 flex-wrap">
          <!-- Botões Exportar -->
          <button
            @click="exportarExcel"
            class="bg-green-600 text-white px-3 md:px-6 py-2.5 rounded-lg font-bold hover:bg-green-700 transition-colors whitespace-nowrap text-sm md:text-base shadow-sm flex items-center gap-2"
          >
            <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span class="hidden md:inline">Exportar Excel</span>
          </button>
          <button
            @click="exportarPdf"
            class="bg-red-600 text-white px-3 md:px-6 py-2.5 rounded-lg font-bold hover:bg-red-700 transition-colors whitespace-nowrap text-sm md:text-base shadow-sm flex items-center gap-2"
          >
            <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
            <span class="hidden md:inline">Exportar PDF</span>
          </button>
          
          <!-- Botão Novo -->
          <button
            @click="abrirModalCriar"
            class="bg-[#333] text-white px-5 md:px-8 py-2.5 rounded-lg font-bold hover:bg-black transition-colors whitespace-nowrap text-sm md:text-base shadow-sm"
          >
            Novo usuário
          </button>
        </div>
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
            <td class="p-4 md:p-6 text-xs md:text-sm font-bold text-gray-900 border-r border-gray-50">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-gray-100 flex-shrink-0 overflow-hidden border border-gray-200">
                  <img v-if="usuario.foto_perfil" :src="`/storage/${usuario.foto_perfil}`" class="w-full h-full object-cover" />
                  <svg v-else class="w-full h-full text-gray-300 p-1" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                  </svg>
                </div>
                <span>{{ usuario.name }}</span>
              </div>
            </td>
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
                <button 
                  @click="abrirModalEditar(usuario)"
                  class="text-black hover:opacity-60 transition-opacity" 
                  title="Editar"
                >
                  <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                </button>
                <button 
                  @click="confirmarExclusao(usuario)"
                  class="text-red-600 hover:opacity-60 transition-opacity" 
                  title="Excluir"
                >
                  <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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

    <!-- Modais -->
    <ModalUsuario
      v-if="mostrarModal"
      :usuario="usuarioEditando"
      @fechar="fecharModal"
      @salvar="salvarUsuario"
    />

    <ModalConfirmacao
      v-if="usuarioExcluir"
      titulo="Excluir Usuário"
      :mensagem="`Deseja mesmo deletar este usuário? (${usuarioExcluir.name}). Esta ação não pode ser desfeita.`"
      @cancelar="usuarioExcluir = null"
      @confirmar="excluirUsuario"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { userService } from '@/services/user';
import ModalUsuario from '@/components/admin/ModalUsuario.vue';
import ModalConfirmacao from '@/components/admin/ModalConfirmacao.vue';

const usuarios = ref([]);
const loading = ref(true);
const search = ref('');
const currentPage = ref(1);
const total = ref(0);
const perPage = 9;
const totalPages = ref(1);
let searchTimeout = null;

const mostrarModal = ref(false);
const usuarioEditando = ref(null);
const usuarioExcluir = ref(null);

const carregarUsuarios = async () => {
  loading.value = true;
  try {
    const result = await userService.listar({
      page: currentPage.value,
      search: search.value,
      per_page: perPage
    });
    if (result.success && result.data) {
      usuarios.value = result.data.data || [];
      total.value = result.data.total || 0;
      totalPages.value = result.data.last_page || 1;
    } else {
      console.error('Erro ao carregar usuários:', result.message);
      usuarios.value = [];
    }
  } catch (error) {
    console.error('Erro ao carregar usuários:', error);
    usuarios.value = [];
  } finally {
    loading.value = false;
  }
};

const abrirModalCriar = () => {
  usuarioEditando.value = null;
  mostrarModal.value = true;
};

const abrirModalEditar = (usuario) => {
  usuarioEditando.value = usuario;
  mostrarModal.value = true;
};

const fecharModal = () => {
  mostrarModal.value = false;
  usuarioEditando.value = null;
};

const salvarUsuario = async (dados, callback) => {
  const result = usuarioEditando.value 
    ? await userService.atualizar(usuarioEditando.value.id, dados) 
    : await userService.criar(dados);
    
  if (result.success) {
    fecharModal();
    carregarUsuarios();
    if (callback) callback({});
  } else if (callback) {
    callback(result.errors || {});
  }
};

const confirmarExclusao = (usuario) => {
  usuarioExcluir.value = usuario;
};

const excluirUsuario = async () => {
  if (!usuarioExcluir.value) return;
  const result = await userService.excluir(usuarioExcluir.value.id);
  if (result.success) {
    // Poderia mostrar uma mensagem de sucesso aqui
  } else {
    alert(result.message);
  }
  usuarioExcluir.value = null;
  carregarUsuarios();
};

const exportarExcel = () => {
  window.open('/api/usuarios/export/excel', '_blank');
};

const exportarPdf = () => {
  window.open('/api/usuarios/export/pdf', '_blank');
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
