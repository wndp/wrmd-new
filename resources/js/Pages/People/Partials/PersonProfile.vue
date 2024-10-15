<script setup>
import PersonForm from '@/Components/Forms/PersonForm.vue';

defineProps({
  person: Object,
  subdivisions: Array
})
</script>

<template>
  <PersonForm
    :person="person"
    :subdivisions="subdivisions"
    class="mt-4"
  />
</template>
