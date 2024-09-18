<script setup>
import { inject } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/FormElements/DangerButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import {__} from '@/Composables/Translate';

const route = inject('route');

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  media: {
    type: Object,
    required: true
  },
  resource: {
    type: String,
    required: true
  },
  resourceId: {
    type: Number,
    required: true
  }
});

const emit = defineEmits(['close']);

const form = useForm({});

const close = () => emit('close');

const deleteMedia = () => {
  form.delete(route('media.destroy', {
    resource: props.resource,
    resource_id: props.resourceId,
    media: props.media.id
  }), {
      onSuccess: () => close(),
      preserveState: false
  });
};
</script>

<template>
  <ConfirmationModal
    :show="show"
    maxWidth="2xl"
    @close="close"
  >
    <template #title>
      {{ __('Delete Attachment') }}
    </template>
    <template #content>
      <strong>{{ __('Are you sure you want to delete this attachment?') }}</strong>
      <div class="sm:flex mt-4">
        <div class="mb-4 flex-shrink-0 sm:mb-0 sm:mr-4">
          <img
            :src="media.preview_url"
            :alt="media.name"
            class="h-32 w-32 object-scale-down border border-gray-300 bg-white text-gray-300"
          >
        </div>
        <div>
          <h4 class="text-base font-medium">
            {{ media.name }}
          </h4>
          <p class="mt-1 text-sm text-gray-500">
            {{ media.custom_properties.description }}
          </p>
        </div>
      </div>
    </template>
    <template #footer>
      <SecondaryButton @click="close">
        {{ __('Nevermind') }}
      </SecondaryButton>
      <DangerButton
        class="ml-2"
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
        @click="deleteMedia"
      >
        {{ __('Delete Attachment') }}
      </DangerButton>
    </template>
  </ConfirmationModal>
</template>
