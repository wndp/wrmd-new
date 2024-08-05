<script setup>
import { ref, watch } from 'vue'
import { CheckIcon, XMarkIcon } from '@heroicons/vue/20/solid';

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: null
  },
  id: {
    type: String,
    default: null
  }
});

const emit = defineEmits(['update:modelValue', 'change'])

const options = [null, true, false];
const option = ref(props.modelValue !== null ? props.modelValue : options[0])

const nextValue = () => {
  let currentIndex = options.indexOf(option.value);
  let nextIndex = (currentIndex + 1) % options.length;

  option.value = options[nextIndex];
}

watch(option, () => {
  emit('update:modelValue', option.value);
  emit('change', option.value);
});
</script>

<template>
  <button
    :id="id"
    class="relative inline border text-white focus:ring-green-500 h-4 w-4 p-2 rounded disabled:bg-gray-200"
    :class="{
      'border-gray-300 bg-white': option === null,
      'border-green-500 bg-green-500': option === true,
      'border-red-500 bg-red-500': option === false,
    }"
    @click="nextValue"
  >
    <CheckIcon
      v-if="option === true"
      class="absolute top-0 left-0 w-4 h-4"
    />
    <XMarkIcon
      v-if="option === false"
      class="absolute top-0 left-0 w-4 h-4"
    />
  </button>
</template>
