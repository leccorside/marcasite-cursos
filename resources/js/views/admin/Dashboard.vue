<template>
  <div class="p-4 md:p-8">
    <!-- Header com Filtros -->
    <div class="mb-8">
      <h1 class="text-2xl md:text-3xl font-black text-black uppercase tracking-tighter mb-6">Dashboard Administrativo</h1>
      
      <!-- Filtros -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-4">Filtros</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <!-- Filtro Curso -->
          <div>
            <label class="block text-xs font-bold text-gray-700 mb-2">Curso</label>
            <select
              v-model="filtros.curso_id"
              @change="aplicarFiltros"
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-gray-400 bg-white"
            >
              <option value="">Todos os Cursos</option>
              <option v-for="curso in cursos" :key="curso.id" :value="curso.id">{{ curso.nome }}</option>
            </select>
          </div>

          <!-- Filtro Data Início -->
          <div>
            <label class="block text-xs font-bold text-gray-700 mb-2">Data Início</label>
            <input
              v-model="filtros.data_inicio"
              type="date"
              @change="aplicarFiltros"
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-gray-400"
            />
          </div>

          <!-- Filtro Data Fim -->
          <div>
            <label class="block text-xs font-bold text-gray-700 mb-2">Data Fim</label>
            <input
              v-model="filtros.data_fim"
              type="date"
              @change="aplicarFiltros"
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-gray-400"
            />
          </div>

          <!-- Botão Limpar -->
          <div class="flex items-end">
            <button
              @click="limparFiltros"
              class="w-full bg-gray-100 text-gray-700 px-4 py-2.5 rounded-lg font-bold hover:bg-gray-200 transition-colors"
            >
              Limpar Filtros
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-20">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-800"></div>
    </div>

    <div v-else-if="stats">
      <!-- Cards de Estatísticas -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Cursos -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Total Cursos</span>
            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
          </div>
          <div class="text-3xl font-black text-gray-900">{{ stats.cards.total_cursos }}</div>
          <div class="text-xs text-gray-500 mt-1">{{ stats.cards.total_cursos_ativos }} ativos</div>
        </div>

        <!-- Total Usuários -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Total Usuários</span>
            <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
          </div>
          <div class="text-3xl font-black text-gray-900">{{ stats.cards.total_usuarios }}</div>
          <div class="text-xs text-gray-500 mt-1">{{ stats.cards.total_alunos }} alunos, {{ stats.cards.total_admins }} admins</div>
        </div>

        <!-- Total Inscrições -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Total Inscrições</span>
            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </div>
          <div class="text-3xl font-black text-gray-900">{{ stats.cards.total_inscricoes }}</div>
          <div class="text-xs text-gray-500 mt-1">{{ stats.cards.inscricoes_pagas }} pagas, {{ stats.cards.inscricoes_pendentes }} pendentes</div>
        </div>

        <!-- Receita Total -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Receita Total</span>
            <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="text-3xl font-black text-gray-900">{{ formatCurrency(stats.cards.receita_total) }}</div>
          <div class="text-xs text-gray-500 mt-1">{{ formatCurrency(stats.cards.receita_mes_atual) }} este mês</div>
        </div>
      </div>

      <!-- Gráficos -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Gráfico de Inscrições por Mês -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <h3 class="text-sm font-black text-gray-700 uppercase tracking-widest mb-4">Inscrições por Mês</h3>
          <div class="h-64">
            <canvas ref="chartInscricoes"></canvas>
          </div>
        </div>

        <!-- Gráfico de Receita por Mês -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <h3 class="text-sm font-black text-gray-700 uppercase tracking-widest mb-4">Receita por Mês</h3>
          <div class="h-64">
            <canvas ref="chartReceita"></canvas>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Gráfico de Status -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <h3 class="text-sm font-black text-gray-700 uppercase tracking-widest mb-4">Status das Inscrições</h3>
          <div class="h-64">
            <canvas ref="chartStatus"></canvas>
          </div>
        </div>

        <!-- Top 5 Cursos -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <h3 class="text-sm font-black text-gray-700 uppercase tracking-widest mb-4">Top 5 Cursos Mais Populares</h3>
          <div class="space-y-3">
            <div
              v-for="(curso, index) in stats.graficos.top_cursos"
              :key="curso.id"
              class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
            >
              <div class="flex items-center gap-3">
                <span class="text-lg font-black text-gray-400">#{{ index + 1 }}</span>
                <span class="font-bold text-gray-900">{{ curso.nome }}</span>
              </div>
              <span class="text-sm font-black text-blue-600">{{ curso.total_inscricoes }} inscrições</span>
            </div>
            <div v-if="stats.graficos.top_cursos.length === 0" class="text-center py-8 text-gray-400">
              Nenhum dado disponível
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, nextTick } from 'vue';
import {
  Chart,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  LineController,
  BarElement,
  BarController,
  ArcElement,
  DoughnutController,
  Title,
  Tooltip,
  Legend
} from 'chart.js';
import axios from 'axios';
import { cursoService } from '@/services/curso';

Chart.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  LineController,
  BarElement,
  BarController,
  ArcElement,
  DoughnutController,
  Title,
  Tooltip,
  Legend
);

const loading = ref(true);
const stats = ref(null);
const cursos = ref([]);
const filtros = ref({
  curso_id: '',
  data_inicio: '',
  data_fim: ''
});

