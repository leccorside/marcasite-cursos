<template>
  <header class="bg-white border-b border-gray-200 sticky top-0 z-40">
    <div class="px-6 py-4 flex items-center justify-between">
      <div class="flex items-center">
        <!-- Left: Hamburger Menu (Mobile) -->
        <button
          @click="$emit('toggle-sidebar')"
          class="lg:hidden text-gray-600 hover:text-gray-900 mr-4"
          aria-label="Toggle menu"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>

        <!-- Search Icon (Mobile) / Search Input (Desktop) -->
        <div class="relative flex items-center">
          <button 
            @click="showMobileSearch = !showMobileSearch"
            class="md:hidden p-2 text-gray-600 hover:text-gray-900 focus:outline-none"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </button>

          <!-- Desktop Search -->
          <div class="hidden md:block relative w-64 lg:w-96">
            <input
              type="text"
              placeholder="Buscar..."
              class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400 focus:border-transparent"
            />
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Right: Notifications and User -->
      <div class="flex items-center gap-4">
        <!-- ... resto do código ... -->
        <!-- Notifications -->
        <button class="relative text-gray-600 hover:text-gray-900">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
            />
          </svg>
          <span
            v-if="notificationCount > 0"
            class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center"
          >
            {{ notificationCount }}
          </span>
        </button>

        <!-- User Menu -->
        <div class="relative">
          <div class="flex items-center gap-3">
            <span class="text-gray-700 font-medium">{{ userName }}</span>
            <button
              @click.stop="showUserMenu = !showUserMenu"
              class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center hover:bg-gray-200 transition-colors cursor-pointer overflow-hidden border border-gray-200"
            >
              <img 
                v-if="auth.user.value?.foto_perfil && !imageError" 
                :src="`/storage/${auth.user.value.foto_perfil}`" 
                @error="imageError = true"
                class="w-full h-full object-cover" 
              />
              <svg v-else class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                />
              </svg>
            </button>
          </div>

          <!-- Dropdown Menu -->
          <transition
            enter-active-class="transition ease-out duration-100"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
          >
            <div
              v-if="showUserMenu"
              ref="userMenuRef"
              class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
            >
              <div class="px-4 py-2 border-b border-gray-200">
                <p class="text-sm font-medium text-gray-900">{{ userName }}</p>
                <p class="text-xs text-gray-500">{{ userEmail }}</p>
              </div>
              <button
                @click="handleLogout"
                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors flex items-center gap-2"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                  />
                </svg>
                Sair
              </button>
            </div>
          </transition>
        </div>
      </div>
    </div>

    <!-- Mobile Search (Abaixo do Header) -->
    <transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0 -translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-2"
    >
      <div v-if="showMobileSearch" class="md:hidden px-6 pb-4">
        <div class="relative w-full">
          <input
            ref="mobileSearchInput"
            type="text"
            placeholder="O que você procura?"
            class="w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400 focus:border-transparent bg-gray-50 shadow-inner"
            @blur="showMobileSearch = false"
          />
          <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <button @click="showMobileSearch = false" class="absolute right-3 top-3 text-gray-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    </transition>
  </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue';
import { useRouter } from 'vue-router';
import { useAuth } from '@/composables/useAuth';

defineEmits(['toggle-sidebar']);

const router = useRouter();
const auth = useAuth();
const showUserMenu = ref(false);
const showMobileSearch = ref(false);
const notificationCount = ref(2);
const userMenuRef = ref(null);
const mobileSearchInput = ref(null);
const imageError = ref(false);

watch(showMobileSearch, (val) => {
  if (val) {
    nextTick(() => {
      mobileSearchInput.value?.focus();
    });
  }
});

const userName = computed(() => {
  return auth.user.value?.name || auth.user.value?.aluno?.nome || 'Usuário';
});

const userEmail = computed(() => {
  return auth.user.value?.email || '';
});

// Resetar erro de imagem quando a foto do usuário mudar
watch(() => auth.user.value?.foto_perfil, () => {
  imageError.value = false;
});

const handleLogout = async () => {
  showUserMenu.value = false;
  await auth.logout();
  await router.push('/login');
};

// Fechar menu ao clicar fora
const handleClickOutside = (event) => {
  if (userMenuRef.value && !userMenuRef.value.contains(event.target)) {
    const menuButton = event.target.closest('button[class*="rounded-full"]');
    if (!menuButton) {
      showUserMenu.value = false;
    }
  }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>
