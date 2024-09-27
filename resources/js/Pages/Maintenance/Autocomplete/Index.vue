<script setup>
import { inject, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import MaintenanceAside from '../Partials/MaintenanceAside.vue';
// import Panel from '@/Components/Panel.vue';
// import InputLabel from '@/Components/FormElements/InputLabel.vue';
// import FileInput from '@/Components/FormElements/FileInput.vue';
import TextareaAutosize from '@/Components/FormElements/TextareaAutosize.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import ValidationErrors from '@/Components/FormElements/ValidationErrors.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import { PlusIcon } from '@heroicons/vue/24/outline';
import AutocompleteRow from './Partials/AutocompleteRow.vue';

const route = inject('route');

const props = defineProps({
  autocompletes: {
    type: Array,
    required: true
  }
});

let form = useForm({
  field: '',
  values: '',
});

let autocompleteOptions = props.autocompletes.map(option => option.field);

let autoCompleteAble = computed(
  () => usePage().props.options.autoCompleteAble.filter(option => ! autocompleteOptions.includes(option.value))
);

let store = () => {
  form.post(route('maintenance.autocomplete.store'), {
    preserveState: false
  });
}
</script>

<template>
  <AppLayout title="Autocomplete">
    <div class="lg:grid grid-cols-8 gap-8 mt-4">
      <MaintenanceAside class="mb-4 lg:mb-0 col-span-2" />
      <div class="col-span-6">
        <div class="shadow sm:rounded-md sm:overflow-hidden">
          <div class="bg-white py-6 px-4 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
              {{ __('Autocomplete') }}
            </h3>
            <p class="mt-2 text-sm text-gray-500">{{ __('Autocomplete allows you to define a list of values for some text input fields that will be used as suggestions to users as they type.') }} {{ __('It should be used in a field where the same content is often entered. For example, the City Found field may have could benefit from a list of all the near by cities and towns in the area your organization services.') }}</p>
          </div>
        </div>
        <ValidationErrors class="mt-4" />
        <div class="flex flex-col mt-4">
          <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
              <div class="shadow overflow-hidden border-b border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-blue-100">
                    <tr>
                      <th
                        scope="col"
                        class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                      >
                        {{ __('Field') }}
                      </th>
                      <th
                        scope="col"
                        class="hidden px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-full xl:table-cell"
                      >
                        {{ __('Values') }}
                      </th>
                      <th
                        scope="col"
                        class="hidden px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider xl:table-cell"
                      />
                      <th
                        scope="col"
                        class="hidden px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider xl:table-cell"
                      />
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                      <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-800">
                        <SelectInput
                          v-model="form.field"
                          name="new_field"
                          :options="autoCompleteAble"
                        />
                        <div class="xl:hidden mt-2">
                          <TextareaAutosize
                            v-model="form.values"
                            name="new_values"
                          />
                          <PrimaryButton
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                            class="mt-2"
                            @click="store"
                          >
                            <PlusIcon class="h-5 w-5" />
                          </PrimaryButton>
                        </div>
                      </td>
                      <td class="hidden px-2 py-2 whitespace-nowrap text-sm text-gray-500 xl:table-cell">
                        <TextareaAutosize
                          v-model="form.values"
                          name="new_values"
                        />
                      </td>
                      <td class="hidden px-2 py-2 whitespace-nowrap text-sm text-gray-500 xl:table-cell">
                        <PrimaryButton
                          :class="{ 'opacity-25': form.processing }"
                          :disabled="form.processing"
                          @click="store"
                        >
                          <PlusIcon class="h-5 w-5" />
                        </PrimaryButton>
                      </td>
                      <td class="hidden px-2 py-2 whitespace-nowrap text-sm text-gray-500 xl:table-cell" />
                    </tr>
                    <tr
                      is="vue:AutocompleteRow"
                      v-for="autocomplete in autocompletes"
                      :key="autocomplete.field"
                      :autocomplete="autocomplete"
                    />
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
