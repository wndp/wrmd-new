<script>
import VueComboBlocks from 'vue-combo-blocks';
import TextInput from '@/Components/FormElements/TextInput.vue';
import { ArrowPathIcon } from '@heroicons/vue/24/solid';
import LocalStorage from '@/Composables/LocalStorage';
import debounce from 'lodash/debounce';

const localStorage = LocalStorage();

export default {
    components: {
        VueComboBlocks,
        TextInput,
        ArrowPathIcon
    },
    props: ['modelValue'],
    emits: ['update:modelValue', 'select'],
    data() {
        return {
            // selected: {
            //     common_name: this.modelValue
            // },
            selected: this.modelValue,
            fetching: false,
            prefetchedList: [],
            searchedList: [],
            text: ''
            //filteredList: []
        };
    },
    computed: {
        filteredList() {
            return this.prefetchedList.filter(item => {
                return this.text.toLowerCase().split(/[\s|-]/).every(word => {
                    return item.label.toLowerCase().includes(word);
                });
            })
            .concat(this.searchedList)
            // .concat({
            //     common_name: this.modelValue,
            // })
            .map(item => item.label)
            .slice(0, 10);
        }
    },
    mounted() {
        this.prefetchCommonNames();
    },
    methods: {
        itemToString(item) {
            return item;//? item.common_name : '';
        },
        stateReducer(state, actionAndChanges) {
          const { changes, type } = actionAndChanges;
          switch (type) {
            case VueComboBlocks.stateChangeTypes.InputBlur:
              //this.text = state.inputValue;
              return {
                ...changes,
                inputValue: state.inputValue, // Allow selection of item that's not in the options array
                selectedItem: state.inputValue // Allow selection of item that's not in the options array
              };
            default:
              return changes;
          }
        },
        updateList(text) {
            if (text.trim().length > 0) {
                this.fetchCommonNames(text);
            }
        },
        onChange() {
            this.$emit('update:modelValue', this.selected);
        },
        onSelect() {
            this.$emit('select', this.prefetchedList.find((item) => item.label === this.selected));
        },
        prefetchCommonNames() {
            let cacheStatus = localStorage.status('wrmdCommonNames');

            // Has Data
            if (cacheStatus === 1) {
                this.prefetchedList = localStorage.get('wrmdCommonNames');

            // Expired or Empty Cache
            } else {
                window.axios.get('/internal-api/search/common-names-prefetch').then(response => {
                    localStorage.store('wrmdCommonNames', response.data, 7200); // 2 hours
                    this.prefetchedList = response.data;
                });
            }
        },
        fetchCommonNames: debounce(function (text) {
            this.fetching = true;
            window.axios.get('/internal-api/search/common-names/?q=' + text).then(response => {
                this.searchedList = response.data;
                this.fetching = false;
            });
        }, 500),
    },
};
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
        reset,
        inputValue
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
          v-bind="getInputProps(), $attrs"
          v-model="text"
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
              >{{ item }}</span>
            </li>
            <li
              v-show="fetching"
              class="text-green-600 font-medium cursor-default select-none relative py-2 px-4"
            >
              <div class="flex items-center">
                <ArrowPathIcon class="mr-3 flex-shrink-0 h-4 w-4" />
                Fetching New Results
              </div>
            </li>
          </ul>
        </Transition>
      </div>
    </VueComboBlocks>
  </div>
</template>