const chartInscricoes = ref(null);
const chartReceita = ref(null);
const chartStatus = ref(null);

let chartInscricoesInstance = null;
let chartReceitaInstance = null;
let chartStatusInstance = null;

const carregarCursos = async () => {
  const result = await cursoService.listar({ per_page: 1000 });
  if (result.success) {
    cursos.value = result.data.data || [];
  }
};

const carregarStats = async () => {
  loading.value = true;
  try {
    const params = {};
    if (filtros.value.curso_id) params.curso_id = filtros.value.curso_id;
    if (filtros.value.data_inicio) params.data_inicio = filtros.value.data_inicio;
    if (filtros.value.data_fim) params.data_fim = filtros.value.data_fim;

    const response = await axios.get('/api/dashboard/stats', { params });
    stats.value = response.data;
    
    // Aguardar o DOM estar pronto e criar gráficos
    await nextTick();
    setTimeout(() => {
      criarGraficos();
    }, 100);
  } finally {
    loading.value = false;
  }
};

const criarGraficos = () => {
  if (!stats.value || !stats.value.graficos) {
    console.log('Stats ou graficos não disponíveis');
    return;
  }

  // Destruir gráficos existentes
  if (chartInscricoesInstance) {
    chartInscricoesInstance.destroy();
    chartInscricoesInstance = null;
  }
  if (chartReceitaInstance) {
    chartReceitaInstance.destroy();
    chartReceitaInstance = null;
  }
  if (chartStatusInstance) {
    chartStatusInstance.destroy();
    chartStatusInstance = null;
  }

  // Gráfico de Inscrições por Mês
  if (chartInscricoes.value) {
    const dadosInscricoes = stats.value.graficos.inscricoes_por_mes || [];
    console.log('Dados de inscrições por mês:', dadosInscricoes);
    console.log('Canvas disponível:', chartInscricoes.value);
    
    const labels = dadosInscricoes.length > 0 
      ? dadosInscricoes.map(item => item.mes || 'Sem mês') 
      : ['Nenhum dado'];
    const valores = dadosInscricoes.length > 0 
      ? dadosInscricoes.map(item => Number(item.total) || 0) 
      : [0];

    console.log('Criando gráfico de inscrições com labels:', labels, 'valores:', valores);

    try {
      chartInscricoesInstance = new Chart(chartInscricoes.value, {
        type: 'line',
        data: {
          labels: labels,
          datasets: [{
            label: 'Inscrições',
            data: valores,
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          aspectRatio: 2,
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                stepSize: 1
              }
            }
          }
        }
      });
      console.log('Gráfico de inscrições criado com sucesso');
    } catch (error) {
      console.error('Erro ao criar gráfico de inscrições:', error);
    }
  } else {
    console.log('Canvas de inscrições não disponível');
  }

  // Gráfico de Receita por Mês
  if (chartReceita.value && stats.value.graficos.receita_por_mes) {
    const dadosReceita = stats.value.graficos.receita_por_mes || [];
    const labelsReceita = dadosReceita.length > 0 
      ? dadosReceita.map(item => item.mes) 
      : ['Nenhum dado'];
    const valoresReceita = dadosReceita.length > 0 
      ? dadosReceita.map(item => item.total) 
      : [0];

    chartReceitaInstance = new Chart(chartReceita.value, {
      type: 'bar',
      data: {
        labels: labelsReceita,
        datasets: [{
          label: 'Receita (R$)',
          data: valoresReceita,
          backgroundColor: 'rgba(34, 197, 94, 0.8)',
          borderColor: 'rgb(34, 197, 94)',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        aspectRatio: 2,
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                return new Intl.NumberFormat('pt-BR', {
                  style: 'currency',
                  currency: 'BRL'
                }).format(context.parsed.y);
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function(value) {
                return new Intl.NumberFormat('pt-BR', {
                  style: 'currency',
                  currency: 'BRL',
                  notation: 'compact'
                }).format(value);
              }
            }
          }
        }
      }
    });
  }

  // Gráfico de Status
  if (chartStatus.value && stats.value.graficos.status_inscricoes) {
    const dadosStatus = stats.value.graficos.status_inscricoes || [];
    const labelsStatus = dadosStatus.map(item => item.status);
    const valoresStatus = dadosStatus.map(item => item.total);

    chartStatusInstance = new Chart(chartStatus.value, {
      type: 'doughnut',
      data: {
        labels: labelsStatus,
        datasets: [{
          data: valoresStatus,
          backgroundColor: [
            'rgba(34, 197, 94, 0.8)',
            'rgba(251, 191, 36, 0.8)',
            'rgba(239, 68, 68, 0.8)'
          ],
          borderColor: [
            'rgb(34, 197, 94)',
            'rgb(251, 191, 36)',
            'rgb(239, 68, 68)'
          ],
          borderWidth: 2
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        aspectRatio: 1.5,
        plugins: {
          legend: {
            position: 'bottom'
          }
        }
      }
    });
  }
};

const aplicarFiltros = () => {
  carregarStats();
};

const limparFiltros = () => {
  filtros.value = {
    curso_id: '',
    data_inicio: '',
    data_fim: ''
  };
  carregarStats();
};

const formatCurrency = (value) => {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  }).format(value || 0);
};

onMounted(() => {
  carregarCursos();
  carregarStats();
});
</script>
