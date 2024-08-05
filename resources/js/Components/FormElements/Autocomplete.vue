<script setup>
import VueComboBlocks from 'vue-combo-blocks';
import TextInput from '@/Components/FormElements/TextInput.vue';
import { TransitionRoot } from '@headlessui/vue'
import debounce from 'lodash/debounce';
</script>

<script>
export default {
  props: {
    modelValue: {
      type: String,
      default: null
    },
    source: {
      type: [Array, String],
      required: true
    },
    optionFormat: {
      type: [String, Function],
      default: 'label'
    },
    valueFormat: {
      type: [String, Function],
      default: 'value'
    },
    placeholder: {
      type: String,
      default: 'Search'
    },
    name: {
      type: String,
      default: null
    }
  },
  emits: ['update:modelValue', 'selected'],
  data() {
    return {
      selected: this.modelValue,
      options: [],
    };
  },
  methods: {
    itemToString(item) {
      return item ? this.formatValue(item) : '';
    },
    search(text) {
      if (typeof this.source === 'string') {
        this.debounceSearch(text);
      } else if (Array.isArray(this.source)) {
        this.arrayLikeSearch(text);
      }
    },
    debounceSearch: debounce(function(text) {
      window.axios.get(`${this.source}?search=${text}`)
      .then(response => {
        this.options = response.data;
      });
    }, 500),
    arrayLikeSearch (text) {
      this.options = this.source.filter(item => {
        item = this.formatOption(item)
        return text.toLowerCase().split(/[\s|-]/).every(text => {
          return item.toLowerCase().includes(text)
        })
      })
    },
    formatOption (item) {
      if (typeof item === 'string') return item;
      switch (typeof this.optionFormat) {
        case 'function':
          return this.optionFormat(item)
        case 'string':
          if (! item[this.optionFormat]) {
            throw new Error(`"${this.optionFormat}" property expected on result but is not defined.`)
          }
          return item[this.optionFormat]
        default:
          throw new TypeError()
      }
    },
    formatValue (item) {
      if (typeof item === 'string') return item;
      switch (typeof this.valueFormat) {
        case 'function':
          return this.valueFormat(item)
        case 'string':
          if (item[this.valueFormat]) {
            return item[this.valueFormat]
          }
          break;
        default:
          throw new TypeError()
      }
    },
    onChange() {
      this.$emit('selected', this.selected);
      this.$emit('update:modelValue', this.formatValue(this.selected));
    },
    stateReducer(state, actionAndChanges) {
      const { changes, type,  } = actionAndChanges;
      switch (type) {
        case VueComboBlocks.stateChangeTypes.InputBlur:
        return {
          ...changes,
          inputValue: state.inputValue, // Allow selection of item that's not in the options array
          selectedItem: state.inputValue // Allow selection of item that's not in the options array
        };
        default:
          return changes;
      }
    },
  },
};
</script>

<template>
  <div class="relative w-full">
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
        getComboboxProps
      }"
      v-model="selected"
      :itemToString="itemToString"
      :items="options"
      :stateReducer="stateReducer"
      :inputId="`${name}_id`"
      @input-value-change="search"
      @update:model-value="onChange"
    >
      <div v-bind="getComboboxProps()">
        <slot
          :getInputProps="getInputProps"
          :getInputEventListeners="getInputEventListeners"
          :inputName="name"
        >
          <TextInput
            v-bind="getInputProps()"
            :placeholder="placeholder"
            :name="name"
            autocomplete="none"
            v-on="getInputEventListeners()"
          />
        </slot>
        <TransitionRoot
          :show="isOpen && options.length > 0"
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
              v-for="(item, index) in options"
              :key="item"
              v-bind="getItemProps({ item, index })"
              :class="[
                hoveredIndex === index ? 'text-blue-900 bg-blue-100' : 'text-gray-900',
                'cursor-default select-none relative py-2 px-4',
              ]"
              v-on="getItemEventListeners({ item, index })"
            >
              <span
                :class="[
                  selected === formatOption(item) ? 'font-medium' : 'font-normal',
                  'block truncate',
                ]"
              >{{ formatOption(item) }}</span>
            </li>
          </ul>
        </TransitionRoot>
      </div>
    </VueComboBlocks>
  </div>
</template>
