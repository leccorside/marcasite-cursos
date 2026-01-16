<template>
  <div class="p-4 md:p-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
      <h1 class="text-2xl md:text-3xl font-black text-black uppercase tracking-tighter">Meus Cursos</h1>
      
      <!-- Search Bar -->
      <div class="relative w-full md:max-w-md">
        <input
          v-model="search"
          type="text"
          placeholder="Buscar nos meus cursos..."
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

    <!-- Feedback de Pagamento -->
    <div v-if="paymentStatus" :class="[
      'mb-8 p-4 rounded-xl font-bold flex items-center gap-3 border shadow-sm',
      paymentStatus === 'success' ? 'bg-green-50 text-green-800 border-green-200' : 
      paymentStatus === 'pending' ? 'bg-yellow-50 text-yellow-800 border-yellow-200' :
      'bg-red-50 text-red-800 border-red-200'
    ]">
      <svg v-if="paymentStatus === 'success'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <span>
        {{ 
          paymentStatus === 'success' ? 'Pagamento realizado com sucesso! Seu acesso já está disponível.' : 
          paymentStatus === 'pending' ? 'Seu pagamento está sendo processado. Em breve você terá acesso ao curso.' :
          'O pagamento não pôde ser concluído. Por favor, tente novamente.'
        }}
      </span>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-20">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-800"></div>
    </div>

    <!-- Empty State -->
    <div v-else-if="inscricoes.length === 0" class="text-center py-20 bg-white rounded-2xl shadow-sm border border-gray-100">
      <svg class="mx-auto h-16 w-16 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.246.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
      </svg>
      <p class="text-gray-400 text-xl font-medium mb-6">Você ainda não se inscreveu em nenhum curso.</p>
      <router-link to="/vitrine" class="inline-flex bg-black text-white px-8 py-3 rounded-xl font-bold hover:bg-gray-800 transition-colors">
        Ver Cursos Disponíveis
      </router-link>
    </div>

    <!-- Table -->
    <div v-else class="overflow-x-auto border border-gray-100 rounded-2xl shadow-sm bg-white">
      <table class="min-w-full">
        <thead>
          <tr class="text-left bg-gray-50/50 border-b border-gray-100">
            <th class="p-6 text-sm font-black text-black uppercase tracking-widest border-r border-gray-100">Curso</th>
            <th class="p-6 text-sm font-black text-black uppercase tracking-widest border-r border-gray-100">Inscrição</th>
            <th class="p-6 text-sm font-black text-black uppercase tracking-widest text-center border-r border-gray-100">Status</th>
            <th class="p-6 text-sm font-black text-black uppercase tracking-widest text-center border-r border-gray-100">Investimento</th>
            <th class="p-6 text-sm font-black text-black uppercase tracking-widest text-right"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <tr v-for="inscricao in inscricoes" :key="inscricao.id" class="hover:bg-gray-50/50 transition-colors">
            <td class="p-6 border-r border-gray-50">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gray-100 rounded-lg flex-shrink-0 overflow-hidden">
                  <img v-if="inscricao.curso.thumbnail" :src="`/storage/${inscricao.curso.thumbnail}`" class="w-full h-full object-cover" />
                  <div v-else class="w-full h-full flex items-center justify-center text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                  </div>
                </div>
                <span class="text-sm font-bold text-gray-900">{{ inscricao.curso.nome }}</span>
              </div>
            </td>
            <td class="p-6 text-sm font-medium text-gray-500 border-r border-gray-50">
              {{ formatDate(inscricao.created_at) }}
            </td>
            <td class="p-6 text-center border-r border-gray-50">
              <span :class="[
                'px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest',
                inscricao.status === 'pago' ? 'bg-green-100 text-green-700' : 
                inscricao.status === 'pendente' ? 'bg-yellow-100 text-yellow-700' :
                'bg-red-100 text-red-700'
              ]">
                {{ inscricao.status }}
              </span>
            </td>
            <td class="p-6 text-sm font-black text-gray-900 text-center border-r border-gray-50">
              {{ formatCurrency(inscricao.valor_pago) }}
            </td>
            <td class="p-6 text-right">
              <button 
                v-if="inscricao.status === 'pago'"
                @click="verMateriais(inscricao.curso)"
                class="text-black hover:opacity-60 transition-opacity"
                title="Acessar Materiais"
              >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
              </button>
              <button 
                v-else-if="inscricao.status === 'pendente'"
                @click="pagarInscricao(inscricao)"
                :disabled="processando === inscricao.id"
                class="bg-black text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-gray-800 transition-colors disabled:opacity-50"
              >
                {{ processando === inscricao.id ? 'Aguarde...' : 'Pagar Agora' }}
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal Materiais -->
    <div v-if="cursoSelecionado" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full max-h-[80vh] overflow-hidden flex flex-col">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
          <div>
            <h3 class="text-xl font-black text-black uppercase tracking-tighter">Materiais do Curso</h3>
            <p class="text-sm text-gray-500 font-bold mt-1">{{ cursoSelecionado.nome }}</p>
          </div>
          <button @click="cursoSelecionado = null" class="text-gray-400 hover:text-black transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        
        <div class="p-6 overflow-y-auto flex-1">
          <div v-if="!cursoSelecionado.materiais || cursoSelecionado.materiais.length === 0" class="text-center py-10">
            <p class="text-gray-400 font-medium">Nenhum material disponível para este curso.</p>
          </div>
          <div v-else class="space-y-3">
            <a 
              v-for="material in cursoSelecionado.materiais" 
              :key="material.id"
              :href="`/storage/${material.arquivo}`"
              target="_blank"
              class="flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors group border border-gray-100"
            >
              <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-gray-400 border border-gray-100 shadow-sm">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                  </svg>
                </div>
                <div>
                  <p class="text-sm font-bold text-gray-900 line-clamp-1">{{ material.nome }}</p>
                  <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest">{{ material.tipo_arquivo }}</p>
                </div>
              </div>
              <svg class="w-5 h-5 text-gray-300 group-hover:text-black transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
              </svg>
            </a>
          </div>
        </div>
        
        <div class="p-6 border-t border-gray-100 flex justify-end">
          <button @click="cursoSelecionado = null" class="px-6 py-2 bg-black text-white rounded-xl font-bold hover:bg-gray-800 transition-colors">
            Fechar
          </button>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="totalPages > 1" class="mt-8 flex justify-center items-center gap-2">
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
import { ref, onMounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import { inscricaoService } from '@/services/inscricao';

const route = useRoute();
const inscricoes = ref([]);
const loading = ref(true);
const search = ref('');
const currentPage = ref(1);
const totalPages = ref(1);
const paymentStatus = ref(route.query.payment_status || null);
const cursoSelecionado = ref(null);
const processando = ref(null);
let searchTimeout = null;

const carregarInscricoes = async () => {
  loading.value = true;
  try {
    const result = await inscricaoService.listar({
      page: currentPage.value,
      search: search.value
    });
    if (result.success) {
      inscricoes.value = result.data.data;
      totalPages.value = result.data.last_page;
    }
  } finally {
    loading.value = false;
  }
};

const pagarInscricao = async (inscricao) => {
  processando.value = inscricao.id;
  try {
    const result = await inscricaoService.criar(inscricao.curso_id);
    if (result.success) {
      if (result.data.checkout_url) {
        window.location.href = result.data.checkout_url;
      } else {
        // Curso gratuito ou aprovado automaticamente
        alert(result.data.message || 'Inscrição confirmada com sucesso!');
        carregarInscricoes(); // Atualiza a lista para mostrar como PAGO e liberar materiais
      }
    } else {
      alert(result.message || 'Erro ao processar sua inscrição.');
    }
  } catch (error) {
    console.error(error);
    alert('Erro inesperado.');
  } finally {
    processando.value = null;
  }
};

const verMateriais = (curso) => {
  cursoSelecionado.value = curso;
};

const debounceSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    currentPage.value = 1;
    carregarInscricoes();
  }, 500);
};

const irParaPagina = (p) => {
  currentPage.value = p;
  carregarInscricoes();
};

const formatCurrency = (value) => {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  }).format(value);
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  return new Date(dateString).toLocaleDateString('pt-BR');
};

onMounted(carregarInscricoes);
</script>
