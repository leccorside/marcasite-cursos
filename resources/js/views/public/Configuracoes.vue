<template>
  <div class="p-4 md:p-8 max-w-2xl mx-auto">
    <h1 class="text-2xl md:text-3xl font-black text-black uppercase tracking-tighter mb-8">Configurações de Perfil</h1>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="p-8">
        <form @submit.prevent="salvarPerfil" class="space-y-6">
          <!-- Foto de Perfil -->
          <div class="flex flex-col items-center mb-10 pb-10 border-b border-gray-100">
            <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4 self-start">Foto de Perfil</h3>
            <div 
              @click="$refs.fileInput.click()"
              class="relative w-32 h-32 rounded-3xl overflow-hidden cursor-pointer group border-2 border-gray-100 shadow-sm"
            >
              <img v-if="fotoPreview" :src="fotoPreview" class="w-full h-full object-cover transition-transform group-hover:scale-110" />
              <div v-else class="w-full h-full bg-gray-50 flex items-center justify-center">
                <svg class="w-12 h-12 text-gray-200" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                </svg>
              </div>
              <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </div>
            </div>
            <input 
              ref="fileInput" 
              type="file" 
              class="hidden" 
              accept="image/*" 
              @change="handleFileChange" 
            />
            <button 
              type="button" 
              @click="$refs.fileInput.click()"
              class="mt-4 text-xs font-bold text-gray-500 hover:text-black transition-colors underline underline-offset-4"
            >
              Alterar Foto
            </button>
            <p v-if="errors.foto_perfil" class="mt-2 text-xs text-red-500 font-bold">{{ errors.foto_perfil[0] }}</p>
          </div>

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
  password_confirmation: '',
  foto_perfil: null
});

const fotoPreview = ref(null);

onMounted(() => {
  const user = auth.user.value;
  if (user) {
    form.value.name = user.name;
    form.value.email = user.email;
    form.value.cpf = user.aluno?.cpf || '';
    if (user.foto_perfil) {
      fotoPreview.value = `/storage/${user.foto_perfil}`;
    }
  }
});

const handleFileChange = (e) => {
  const file = e.target.files[0];
  if (file) {
    form.value.foto_perfil = file;
    fotoPreview.value = URL.createObjectURL(file);
  }
};

const salvarPerfil = async () => {
  loading.value = true;
  errors.value = {};
  mensagem.value = '';

  try {
    const formData = new FormData();
    formData.append('name', form.value.name);
    formData.append('_method', 'PUT'); // Necessário para PUT com FormData no Laravel

    if (form.value.password) {
      formData.append('password', form.value.password);
      formData.append('password_confirmation', form.value.password_confirmation);
    }

    if (form.value.foto_perfil instanceof File) {
      formData.append('foto_perfil', form.value.foto_perfil);
    }

    const response = await axios.post('/api/perfil', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
    
    mensagem.value = 'Perfil atualizado com sucesso!';
    mensagemTipo.value = 'sucesso';
    
    // Limpar campos de senha
    form.value.password = '';
    form.value.password_confirmation = '';
    form.value.foto_perfil = null;
    
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
