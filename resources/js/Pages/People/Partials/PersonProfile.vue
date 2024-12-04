<script setup>
import PersonCard from '@/Components/FormCards/PersonCard.vue';

defineProps({
  person: Object,
  subdivisions: Array
})
</script>

<template>
  <PersonCard
    :person="person"
    :subdivisions="subdivisions"
    class="mt-4"
  />
</template>
