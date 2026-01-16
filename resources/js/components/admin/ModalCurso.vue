<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-4xl w-full max-h-[95vh] overflow-hidden flex flex-col">
      <!-- Header -->
      <div class="flex justify-between items-center p-8 border-b border-gray-100">
        <h2 class="text-2xl font-bold text-gray-800">{{ curso ? 'Editar curso' : 'Novo curso' }}</h2>
        <button @click="$emit('fechar')" class="text-gray-400 hover:text-gray-600 transition-colors">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Form Body -->
      <form @submit.prevent="handleSalvar" class="overflow-y-auto flex-1 p-8 text-left">
        <div class="flex flex-col lg:flex-row gap-12">
          
          <!-- Lado Esquerdo: Detalhes do curso -->
          <div class="flex-1 space-y-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-none">Detalhes de curso</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Nome -->
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Nome*</label>
                <input v-model="form.nome" type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-gray-400 outline-none" :class="{'border-red-500': erros.nome}" />
                <p v-if="erros.nome" class="text-red-500 text-xs mt-1">{{ erros.nome[0] }}</p>
              </div>
              <!-- Categoria -->
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Categoria*</label>
                <div class="relative">
                  <select v-model="form.categoria" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-gray-400 outline-none bg-white appearance-none" :class="{'border-red-500': erros.categoria}">
                    <option value="">Select</option>
                    <option value="Desenvolvimento">Desenvolvimento</option>
                    <option value="Design">Design</option>
                    <option value="Marketing">Marketing</option>
                  </select>
                  <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none text-gray-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </div>
                </div>
                <p v-if="erros.categoria" class="text-red-500 text-xs mt-1">{{ erros.categoria[0] }}</p>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Valor -->
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Valor*</label>
                <input v-model="displayValor" type="text" placeholder="R$ 0,00" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-gray-400 outline-none" :class="{'border-red-500': erros.valor}" @input="handleValorInput" />
                <p v-if="erros.valor" class="text-red-500 text-xs mt-1">{{ erros.valor[0] }}</p>
              </div>
              <!-- Vagas -->
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Vagas*</label>
                <input v-model="form.vagas_total" type="text" placeholder="300" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-gray-400 outline-none" :class="{'border-red-500': erros.vagas_total}" @input="handleVagasInput" />
                <p v-if="erros.vagas_total" class="text-red-500 text-xs mt-1">{{ erros.vagas_total[0] }}</p>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Inscrições de -->
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Inscrições de:</label>
                <input v-model="form.data_inicio_inscricoes" type="date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-gray-400 outline-none" :class="{'border-red-500': erros.data_inicio_inscricoes}" />
                <p v-if="erros.data_inicio_inscricoes" class="text-red-500 text-xs mt-1">{{ erros.data_inicio_inscricoes[0] }}</p>
              </div>
              <!-- Até -->
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Até:</label>
                <input v-model="form.data_fim_inscricoes" type="date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-gray-400 outline-none" :class="{'border-red-500': erros.data_fim_inscricoes}" />
                <p v-if="erros.data_fim_inscricoes" class="text-red-500 text-xs mt-1">{{ erros.data_fim_inscricoes[0] }}</p>
              </div>
            </div>

            <!-- Descrição -->
            <div>
              <label class="block text-sm font-bold text-gray-700 mb-1">Descrição*</label>
              <textarea v-model="form.descricao" required rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-gray-400 outline-none resize-none" :class="{'border-red-500': erros.descricao}"></textarea>
              <p v-if="erros.descricao" class="text-red-500 text-xs mt-1">{{ erros.descricao[0] }}</p>
            </div>

            <!-- Materiais -->
            <div>
              <label class="block text-sm font-bold text-gray-700 mb-2">Materiais</label>
              
              <div v-if="materiaisExistentes.length > 0 || materiaisSelecionados.length > 0" class="mb-4 space-y-2">
                <!-- Materiais que já estão no banco -->
                <div v-for="mat in materiaisExistentes" :key="mat.id" class="flex items-center justify-between bg-white p-3 rounded-lg border border-gray-200">
                  <div class="flex items-center gap-3 overflow-hidden">
                    <span class="text-gray-400 text-xl font-light shrink-0">+</span>
                    <a :href="`/storage/${mat.arquivo}`" target="_blank" class="text-sm text-gray-700 hover:text-black truncate font-medium" :title="mat.nome">
                      {{ mat.nome }}
                    </a>
                  </div>
                  <button @click="removerMaterialExistente(mat.id)" type="button" class="text-red-400 hover:text-red-600 transition-colors shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>

                <!-- Novos materiais selecionados agora -->
                <div v-for="(file, index) in materiaisSelecionados" :key="'new-'+index" class="flex items-center justify-between bg-white p-3 rounded-lg border border-gray-200">
                  <div class="flex items-center gap-3 overflow-hidden">
                    <span class="text-gray-400 text-xl font-light shrink-0">+</span>
                    <span class="text-sm text-gray-700 truncate font-medium">{{ file.name }}</span>
                  </div>
                  <button @click="removerMaterial(index)" type="button" class="text-red-400 hover:text-red-600 transition-colors shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>
              </div>

              <div class="flex gap-2">
                <input type="file" ref="materialInput" @change="onMaterialChange" class="hidden" multiple />
                <button @click="$refs.materialInput.click()" type="button" class="bg-[#4b5563] text-white px-8 py-2.5 rounded-lg font-bold hover:bg-gray-700 transition-colors">
                  Upload
                </button>
                <button @click="$refs.materialInput.click()" type="button" class="bg-black text-white w-12 h-11 flex items-center justify-center rounded-lg hover:bg-gray-900 transition-colors">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v12M6 12h12" />
                  </svg>
                </button>
              </div>
              <p v-if="erros.materiais" class="text-red-500 text-xs mt-1">{{ erros.materiais[0] }}</p>
            </div>

            <!-- Ativo -->
            <div class="flex items-center gap-2">
              <input v-model="form.ativo" type="checkbox" id="ativo" class="w-5 h-5 accent-gray-800 rounded border-gray-300" />
              <label for="ativo" class="text-sm font-medium text-gray-700 cursor-pointer font-bold">Ativo</label>
            </div>
          </div>

          <!-- Lado Direito: Thumbnail -->
          <div class="w-full lg:w-64 border-l border-gray-100 lg:pl-12 flex flex-col items-center lg:items-start">
            <h3 class="text-lg font-bold text-gray-800 mb-6 border-none">Thumbnail</h3>
            
            <div class="w-48 h-48 bg-gray-50 rounded-3xl flex items-center justify-center mb-6 border border-gray-50 shadow-inner overflow-hidden relative group">
              <img v-if="thumbnailPreview" :src="thumbnailPreview" class="w-full h-full object-cover" />
              <svg v-else class="w-24 h-24 text-gray-200" fill="currentColor" viewBox="0 0 24 24">
                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zm-5.04-6.71l-2.75 3.54-1.96-2.36L6.5 17h11l-3.54-4.71z"/>
              </svg>
              <div v-if="thumbnailPreview" @click="removerThumbnail" class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                <span class="text-white text-xs font-bold">Remover</span>
              </div>
            </div>

            <input type="file" ref="thumbnailInput" @change="onThumbnailChange" accept="image/jpeg,image/jpg,image/png,image/gif" class="hidden" />
            <button @click="$refs.thumbnailInput.click()" type="button" class="w-full bg-gray-600 text-white py-2 rounded-lg font-medium hover:bg-gray-700 transition-colors">Upload</button>
            <p v-if="erros.thumbnail" class="text-red-500 text-xs mt-1">{{ erros.thumbnail[0] }}</p>
          </div>
        </div>

        <!-- Botões de Ação -->
        <div class="mt-12 flex gap-4">
          <button type="submit" :disabled="salvando" class="bg-gray-800 text-white px-10 py-2.5 rounded-lg font-bold hover:bg-gray-900 transition-colors disabled:opacity-50 min-w-[140px]">
            {{ salvando ? 'Salvando...' : (curso ? 'Salvar' : 'Adicionar') }}
          </button>
          <button type="button" @click="$emit('fechar')" class="bg-gray-100 text-gray-700 px-10 py-2.5 rounded-lg font-bold hover:bg-gray-200 transition-colors min-w-[140px]">
            Cancelar
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';

