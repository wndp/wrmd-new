<script setup>
import {ref, watch} from 'vue';
import TextInput from './TextInput.vue';
import SelectInput from './SelectInput.vue';

defineOptions({
    inheritAttrs: false,
});

const props = defineProps({
    text: {
        type: [String, Number],
        default: null
    },
    unit: {
        type: [String, Number],
        default: null
    },
    units: {
        type: Array,
        required: true
    },
    name: {
        type: String,
        required: true
    },
    type: {
        type: String,
        default: 'number'
    }
});

const emit = defineEmits(['update:text', 'update:unit']);

const mutableTextValue = ref(props.text);
const mutableUnitValue = ref(props.unit);

watch(() => props.text, (newValue) => mutableTextValue.value = newValue);
watch(() => props.unit, (newValue) => mutableUnitValue.value = newValue);
watch(() => mutableTextValue.value, (newValue) => emit('update:text', newValue));
watch(() => mutableUnitValue.value, (newValue) => emit('update:unit', newValue));
</script>

<template>
  <div>
    <div class="flex relative rounded-md shadow-sm">
      <TextInput
        v-model="mutableTextValue"
        :name="name"
        :type="type"
        class="rounded-r-none border-r-gray-200 text-right flex-1"
      />
      <div class="absolut inset-y-0 right-0 flex items-center">
        <label
          for="currency"
          class="sr-only"
        >Unit</label>
        <SelectInput
          v-model="mutableUnitValue"
          :name="`${name}_unit`"
          :options="units"
          class="rounded-l-none border-l-gray-200"
        />
      </div>
    </div>
  </div>
</template>
