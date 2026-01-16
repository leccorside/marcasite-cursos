<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
      <!-- Header -->
      <div class="p-6 border-b border-gray-200">
        <h2 class="text-xl font-bold text-gray-900">{{ titulo }}</h2>
      </div>

      <!-- Body -->
      <div class="p-6">
        <p class="text-gray-700">{{ mensagem }}</p>
      </div>

      <!-- Actions -->
      <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
        <button
          @click="$emit('cancelar')"
          class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
        >
          Cancelar
        </button>
        <button
          @click="$emit('confirmar')"
          class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
        >
          Confirmar
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  titulo: {
    type: String,
    default: 'Confirmar Exclusão',
  },
  mensagem: {
    type: String,
    required: true,
  },
});

defineEmits(['confirmar', 'cancelar']);
</script>
