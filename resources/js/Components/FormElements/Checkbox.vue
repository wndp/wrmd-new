<template>
  <input
    type="checkbox"
    class="focus:ring-green-500 h-4 w-4 text-green-600 border-gray-300 rounded disabled:bg-gray-200"
    :checked="isChecked"
    :value="value"
    @change="updateInput"
  >
</template>
<script>
  export default {
    props: {
      modelValue: [
        Boolean,
        String,
        Number,
        Array
      ],
      value: String|Number,
      trueValue: { default: true },
      falseValue: { default: false },
    },
    emits: ['update:modelValue'],
    computed: {
      isChecked() {
        if (this.modelValue instanceof Array) {
          return this.modelValue.includes(this.value);
        }

        return this.modelValue === this.trueValue;
      }
    },
    methods: {
      updateInput(event) {
        let isChecked = event.target.checked;

        if (this.modelValue instanceof Array) {
          let newValue = [...this.modelValue];

          if (isChecked) {
            newValue.push(this.value);
          } else {
            newValue.splice(newValue.indexOf(this.value), 1);
          }

          this.$emit('update:modelValue', newValue);
        } else {
          this.$emit('update:modelValue', isChecked ? this.trueValue : this.falseValue);
        }
      }
    }
  };
</script>
