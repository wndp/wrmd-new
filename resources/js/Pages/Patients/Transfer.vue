<script setup>
import {useForm, usePage} from '@inertiajs/vue3';
import PatientLayout from '@/Layouts/PatientLayout.vue';
import Panel from '@/Components/Panel.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import Toggle from '@/Components/FormElements/Toggle.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import {__} from '@/Composables/Translate';

defineProps({
  teams: Array
});

const form = useForm({
  transferTo: '',
  collaborate: false
});

const store = () => {
  form.post(route('share.transfer.store', {
    patient: usePage().props.admission.patient
  }));
};
</script>

<template>
  <PatientLayout title="Continued Care">
    <Panel>
      <template #heading>
        {{ __('Transfer Patient') }}
      </template>
      <template #description>
        {{ __('You may transfer this patient to another organization as a copied version of the patient or a shared patient collaborated on between both :organization and the organization of your choice.', {organization: $page.props.auth.team.name}) }} {{ __('The recipient organization will be sent a transfer request message, allowing them to accept or deny the transfer.') }}
      </template>
      <template #content>
        <FormRow id="transferTo" class="col-span-6" :label="__('Transfer To')">
          <SelectInput
            v-model="form.transferTo"
            name="transfer_to"
            :options="teams"
          />
          <InputError
            :message="form.errors.transferTo"
            class="mt-1"
          />
        </FormRow>
        <FormRow id="collaborate" class="col-span-6">
          <Toggle
            v-model="form.collaborate"
            dusk="collaborate"
            :label="__('Share this patient as a collaborative record with your chosen organization.')"
            class="mr-2"
          />
        </FormRow>
      </template>
      <template #footing>
        <div class="flex items-center justify-end text-right">
          <ActionMessage
            :on="form.recentlySuccessful"
            class="mr-3"
          >
            {{ __('Saved.') }}
          </ActionMessage>
          <PrimaryButton
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
            @click="store"
          >
            {{ __('Send Transfer Request') }}
          </PrimaryButton>
        </div>
      </template>
    </Panel>
  </PatientLayout>
</template>
