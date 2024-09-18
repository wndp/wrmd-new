<script setup>
import {ref} from 'vue';
import VueComboBlocks from 'vue-combo-blocks';
import TextInput from '@/Components/FormElements/TextInput.vue';
import Checkbox from '@/Components/FormElements/Checkbox.vue';
import { TransitionRoot } from '@headlessui/vue'
import debounce from 'lodash/debounce';
import axios from 'axios';

const props = defineProps({
  source: {
    type: String,
    required: true
  },
  sourceId: {
    type: Number,
    required: true
  },
});

const selectedItems = ref([]);
const filteredList = ref([]);

const itemToString = (item) => item ? item.value : '';

const handleSelectedItemChange = (selectedItem) => {
  if (!selectedItem) return;

  const index = selectedItems.value.indexOf(selectedItem);

  if (index > 0) {
    selectedItems.value = [
      ...selectedItems.value.slice(0, index),
      ...selectedItems.value.slice(index + 1),
    ];
  } else if (index === 0) {
    selectedItems.value = [...selectedItems.value.slice(1)];
  } else {
    // Add item
    selectedItems.value.push(selectedItem);
  }
};

const stateReducer = (state, actionAndChanges) => {
  const { changes, type } = actionAndChanges;
  switch (type) {
    case VueComboBlocks.stateChangeTypes.InputKeyUpEnter:
    case VueComboBlocks.stateChangeTypes.ItemClick:
      saveRelationship(changes.selectedItem);
      return {
        ...changes,
        isOpen: true, // keep menu open after selection.
        hoveredIndex: state.hoveredIndex,
        inputValue: state.inputValue, // Keep the input value the same.
      };
    case VueComboBlocks.stateChangeTypes.InputBlur:
      return {
        ...changes,
        inputValue: '', // don't add the item string as input value at selection.
      };
    default:
      return changes;
  }
};

const saveRelationship = (item) => {
  axios.post(route('internal-api.related.store'), {
    source_type: props.source,
    source_id: props.sourceId,
    related_type: item.resource,
    related_id: item.id,
  })
};

const search = (text) => debounceSearch(text);

const debounceSearch = debounce(function(text) {
  axios.get(route('internal-api.related.search', {model: props.source, search: text }))
  .then(response => {
    filteredList.value = response.data;
  });
}, 500);
</script>

<template>
  <div class="relative">
    <vue-combo-blocks
      v-slot="{
        getInputProps,
        getInputEventListeners,
        hoveredIndex,
        isOpen,
        getMenuProps,
        getMenuEventListeners,
        getItemProps,
        getItemEventListeners,
        getComboboxProps
      }"
      :itemToString="itemToString"
      :items="filteredList"
      :stateReducer="stateReducer"
      @input-value-change="search"
      @select="handleSelectedItemChange"
    >
      <div v-bind="getComboboxProps()">
        <TextInput
          data-testid="multiselect-input"
          v-bind="getInputProps()"
          placeholder="Search"
          v-on="getInputEventListeners()"
        />
        <TransitionRoot
          :show="isOpen && filteredList.length > 0"
          leave="transition duration-100 ease-in"
          leaveFrom="opacity-100"
          leaveTo="opacity-0"
        >
          <ul
            v-bind="getMenuProps()"
            class="absolute z-20 w-full py-1 mt-1 overflow-auto text-base bg-white rounded-md shadow-lg max-h-60 ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
            v-on="getMenuEventListeners()"
          >
            <li
              v-for="(item, index) in filteredList"
              :key="item.id"
              :data-testid="`vue-combo-blocks-item-${index}`"
              :class="[
                hoveredIndex === index ? 'text-blue-900 bg-blue-100' : 'text-gray-900',
                'cursor-default select-none relative py-2 px-4',
              ]"
              v-bind="getItemProps({ item, index })"
              v-on="getItemEventListeners({ item, index })"
            >
              <div class="flex items-start">
                <div class="flex items-center h-5">
                  <Checkbox
                    v-model="selectedItems"
                    :value="item"
                  />
                </div>
                <div class="ml-3 text-sm">
                  {{ item.heading }}
                </div>
              </div>
            </li>
          </ul>
        </TransitionRoot>
        <ul
          role="list"
          class="divide-y divide-gray-200 mt-2"
        >
          <li
            v-for="item in selectedItems"
            :key="item.id"
            class="relative bg-white py-5 px-4 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 hover:bg-gray-50"
          >
            <div class="flex justify-between space-x-3">
              <div class="min-w-0 flex-1">
                <a
                  href="#"
                  class="block focus:outline-none"
                >
                  <span
                    class="absolute inset-0"
                    aria-hidden="true"
                  />
                  <p class="truncate text-sm font-medium text-gray-900">{{ item.heading }}</p>
                  <p class="truncate text-sm text-gray-500">{{ item.sub_heading }}</p>
                </a>
              </div>
              <time
                :datetime="item.date"
                class="flex-shrink-0 whitespace-nowrap text-sm text-gray-500"
              >{{ item.date }}</time>
            </div>
          </li>
        </ul>
      </div>
    </vue-combo-blocks>
  </div>
</template>
