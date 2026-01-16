<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col">
      <!-- Header -->
      <div class="flex justify-between items-center p-6 border-b border-gray-100">
        <div>
          <h2 class="text-2xl font-bold text-gray-800">Alunos Inscritos</h2>
          <p class="text-sm text-gray-500 mt-1">{{ cursoNome }}</p>
        </div>
        <button @click="$emit('fechar')" class="text-gray-400 hover:text-gray-600 transition-colors">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Content -->
      <div class="p-6 overflow-y-auto flex-1">
        <div v-if="loading" class="flex justify-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-800"></div>
        </div>

        <div v-else-if="inscritos.length === 0" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
          </svg>
          <p class="text-gray-500 text-lg font-medium">Ainda não temos inscritos para esse curso.</p>
        </div>

        <div v-else class="overflow-x-auto border border-gray-100 rounded-xl shadow-sm bg-white">
          <table class="min-w-full">
            <thead>
              <tr class="text-left bg-gray-50/50 border-b border-gray-100">
                <th class="p-4 text-xs font-black text-black uppercase tracking-widest border-r border-gray-100">Aluno</th>
                <th class="p-4 text-xs font-black text-black uppercase tracking-widest border-r border-gray-100">CPF</th>
                <th class="p-4 text-xs font-black text-black uppercase tracking-widest border-r border-gray-100 text-center">Status</th>
                <th class="p-4 text-xs font-black text-black uppercase tracking-widest text-center">Inscrição</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="inscrito in inscritos" :key="inscrito.id" class="hover:bg-gray-50/50 transition-colors">
                <td class="p-4 border-r border-gray-50">
                  <div class="flex flex-col">
                    <span class="text-sm font-bold text-gray-900">{{ inscrito.aluno_nome }}</span>
                    <span class="text-xs text-gray-500">{{ inscrito.aluno_email }}</span>
                  </div>
                </td>
                <td class="p-4 text-sm font-medium text-gray-900 border-r border-gray-50">{{ inscrito.aluno_cpf }}</td>
                <td class="p-4 text-center border-r border-gray-50">
                  <div class="flex items-center justify-center gap-2">
                    <select 
                      v-model="inscrito.status"
                      @change="alterarStatus(inscrito)"
                      :disabled="atualizandoId === inscrito.id"
                      :class="[
                        'px-2 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider border-0 cursor-pointer focus:ring-0',
                        inscrito.status === 'pago' ? 'bg-green-100 text-green-700' : 
                        inscrito.status === 'cancelado' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700'
                      ]"
                    >
                      <option value="pendente">Pendente</option>
                      <option value="pago">Pago</option>
                      <option value="cancelado">Cancelado</option>
                    </select>
                    <div v-if="atualizandoId === inscrito.id" class="animate-spin h-3 w-3 border-b-2 border-gray-500 rounded-full"></div>
                  </div>
                </td>
                <td class="p-4 text-sm font-medium text-gray-900 text-center">
                  {{ inscrito.data_inscricao }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Footer -->
      <div class="p-6 border-t border-gray-100 flex justify-end">
        <button @click="$emit('fechar')" class="bg-gray-100 text-gray-700 px-8 py-2 rounded-lg font-bold hover:bg-gray-200 transition-colors">
          Fechar
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { cursoService } from '@/services/curso';
import { inscricaoService } from '@/services/inscricao';

const props = defineProps({
  cursoId: { type: Number, required: true },
  cursoNome: { type: String, required: true }
});

const emit = defineEmits(['fechar']);

const inscritos = ref([]);
const loading = ref(true);
const atualizandoId = ref(null);

const carregarInscritos = async () => {
  loading.value = true;
  try {
    const result = await cursoService.listarInscritos(props.cursoId);
    if (result.success) {
      inscritos.value = result.data;
    }
  } finally {
    loading.value = false;
  }
};

const alterarStatus = async (inscrito) => {
  atualizandoId.value = inscrito.id;
  try {
    const result = await inscricaoService.atualizarStatus(inscrito.id, inscrito.status);
    if (result.success) {
      // O status já está atualizado via v-model, mas poderíamos recarregar para garantir
      // carregarInscritos();
    } else {
      alert(result.message);
      carregarInscritos(); // Volta ao status original se der erro
    }
  } finally {
    atualizandoId.value = null;
  }
};

onMounted(carregarInscritos);
</script>