const props = defineProps({
  curso: { type: Object, default: null },
});

const emit = defineEmits(['fechar', 'salvar']);

const form = ref({
  nome: '',
  descricao: '',
  categoria: '',
  valor: '',
  vagas_total: 300,
  data_inicio_inscricoes: '',
  data_fim_inscricoes: '',
  ativo: true,
  thumbnail: null,
  materiais: []
});

const displayValor = ref('');
const thumbnailPreview = ref(null);
const materiaisSelecionados = ref([]);
const materiaisExistentes = ref([]);
const erros = ref({});
const salvando = ref(false);

const thumbnailInput = ref(null);
const materialInput = ref(null);

// Máscara de Moeda
const handleValorInput = (e) => {
  let value = e.target.value.replace(/\D/g, '');
  if (!value) {
    displayValor.value = '';
    form.value.valor = '';
    return;
  }
  const numericValue = Number(value) / 100;
  displayValor.value = numericValue.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
  form.value.valor = numericValue;
};

// Máscara de Vagas (apenas inteiros)
const handleVagasInput = (e) => {
  let value = e.target.value.replace(/\D/g, '');
  form.value.vagas_total = value ? parseInt(value) : '';
  e.target.value = value;
};

const onThumbnailChange = (e) => {
  const file = e.target.files[0];
  if (!file) return;
  const allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
  if (!allowed.includes(file.type)) {
    alert('Use apenas JPG, JPEG, PNG ou GIF.');
    return;
  }
  form.value.thumbnail = file;
  thumbnailPreview.value = URL.createObjectURL(file);
};

