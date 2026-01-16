import { ref } from 'vue';

// Estado global do loading
const isLoading = ref(false);

/**
 * Composable para gerenciar o estado de loading global
 */
export function useLoading() {
  /**
   * Inicia o loading
   */
  const startLoading = () => {
    isLoading.value = true;
  };

  /**
   * Para o loading
   */
  const stopLoading = () => {
    isLoading.value = false;
  };

  /**
   * Executa uma função assíncrona com loading automático
   */
  const withLoading = async (fn) => {
    try {
      startLoading();
      const result = await fn();
      return result;
    } finally {
      stopLoading();
    }
  };

  return {
    isLoading,
    startLoading,
    stopLoading,
    withLoading,
  };
}
