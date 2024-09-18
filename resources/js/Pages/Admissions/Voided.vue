<script setup>
import { inject } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Alert from '@/Components/Alert.vue';
import AlertAction from '@/Components/AlertAction.vue';
import PatientPagination from '@/Layouts/Partials/PatientPagination.vue';
import {__} from '@/Composables/Translate';
import {can} from '@/Composables/Can';
import {Abilities} from '@/Enums/Abilities';

const route = inject('route');

const props = defineProps({
  admission: {
    type: Object,
    required: true
  }
})

const restore = () => {
  router.put(route('patients.unvoid.update', props.admission.patient));
}
</script>

<template>
  <AppLayout title="Admissions">
    <PatientPagination />
    <Alert
      color="red"
      class="mt-4"
    >
      {{ __('Patient :caseNumber has been voided. Voided patients are removed from all reports and analytics. The patients case number remains as a placeholder to maintain continuity. All data saved on the patient still exists and may be restored if this patient was voided by mistake.', {caseNumber: admission.case_number}) }}
      <AlertAction
        v-if="can(Abilities.UN_VOID_PATIENT)"
        color="red"
        @click="restore"
      >
        {{ __('Restore patient') }}
      </AlertAction>
    </Alert>
    <div class="w-full flex flex-col justify-center items-center flex-wrap mt-12 text-gray-600">
      <h3 class="text-5xl">
        {{ __('Patient') }} {{ admission.case_number }}
      </h3>
      <h2 class="font-mono uppercase rotate-6 text-9xl mt-8 text-red-600 drop-shadow-lg">
        {{ __('Void') }}
      </h2>
    </div>
  </AppLayout>
</template>
