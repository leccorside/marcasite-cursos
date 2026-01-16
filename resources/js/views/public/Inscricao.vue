<template>
  <div class="p-4 md:p-8 max-w-4xl mx-auto">
    <!-- Voltar -->
    <div class="mb-6">
      <router-link to="/vitrine" class="inline-flex items-center gap-2 text-gray-500 hover:text-black transition-colors font-bold text-sm uppercase tracking-widest">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Voltar para vitrine
      </router-link>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-20">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-800"></div>
    </div>

    <div v-else-if="curso" class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
      <div class="flex flex-col md:flex-row">
        <!-- Esquerda: Informações do Curso -->
        <div class="md:w-1/2 p-8 border-b md:border-b-0 md:border-r border-gray-100">
          <div class="mb-6">
            <span class="px-3 py-1 bg-gray-100 text-gray-600 text-[10px] font-black uppercase tracking-widest rounded-full">
              {{ curso.categoria }}
            </span>
            <h1 class="text-3xl font-black text-black mt-4 leading-tight uppercase tracking-tighter">{{ curso.nome }}</h1>
          </div>

          <div class="prose prose-sm text-gray-600 mb-8">
            <p>{{ curso.descricao }}</p>
          </div>

          <div class="space-y-4">
            <div class="flex items-center gap-3 text-sm text-gray-700">
              <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <span>Inscrições até {{ formatDate(curso.data_fim_inscricoes) }}</span>
            </div>
            <div class="flex items-center gap-3 text-sm text-gray-700">
              <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              <span>{{ curso.vagas_disponiveis }} vagas restantes</span>
            </div>
          </div>
        </div>

        <!-- Direita: Confirmação e Pagamento -->
        <div class="md:w-1/2 p-8 bg-gray-50 flex flex-col justify-between">
          <div>
            <h2 class="text-xl font-bold text-black mb-6">Confirmação de Inscrição</h2>
            
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm mb-6">
              <div class="flex justify-between items-center mb-4">
                <span class="text-gray-500 font-medium uppercase text-xs tracking-widest">Valor do Curso</span>
                <span class="text-2xl font-black text-black tracking-tighter">{{ formatCurrency(curso.valor) }}</span>
              </div>
              <div class="border-t border-gray-100 pt-4 text-xs text-gray-500 italic">
                Ao clicar em confirmar, você será redirecionado para o checkout seguro do Mercado Pago.
              </div>
            </div>

            <!-- Dados do Aluno (Resumo) -->
            <div class="mb-8">
              <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Seus Dados</h3>
              <div class="space-y-1">
                <p class="text-sm font-bold text-gray-900">{{ auth.user.value?.aluno?.nome }}</p>
                <p class="text-xs text-gray-500">{{ auth.user.value?.aluno?.email }}</p>
                <p class="text-xs text-gray-500">CPF: {{ auth.user.value?.aluno?.cpf }}</p>
              </div>
            </div>
          </div>

          <div class="space-y-4">
            <!-- Botão se já estiver inscrito -->
            <template v-if="curso.status_inscricao === 'pago'">
              <div class="w-full bg-green-100 text-green-700 py-4 px-6 rounded-xl font-black uppercase tracking-widest text-center border border-green-200 shadow-sm">
                Você já está inscrito!
              </div>
              <router-link 
                to="/meus-cursos"
                class="w-full bg-black text-white py-4 px-6 rounded-xl font-black uppercase tracking-widest hover:bg-gray-800 transition-all text-center block"
              >
                Acessar Meus Cursos
              </router-link>
            </template>

            <template v-else-if="curso.status_inscricao === 'pendente'">
              <div class="w-full bg-yellow-100 text-yellow-700 py-4 px-6 rounded-xl font-black uppercase tracking-widest text-center border border-yellow-200 shadow-sm">
                Inscrição Pendente
              </div>
              <button 
                @click="confirmarInscricao"
                :disabled="processando"
                class="w-full bg-black text-white py-4 px-6 rounded-xl font-black uppercase tracking-widest hover:bg-gray-800 transition-all shadow-lg flex items-center justify-center gap-3 group"
              >
                <span v-if="processando">Processando...</span>
                <span v-else>Pagar Agora</span>
              </button>
            </template>

            <template v-else>
              <button 
                @click="confirmarInscricao"
                :disabled="processando || !isInscricoesAbertas"
                class="w-full bg-black text-white py-4 px-6 rounded-xl font-black uppercase tracking-widest hover:bg-gray-800 transition-all shadow-lg disabled:opacity-50 flex items-center justify-center gap-3 group"
              >
                <span v-if="processando">Processando...</span>
                <template v-else>
                  <span>Confirmar e Pagar</span>
                  <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                  </svg>
                </template>
              </button>
              <p v-if="!isInscricoesAbertas" class="text-red-500 text-center text-xs font-bold uppercase tracking-widest">
                {{ curso.vagas_disponiveis <= 0 ? 'Infelizmente não há mais vagas para este curso.' : 'O prazo para inscrições já se encerrou.' }}
              </p>
            </template>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { cursoService } from '@/services/curso';
import { inscricaoService } from '@/services/inscricao';
import { useAuth } from '@/composables/useAuth';

const props = defineProps({
  id: { type: [String, Number], required: true }
});

const route = useRoute();
const router = useRouter();
const auth = useAuth();

const curso = ref(null);
const loading = ref(true);
const processando = ref(false);

const isInscricoesAbertas = computed(() => {
  if (!curso.value) return false;
  if (Number(curso.value.vagas_disponiveis) <= 0) return false;
  if (!curso.value.data_fim_inscricoes) return true;

  const hoje = new Date();
  hoje.setHours(0, 0, 0, 0);

  // Garantir que pegamos apenas a parte da data YYYY-MM-DD
  const dataString = typeof curso.value.data_fim_inscricoes === 'string' 
    ? curso.value.data_fim_inscricoes.split('T')[0] 
    : curso.value.data_fim_inscricoes;

  // Criar data de fim de forma segura (ano, mês-0-indexado, dia, hora, min, seg)
  const [year, month, day] = dataString.split('-').map(Number);
  const dataFim = new Date(year, month - 1, day, 23, 59, 59);
  
  return hoje <= dataFim;
});

const carregarCurso = async () => {
  loading.value = true;
  try {
    const result = await cursoService.buscar(props.id);
    if (result.success) {
      curso.value = result.data;
    } else {
      router.push('/vitrine');
    }
  } finally {
    loading.value = false;
  }
};

const confirmarInscricao = async () => {
  processando.value = true;
  try {
    const result = await inscricaoService.criar(props.id);
    if (result.success) {
      if (result.data.checkout_url) {
        // Se for um link de mock (desenvolvimento), avisa o usuário
        if (result.data.checkout_url.startsWith('#mock')) {
          alert('Modo de Teste: Token do Mercado Pago não configurado. Redirecionando para Meus Cursos...');
          router.push('/meus-cursos');
          return;
        }
        
        // Redireciona para o Mercado Pago
        window.location.href = result.data.checkout_url;
      } else {
        alert(result.data.message || 'Inscrição realizada com sucesso!');
        router.push('/meus-cursos');
      }
    } else {
      alert(result.message || 'Ocorreu um erro ao processar sua inscrição.');
    }
  } catch (error) {
    console.error('Erro na inscrição:', error);
    alert('Erro inesperado ao conectar com o servidor. Tente novamente.');
  } finally {
    processando.value = false;
  }
};

const formatCurrency = (value) => {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  }).format(value);
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleDateString('pt-BR');
};

onMounted(carregarCurso);
</script>
