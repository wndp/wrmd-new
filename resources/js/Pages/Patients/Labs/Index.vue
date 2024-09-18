<template>
  <PatientLayout title="Lab">
    <div class="md:grid md:grid-cols-2 md:gap-8">
      <div>
        <div class="sm:flex sm:items-center">
          <div class="sm:flex-auto">
            <h1 class="text-xl font-semibold text-gray-900">
              {{ __('Lab Tests') }}
            </h1>
            <p class="mt-2 text-sm text-gray-700">
              A list of all the lab results for this patient including the date and the name of the technician.
            </p>
          </div>
          <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <Menu
              as="div"
              class="relative inline-block text-left"
            >
              <div>
                <MenuButton class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-blue-600 text-sm font-medium text-white hover:bg-blue-700 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300">
                  <span class="sr-only">{{ __('Open options') }}</span>
                  <span>{{ __('Add Lab Results') }}</span>
                  <ChevronDownIcon
                    class="ml-1 h-5 w-5"
                    aria-hidden="true"
                  />
                </MenuButton>
              </div>
              <TransitionRoot
                enter-active="transition ease-out duration-100"
                enter-from="transform opacity-0 scale-95"
                enter-to="transform opacity-100 scale-100"
                leave-active="transition ease-in duration-75"
                leave-from="transform opacity-100 scale-100"
                leave-to="transform opacity-0 scale-95"
              >
                <MenuItems class="absolute origin-top-left sm:origin-top-right left-0 sm:left-auto sm:right-0 z-40 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none">
                  <div class="py-1">
                    <MenuItem
                      v-slot="{ active }"
                    >
                      <button
                        type="button"
                        :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'w-full flex justify-between px-4 py-2 text-sm']"
                        @click="addLabsFor('foo')"
                      >
                        <span>{{ __(':test Lab Results', {test: 'Foo'}) }}</span>
                      </button>
                    </MenuItem>
                    <MenuItem
                      v-for="test in $page.props.options.labTests"
                      :key="test.label"
                      v-slot="{ active }"
                    >
                      <button
                        type="button"
                        :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'w-full flex justify-between px-4 py-2 text-sm']"
                        @click="addLabsFor(test.value)"
                      >
                        <span>{{ __(':test Lab Results', {test: test.label}) }}</span>
                      </button>
                    </MenuItem>
                  </div>
                </MenuItems>
              </TransitionRoot>
            </Menu>
          </div>
        </div>
        <div class="mt-8 flex flex-col">
          <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full align-middle py-2 px-4 md:px-6 lg:px-8">
              <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                <table class="min-w-full divide-y divide-gray-300">
                  <thead class="bg-blue-100">
                    <tr>
                      <th
                        scope="col"
                        class="py-3.5 pl-4 pr-3 sm:pl-6 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                      >
                        {{ __('Test') }}
                      </th>
                      <th
                        scope="col"
                        class="px-3 py-3.5 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                      >
                        {{ __('Technician') }}
                      </th>
                      <th
                        scope="col"
                        class="px-3 py-3.5 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                      >
                        {{ __('Date') }}
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white">
                    <tr
                      v-for="(lab, labIdx) in patient.labs"
                      :key="lab.id"
                      :class="labIdx % 2 === 0 ? undefined : 'bg-gray-50'"
                    >
                      <td class="whitespace-nowrap pl-4 pr-3 py-4 sm:pl-6 text-sm font-medium text-gray-900">
                        {{ lab.test }}
                      </td>
                      <td class="whitespace-nowrap px-3 py-3.5 text-sm text-gray-500">
                        {{ lab.technician }}
                      </td>
                      <td class="whitespace-nowrap px-3 py-3.5 text-sm text-gray-500">
                        {{ lab.date_for_humans }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div>
        something else
      </div>
    </div>
    <component
      :is="labComponent"
      v-if="showAddLab"
    />
  </PatientLayout>
</template>

<script setup>
import PatientLayout from '@/Layouts/PatientLayout.vue';
import { ChevronDownIcon } from '@heroicons/vue/24/outline';
import { TransitionRoot, Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import upperFirst from 'lodash/upperFirst'
</script>

<script>
export default {
  data() {
    return {
      showAddLab: false,
      labComponent: null
    }
  },
  computed: {
    patient() {
      return this.$page.props.admission.patient;
    }
  },
  methods: {
    addLabsFor(test) {
      test = upperFirst(test)
      this.labComponent = `Save${test}Test`;
      this.showAddLab = true;
    }
  }
};
</script>
