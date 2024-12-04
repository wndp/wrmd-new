<script setup>
import {ref, watch} from 'vue';
import Autocomplete from 'primevue/autocomplete';
import {__} from '@/Composables/Translate';
import debounce from 'lodash/debounce';
import axios from 'axios';

defineProps({
    commonName: {
        type: String,
        default: null
    },
    taxonId: {
        type: [String, Number],
        default: null
    },
});

const emit = defineEmits(['update:commonName', 'update:taxonId']);

const selectedTaxon = ref({
    label: '',
    value: 0,
    data: {}
});

const filteredOptions = ref([]);

const searchTaxa = debounce(function({query}) {
  if (query.length < 2) {
      return filteredOptions.value = [];
    }
  axios.get('/internal-api/search/common-names/?search=' + query).then(response => {
    filteredOptions.value = response.data;
  });
}, 500);

watch(selectedTaxon, (newValue) => {
  if (newValue.value) {
    emit('update:taxonId', newValue.value);
    emit('update:commonName', newValue.label);
  } else if (typeof newValue === 'string') {
    emit('update:taxonId', null);
    emit('update:commonName', newValue);
  }
});
</script>

<template>
  <Autocomplete
    v-model="selectedTaxon"
    :suggestions="filteredOptions"
    optionLabel="label"
    :placeholder="__('Search species')"
    :showEmptyMessage="false"
    appendTo="self"
    inputClass="focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md py-1.5 px-2 shadow-sm"
    pt:overlay="w-full py-1 mt-1 overflow-auto text-base bg-white rounded-md shadow-lg max-h-60 ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
    pt:loader="absolute top-[50%] right-[0.5rem] -mt-2 animate-spin"
    pt:option="cursor-default select-none relative py-2 px-4"
    pt:emptyMessage="cursor-default select-none relative py-2 px-4"
    @complete="searchTaxa"
  />
</template>
