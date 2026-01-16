<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center px-4 py-12">
    <div class="max-w-md w-full">
      <!-- Logo/Header -->
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Marcasite Cursos</h1>
        <p class="text-gray-600">Faça login para continuar</p>
      </div>

      <!-- Form -->
      <div class="bg-white rounded-lg shadow-md p-8">
        <form @submit.prevent="handleLogin">
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
          <div class="mb-6">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
              Senha *
            </label>
            <input
              id="password"
              v-model="form.password"
              type="password"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400"
              placeholder="******"
            />
            <span v-if="errors.password" class="text-red-500 text-sm mt-1">{{ errors.password[0] }}</span>
          </div>

          <!-- Esqueci minha senha -->
          <div class="mb-6 text-right">
            <router-link
              to="/forgot-password"
              class="text-sm text-gray-600 hover:text-gray-900"
            >
              Esqueci minha senha
            </router-link>
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
              {{ loading ? 'Entrando...' : 'Entrar' }}
            </button>

            <router-link
              to="/register"
              class="block w-full text-center text-gray-700 hover:text-gray-900 text-sm"
            >
              Não tem conta? Cadastre-se
            </router-link>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuth } from '@/composables/useAuth';

const router = useRouter();
const { login, loading } = useAuth();

const form = ref({
  email: '',
  password: '',
});

const errors = ref({});
const errorMessage = ref('');

const handleLogin = async () => {
  errors.value = {};
  errorMessage.value = '';

  const result = await login(form.value.email, form.value.password);

  if (result.success) {
    await router.push(result.redirect);
  } else {
    if (result.errors) {
      errors.value = result.errors;
    } else {
      errorMessage.value = result.message || 'Erro ao fazer login';
    }
  }
};
</script>
