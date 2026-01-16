<template>
  <div class="p-4 md:p-8">
    <div class="mb-10">
      <h1 class="text-3xl md:text-4xl font-black text-black uppercase tracking-tighter mb-2">Olá, {{ auth.user.value?.name }}!</h1>
      <p class="text-gray-500 font-bold uppercase text-xs tracking-widest">Seja bem-vindo de volta à Marcasite Cursos.</p>
    </div>

    <!-- Cards de Resumo -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
      <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between group hover:shadow-md transition-all">
        <div>
          <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center text-black mb-6 border border-gray-100 shadow-sm group-hover:scale-110 transition-transform">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.246.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
          </div>
          <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-1">Cursos Inscritos</h3>
          <p class="text-4xl font-black text-black tracking-tighter">{{ stats.total_inscricoes }}</p>
        </div>
        <router-link to="/meus-cursos" class="mt-8 text-xs font-black uppercase tracking-widest text-black hover:opacity-60 flex items-center gap-2">
          Ver todos <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" /></svg>
        </router-link>
      </div>

      <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between group hover:shadow-md transition-all">
        <div>
          <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center text-black mb-6 border border-gray-100 shadow-sm group-hover:scale-110 transition-transform">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-1">Cursos Concluídos</h3>
          <p class="text-4xl font-black text-black tracking-tighter">0</p>
        </div>
        <span class="mt-8 text-[10px] font-black uppercase tracking-widest text-gray-300">Em breve</span>
      </div>

      <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between group hover:shadow-md transition-all">
        <div>
          <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center text-black mb-6 border border-gray-100 shadow-sm group-hover:scale-110 transition-transform">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-1">Pagamentos Pendentes</h3>
          <p class="text-4xl font-black text-black tracking-tighter">{{ stats.total_pendentes }}</p>
        </div>
        <router-link to="/meus-cursos" class="mt-8 text-xs font-black uppercase tracking-widest text-black hover:opacity-60 flex items-center gap-2">
          Resolver agora <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" /></svg>
        </router-link>
      </div>
    </div>

    <!-- Seção de Acesso Rápido -->
    <div class="bg-black text-white p-8 md:p-12 rounded-3xl overflow-hidden relative group">
      <div class="relative z-10 max-w-lg">
        <h2 class="text-2xl md:text-3xl font-black uppercase tracking-tighter mb-4">Explore novos conhecimentos</h2>
        <p class="text-gray-400 font-bold mb-8 leading-relaxed">Confira os cursos disponíveis na nossa vitrine e comece a aprender hoje mesmo com os melhores profissionais.</p>
        <router-link to="/vitrine" class="inline-flex bg-white text-black px-8 py-4 rounded-xl font-black uppercase tracking-widest hover:bg-gray-200 transition-colors shadow-xl">
          Ir para Vitrine
        </router-link>
      </div>
      <!-- Elemento Decorativo -->
      <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-gray-800 rounded-full blur-3xl opacity-50 group-hover:scale-110 transition-transform duration-700"></div>
      <div class="absolute -right-10 -top-10 w-40 h-40 bg-gray-900 rounded-full blur-2xl opacity-50 group-hover:translate-x-4 transition-transform duration-700"></div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuth } from '@/composables/useAuth';
import axios from 'axios';

const auth = useAuth();
const stats = ref({
  total_inscricoes: 0,
  total_pendentes: 0
});

const carregarStats = async () => {
  try {
    const response = await axios.get('/api/stats/aluno');
    stats.value = response.data;
  } catch (error) {
    console.error('Erro ao carregar stats:', error);
  }
};

onMounted(carregarStats);
</script>
