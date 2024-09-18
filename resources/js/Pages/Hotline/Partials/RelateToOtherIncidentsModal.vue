<script setup>
import {useForm} from '@inertiajs/vue3';
import DialogModal from '@/Components/DialogModal.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import RelatedModels from './RelatedModels.vue';

const props = defineProps({
  incident: {
    type: Object,
    required: true
  },
  show: Boolean,
});

const emit = defineEmits(['close']);

const form = useForm({
  relations: []
});

const close = () => emit('close');

const doSave = () => {
    form.store(route('internal-api.related.store', {
        source_type: 'incident',
        source_id: props.incident.id,
        related_type: 'incident',
        related_id: 'required|numeric',
    }), {
        preserveState: false,
        //onSuccess: () => this.close(),
        onError: () => form.reset('password'),
    });
};
</script>

<template>
  <DialogModal
    :show="show"
    maxWidth="2xl"
    @close="close"
  >
    <template #title>
      {{ __('Delete Incident') }}
    </template>
    <template #content>
      <strong>{{ __('Is this incident related to other incidents?') }}</strong>
      <p class="text-sm text-gray-600 mt-4">
        {{ __("If there are multiple incidents created about the same event you can associate them together to better track the event's progress.") }}
      </p>
      <RelatedModels
        v-model="form.relations"
        class="mt-4"
        source="incident"
        :sourceId="incident.id"
      />
    </template>
    <template #footer>
      <SecondaryButton @click="close">
        {{ __('Nevermind') }}
      </SecondaryButton>
      <PrimaryButton
        class="ml-2"
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
        @click="doSave"
      >
        {{ __('Update Relationships') }}
      </PrimaryButton>
    </template>
  </DialogModal>
</template>
