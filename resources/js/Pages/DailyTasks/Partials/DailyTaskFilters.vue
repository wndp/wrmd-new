<script setup>
import {ref, computed, nextTick} from 'vue';
import {useForm, usePage} from '@inertiajs/vue3';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import CheckboxCombobox from '@/Components/FormElements/CheckboxCombobox.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import Toggle from '@/Components/FormElements/Toggle.vue';
import { TransitionRoot } from '@headlessui/vue';
import { ChevronDownIcon, ChevronUpIcon, EyeIcon } from '@heroicons/vue/24/outline';
import {__} from '@/Composables/Translate';

const props = defineProps({
  filters: {
      type: Object,
      required: true
  }
});

const showOtherFilters = ref(false);

const form = useForm({
    date: props.filters.date,
    facility: props.filters.facility,
    group_by: props.filters.group_by,
    include: props.filters.include || usePage().props.options.schedulables.map(o => o.value),
    include_non_pending: props.filters.include_non_pending,
    include_non_possession: props.filters.include_non_possession
});

const groupBys = [
    {
        value: 'Area',
        label: __('Area / Room')
    },
    {
        value: 'Enclosure',
        label: __('Enclosure')
    },
    {
        value: 'Type',
        label: __('Type')
    }
];

const mutatedFacilities = computed(() => {
  return usePage().props.options.patientLocationFacilitiesOptions.concat([
      {
          value: 'anywhere',
          label: __('Anywhere')
      },
      {
          value: 'none-assigned',
          label: __('None assigned')
      }
  ]);
});

const filterChanged = () => {
    if (! showOtherFilters.value) {
        nextTick(() => filter());
    }
};

const filter = () => form.get(route('daily-tasks.index'));
</script>

<template>
  <div class="bg-white shadow rounded-b-lg px-4 py-5 sm:p-3 relative space-y-4">
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
      <div>
        <InputLabel
          for="date-range"
          class="block text-sm font-medium text-gray-700"
        >
          {{ __('Due Date') }}
        </InputLabel>
        <div class="mt-1 relative rounded-md">
          <DatePicker
            id="date"
            v-model="form.date"
            :configs="{maxDate: null}"
            @change="filterChanged"
          />
        </div>
      </div>
      <div>
        <InputLabel for="group_by">
          {{ __('Group By') }}
        </InputLabel>
        <div class="mt-1 relative rounded-md">
          <SelectInput
            v-model="form.group_by"
            name="group_by"
            :options="groupBys"
            @change="filterChanged"
          />
        </div>
      </div>
      <div>
        <InputLabel for="facility">
          {{ __('Current Facility') }}
        </InputLabel>
        <div class="mt-1 relative rounded-md">
          <SelectInput
            v-model="form.facility"
            name="facility"
            :options="mutatedFacilities"
            @change="filterChanged"
          />
        </div>
      </div>
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
    >
      <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <div>
          <InputLabel for="groupBy">
            {{ __('Included Categories') }}
          </InputLabel>
          <div class="mt-1 relative rounded-md">
            <CheckboxCombobox
              v-model="form.include"
              name="category"
              :options="$page.props.options.schedulableOptions"
            />
          </div>
        </div>
        <div>
          <InputLabel for="groupBy">
            {{ __('Included Non-Pending Patients') }}
          </InputLabel>
          <div class="mt-2 relative rounded-md">
            <Toggle
              v-model="form.include_non_pending"
              dusk="include_non_pending"
            />
          </div>
        </div>
        <div>
          <InputLabel for="groupBy">
            {{ __('Include Patients Not in Our Possession') }}
          </InputLabel>
          <div class="mt-2 relative rounded-md">
            <Toggle
              v-model="form.include_non_possession"
              dusk="include_non_possession"
            />
          </div>
        </div>
      </div>
      <PrimaryButton
        class="mt-4"
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
        @click="filter"
      >
        <EyeIcon class="h-6 w-6 mr-2" />
        {{ __('Filter Tasks') }}
      </PrimaryButton>
    </TransitionRoot>
    <button
      type="button"
      class="flex justify-center items-center w-7 h-7 bg-white border border-gray-300 rounded absolute -bottom-2 right-2 hover:bg-blue-100 focus:z-10 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
      dusk="filters-button"
      @click="showOtherFilters = !showOtherFilters"
    >
      <ChevronUpIcon
        v-if="showOtherFilters"
        class="h-6 w-6 text-blue-500"
      />
      <ChevronDownIcon
        v-else
        class="h-6 w-6 text-blue-500"
      />
    </button>
  </div>
</template>
