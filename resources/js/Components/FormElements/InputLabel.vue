<template>
  <label class="block font-medium text-sm text-gray-700">  <!-- items-center -->
    <span v-if="value">{{ value }}</span>
    <template v-else><slot /></template>
  </label>
</template>

<script>
    export default {
        props: ['value']
    };
</script>
