<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center px-4 py-12">
    <div class="max-w-md w-full">
      <!-- Header -->
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Redefinir Senha</h1>
        <p class="text-gray-600">Digite sua nova senha</p>
      </div>

      <!-- Form -->
      <div class="bg-white rounded-lg shadow-md p-8">
        <form @submit.prevent="handleResetPassword">
          <!-- Token (hidden) -->
          <input type="hidden" v-model="form.token" />

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

          <!-- Nova Senha -->
          <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
              Nova Senha *
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

          <!-- Confirmar Senha -->
          <div class="mb-6">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
              Confirmar Nova Senha *
            </label>
            <input
              id="password_confirmation"
              v-model="form.password_confirmation"
              type="password"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400"
            />
            <span v-if="errors.password_confirmation" class="text-red-500 text-sm mt-1">{{ errors.password_confirmation[0] }}</span>
          </div>

          <!-- Mensagem de sucesso -->
          <div v-if="successMessage" class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded-lg text-sm">
            {{ successMessage }}
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
              {{ loading ? 'Redefinindo...' : 'Redefinir Senha' }}
            </button>

            <router-link
              to="/login"
              class="block w-full text-center text-gray-700 hover:text-gray-900 text-sm"
            >
              Voltar para login
            </router-link>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuth } from '@/composables/useAuth';

const router = useRouter();
const route = useRoute();
const { resetPassword, loading } = useAuth();

const form = ref({
  token: '',
  email: '',
  password: '',
  password_confirmation: '',
});

const errors = ref({});
const errorMessage = ref('');
const successMessage = ref('');

onMounted(() => {
  // Pegar token e email da URL
  form.value.token = route.query.token || '';
  form.value.email = route.query.email || '';
});

const handleResetPassword = async () => {
  errors.value = {};
  errorMessage.value = '';
  successMessage.value = '';

  const result = await resetPassword(
    form.value.token,
    form.value.email,
    form.value.password,
    form.value.password_confirmation
  );

  if (result.success) {
    successMessage.value = result.message || 'Senha redefinida com sucesso!';
    setTimeout(() => {
      router.push('/login');
    }, 2000);
  } else {
    if (result.errors) {
      errors.value = result.errors;
    } else {
      errorMessage.value = result.message || 'Erro ao redefinir senha';
    }
  }
};
</script>
