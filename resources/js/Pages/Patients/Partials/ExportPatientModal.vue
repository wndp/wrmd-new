<script setup>
import {useForm} from '@inertiajs/vue3';
import DialogModal from '@/Components/DialogModal.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import { XMarkIcon } from '@heroicons/vue/24/outline';
import {__} from '@/Composables/Translate';

const props = defineProps({
  show: Boolean,
  patient: {
      type: Object,
      default: () => ({})
  }
});

const emit = defineEmits(['close']);

const form = useForm({
    fields: []
});

const close = () => emit('close');

const exportPatients = () => form.post(route('share.export.store', {
  patient: props.patient?.id
}), {
  preserveScroll: true,
  onStart: () => {
    form.reset();
    close();
  }
});
</script>

<template>
  <DialogModal
    :show="show"
    @close="close"
  >
    <template #title>
      {{ __('Export Patient Record') }}
    </template>
    <template #content>
      <p class="font-medium text-sm text-gray-500">
        {{ __('Choose which fields to export.') }}
      </p>
      <p class="font-medium text-sm text-gray-500 mt-1">
        <sup class="">*</sup>{{ __('To export all fields, save time by not choosing any fields and just click the Export Patients button.') }}
      </p>
      <div class="mt-4 space-y-2">
        <!-- <Multiselect
                    mode="multiple"
                    :groups="true"
                    :options="$page.props.exportableOption"
                    :closeOnSelect="false"
                    :hideSelected="false"
                    v-model="form.fields"
                    :classes="multiselectClasses"
                >
                    <template v-slot:clear="{ clear }">
                        <button @mousedown.prevent="clear()">
                            <XMarkIcon class="h-4 w-4 text-gray-700"/>
                        </button>
                    </template>
                </Multiselect> -->
      </div>
    </template>
    <template #footer>
      <SecondaryButton @click="close">
        {{ __('Nevermind') }}
      </SecondaryButton>
      <PrimaryButton
        class="ml-2"
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
        @click="exportPatients"
      >
        {{ __('Export Patient Record') }}
      </PrimaryButton>
    </template>
  </DialogModal>
</template>
