<template>
  <input
    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer focus:ring-blue-500 focus:border-blue-500 focus:outline-none"
    type="file"
    @input="$emit('update:modelValue', $event.target.value)"
  >
  <!-- <input
    ref="input"
    type="file"
    class="focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md cursor-pointer bg-gray-50 py-1.5 px-2"
    :value="modelValue"
    @input="$emit('update:modelValue', $event.target.value)"
  > -->
</template>

<script>
export default {
};
</script>

<style scoped>
  input[type=file]::file-selector-button,
  input[type=file]::-webkit-file-upload-button {
    border-color: rgb(212 212 212); /*.border-gray-300*/
    color: rgb(55, 65, 81);
    font-size: 0.875rem;
    line-height: 1.25rem;
    cursor: pointer;
    border: 0;
    padding-top: 0.375rem;
    padding-bottom: 0.375rem;
    padding-left: 0.5rem;
    padding-right: 0.5rem;
    margin-right: 1rem;
  }
</style>