const removerThumbnail = () => {
  form.value.thumbnail = null;
  thumbnailPreview.value = null;
  if (thumbnailInput.value) thumbnailInput.value.value = '';
};

const onMaterialChange = (e) => {
  const files = Array.from(e.target.files);
  materiaisSelecionados.value.push(...files);
  form.value.materiais = materiaisSelecionados.value;
};

const removerMaterial = (index) => {
  materiaisSelecionados.value.splice(index, 1);
  form.value.materiais = materiaisSelecionados.value;
};

const removerMaterialExistente = (id) => {
  if (confirm('Deseja remover este material permanentemente ao salvar?')) {
    materiaisExistentes.value = materiaisExistentes.value.filter(m => m.id !== id);
    // Adiciona uma flag para o backend saber quais materiais foram removidos (opcional, dependendo da lógica do controller)
    if (!form.value.materiais_removidos) form.value.materiais_removidos = [];
    form.value.materiais_removidos.push(id);
  }
};

const handleSalvar = () => {
  erros.value = {};
  salvando.value = true;
  const formData = new FormData();
  
  Object.keys(form.value).forEach(key => {
    if (key !== 'materiais' && key !== 'thumbnail') {
      let value = form.value[key];
      // Converte boolean para 1 ou 0 para garantir que o Laravel aceite via FormData
      if (key === 'ativo') {
        value = value ? 1 : 0;
      }
      if (value !== null && value !== undefined) {
        formData.append(key, value);
      }
    }
  });

  if (form.value.thumbnail instanceof File) formData.append('thumbnail', form.value.thumbnail);
  
  // Enviar lista de IDs de materiais para remover
  if (form.value.materiais_removidos && form.value.materiais_removidos.length > 0) {
    form.value.materiais_removidos.forEach((id, i) => {
      formData.append(`materiais_removidos[${i}]`, id);
    });
  }

  if (form.value.materiais && form.value.materiais.length > 0) {
    form.value.materiais.forEach((file, i) => {
      if (file instanceof File) {
        formData.append(`materiais[${i}]`, file);
      }
    });
  }
  emit('salvar', formData, (errors) => {
    if (errors && Object.keys(errors).length > 0) erros.value = errors;
    salvando.value = false;
  });
};

watch(() => props.curso, (curso) => {
  if (curso) {
    // Resetar estados antes de carregar o novo curso
    materiaisExistentes.value = [];
    materiaisSelecionados.value = [];
    thumbnailPreview.value = null;

    form.value = { 
      ...curso, 
      data_inicio_inscricoes: curso.data_inicio_inscricoes?.split('T')[0] || '', 
      data_fim_inscricoes: curso.data_fim_inscricoes?.split('T')[0] || '',
      materiais: [],
      thumbnail: null
    };
    
    // Garantir que materiaisExistentes receba os dados do curso
    if (curso.materiais && Array.isArray(curso.materiais)) {
      materiaisExistentes.value = [...curso.materiais];
    }
    
    displayValor.value = curso.valor ? Number(curso.valor).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) : '';
    
    if (curso.thumbnail) {
      thumbnailPreview.value = `/storage/${curso.thumbnail}`;
    }
  } else {
    // Resetar formulário para novo curso
    form.value = { 
      nome: '', 
      descricao: '', 
      categoria: '', 
      valor: '', 
      vagas_total: 300, 
      data_inicio_inscricoes: '', 
      data_fim_inscricoes: '', 
      ativo: true, 
      thumbnail: null, 
      materiais: [] 
    };
    displayValor.value = ''; 
    thumbnailPreview.value = null; 
    materiaisSelecionados.value = [];
    materiaisExistentes.value = [];
  }
  erros.value = {};
}, { immediate: true, deep: true });

onMounted(() => {
  const handleEsc = (e) => { if (e.key === 'Escape') emit('fechar'); };
  window.addEventListener('keydown', handleEsc);
  return () => window.removeEventListener('keydown', handleEsc);
});
</script>
