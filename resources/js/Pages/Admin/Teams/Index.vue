<script setup>
import {ref, onMounted} from 'vue';
import {useForm} from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import AdminNavigation from '../Partials/AdminNavigation.vue';
import TeamsHeader from './Partials/TeamsHeader.vue';
import { TransitionRoot } from '@headlessui/vue';
import Paginator from '@/Components/Paginator.vue';
import Badge from '@/Components/Badge.vue';
import FormSection from '@/Components/FormElements/FormSection.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import URI from 'urijs';
import LocalStorage from '@/Composables/LocalStorage';

const localStorage = LocalStorage();

defineProps({
  teams: Object,
  countries: Array,
  subdivisions: Array
});

const uriObj = new URI().query(true);

const showOtherFilters = ref(false);

const form = useForm({
    status: uriObj.status || 'Active',
    organization: uriObj.organization,
    federal_permit_number: uriObj.federal_permit_number,
    subdivision_permit_number: uriObj.subdivision_permit_number,
    country: uriObj.country,
    address: uriObj.address,
    city: uriObj.city,
    subdivision: uriObj.subdivision,
    postal_code: uriObj.postal_code,
    contact_name: uriObj.contact_name,
    contact_email: uriObj.contact_email,
    phone_number: uriObj.phone_number,
    website: uriObj.website,
});

onMounted(() => localStorage.store('accountFilters', new URI().query(true)));

const filterAccounts = () => {
    form.get(route('teams.index'), {
        preserveScroll: true,
        only: ['teams']
    });
};
</script>

