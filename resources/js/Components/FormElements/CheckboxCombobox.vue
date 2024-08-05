<script setup>
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import { ChevronDownIcon } from '@heroicons/vue/24/outline';
import Checkbox from '@/Components/FormElements/Checkbox.vue';
import { Popover, PopoverButton, PopoverPanel } from '@headlessui/vue';
import {__} from '@/Composables/Translate';
</script>

<script>
import isArray from 'lodash/isArray';

export default {
    props: {
        modelValue: {
            type: [Array, String],
            required: true
        },
        options: {
            type: Array,
            required: true
        },
        canFilter: {
            type: Boolean,
            default: false
        },
        name: {
            type: String,
            default: ''
        }
    },
    emits: ['update:modelValue', 'change'],
    data() {
        return {
            filter: '',
            isOpen: false,
            value: isArray(this.modelValue) ? this.modelValue : Array.of(this.modelValue)
        };
    },
    computed: {
        filteredOptions() {
            if (! this.canFilter) return this.options;

            return this.options.filter(option => {
                return this.filter.toLowerCase().split(/[\s|-]/).every(word => {
                    return option.label.toLowerCase().includes(word);
                });
            })
        },
        menuButtonText() {
            if (this.value.length === 0) {
                return __('Nothing Selected');
            } else if (this.value.length === 1) {
                return this.options.find(o => o.value === this.value[0]).label;
            }

            return __('Multiple');
        }
    },
    methods: {
        handleChange() {
            this.$emit('update:modelValue', this.value);
            this.$emit('change', this.value);
        },
    }
};
</script>

<template>
  <Popover
    as="div"
    class="relative"
    :dusk="`dusk-${name}`"
  >
    <PopoverButton
      as="div"
      class="text-left flex justify-between items-center w-full pl-2 pr-3 py-1.5 text-base border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
      @click="filter = ''"
    >
      {{ menuButtonText }}
      <ChevronDownIcon
        class="w-4 h-4 ml-2 -mr-1 text-gray-500"
        aria-hidden="true"
      />
    </PopoverButton>
    <PopoverPanel class="absolute z-20 right-0 w-full mt-2 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
      <div class="px-3 py-4 space-y-2 overflow-y-auto max-h-72">
        <TextInput
          v-if="canFilter"
          v-model="filter"
          :placeholder="__('Search')"
          @keyup.prevent="null"
        />
        <div
          v-for="option in filteredOptions"
          :key="option.value"
          class="relative flex items-start"
        >
          <div class="flex items-center h-5">
            <Checkbox
              :id="`${name}_${option.value}`"
              v-model="value"
              :value="option.value"
              @change="handleChange"
            />
          </div>
          <div class="ml-3 text-sm">
            <InputLabel :for="`${name}_${option.value}`">{{ option.label }}</InputLabel>
          </div>
        </div>
      </div>
    </PopoverPanel>
  </Popover>
</template>
