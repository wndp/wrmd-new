<script setup>
import {useForm, router} from '@inertiajs/vue3';
import PatientLayout from '@/Layouts/PatientLayout.vue';
import PersonCard from '@/Components/FormCards/PersonCard.vue';
import Alert from '@/Components/Alert.vue';
import AlertAction from '@/Components/AlertAction.vue';
import {__} from '@/Composables/Translate';
import {can} from '@/Composables/Can';
import {Abilities} from '@/Enums/Abilities';

const props = defineProps({
  patient: {
    type: Object,
    required: true
  },
  rescuer: {
    type: Object,
    required: true
  }
});

const form = useForm({
  custom_values: props.rescuer.custom_values || {},
  entity_id: props.rescuer.entity_id,
  organization: props.rescuer.organization,
  first_name: props.rescuer.first_name,
  last_name: props.rescuer.last_name,
  phone: props.rescuer.phone,
  alternate_phone: props.rescuer.alternate_phone,
  email: props.rescuer.email,
  subdivision: props.rescuer.subdivision,
  city: props.rescuer.city,
  address: props.rescuer.address,
  county: props.rescuer.county,
  postal_code: props.rescuer.postal_code,
  notes: props.rescuer.notes,
  no_solicitations: 'no_solicitations' in props.rescuer ? props.rescuer.no_solicitations : true,
  is_volunteer: props.rescuer.is_volunteer,
  is_member: props.rescuer.is_member,
});

const viewPatients = () => {
  router.visit(route('patients.index', {
    list: 'rescuer-patients-list',
    rescuerId: props.patient.rescuer_id
  }));
};

const detachRescuer = () => {
  useForm({}).delete(
    route('patients.detach_rescuer.destroy', props.patient)
  );
};

const updateRescuer = () => {
  form.put(route('people.update', {
    person: props.rescuer
  }), {
    preserveScroll: true,
    //onError: () => this.stopAutoSave()
  });
};
</script>

<template>
  <PatientLayout title="Rescuer">
    <Alert
      v-if="rescuer.patients_count > 1"
      class="mb-4"
    >
      <p>{{ __('This rescuer has admitted multiple patients. Updating this rescuer will change across all those patients.') }}</p>
      <div class="flex space-x-8">
        <AlertAction @click="viewPatients">
          {{ __("View Rescuer's Patients") }}
        </AlertAction>
        <AlertAction @click="detachRescuer">
          {{ __('Detach this Rescuer') }}
        </AlertAction>
      </div>
    </Alert>
    <PersonCard
      :form="form"
      :canSubmit="can(Abilities.UPDATE_RESCUER) && $page.props.patientMeta.locked_at === null"
      affiliation="Rescuer"
      @submitted="updateRescuer"
    />
  </PatientLayout>
</template>
