<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Autocomplete from './Autocomplete.vue';

const props = defineProps({
  modelValue: {
    type: String
  },
  autoComplete: {
    type: String,
    default: ''
  }
});
const emit = defineEmits(['update:modelValue']);

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

<template>
  <Autocomplete
    v-if="hasAutoComplete"
    v-slot="{
      getInputProps,
      getInputEventListeners,
      name
    }"
    v-model="autocompleteModelValue"
    :source="autocompleteOptions"
    placeholder=""
  >
    <textarea
      rows="3"
      class="focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md py-1.5 px-2 shadow-sm"
      v-bind="getInputProps()"
      :name="name"
      v-on="getInputEventListeners()"
    />
  </Autocomplete>
  <textarea
    v-else
    rows="3"
    class="focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md py-1.5 px-2 shadow-sm"
    :value="modelValue"
    @input="$emit('update:modelValue', $event.target.value)"
  />
</template>

