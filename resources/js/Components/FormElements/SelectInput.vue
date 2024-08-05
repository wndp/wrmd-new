<template>
  <select
    ref="select"
    :value="modelValue"
    class="block py-1.5 pl-2 pr-10 max-w-full text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
    @change="$emit('update:modelValue', $event.target.value)"
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

<script>
  import isString from 'lodash/isString';
  import isNumber from 'lodash/isNumber';

  export default {
    props: ['modelValue', 'options'],
    emits: ['update:modelValue'],
    computed: {
      computedOptions() {
        if (isString(this.options[0]) || isNumber(this.options[0])) {
          return this.options.map(n => {
            return {
              value: n,
              label: n
            };
          });
        }

        return this.options;
      }
    },
    methods: {
      focus() {
        this.$refs.input.focus();
      }
    }
  };
</script>
