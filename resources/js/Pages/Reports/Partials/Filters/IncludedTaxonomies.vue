<script setup>
import {ref} from 'vue';
import CheckboxCombobox from '@/Components/FormElements/CheckboxCombobox.vue';

const props = defineProps({
  filter: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['filter-changed']);

const value = ref(props.filter.options.map(option => option.value),);

const handleChange = () => emit('filter-changed', { class: props.filter.class, value: value.value, trigger: 'inside' });
</script>

<template>
  <div>
    <label
      for="xx"
      class="block text-sm font-medium text-gray-700"
    >{{ filter.name }}</label>
    <CheckboxCombobox
      v-model="value"
      name="members"
      :options="filter.options"
      class="mt-1"
      dusk="included-taxonomies"
      @change="handleChange"
    />
  </div>
</template>
