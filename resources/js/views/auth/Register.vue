<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center px-4 py-12">
    <div class="max-w-md w-full">
      <!-- Header -->
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Criar Conta</h1>
        <p class="text-gray-600">Preencha os dados abaixo para se cadastrar</p>
      </div>

      <!-- Form -->
      <div class="bg-white rounded-lg shadow-md p-8">
        <form @submit.prevent="handleRegister">
          <!-- Nome -->
          <div class="mb-4">
            <label for="nome" class="block text-sm font-medium text-gray-700 mb-2">
              Nome *
            </label>
            <input
              id="nome"
              v-model="form.nome"
              type="text"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400"
              placeholder="Seu nome completo"
            />
            <span v-if="errors.nome" class="text-red-500 text-sm mt-1">{{ errors.nome[0] }}</span>
          </div>

          <!-- CPF -->
          <div class="mb-4">
            <label for="cpf" class="block text-sm font-medium text-gray-700 mb-2">
              CPF *
            </label>
            <input
              id="cpf"
              v-model="form.cpf"
              type="text"
              required
              @input="formatCPF"
              maxlength="14"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400"
              placeholder="000.000.000-00"
            />
            <span v-if="errors.cpf" class="text-red-500 text-sm mt-1">{{ errors.cpf[0] }}</span>
          </div>

          <!-- Email -->
          <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
              E-mail *
            </label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400"
              placeholder="email@address.com"
            />
            <span v-if="errors.email" class="text-red-500 text-sm mt-1">{{ errors.email[0] }}</span>
          </div>

          <!-- Senha -->
          <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
              Senha *
            </label>
            <input
              id="password"
              v-model="form.password"
              type="password"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400"
              placeholder="Mínimo 8 caracteres"
            />
            <span v-if="errors.password" class="text-red-500 text-sm mt-1">{{ errors.password[0] }}</span>
          </div>

          <!-- Confirmação de Senha -->
          <div class="mb-4">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
              Confirmar Senha *
            </label>
            <input
              id="password_confirmation"
              v-model="form.password_confirmation"
              type="password"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400"
              placeholder="Digite a senha novamente"
            />
            <span v-if="errors.password_confirmation" class="text-red-500 text-sm mt-1">{{ errors.password_confirmation[0] }}</span>
          </div>

          <!-- Aceitar Políticas de Privacidade -->
          <div class="mb-6">
            <label class="flex items-start cursor-pointer">
              <input
                v-model="form.aceita_politicas"
                type="checkbox"
                required
                class="mt-1 mr-2 w-4 h-4 text-gray-800 border-gray-300 rounded focus:ring-gray-400"
              />
              <span class="text-sm text-gray-700">
                Concordo com as 
                <button
                  type="button"
                  @click="showPrivacyModal = true"
                  class="text-gray-800 underline hover:text-gray-900"
                >
                  Políticas de Privacidade
                </button>
              </span>
            </label>
            <span v-if="errors.aceita_politicas" class="text-red-500 text-sm mt-1 block">{{ errors.aceita_politicas[0] }}</span>
          </div>

          <!-- Mensagem de erro -->
          <div v-if="errorMessage" class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm">
            {{ errorMessage }}
          </div>

          <!-- Botões -->
          <div class="space-y-3">
            <button
              type="submit"
              :disabled="loading"
              class="w-full bg-gray-800 text-white py-2 px-4 rounded-lg hover:bg-gray-900 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{ loading ? 'Cadastrando...' : 'Cadastrar' }}
            </button>

            <router-link
              to="/login"
              class="block w-full text-center text-gray-700 hover:text-gray-900 text-sm"
            >
              Já tem conta? Faça login
            </router-link>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal de Políticas de Privacidade -->
    <div
      v-if="showPrivacyModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click.self="showPrivacyModal = false"
    >
      <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full max-h-[90vh] overflow-hidden flex flex-col">
        <!-- Header do Modal -->
        <div class="flex justify-between items-center p-6 border-b border-gray-200">
          <h2 class="text-2xl font-bold text-gray-900">Políticas de Privacidade</h2>
          <button
            @click="showPrivacyModal = false"
            class="text-gray-400 hover:text-gray-600 transition-colors"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Conteúdo do Modal -->
        <div class="p-6 overflow-y-auto flex-1">
          <div class="prose max-w-none text-gray-700">
            <h3 class="text-xl font-semibold mb-4">1. Coleta de Informações</h3>
            <p class="mb-4">
              Ao se cadastrar em nossa plataforma, coletamos as seguintes informações: nome completo, CPF, endereço de e-mail e senha.
              Essas informações são essenciais para fornecer nossos serviços e garantir a segurança de sua conta.
            </p>

            <h3 class="text-xl font-semibold mb-4">2. Uso das Informações</h3>
            <p class="mb-4">
              Utilizamos suas informações pessoais para:
            </p>
            <ul class="list-disc list-inside mb-4 space-y-2">
              <li>Processar e gerenciar seu cadastro na plataforma</li>
              <li>Fornecer acesso aos cursos e materiais disponíveis</li>
              <li>Enviar informações sobre seus cursos e atualizações relevantes</li>
              <li>Melhorar nossos serviços e experiência do usuário</li>
              <li>Cumprir obrigações legais e regulatórias</li>
            </ul>

            <h3 class="text-xl font-semibold mb-4">3. Proteção de Dados</h3>
            <p class="mb-4">
              Implementamos medidas de segurança técnicas e organizacionais para proteger suas informações pessoais contra acesso não autorizado,
              alteração, divulgação ou destruição. Seus dados são armazenados de forma segura e são acessíveis apenas por pessoal autorizado.
            </p>

            <h3 class="text-xl font-semibold mb-4">4. Compartilhamento de Informações</h3>
            <p class="mb-4">
              Não vendemos, alugamos ou compartilhamos suas informações pessoais com terceiros para fins comerciais.
              Podemos compartilhar suas informações apenas nas seguintes situações:
            </p>
            <ul class="list-disc list-inside mb-4 space-y-2">
              <li>Com prestadores de serviços que nos ajudam a operar a plataforma (sob acordo de confidencialidade)</li>
              <li>Quando exigido por lei ou ordem judicial</li>
              <li>Para proteger nossos direitos e segurança</li>
            </ul>

            <h3 class="text-xl font-semibold mb-4">5. Seus Direitos</h3>
            <p class="mb-4">
              Você tem o direito de:
            </p>
            <ul class="list-disc list-inside mb-4 space-y-2">
              <li>Acessar suas informações pessoais a qualquer momento</li>
              <li>Corrigir informações incorretas ou desatualizadas</li>
              <li>Solicitar a exclusão de suas informações pessoais</li>
              <li>Opor-se ao processamento de suas informações pessoais</li>
              <li>Solicitar a portabilidade de seus dados</li>
            </ul>

            <h3 class="text-xl font-semibold mb-4">6. Cookies e Tecnologias Similares</h3>
            <p class="mb-4">
              Utilizamos cookies e tecnologias similares para melhorar sua experiência na plataforma, analisar o uso do site
              e personalizar o conteúdo. Você pode configurar seu navegador para recusar cookies, mas isso pode afetar algumas funcionalidades.
            </p>

            <h3 class="text-xl font-semibold mb-4">7. Alterações nas Políticas</h3>
            <p class="mb-4">
              Podemos atualizar estas Políticas de Privacidade periodicamente. Notificaremos você sobre alterações significativas
              por e-mail ou através de avisos na plataforma. Recomendamos que revise esta política regularmente.
            </p>

            <h3 class="text-xl font-semibold mb-4">8. Contato</h3>
            <p class="mb-4">
              Se você tiver dúvidas, preocupações ou solicitações relacionadas a estas Políticas de Privacidade ou ao tratamento
              de suas informações pessoais, entre em contato conosco através do e-mail: privacidade@marcasite.com.br
            </p>

            <p class="text-sm text-gray-500 mt-6">
              <strong>Última atualização:</strong> {{ new Date().toLocaleDateString('pt-BR') }}
            </p>
          </div>
        </div>

        <!-- Footer do Modal -->
        <div class="p-6 border-t border-gray-200 flex justify-end">
          <button
            @click="showPrivacyModal = false"
            class="px-6 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-colors"
          >
            Fechar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuth } from '@/composables/useAuth';

