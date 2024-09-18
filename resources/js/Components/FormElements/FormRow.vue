<script setup>
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import RequiredInput from '@/Components/FormElements/RequiredInput.vue';

defineProps({
    id: {
        type: String,
        default: ''
    },
    label: {
        type: String,
        default: null
    },
    required: {
      type: Boolean,
      default: false
    }
});
</script>

<template>
  <div>
    <InputLabel
      v-if="label !== null"
      :for="id"
      :class="{'mb-2': label !== null}"
    >
      {{ label }}
      <RequiredInput v-if="required" />
    </InputLabel>
    <slot />
  </div>
</template>
