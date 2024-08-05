<template>
  <ConfirmationModal
    :show="show"
    max-width="2xl"
    @close="close"
  >
    <template #title>
      <span class="font-bold text-red-500">{{ __('Are you sure you want to exit this group?') }}</span>
    </template>
    <template #content>
      <h2 class="text-lg leading-6 font-medium text-gray-900 mt-4">
        {{ group.name }}
      </h2>
      <p class="mt-1 text-sm text-gray-500">
        {{ group.description }}
      </p>
      <h3 class="text-base leading-6 font-medium text-gray-700 mt-8">
        {{ __('Unexpected bad things will happen if you do not read this!') }}
      </h3>
      <p class="mt-1 text-sm text-red-700">
        {{ __('Only a group admin can add your organization back into this group if you exit it.') }}
      </p>
    </template>
    <template #footer>
      <SecondaryButton @click="close">
        {{ __('Nevermind') }}
      </SecondaryButton>
      <DangerButton
        class="ml-2"
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
        @click="exitGroup"
      >
        {{ __('Exit Group') }}
      </DangerButton>
    </template>
  </ConfirmationModal>
</template>

<script setup>
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/FormElements/DangerButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
</script>

<script>
export default {
  props: {
    group: {
      type: Object,
      default: () => ({})
    },
    show: Boolean
  },
  emits: ['close'],
  data() {
    return {
      form: this.$inertia.form({})
    };
  },
  methods: {
    close() {
      this.$emit('close');
    },
    exitGroup() {
      this.form.delete(this.route('forum.group_exit.destroy', {
        group: this.group
      }), {
        preserveState: false,
        onSuccess: () => this.close()
      });
    }
  }
};
</script>
