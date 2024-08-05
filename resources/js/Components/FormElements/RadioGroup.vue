<template>
  <div class="flex sapce-between space-x-4">
    <div
      v-for="(option, i) in computedOptions"
      :key="i"
      class="flex items-start"
    >
      <div class="flex items-center">
        <input
          :id="option.value+i"
          v-model="proxyModelValue"
          :name="name"
          type="radio"
          :value="option.value"
          class="focus:ring-green-500 h-4 w-4 text-green-600 border-gray-300 rounded-full disabled:bg-gray-200"
          @input="$emit('update:modelValue', option.value)"
        >
        <Label
          :for="option.value+i"
          class="ml-2 font-normal"
        >{{ option.name }}</Label>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import Label from '@/Components/FormElements/Label.vue';

const emit = defineEmits(['update:modelValue']);

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    options: {
      type: Array,
      default: undefined,
    },
    name: {
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

const computedOptions = computed(() => {
  if (typeof props.options[1] === 'object' && props.options[1] !== null) {
      return props.options;
  }
  return props.options.map(n => {
      return {
          value: n,
          name: n
      };
  });
})
</script>
