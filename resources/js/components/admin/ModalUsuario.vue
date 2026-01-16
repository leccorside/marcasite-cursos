<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-4xl w-full overflow-hidden flex flex-col">
      <!-- Header -->
      <div class="flex justify-between items-center p-8">
        <h2 class="text-2xl font-bold text-gray-800">{{ usuario ? 'Editar usuário' : 'Novo usuário' }}</h2>
        <button @click="$emit('fechar')" class="text-gray-400 hover:text-gray-600 transition-colors">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <form @submit.prevent="handleSubmit" class="p-8 pt-0">
        <div class="flex flex-col md:flex-row gap-12">
          <!-- Esquerda: Detalhes -->
          <div class="flex-1 space-y-6">
            <h3 class="text-lg font-bold text-gray-700 mb-4">Detalhes de usuário</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Nome*</label>
                <input
                  v-model="form.name"
                  type="text"
                  placeholder="Seu nome"
                  class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-1 focus:ring-gray-400"
                  required
                />
                <p v-if="errors.name" class="mt-1 text-xs text-red-500 font-bold">{{ errors.name[0] }}</p>
              </div>
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Tipo de usuário*</label>
                <select
                  v-model="form.tipo"
                  class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-1 focus:ring-gray-400 bg-white appearance-none"
                  required
                >
                  <option value="" disabled>Select</option>
                  <option value="admin">Administrador</option>
                  <option value="aluno">Aluno</option>
                </select>
                <p v-if="errors.tipo" class="mt-1 text-xs text-red-500 font-bold">{{ errors.tipo[0] }}</p>
              </div>
            </div>

            <div>
              <label class="block text-sm font-bold text-gray-700 mb-2">Email *</label>
              <input
                v-model="form.email"
                type="email"
                placeholder="email@address.com"
                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-1 focus:ring-gray-400"
                required
              />
              <p v-if="errors.email" class="mt-1 text-xs text-red-500 font-bold">{{ errors.email[0] }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Senha</label>
                <input
                  v-model="form.password"
                  type="password"
                  placeholder="******"
                  class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-1 focus:ring-gray-400"
                  :required="!usuario"
                />
                <p v-if="errors.password" class="mt-1 text-xs text-red-500 font-bold">{{ errors.password[0] }}</p>
              </div>
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Confirmação de Senha</label>
                <input
                  v-model="form.password_confirmation"
                  type="password"
                  placeholder="******"
                  class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-1 focus:ring-gray-400"
                  :required="!usuario && form.password"
                />
              </div>
            </div>

            <div class="flex items-center gap-3">
              <input 
                type="checkbox" 
                id="enviar_email" 
                v-model="form.enviar_email"
                class="w-5 h-5 rounded border-gray-300 text-black focus:ring-black"
              >
              <label for="enviar_email" class="text-sm font-bold text-gray-700 cursor-pointer">
                Enviar confirmação por e-mail
              </label>
            </div>
            
            <div class="flex items-center gap-3">
              <input 
                type="checkbox" 
                id="ativo" 
                v-model="form.ativo"
                class="w-5 h-5 rounded border-gray-300 text-black focus:ring-black"
              >
              <label for="ativo" class="text-sm font-bold text-gray-700 cursor-pointer">
                Usuário ativo
              </label>
            </div>
          </div>

          <!-- Direita: Foto de Perfil -->
          <div class="md:w-64 flex flex-col items-center">
            <h3 class="text-lg font-bold text-gray-700 mb-8 self-start">Foto de perfil</h3>
            
            <div 
              @click="$refs.fileInput.click()"
              class="relative w-48 h-48 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 flex flex-col items-center justify-center cursor-pointer hover:bg-gray-100 transition-colors overflow-hidden group"
            >
              <template v-if="fotoPreview">
                <img :src="fotoPreview" class="w-full h-full object-cover" />
                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                  <span class="text-white text-xs font-bold uppercase tracking-widest">Alterar</span>
                </div>
              </template>
              <template v-else>
                <div class="bg-gray-200 rounded-full p-6 mb-2">
                  <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                </div>
              </template>
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
              class="mt-6 bg-[#666] text-white px-10 py-2.5 rounded-lg font-bold hover:bg-black transition-colors"
            >
              Upload
            </button>
            <p v-if="errors.foto_perfil" class="mt-2 text-xs text-red-500 font-bold">{{ errors.foto_perfil[0] }}</p>
          </div>
        </div>

        <!-- Botoes -->
        <div class="flex gap-4 mt-12">
          <button
            type="submit"
            :disabled="loading"
            class="bg-[#333] text-white px-10 py-3 rounded-lg font-bold hover:bg-black transition-all disabled:opacity-50"
          >
            {{ loading ? 'Processando...' : (usuario ? 'Salvar' : 'Adicionar') }}
          </button>
          <button
            type="button"
            @click="$emit('fechar')"
            class="bg-gray-200 text-gray-700 px-10 py-3 rounded-lg font-bold hover:bg-gray-300 transition-all"
          >
            Cancelar
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const props = defineProps({
  usuario: { type: Object, default: null }
});

const emit = defineEmits(['fechar', 'salvar']);

const form = ref({
  name: '',
  email: '',
  tipo: '',
  password: '',
  password_confirmation: '',
  ativo: true,
  enviar_email: true,
  foto_perfil: null
});

const fotoPreview = ref(null);
const errors = ref({});
const loading = ref(false);

onMounted(() => {
  if (props.usuario) {
    form.value.name = props.usuario.name;
    form.value.email = props.usuario.email;
    form.value.tipo = props.usuario.tipo;
    form.value.ativo = props.usuario.ativo;
    if (props.usuario.foto_perfil) {
      fotoPreview.value = `/storage/${props.usuario.foto_perfil}`;
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

const handleSubmit = async () => {
  loading.value = true;
  errors.value = {};

  const formData = new FormData();
  formData.append('name', form.value.name);
  formData.append('email', form.value.email);
  formData.append('tipo', form.value.tipo);
  formData.append('ativo', form.value.ativo ? '1' : '0');
  
  if (form.value.password) {
    formData.append('password', form.value.password);
    formData.append('password_confirmation', form.value.password_confirmation);
  }

  if (form.value.foto_perfil) {
    formData.append('foto_perfil', form.value.foto_perfil);
  }

  emit('salvar', formData, (errs) => {
    loading.value = false;
    errors.value = errs;
  });
};
</script>
