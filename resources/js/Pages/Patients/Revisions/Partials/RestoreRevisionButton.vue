<template>
  <PrimaryButton
    :disabled="disabled"
    @click="restore"
  >
    <slot />
  </PrimaryButton>
</template>

<script setup>
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
</script>

<script>
 export default {
  props: {
    revisionId: {
      type: Number,
      required: true
    },
    attribute: {
      type: String,
      default: null
    }
  },
  data() {
    return {
      busy: false,
      disabled: false
    }
  },
  methods: {
    restore() {
      let response = window.confirm(
        this.attribute
        ? `Are you sure you want to restore ${this.attribute} to the OLD value?`
        : 'Are you sure you want to restore ALL OLD values?'
      )

      if (response) {
        this.disabled = true;
        this.busy = true;

        window.axios.put(`/internal-api/revisions/restore/${this.revisionId}/${this.attribute}`)
          .then(() => {
            this.busy = false;

            this.$notify({
              title: this.__('Revision Restored'),
              text: this.attribute
                ? this.__('The :attribute attribute was restored to the previous value.', {
                  attribute: this.attribute
                })
                : 'All old values restored to their previous values',
            });
          })
      }
    }
  }
}
</script>
