<script setup>
import {ref, computed, onMounted} from 'vue';
import {__} from '@/Composables/Translate';
import VueComboBlocks from 'vue-combo-blocks';
import TextInput from '@/Components/FormElements/TextInput.vue';
import { ArrowPathIcon } from '@heroicons/vue/24/solid';
import LocalStorage from '@/Composables/LocalStorage';
import debounce from 'lodash/debounce';
import axios from 'axios';

const localStorage = LocalStorage();

const props = defineProps({
    commonName: {
        type: String,
        default: null
    },
    taxonId: {
        type: [String, Number],
        default: null
    },
});

const emit = defineEmits(['update:commonName', 'update:taxonId', 'select']);

const selected = ref(props.commonName);
const fetching = ref(false);
const prefetchedList = ref([]);
const searchedList = ref([]);
const inputText = ref('');

const filteredList = computed(() => {
    return prefetchedList.value.filter(item => {
        return inputText.value.toLowerCase().split(/[\s|-]/).every(word => {
            return item.label.toLowerCase().includes(word);
        });
    })
    //.concat(this.searchedList)
    // .concat({
    //     common_name: this.modelValue,
    // })
    //.map(item => item.label)
    .slice(0, 10);
});

const itemToString = (item) => item ? item.label : '';

const updateList = (text) => {
    if (text.trim().length > 0) {
        inputText.value = text;
        fetchCommonNames(text);
    }
};

const fetchCommonNames = debounce(function (text) {
    fetching.value = true;
    axios.get('/internal-api/search/common-names/?q=' + text).then(response => {
        searchedList.value = response.data;
        fetching.value = false;
    });
}, 500);

const prefetchCommonNames = () => {
    let cacheStatus = localStorage.status('PrefetchedCommonNames');

    // Has Data
    if (cacheStatus === 1) {
        prefetchedList.value = localStorage.get('PrefetchedCommonNames');

    // Expired or Empty Cache
    } else {
        window.axios.get('/internal-api/search/common-names-prefetch').then(response => {
            localStorage.store('PrefetchedCommonNames', response.data, 7200); // 2 hours
            prefetchedList.value = response.data;
        });
    }
};

const stateReducer = (state, actionAndChanges) => {
  const { changes, type } = actionAndChanges;
  switch (type) {
    case VueComboBlocks.stateChangeTypes.InputChange:
        onInputChange()
        return changes;
    case VueComboBlocks.stateChangeTypes.InputBlur:
      //inputText.value = state.inputValue;
    // if (! getExactMatch(state.inputValue)) {

    // }
    console.log('blur')
      return {
        ...changes,
        inputValue: state.inputValue, // Allow selection of item that's not in the options array
        //selectedItem: state.inputValue // Allow selection of item that's not in the options array
      };
    default:
      return changes;
  }
};

const getExactMatch = (text) => prefetchedList.value.find(item => item.label === text);

// const onSelect = () => emit('select', prefetchedList.value.find(item => item.label === selected.value));

const onChange = () => {
    console.log('onChange')
    emit('update:taxonId', selected.value.data.taxon_id);
    emit('update:commonName', selected.value.label);
};

const onInputChange = (e) => {
    console.log('onInputChange', inputText.value)
    selected.value = null;
    emit('update:taxonId', null);
    emit('update:commonName', null);
}

const onSelect = () => '';
//const onChange = () => '';

onMounted(() => prefetchCommonNames());
</script>

<template>
  <div class="relative">
    <VueComboBlocks
      v-slot="{
        getInputProps,
        getInputEventListeners,
        hoveredIndex,
        isOpen,
        getMenuProps,
        getMenuEventListeners,
        getItemProps,
        getItemEventListeners,
        getComboboxProps,
        // reset,
        // inputValue
      }"
      v-model="selected"
      :itemToString="itemToString"
      :items="filteredList"
      :stateReducer="stateReducer"
      @input-value-change="updateList"
      @update:model-value="onChange"
      @select="onSelect"
    >
      <div v-bind="getComboboxProps()">
        <TextInput
          v-bind="getInputProps()"
          placeholder="Search"
          name="common_name"
          v-on="getInputEventListeners()"
        />
        <Transition
          leaveActiveClass="transition duration-100 ease-in"
          leaveFromClass="opacity-100"
          leaveToClass="opacity-0"
        >
          <ul
            v-show="isOpen"
            v-bind="getMenuProps()"
            class="absolute z-20 w-full py-1 mt-1 overflow-auto text-base bg-white rounded-md shadow-lg max-h-60 ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
            v-on="getMenuEventListeners()"
          >
            <li
              v-for="(item, index) in filteredList"
              :key="item.id"
              v-bind="getItemProps({ item, index })"
              :class="[
                hoveredIndex === index ? 'text-blue-900 bg-blue-100' : 'text-gray-900',
                'cursor-default select-none relative py-2 px-4',
              ]"
              v-on="getItemEventListeners({ item, index })"
            >
              <span
                :class="[
                  selected === item ? 'font-medium' : 'font-normal',
                  'block truncate',
                ]"
              >{{ item.label }}</span>
            </li>
            <li
              v-show="fetching"
              class="text-green-600 font-medium cursor-default select-none relative py-2 px-4"
            >
              <div class="flex items-center">
                <ArrowPathIcon class="mr-3 flex-shrink-0 h-4 w-4" />
                {{ __('Search for new species...') }}
              </div>
            </li>
          </ul>
        </Transition>
      </div>
    </VueComboBlocks>
  </div>
</template>
