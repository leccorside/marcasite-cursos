<template>
  <div class="p-4 md:p-8 max-w-2xl mx-auto">
    <h1 class="text-2xl md:text-3xl font-black text-black uppercase tracking-tighter mb-8">Configurações de Perfil</h1>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="p-8">
        <form @submit.prevent="salvarPerfil" class="space-y-6">
          <!-- Nome -->
          <div>
            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Nome Completo</label>
            <input
              v-model="form.name"
              type="text"
              class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-1 focus:ring-black transition-all"
              placeholder="Seu nome"
              required
            />
            <p v-if="errors.name" class="mt-1 text-xs text-red-500 font-bold">{{ errors.name[0] }}</p>
          </div>

          <!-- Email -->
          <div>
            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">E-mail</label>
            <input
              v-model="form.email"
              type="email"
              class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-500 cursor-not-allowed"
              placeholder="seu@email.com"
              disabled
            />
            <p class="mt-1 text-[10px] text-gray-400 font-bold italic">O e-mail não pode ser alterado.</p>
          </div>

          <!-- CPF (apenas leitura) -->
          <div>
            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">CPF</label>
            <input
              v-model="form.cpf"
              type="text"
              class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-500 cursor-not-allowed"
              placeholder="000.000.000-00"
              disabled
            />
          </div>

          <div class="pt-4 border-t border-gray-100">
            <h3 class="text-sm font-black text-black uppercase tracking-widest mb-4">Alterar Senha</h3>
            <div class="space-y-4">
              <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Nova Senha</label>
                <input
                  v-model="form.password"
                  type="password"
                  class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-1 focus:ring-black transition-all"
                  placeholder="Deixe em branco para não alterar"
                />
                <p v-if="errors.password" class="mt-1 text-xs text-red-500 font-bold">{{ errors.password[0] }}</p>
              </div>
              <div v-if="form.password">
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Confirmar Nova Senha</label>
                <input
                  v-model="form.password_confirmation"
                  type="password"
                  class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-1 focus:ring-black transition-all"
                  placeholder="Confirme a nova senha"
                />
              </div>
            </div>
          </div>

          <!-- Feedback -->
          <div v-if="mensagem" :class="['p-4 rounded-xl text-sm font-bold', mensagemTipo === 'sucesso' ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800']">
            {{ mensagem }}
          </div>

          <button
            type="submit"
            :disabled="loading"
            class="w-full bg-black text-white py-4 px-6 rounded-xl font-black uppercase tracking-widest hover:bg-gray-800 transition-all shadow-lg disabled:opacity-50"
          >
            {{ loading ? 'Salvando...' : 'Salvar Alterações' }}
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuth } from '@/composables/useAuth';
import axios from 'axios';

const auth = useAuth();
const loading = ref(false);
const errors = ref({});
const mensagem = ref('');
const mensagemTipo = ref('sucesso');

const form = ref({
  name: '',
  email: '',
  cpf: '',
  password: '',
  password_confirmation: ''
});

onMounted(() => {
  const user = auth.user.value;
  if (user) {
    form.value.name = user.name;
    form.value.email = user.email;
    form.value.cpf = user.aluno?.cpf || '';
  }
});

const salvarPerfil = async () => {
  loading.value = true;
  errors.value = {};
  mensagem.value = '';

  try {
    const response = await axios.put('/api/perfil', form.value);
    mensagem.value = 'Perfil atualizado com sucesso!';
    mensagemTipo.value = 'sucesso';
    
    // Limpar campos de senha
    form.value.password = '';
    form.value.password_confirmation = '';
    
    // Atualizar dados no auth composable
    await auth.checkAuth(true); // Forçar atualização do cache
  } catch (error) {
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors;
    } else {
      mensagem.value = error.response?.data?.message || 'Erro ao atualizar perfil';
      mensagemTipo.value = 'erro';
    }
  } finally {
    loading.value = false;
  }
};
</script>
