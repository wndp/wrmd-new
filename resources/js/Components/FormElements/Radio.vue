<script setup>
import { computed } from 'vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';

const emit = defineEmits(['update:modelValue']);

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    value: {
      type: String,
      default: undefined,
    },
    label: {
      type: String,
      default: null
    }
});

const proxyModelValue = computed({
  get() {
    return props.modelValue;
  },

  set(val) {
    emit('update:modelValue', val);
  },
});
</script>

<template>
  <div class="flex items-center">
    <input
      v-model="proxyModelValue"
      type="radio"
      :value="value"
      class="focus:ring-green-500 h-4 w-4 text-green-600 border-gray-300 rounded-full disabled:bg-gray-200"
    >
    <InputLabel
      v-if="label"
      :for="value"
      class="ml-2 font-normal"
    >{{ label }}</InputLabel>
  </div>
</template>
