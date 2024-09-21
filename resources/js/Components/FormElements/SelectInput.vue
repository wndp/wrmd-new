<script setup>
import {ref, computed} from 'vue';
import isString from 'lodash/isString';
import isNumber from 'lodash/isNumber';

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: null
    },
    options: {
        type: Array,
        required: true,
    },
    hasBlankOption: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits([
    'update:modelValue',
]);

//const select = ref(null);

const computedOptions = computed(() => {
    let options = [];

    if (props.hasBlankOption) {
      options.push({
          label: '',
          value: null,
          data: {},
      });
    }

    if (isString(props.options[0]) || isNumber(props.options[0])) {
      options = props.options.map(n => {
        return {
          value: n,
          label: n
        };
      });
    }

    return [...options, ...props.options];
});

//const focus = () => select.value.focus();

  //export default {
    // props: ['modelValue', 'options'],
    // emits: ['update:modelValue'],
    // computed: {
    //   computedOptions() {
    //     if (isString(this.options[0]) || isNumber(this.options[0])) {
    //       return this.options.map(n => {
    //         return {
    //           value: n,
    //           label: n
    //         };
    //       });
    //     }

    //     return this.options;
    //   }
    // },
    // methods: {
    //   focus() {
    //     this.$refs.input.focus();
    //   }
    // }
  //};
</script>

<template>
  <select
    ref="select"
    :value="modelValue"
    class="block py-1.5 pl-2 pr-10 w-full text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md shadow-sm"
    @change="emit('update:modelValue', $event.target.value)"
  >
    <template
      v-for="(option, i) in computedOptions"
      :key="i"
    >
      <template v-if="option.hasOwnProperty('group')">
        <optgroup :label="option.label">
          <option
            v-for="groupOption in option.group"
            :key="groupOption.value"
            :value="groupOption.value"
          >
            {{ groupOption.label }}
          </option>
        </optgroup>
      </template>
      <option
        v-else
        :value="option.value"
      >
        {{ option.label }}
      </option>
    </template>
  </select>
</template>
