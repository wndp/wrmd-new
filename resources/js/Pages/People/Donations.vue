<script setup>
import {ref, computed} from 'vue';
import {useForm} from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PersonTabs from './Partials/PersonTabs.vue';
import SaveDonationModal from './Partials/SaveDonationModal.vue';
import DeleteDonationModal from './Partials/DeleteDonationModal.vue';
import LocalStorage from '@/Composables/LocalStorage';
import { ArrowLongLeftIcon, TrashIcon, PencilIcon } from '@heroicons/vue/24/outline';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import {__} from '@/Composables/Translate';

const localStorage = LocalStorage();

defineProps({
  person: Object,
  donations: Array,
  donationMethodIsCashId: Number
});

const showUpsertDonation = ref(false);
const confirmingDeletion = ref(false);
const donationRef = ref({});

const uri = computed(() => localStorage.get('peopleFilters'));

const showConfirmingDonationDeletion = (donation) => {
  donationRef.value = donation;
  confirmingDeletion.value = true;
};

const showUpsertDonationDeletion = (donation) => {
  donationRef.value = donation;
  showUpsertDonation.value = true;
};
</script>

<template>
  <AppLayout :title="person.identifier">
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900">
        {{ person.identifier }}
      </h1>
      <Link
        :href="route('people.donors.index', uri)"
        class="inline-flex items-center text-base leading-5 text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700 transition ease-in-out duration-150 mb-8"
      >
        <ArrowLongLeftIcon class="h-6 w-6 mr-2" />
        {{ __('Return to People') }}
      </Link>
    </template>
    <PersonTabs :person="person" />
    <div class="flex flex-col mt-4">
      <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
          <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
            <div class="bg-white px-4 py-5 border-b border-gray-200 sm:px-6">
              <div class="-ml-4 -mt-4 flex justify-between items-center flex-wrap sm:flex-nowrap">
                <div class="ml-4 mt-4">
                  <h3 class="text-lg leading-6 font-medium text-gray-900">
                    {{ __(":name's Donations", {name: person.identifier}) }}
                  </h3>
                </div>
                <div class="ml-4 mt-4 flex-shrink-0">
                  <PrimaryButton @click="showUpsertDonationDeletion()">
                    {{ __('Create new donation') }}
                  </PrimaryButton>
                </div>
              </div>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-blue-100">
                <tr>
                  <th
                    scope="col"
                    class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    style="width: 50px"
                  />
                  <th
                    scope="col"
                    class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    {{ __('Date') }}
                  </th>
                  <th
                    scope="col"
                    class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    {{ __('Method') }}
                  </th>
                  <th
                    scope="col"
                    class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    {{ __('Value') }}
                  </th>
                  <th
                    scope="col"
                    class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    {{ __('Comments') }}
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr
                  v-for="donation in donations"
                  :key="donation.id"
                >
                  <td class="px-3 py-3 whitespace-nowrap text-sm">
                    <div class="flex justify-between items-start">
                      <button
                        type="button"
                        dusk="donation-delete"
                      >
                        <TrashIcon
                          class="w-5 h-5 mr-4 text-red-600"
                          @click="showConfirmingDonationDeletion(donation)"
                        />
                      </button>
                      <button
                        type="button"
                        dusk="donation-edit"
                      >
                        <PencilIcon
                          class="w-5 h-5 mr-4 text-blue-600"
                          @click="showUpsertDonationDeletion(donation)"
                        />
                      </button>
                    </div>
                  </td>
                  <td class="px-3 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ donation.donated_at_for_humans }}
                  </td>
                  <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-500">
                    {{ donation.method }}
                  </td>
                  <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-500">
                    {{ donation.value_for_humans }}
                  </td>
                  <td class="px-3 py-3 text-sm text-gray-500">
                    {{ donation.comments }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <DeleteDonationModal
      v-if="confirmingDeletion"
      :person="person"
      :donation="donationRef"
      :show="true"
      @close="confirmingDeletion = false"
    />
    <SaveDonationModal
      v-if="showUpsertDonation"
      :person="person"
      :donation="donationRef"
      :donationMethodIsCashId="donationMethodIsCashId"
      :show="true"
      @close="showUpsertDonation = false"
    />
  </AppLayout>
</template>
