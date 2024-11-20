<template>
  <AppLayout title="Accounts">
    <div class="max-w-7xl mx-auto">
      <h1 class="text-2xl font-semibold text-gray-900">
        Accounts
      </h1>
    </div>
    <AccountsHeader class="mt-5" />
    <FormSection
      class="mt-8"
      @submitted="filterAccounts"
    >
      <template #title>
        Search Accounts
      </template>
      <div class="col-span-4 sm:col-span-1">
        <Label for="country">Status</label>
        <Select
          v-model="form.status"
          name="status"
          :options="$page.props.options.statuses"
          class="mt-1"
        />
      </div>
      <div class="col-span-4 sm:col-span-3">
        <Label for="country">Organization</label>
        <Input
          v-model="form.organization"
          name="organization"
          class="mt-1"
        />
      </div>
      <TransitionRoot
        as="div"
        :show="showOtherFilters"
        enter="transform transition ease-in-out duration-500 sm:duration-100"
        enter-from="opacity-0 -top-16"
        enter-to="opacity-100 top-0"
        leave="transform transition ease-in-out duration-100 sm:duration-100"
        leave-from="opacity-100"
        leave-to="opacity-0"
        class="col-span-4 grid grid-cols-4 gap-6"
      >
        <div class="col-span-2 sm:col-span-1">
          <Label for="federal-number">Federal Permit #</label>
          <Input
            v-model="form.federal_permit_number"
            name="federal_permit_number"
            autocomplete="off"
            class="mt-1"
          />
        </div>
        <div class="col-span-2 sm:col-span-1">
          <Label for="subdivision-number">State Permit #</label>
          <Input
            v-model="form.subdivision_permit_number"
            name="subdivision_permit_number"
            autocomplete="off"
            class="mt-1"
          />
        </div>
        <div class="col-span-4 sm:col-span-2">
          <Label for="country">Country / Region</label>
          <Select
            v-model="form.country"
            name="country"
            autocomplete="off"
            :options="$page.props.options.countries"
            class="mt-1 block w-full"
          />
        </div>
        <div class="col-span-4 sm:col-span-2">
          <Label for="address">Address</label>
          <Input
            v-model="form.address"
            name="address"
            autocomplete="off"
            class="mt-1"
          />
        </div>
        <div class="col-span-4 sm:col-span-2">
          <Label for="city">City</label>
          <Input
            v-model="form.city"
            name="city"
            autocomplete="off"
            class="mt-1"
          />
        </div>
        <div class="col-span-4 sm:col-span-2">
          <Label for="subdivision">State</label>
          <Select
            v-model="form.subdivision"
            name="subdivision"
            :options="$page.props.options.subdivisions"
            class="mt-1"
          />
        </div>
        <div class="col-span-4 sm:col-span-2">
          <Label for="postal-code">ZIP / Postal</label>
          <Input
            v-model="form.postal_code"
            name="postal_code"
            autocomplete="off"
            class="mt-1"
          />
        </div>
        <div class="col-span-4 sm:col-span-2">
          <Label for="first-name">Contact name</label>
          <Input
            v-model="form.contact_name"
            name="contact_name"
            autocomplete="off"
            class="mt-1"
          />
        </div>
        <div class="col-span-4 sm:col-span-2">
          <Label for="email-address">Email address</label>
          <Input
            v-model="form.contact_email"
            type="email"
            name="contact_email"
            autocomplete="off"
            class="mt-1"
          />
        </div>
        <div class="col-span-4 sm:col-span-2">
          <Label for="phone-number">Phone Number</label>
          <Input
            v-model="form.phone_number"
            name="phone_number"
            autocomplete="off"
            class="mt-1"
          />
        </div>
        <div class="col-span-4 sm:col-span-2">
          <Label for="website">Website</label>
          <Input
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
                  v-for="(account, i) in accounts.data"
                  :key="account.id"
                  :class="i % 2 === 0 ? 'bg-white' : 'bg-gray-50'"
                >
                  <td class="px-2 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <img
                          class="h-10 w-10 rounded-full"
                          :src="account.profile_photo_url"
                          :alt="account.organization"
                        >
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-700">
                          <Link
                            :href="route('accounts.show', account)"
                            class="hover:text-gray-900"
                          >
                            {{ account.organization }}
                          </Link>
                        </div>
                        <div class="text-sm text-gray-500">
                          {{ account.locale }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-2 py-4 text-sm text-gray-900 align-top">
                    <div class="text-sm font-medium text-gray-900">
                      {{ account.phone_number }}
                    </div>
                    <div class="text-sm text-gray-500">
                      {{ account.contact_email }}
                    </div>
                  </td>
                  <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-900">
                    <Badge :color="account.status === 'Active' ? 'green' : 'red'">
                      {{ account.status }}
                    </Badge>
                  </td>
                  <!-- <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div><b>Created</b>{{ account.created_at_for_humns }}</div>
                                        <div><b>Last Sign In</b>{{ account.last_sign_in }}</div>
                                    </td> -->
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <Paginator :properties="accounts" />
  </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import AccountsHeader from './Partials/AccountsHeader.vue';
import { TransitionRoot } from '@headlessui/vue';
import Paginator from '@/Components/Paginator.vue';
import Badge from '@/Components/Badge.vue';
import FormSection from '@/Components/FormElements/FormSection.vue';
import Label from '@/Components/FormElements/Label.vue';
import Input from '@/Components/FormElements/Input.vue';
import Select from '@/Components/FormElements/Select.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import URI from 'urijs';
import LocalStorage from '@/Utilities/LocalStorage';

export default {
    components: {
        AppLayout,
        AccountsHeader,
        TransitionRoot,
        Paginator,
        Badge,
        FormSection,
        Label,
        Input,
        Select,
        PrimaryButton
    },
    props: {
        accounts: Object,
        countries: Array,
        subdivisions: Array
    },
    data() {
        let uriObj = new URI().query(true);

        return {
            showOtherFilters: false,
            form: this.$inertia.form({
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
            })
        };
    },
    mounted() {
        LocalStorage.store(
            'accountFilters',
            new URI().query(true)
        );
    },
    methods: {
        filterAccounts() {
            this.form.get(this.route('accounts.index'), {
                preserveScroll: true,
                only: ['accounts']
            });
        },
    }
};
</script>
