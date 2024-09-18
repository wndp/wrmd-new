<script setup>
import {inject, onMounted, ref} from 'vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';

const emitter = inject('emitter');

const props = defineProps({
  filter: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['filter-changed']);

const value = ref(props.filter.currentValue);

const handleChange = emit('filter-changed', { class: props.filter.class, value: value.value[0], trigger: 'inside' })

onMounted(() => {
    emitter.on('date-range-change', range => {
        if (props.filter.class.includes('DateFrom')) {
            value.value = range.from;
        } else if (props.filter.class.includes('DateTo')) {
            value.value = range.to;
        }
    });
});
</script>

<template>
  <div>
    <label
      class="block text-sm font-medium text-gray-700"
    >{{ filter.name }}</label>
    <DatePicker
      v-model="value"
      class="mt-1"
      @change="handleChange"
    />
  </div>
</template>
