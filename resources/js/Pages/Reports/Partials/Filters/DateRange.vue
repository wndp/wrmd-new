<script setup>
import {inject, ref} from 'vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import { dateFromPeriod } from '@/Composables/DatePeriod';

const emitter = inject('emitter');

const props = defineProps({
  filter: {
    type: Object,
    required: true
  }
});

const value = ref(props.filter.currentValue);

const handleChange = () => emitter.emit('date-range-change', dateFromPeriod(value));
</script>

<template>
  <div>
    <label
      for="date-range"
      class="block text-sm font-medium text-gray-700"
    >{{ filter.name }}</label>
    <div class="mt-1 relative rounded-md">
      <SelectInput
        v-model="value"
        :options="filter.options"
        @change="handleChange"
      />
    </div>
  </div>
</template>
