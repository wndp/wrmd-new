<template>
  <PatientLayout title="Revisions">
    <div class="flex flex-col">
      <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
          <div class="shadow overflow-hidden border-b border-gray-200">
            <div class="bg-white px-4 py-5 border-b border-gray-200 sm:px-6">
              <h3 class="text-lg leading-6 font-medium text-gray-900">
                {{ __('Revisions') }}
              </h3>
              <div class="mt-1 text-sm text-gray-500">
                {{ __('Below is a list of all recorded revisions for this patient. Each revision records exactly what was changed on the patient, when that revision occurred and by whom.') }}
              </div>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-blue-100">
                <tr>
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  />
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    {{ __('Type') }}
                  </th>
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    {{ __('User') }}
                  </th>
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    {{ __('Action') }}
                  </th>
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    {{ __('Date') }}
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr
                  v-for="revision in revisions"
                  :key="revision.id"
                >
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                    <Link
                      :href="route('patients.revisions.show', [revision, caseQueryString])"
                      class="hover:text-blue-700"
                    >
                      {{ __('View') }}
                    </Link>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ revision.revisionable_type.split("\\").pop() }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ revision.user_name }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ revision.action }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ revision.created_at_for_humans }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </PatientLayout>
</template>

<script setup>
import PatientLayout from '@/Layouts/PatientLayout.vue';
</script>

<script>
  export default {
    props: {
      revisions: Array
    },
    data() {
      return {
        caseQueryString: {
          y: this.$page.props.admission.case_year,
          c: this.$page.props.admission.case_id,
        },
      }
    },
    // computed: {
    //   patient() {
    //     return this.$page.props.admission.patient;
    //   }
    // }
  };
</script>
