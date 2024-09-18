<template>
  <AppLayout title="Custom Fields">
    <div class="lg:grid grid-cols-8 gap-8 mt-4">
      <MaintenanceAside class="mb-4 lg:mb-0 col-span-2" />
      <div class="col-span-6">
        <div class="shadow sm:rounded-md sm:overflow-hidden">
          <div class="bg-white py-6 px-4 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
              {{ __('Custom Fields') }}
            </h3>
            <p class="mt-1 text-sm text-gray-500">
              <strong class="font-bold">{{ __('You may only create :numAllowedFields custom fields.', {numAllowedFields}) }}</strong> {{ __("Before creating your custom field, make sure that you are not recreating a field that already exist in Wildlife Rehabilitation MD. It's better to have your data following the same standards as other Wildlife Rehabilitation MD accounts to ensure consistency of data collected on each patient.") }}
            </p>
          </div>
        </div>
        <div class="sm:flex sm:items-center sm:justify-between mt-8">
          <div>
            <h3 class="text-lg leading-6 font-medium text-gray-900">
              {{ __('Fields') }}
            </h3>
            <h4 class="mt-1 text-sm text-gray-500">
              {{ __('Total') }}: {{ fieldsCount }}
            </h4>
          </div>
          <div class="mt-3 sm:mt-0 sm:ml-4">
            <label
              for="mobile-search-candidate"
              class="sr-only"
            >{{ __('Search') }}</label>
            <label
              for="desktop-search-candidate"
              class="sr-only"
            >{{ __('Search') }}</label>
            <div class="flex rounded-md shadow-sm">
              <div class="relative flex-grow focus-within:z-10">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <MagnifyingGlassIcon
                    class="h-5 w-5 text-gray-400"
                    aria-hidden="true"
                  />
                </div>
                <input
                  id="mobile-search-candidate"
                  type="text"
                  name="mobile-search-candidate"
                  class="focus:ring-blue-500 focus:border-blue-500 block w-full rounded-none rounded-l-md pl-10 sm:hidden border-gray-300"
                  :placeholder="__('Search')"
                >
                <input
                  id="desktop-search-candidate"
                  v-model="form.search"
                  type="text"
                  name="desktop-search-candidate"
                  class="hidden focus:ring-blue-500 focus:border-blue-500 w-full rounded-none rounded-l-md pl-10 sm:block sm:text-sm border-gray-300"
                  :placeholder="`${__('Search custom fields')}...`"
                  @keyup="search"
                >
              </div>
              <PrimaryButton
                class="rounded-l-none"
                @click="$inertia.get(route('maintenance.custom_fields.create'))"
              >
                <PlusIcon
                  class="h-5 w-5"
                  aria-hidden="true"
                />
                <span class="ml-2 whitespace-nowrap">{{ __('New Custom Field') }}</span>
              </PrimaryButton>
            </div>
          </div>
        </div>
        <div class="mt-2 flow-root">
          <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
              <div class="shadow overflow-hidden border-b border-gray-200">
                <table class="min-w-full">
                  <thead class="bg-blue-100">
                    <tr>
                      <th
                        scope="col"
                        class="px-6 py-3"
                      />
                      <th
                        scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                      >
                        {{ __('Name') }}
                      </th>
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
                        {{ __('Panel') }}
                      </th>
                      <th
                        scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                      >
                        {{ __('Required') }}
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white">
                    <template
                      v-for="fieldGroup in fields"
                      :key="fieldGroup[0].group"
                    >
                      <tr class="border-t border-gray-200">
                        <th
                          colspan="5"
                          scope="colgroup"
                          class="bg-gray-50 py-2 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-3"
                        >
                          {{ fieldGroup[0].group }}
                        </th>
                      </tr>
                      <tr
                        v-for="(field, childCategoryIdx) in fieldGroup"
                        :key="field.id"
                        :class="[childCategoryIdx === 0 ? 'border-gray-300' : 'border-gray-200', 'border-t']"
                      >
                        <td class="whitespace-nowrap px-6 py-4 text-sm leading-5 text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700 transition ease-in-out duration-150">
                          <Link :href="route('maintenance.custom_fields.edit', field)">
                            {{ __('Edit') }}
                          </Link>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-800">
                          {{ field.label }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-800">
                          {{ $page.props.options.fieldTypes.find(type => type.value === field.type).label }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-800">
                          {{ field.panel }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-800">
                          {{ field.is_required ? __('Yes') : __('No') }}
                        </td>
                      </tr>
                    </template>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import MaintenanceAside from '../Partials/MaintenanceAside.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import { MagnifyingGlassIcon, PlusIcon } from '@heroicons/vue/24/outline';
import debounce from 'lodash/debounce';
import URI from 'urijs';

const props = defineProps({
  fields: {
    type: Array,
    required: true
  },
  numAllowedFields: {
    type: Number,
    required: true
  }
});

let fieldsCount = computed(() => props.fields.reduce((carry, group) => carry + group.length, 0));
</script>

<script>
export default {
    data() {
        return {
            form: this.$inertia.form({
                search: new URI().query(true).search
            })
        };
    },
    methods: {
      search() {
        this.debounceSearch();
      },
      debounceSearch: debounce(function() {
          if (this.form.search === '') {
              this.$inertia.get(this.route(this.route().current()));
          } else {
              this.form.get(this.route('maintenance.custom_fields.index'));
          }
      }, 500)
    }
};
</script>
