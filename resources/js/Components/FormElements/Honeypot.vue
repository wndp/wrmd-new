<template>
  <div
    v-if="honeypot.enabled"
    :name="`${honeypot.nameFieldName}_wrap`"
    style="display:none;"
  >
    <input
      :id="honeypot.nameFieldName"
      v-model="form[honeypot.nameFieldName]"
      type="text"
      :name="honeypot.nameFieldName"
    >
    <input
      v-model="form[honeypot.validFromFieldName]"
      type="text"
      :name="honeypot.validFromFieldName"
    >
  </div>
</template>

<script>
  export default {
    props: {
      honeypot: {
        type: Object,
        required: true
      }
    }
  };
</script>

