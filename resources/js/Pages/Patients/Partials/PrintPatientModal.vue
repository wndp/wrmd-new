<script setup>
import {useForm} from '@inertiajs/vue3';
import DialogModal from '@/Components/DialogModal.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import CheckboxCombobox from '@/Components/FormElements/CheckboxCombobox.vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
  show: Boolean,
  patientId: {
      type: String,
      required: true
  },
});

const emit = defineEmits(['close']);

const form = useForm({
    include: []
});

const close = () => emit('close');

const printPatients = () => form.post(route('share.print.store', {
  patient: props.patientId
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
      {{ __('Print Patient Record') }}
    </template>
    <template #content>
      <div class="grid sm:grid-cols-4 mt-4">
        <div class="sm:col-span-4">
          <InputLabel for="include">
            {{ __('Choose what to include in the printed patient medical record.') }}
          </InputLabel>
        </div>
        <div class="sm:col-span-2">
          <CheckboxCombobox
            id="include"
            v-model="form.include"
            :options="$page.props.options.includableOptions"
            class="mt-1"
          />
        </div>
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
        @click="printPatients"
      >
        {{ __('Print Patient Record') }}
      </PrimaryButton>
    </template>
  </DialogModal>
</template>
