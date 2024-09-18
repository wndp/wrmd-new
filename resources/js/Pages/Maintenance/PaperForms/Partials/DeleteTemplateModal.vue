<template>
  <ConfirmationModal
    :show="show"
    max-width="2xl"
    @close="close"
  >
    <template #title>
      {{ __('Delete Template') }}
    </template>
    <template #content>
      <strong class="font-bold text-red-500">{{ __('Are you sure you want to delete this paper form template?') }}</strong>
      <p class="text-sm mt-4">
        {{ template.name }}
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
        @click="destroy"
      >
        {{ __('Delete Template') }}
      </DangerButton>
    </template>
  </ConfirmationModal>
</template>

<script setup>
import { inject } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/FormElements/DangerButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';

const route = inject('route');

const props = defineProps({
    template: {
        type: Object,
        required: true
    },
    show: Boolean,
})

const emit = defineEmits(['close']);
const form = useForm({});

const close = () => {
  emit('close');
};

const destroy = () => {
  form.delete(route('maintenance.paper_forms.destroy', {
    slug: props.template.slug
  }), {
    preserveScroll: true,
    onSuccess: () => close()
  });
};

</script>
