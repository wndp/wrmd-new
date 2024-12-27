<template>
  <PatientLayout title="Revisions">
    <div class="p-4 bg-white">
      <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
          <Link
            :href="route('patients.revisions.index', caseQueryString)"
            class="inline-flex items-center text-base leading-5 text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700 transition ease-in-out duration-150"
          >
            <ArrowLongLeftIcon class="h-6 w-6 mr-2" />
            {{ __('Return to Revisions') }}
          </Link>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
          <RestoreRevisionButton
            v-if="revision.action === 'updated'"
            :revision-id="revision.id"
          >
            {{ __('Restore All Old Values') }}
          </RestoreRevisionButton>
        </div>
      </div>
      <section class="mt-4 flex flex-wrap gap-2">
        <div class="inline-flex items-center bg-gray-100 rounded-full p-0.5">
          <Badge color="yellow">
            {{ __('Type') }}
          </Badge>
          <span class="mx-2 text-xs font-bold text-gray-600">{{ revision.revisionable_type.split("\\").pop() }}</span>
        </div>
        <div class="inline-flex items-center bg-gray-100 rounded-full p-0.5">
          <Badge color="yellow">
            {{ __('Type ID') }}
          </Badge>
          <span class="mx-2 text-xs font-bold text-gray-600">{{ revision.revisionable_id }}</span>
        </div>
        <div class="inline-flex items-center bg-gray-100 rounded-full p-0.5">
          <Badge color="yellow">
            {{ __('User') }}
          </Badge>
          <span class="mx-2 text-xs font-bold text-gray-600">{{ revision.user_name }}</span>
        </div>
        <div class="inline-flex items-center bg-gray-100 rounded-full p-0.5">
          <Badge color="yellow">
            {{ __('Action') }}
          </Badge>
          <span class="mx-2 text-xs font-bold text-gray-600">{{ revision.action }}</span>
        </div>
        <div class="inline-flex items-center bg-gray-100 rounded-full p-0.5">
          <Badge color="yellow">
            {{ __('Date') }}
          </Badge>
          <span class="mx-2 text-xs font-bold text-gray-600">{{ revision.created_at_for_humans }}</span>
        </div>
      </section>
    </div>
    <div class="px-4 sm:px-6 lg:px-8">
      <div class="mt-8 flex flex-col">
        <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
          <div class="inline-block min-w-full py-2 align-middle">
            <div class="overflow-hidden shadow-sm ring-1 ring-black ring-opacity-5">
              <table class="min-w-full">
                <thead class="bg-white">
                  <tr class="divide-x divide-gray-200 border-b-2 border-gray-400">
                    <th
                      scope="col"
                      class="sticky px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider"
                    >
                      {{ __('Attribute') }}
                    </th>
                    <th
                      scope="col"
                      class="sticky px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider bg-red-100"
                    >
                      {{ __('Old Value') }}
                    </th>
                    <th
                      scope="col"
                      class="sticky px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider bg-green-100"
                    >
                      {{ __('New Value') }}
                    </th>
                    <th
                      scope="col"
                      class="sticky px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider bg-orange-100"
                    >
                      {{ __('Changes') }}
                    </th>
                    <th
                      scope="col"
                      class="sticky px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    />
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr
                    v-for="(diff, id) in revision.diff"
                    :key="diff.attribute"
                    class="divide-x divide-gray-200"
                    :class="id % 2 === 0 ? undefined : 'bg-gray-50'"
                  >
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-500">
                      {{ diff.attribute }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                      {{ revision.old[diff.attribute] }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                      {{ revision.new[diff.attribute] }}
                    </td>
                    <td
                      class="px-6 py-4 text-sm text-gray-500"
                      v-html="revision.updated_attributes.includes(diff.attribute) ? diff.diff_for_humans : null"
                    />
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      <RestoreRevisionButton
                        v-if="revision.action === 'updated' && revision.updated_attributes.includes(diff.attribute)"
                        :revision-id="revision.id"
                        :attribute="diff.attribute"
                      >
                        {{ __('Restore Old Value') }}
                      </RestoreRevisionButton>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </PatientLayout>
</template>

<script setup>
import PatientLayout from '@/Layouts/PatientLayout.vue';
import Badge from '@/Components/Badge.vue';
import RestoreRevisionButton from './Partials/RestoreRevisionButton.vue';
import { ArrowLongLeftIcon } from '@heroicons/vue/24/outline';
</script>

<script>
  export default {
    props: {
      revision: {
        type: Object,
        required: true
      }
    },
    data() {
      return {
        caseQueryString: {
          y: this.$page.props.admission.case_year,
          c: this.$page.props.admission.case_id,
        },
      }
    },
  };
</script>

<style>

</style>
