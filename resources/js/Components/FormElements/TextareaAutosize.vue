<script setup>
import { onBeforeUnmount, onMounted, ref, computed } from "vue";
import { usePage } from '@inertiajs/vue3';
import autosize from "autosize/dist/autosize";
import Autocomplete from './Autocomplete.vue';

const props = defineProps({
  modelValue: String,
  autoComplete: {
    type: String,
    default: ''
  }
});

const emit = defineEmits(['update:modelValue']);

const el = ref();
const autocompletes = usePage().props.settings.autocomplete || [];

const proxyModelValue = computed({
  get() {
    return props.modelValue;
  },

  set(val) {
    emit('update:modelValue', val);
  },
});

onMounted(() => autosize(el.value));
onBeforeUnmount(() => autosize.destroy(el.value));

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
      inputName
    }"
    v-model="proxyModelValue"
    :source="autocompleteOptions"
    placeholder=""
  >
    <textarea
      ref="el"
      class="focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md py-1.5 px-2 max-h-60 shadow-sm"
      v-bind="getInputProps()"
      :name="inputName"
      rows="1"
      v-on="getInputEventListeners()"
    />
  </Autocomplete>
  <textarea
    v-else
    ref="el"
    :value="proxyModelValue"
    class="focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md py-1.5 px-2 max-h-60 shadow-sm"
    rows="1"
    @input="emit('update:modelValue', $event.target.value)"
  />
</template>
