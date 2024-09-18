<script setup>
import {ref} from 'vue';

const props = defineProps({
  filter: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['filter-changed']);

const value = ref(props.filter.currentValue);
const areas = ref([]); //window.wrmd.settings.areas

const handleChange = () => emit('filter-changed', { class: props.filter.class, value: value.value, trigger: 'inside' });
</script>

<template>
  <div>
    <input
      id="location_area_filter"
      v-model="value"
      type="text"
      name="location_area_filter"
      @change="handleChange"
    >
    <autocomplete
      inputId="location_area_filter"
      :options="areas"
      :enforceSingle="true"
      @selected="handleChange"
    />
  </div>
</template>
