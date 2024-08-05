<template>
  <div>
    <div class="flex relative rounded-md shadow-sm">
      <Input
        v-model="mutableTextValue"
        :name="name"
        class="rounded-r-none border-r-gray-200 text-right"
      />
      <div class="absolut inset-y-0 right-0 flex items-center">
        <label
          for="currency"
          class="sr-only"
        >Unit</label>
        <Select
          v-model="mutableUnitValue"
          :name="`${name}_unit`"
          :options="units"
          class="rounded-l-none border-l-gray-200"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import Input from './Input.vue';
import Select from './Select.vue';
</script>

<script>
export default {
    inheritAttrs: false,
    props: ['text', 'unit', 'units', 'name'],
    emits: ['update:text', 'update:unit'],
    data() {
      return {
        mutableTextValue: this.text,
        mutableUnitValue: this.unit
      }
    },
    watch: {
        text (newValue) {
            this.mutableTextValue = newValue;
        },
        unit (newValue) {
            this.mutableUnitValue = newValue;
        },
        mutableTextValue (newValue) {
            this.$emit('update:text', newValue);
        },
        mutableUnitValue (newValue) {
            this.$emit('update:unit', newValue);
        }
    },
    methods: {
        focus() {
            this.$refs.input.focus();
        }
    }
};
</script>

