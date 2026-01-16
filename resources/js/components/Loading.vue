<template>
  <Transition
    name="loading"
    enter-active-class="loading-enter-active"
    leave-active-class="loading-leave-active"
    enter-from-class="loading-enter-from"
    leave-to-class="loading-leave-to"
  >
    <div v-if="loading" class="loading-overlay">
      <div class="loading-container">
        <div class="loading-spinner">
          <div class="spinner-ring"></div>
          <div class="spinner-ring"></div>
          <div class="spinner-ring"></div>
          <div class="spinner-ring"></div>
        </div>
        <p class="loading-text">Carregando...</p>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { computed } from 'vue';
import { useLoading } from '@/composables/useLoading';

const { isLoading } = useLoading();

const loading = computed(() => isLoading.value);
</script>

<style scoped>
.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(255, 255, 255, 0.95);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
  backdrop-filter: blur(4px);
}

.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1.5rem;
}

.loading-spinner {
  position: relative;
  width: 80px;
  height: 80px;
}

.spinner-ring {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  border: 4px solid transparent;
  border-top-color: #3b82f6;
  border-radius: 50%;
  animation: spin 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
}

.spinner-ring:nth-child(1) {
  animation-delay: -0.45s;
  border-top-color: #3b82f6;
}

.spinner-ring:nth-child(2) {
  animation-delay: -0.3s;
  border-top-color: #60a5fa;
  width: 70%;
  height: 70%;
  top: 15%;
  left: 15%;
}

.spinner-ring:nth-child(3) {
  animation-delay: -0.15s;
  border-top-color: #93c5fd;
  width: 50%;
  height: 50%;
  top: 25%;
  left: 25%;
}

.spinner-ring:nth-child(4) {
  border-top-color: #dbeafe;
  width: 30%;
  height: 30%;
  top: 35%;
  left: 35%;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

.loading-text {
  color: #4b5563;
  font-size: 1rem;
  font-weight: 500;
  letter-spacing: 0.05em;
  animation: pulse 1.5s ease-in-out infinite;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

/* Transições de entrada e saída */
.loading-enter-active,
.loading-leave-active {
  transition: opacity 0.3s ease;
}

.loading-enter-from,
.loading-leave-to {
  opacity: 0;
}

/* Dark mode support (opcional, para futuro) */
@media (prefers-color-scheme: dark) {
  .loading-overlay {
    background-color: rgba(17, 24, 39, 0.95);
  }

  .loading-text {
    color: #e5e7eb;
  }
}
</style>
