<script setup>
import Label from '@/Components/FormElements/Label.vue';

defineProps({
    id: {
        type: String,
        default: ''
    },
    label: {
        type: String,
        default: null
    },
});
</script>

<template>
  <div class="lg:flex lg:items-center">
    <Label
      v-if="label !== null"
      :for="id"
      class="lg:mr-2 lg:w-28 lg:text-right"
    >{{ label }}</Label>
    <div class="mt-2 lg:mt-0 lg:flex-1">
      <slot />
    </div>
  </div>
</template>