const router = useRouter();
const { register, loading } = useAuth();

const form = ref({
  nome: '',
  email: '',
  cpf: '',
  password: '',
  password_confirmation: '',
  aceita_politicas: false,
});

const errors = ref({});
const errorMessage = ref('');
const showPrivacyModal = ref(false);

/**
 * Formata o CPF para o padrão 000.000.000-00
 */
const formatCPF = (event) => {
  let value = event.target.value.replace(/\D/g, ''); // Remove tudo que não é dígito
  
  // Limita a 11 dígitos
  if (value.length > 11) {
    value = value.slice(0, 11);
  }
  
  // Aplica a máscara
  if (value.length <= 3) {
    form.value.cpf = value;
  } else if (value.length <= 6) {
    form.value.cpf = value.slice(0, 3) + '.' + value.slice(3);
  } else if (value.length <= 9) {
    form.value.cpf = value.slice(0, 3) + '.' + value.slice(3, 6) + '.' + value.slice(6);
  } else {
    form.value.cpf = value.slice(0, 3) + '.' + value.slice(3, 6) + '.' + value.slice(6, 9) + '-' + value.slice(9, 11);
  }
};

const handleRegister = async () => {
  errors.value = {};
  errorMessage.value = '';

  if (!form.value.aceita_politicas) {
    errors.value.aceita_politicas = ['Você deve concordar com as Políticas de Privacidade para continuar.'];
    return;
  }

  // Limpar máscara do CPF antes de enviar
  const dataToSend = {
    ...form.value,
    cpf: form.value.cpf.replace(/\D/g, ''), // Remove tudo que não é dígito
  };

  const result = await register(dataToSend);

  if (result.success) {
    await router.push(result.redirect);
  } else {
    if (result.errors) {
      errors.value = result.errors;
    } else {
      errorMessage.value = result.message || 'Erro ao fazer cadastro';
    }
  }
};
</script>
