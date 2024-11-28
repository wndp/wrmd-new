<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import PatientLayout from '@/Layouts/PatientLayout.vue';
import { PencilIcon, PlusIcon } from '@heroicons/vue/24/outline';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import Badge from '@/Components/Badge.vue';
import {__} from '@/Composables/Translate';
import {can} from '@/Composables/Can';
import {Abilities} from '@/Enums/Abilities';

defineProps({
  patient: {
    type: Object,
    required: true
  },
  exams: {
    type: Array,
    required: true
  }
});

const caseQueryString = computed(() => {
  return {
    y: usePage().props.admission.case_year,
    c: usePage().props.admission.case_id,
  }
})

//let confirmingExamDeletion = ref(false);
//let examToDelete = ref({});

// let showConfirmingExamDeletion = (exam) => {
//   examToDelete.value = exam;
//   confirmingExamDeletion.value = true;
// };

</script>

<template>
  <PatientLayout title="Exams">
    <div class="mt-4 sm:flex sm:items-center sm:justify-between">
      <div>
        <h3 class="text-lg leading-6 font-medium text-gray-900">
          {{ __('Exams') }}
        </h3>
        <p class="mt-1 text-sm text-gray-500">
          {{ __('Record field, intake, daily and release exams on your patients.') }}
        </p>
      </div>
      <div class="mt-3 sm:mt-0 sm:ml-4">
        <PrimaryButton
          v-if="can(Abilities.MANAGE_EXAMS) && patient.locked_at === null"
          @click="$inertia.get(route('patients.exam.create', caseQueryString))"
        >
          <PlusIcon
            class="h-5 w-5"
            aria-hidden="true"
          />
          <span class="ml-2 whitespace-nowrap">{{ __('New Exam') }}</span>
        </PrimaryButton>
      </div>
    </div>
    <div class="bg-white rounded-b-lg mt-8">
      <div
        class="align-middle inline-block min-w-full"
      >
        <table class="min-w-full divide-y divide-gray-300">
          <thead class="bg-blue-100">
            <tr>
              <th
                scope="col"
                class="py-3.5 pl-4 pr-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                {{ __('Date') }}
              </th>
              <th
                scope="col"
                class="hidden px-3 py-3.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider lg:table-cell"
              >
                {{ __('Type') }}
              </th>
              <th
                scope="col"
                class="hidden px-3 py-3.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider lg:table-cell"
              >
                {{ __('Content') }}
              </th>
              <th
                scope="col"
                class="hidden relative py-3.5 pl-3 pr-4 lg:table-cell"
              >
                <span class="sr-only">Edit</span>
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 bg-white">
            <tr
              v-for="exam in exams"
              :key="exam.id"
            >
              <td class="w-ful max-w- py-4 pl-4 pr-3 sm:w-auto sm:max-w-none">
                <div class="flex justify-between items-center text-sm font-medium text-gray-900 whitespace-nowrap">
                  {{ exam.examined_at_for_humans }}
                  <Badge class="lg:hidden">
                    {{ exam.type }}
                  </Badge>
                  <div class="lg:hidden">
                    <Link
                      :href="route('patients.exam.edit', {...caseQueryString, ...{exam}})"
                      class="text-blue-600 hover:text-blue-900"
                      dusk="edit-exam"
                    >
                      <PencilIcon class="w-5 h-5" />
                    </Link>
                  </div>
                </div>
                <div class="lg:hidden mt-2 text-sm text-gray-600">
                  {{ exam.summary_body }}
                </div>
              </td>
              <td class="hidden px-3 py-4 text-sm lg:table-cell">
                <Badge>
                  {{ exam.type }}
                </Badge>
              </td>
              <td class="hidden px-3 py-4 text-sm text-gray-600 lg:table-cell">
                {{ exam.summary_body }}
              </td>
              <td class="hidden py-4 pl-3 pr-4 text-right text-sm font-medium lg:table-cell">
                <Link
                  :href="route('patients.exam.edit', {...caseQueryString, ...{exam}})"
                  class="text-blue-600 hover:text-blue-900"
                  dusk="edit-exam"
                >
                  <PencilIcon class="w-5 h-5" />
                </Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <!-- <DeleteExamModal
      v-if="confirmingExamDeletion"
      :patient="patient"
      :exam="examToDelete"
      :show="true"
      @close="confirmingExamDeletion = false"
    /> -->
  </PatientLayout>
</template>