<template>
  <AppLayout title="Accounts">
    <div class="lg:grid grid-cols-8 gap-8 mt-4">
      <AdminNavigation class="mb-4 lg:mb-0 col-span-2" />
      <div class="col-span-6">
        <div class="max-w-7xl mx-auto">
          <h1 class="text-2xl font-semibold text-gray-900">
            Accounts
          </h1>
        </div>
        <TeamsHeader class="mt-5" />
        <FormSection
          class="mt-8"
          @submitted="filterAccounts"
        >
          <template #title>
            Search Accounts
          </template>
          <div class="col-span-4 sm:col-span-1">
            <InputLabel for="country">
              Status
            </InputLabel>
            <SelectInput
              v-model="form.status"
              name="status"
              :options="$page.props.options.accountStatusOptions"
              class="mt-1"
            />
          </div>
          <div class="col-span-4 sm:col-span-3">
            <InputLabel for="country">
              Organization
            </InputLabel>
            <TextInput
              v-model="form.organization"
              name="organization"
              class="mt-1"
            />
          </div>
          <TransitionRoot
            as="div"
            :show="showOtherFilters"
            enter="transform transition ease-in-out duration-500 sm:duration-100"
            enterFrom="opacity-0 -top-16"
            enterTo="opacity-100 top-0"
            leave="transform transition ease-in-out duration-100 sm:duration-100"
            leaveFrom="opacity-100"
            leaveTo="opacity-0"
            class="col-span-4 grid grid-cols-4 gap-6"
          >
            <div class="col-span-2 sm:col-span-1">
              <InputLabel for="federal-number">
                Federal Permit #
              </InputLabel>
              <TextInput
                v-model="form.federal_permit_number"
                name="federal_permit_number"
                autocomplete="off"
                class="mt-1"
              />
            </div>
            <div class="col-span-2 sm:col-span-1">
              <InputLabel for="subdivision-number">
                State Permit #
              </InputLabel>
              <TextInput
                v-model="form.subdivision_permit_number"
                name="subdivision_permit_number"
                autocomplete="off"
                class="mt-1"
              />
            </div>
            <div class="col-span-4 sm:col-span-2">
              <InputLabel for="country">
                Country / Region
              </InputLabel>
              <SelectInput
                v-model="form.country"
                name="country"
                autocomplete="off"
                :options="$page.props.options.countryOptions"
                class="mt-1 block w-full"
              />
            </div>
            <div class="col-span-4 sm:col-span-2">
              <InputLabel for="address">
                Address
              </InputLabel>
              <TextInput
                v-model="form.address"
                name="address"
                autocomplete="off"
                class="mt-1"
              />
            </div>
            <div class="col-span-4 sm:col-span-2">
              <InputLabel for="city">
                City
              </InputLabel>
              <TextInput
                v-model="form.city"
                name="city"
                autocomplete="off"
                class="mt-1"
              />
            </div>
            <div class="col-span-4 sm:col-span-2">
              <InputLabel for="subdivision">
                State
              </InputLabel>
              <SelectInput
                v-model="form.subdivision"
                name="subdivision"
                :options="$page.props.options.subdivisionOptions"
                class="mt-1"
              />
            </div>
            <div class="col-span-4 sm:col-span-2">
              <InputLabel for="postal-code">
                ZIP / Postal
              </InputLabel>
              <TextInput
                v-model="form.postal_code"
                name="postal_code"
                autocomplete="off"
                class="mt-1"
              />
            </div>
            <div class="col-span-4 sm:col-span-2">
              <InputLabel for="first-name">
                Contact name
              </InputLabel>
              <TextInput
                v-model="form.contact_name"
                name="contact_name"
                autocomplete="off"
                class="mt-1"
              />
            </div>
            <div class="col-span-4 sm:col-span-2">
              <InputLabel for="email-address">
                Email address
              </InputLabel>
              <TextInput
                v-model="form.contact_email"
                type="email"
                name="contact_email"
                autocomplete="off"
                class="mt-1"
              />
            </div>
            <div class="col-span-4 sm:col-span-2">
              <InputLabel for="phone-number">
                Phone Number
              </InputLabel>
              <TextInput
                v-model="form.phone_number"
                name="phone_number"
                autocomplete="off"
                class="mt-1"
              />
            </div>
            <div class="col-span-4 sm:col-span-2">
              <InputLabel for="website">
                Website
              </InputLabel>
              <TextInput
                v-model="form.website"
                type="url"
                name="website"
                autocomplete="off"
                class="mt-1"
              />
            </div>
          </TransitionRoot>
          <template #actions>
            <button
              type="button"
              class="text-gray-500 mr-4"
              @click="showOtherFilters = !showOtherFilters"
            >
              Show other filters
            </button>
            <PrimaryButton
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
              @click="filterAccounts"
            >
              Search
            </PrimaryButton>
          </template>
        </FormSection>
        <div class="flex flex-col mt-8">
          <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
              <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-blue-100">
                    <tr>
                      <th
                        scope="col"
                        class="px-2 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                      >
                        Account
                      </th>
                      <th
                        scope="col"
                        class="px-2 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                      >
                        Contact
                      </th>
                      <th
                        scope="col"
                        class="px-2 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                      >
                        Status
                      </th>
                      <!-- <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Dates</th> -->
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr
                      v-for="(team, i) in teams.data"
                      :key="team.id"
                      :class="i % 2 === 0 ? 'bg-white' : 'bg-gray-50'"
                    >
                      <td class="px-2 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                          <div class="flex-shrink-0 h-10 w-10">
                            <img
                              class="h-10 w-10 rounded-full"
                              :src="team.profile_photo_url"
                              :alt="team.name"
                            >
                          </div>
                          <div class="ml-4">
                            <div class="text-sm font-medium text-gray-700">
                              <Link
                                :href="route('teams.show', team)"
                                class="hover:text-gray-900"
                              >
                                {{ team.name }}
                              </Link>
                            </div>
                            <div class="text-sm text-gray-500">
                              {{ team.locale }}
                            </div>
                          </div>
                        </div>
                      </td>
                      <td class="px-2 py-4 text-sm text-gray-900 align-top">
                        <div class="text-sm font-medium text-gray-900">
                          {{ team.phone_number }}
                        </div>
                        <div class="text-sm text-gray-500">
                          {{ team.contact_email }}
                        </div>
                      </td>
                      <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-900">
                        <Badge :color="team.status === 'ACTIVE' ? 'green' : 'red'">
                          {{ team.status_for_humans }}
                        </Badge>
                      </td>
                      <!-- <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div><b>Created</b>{{ team.created_at_for_humns }}</div>
                                            <div><b>Last Sign In</b>{{ team.last_sign_in }}</div>
                                        </td> -->
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <Paginator :properties="teams" />
      </div>
    </div>
  </AppLayout>
</template>
