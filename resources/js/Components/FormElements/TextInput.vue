<template>
  <Autocomplete
    v-if="hasAutoComplete"
    v-model="autocompleteModelValue"
    :source="autocompleteOptions"
    placeholder=""
  />
  <input
    v-else
    ref="input"
    type="text"
    class="focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md py-1.5 px-2"
    :value="modelValue"
    @input="$emit('update:modelValue', $event.target.value)"
  >
</template>

<script setup>
import { onMounted, ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Autocomplete from './Autocomplete.vue';

const props = defineProps({
  modelValue: {
    type: [String, Number]
  },
  autoComplete: {
    type: String,
    default: ''
  }
});
const emit = defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    // if (input.value.hasAttribute('autofocus')) {
    //     input.value.focus();
    // }
});

defineExpose({ focus: () => input.value.focus() });

const autocompletes = usePage().props?.settings?.autocomplete || [];

const autocompleteModelValue = computed({
  get() {
    return props.modelValue;
  },
  set(val) {
    emit('update:modelValue', val);
  },
});

const thisAutocomplete = computed(
  () => autocompletes.find(autocomplete => autocomplete.field === props.autoComplete) || {}
);
const hasAutoComplete = computed(() => thisAutocomplete.value.field === props.autoComplete);
const autocompleteOptions = computed(() => thisAutocomplete.value.values || []);
</script>

