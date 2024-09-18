<script setup>
import {ref} from 'vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';

const props = defineProps({
  filter: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['filter-changed']);

const value = ref(props.filter.currentValue);

const handleChange = () => emit('filter-changed', { class: props.filter.class, value: value.value, trigger: 'inside' });
</script>

<template>
  <div>
    <label
      :for="filter.name"
      class="block text-sm font-medium text-gray-700"
    >{{ filter.name }}</label>
    <div class="mt-1 relative rounded-md">
      <SelectInput
        :id="filter.name"
        v-model="value"
        :options="filter.options"
        @change="handleChange"
      />
    </div>
  </div>
</template>
