<script setup>
import {ref, computed, onMounted} from 'vue';
import { TransitionRoot } from '@headlessui/vue';
import { ChevronDownIcon, ChevronUpIcon, EyeIcon } from '@heroicons/vue/24/outline';
import GenericSelect from './Filters/GenericSelect.vue';
import DateRange from './Filters/DateRange.vue';
import DateFilter from './Filters/DateFilter.vue';
import IncludedTaxonomies from './Filters/IncludedTaxonomies.vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
    filters: {
        type: Array,
        required: true
    }
});

const emit = defineEmits(['apply']);

const filterInputs = {
  'GenericSelect': GenericSelect,
  'DateRange': DateRange,
  'DateFilter': DateFilter,
  'IncludedTaxonomies': IncludedTaxonomies
};

const appliedFilters = ref([]);
const showNotReportPeriod = ref(false);

const reportPeriod = computed(() => props.filters.filter(filter => filter.periodic));
const notReportPeriod = computed(() => props.filters.filter(filter => ! filter.periodic));

onMounted(() => {
    showNotReportPeriod.value = reportPeriod.value.length === 0;

    props.filters.forEach(filter => {
        appliedFilters.value.push({ class: filter.class, value: filter.currentValue });
    });

    applyFilters();
})

const applyFilters = () => emit('apply', appliedFilters.value);

const filterChanged = (filter) => {
    let prevFilterIndex = appliedFilters.value.findIndex(prevFilter => {
        return prevFilter.class === filter.class;
    });

    if (prevFilterIndex !== -1) {
        appliedFilters.value.splice(prevFilterIndex, 1);
    }

    appliedFilters.value.push(filter);

    if (! showNotReportPeriod.value) {
        applyFilters();
    }
}
</script>

<template>
  <div class="bg-white shadow rounded-b-lg px-4 py-5 sm:p-3 relative space-y-4">
    <div
      v-if="reportPeriod.length > 0"
      class="grid grid-cols-2 md:grid-cols-4 gap-4"
    >
      <template
        v-for="filter in reportPeriod"
        :key="filter.name"
      >
        <component
          :is="filterInputs[filter.component]"
          :filter="filter"
          @filter-changed="filterChanged"
        />
      </template>
    </div>

    <TransitionRoot
      as="div"
      :show="showNotReportPeriod"
      enter="transform transition ease-in-out duration-500 sm:duration-100"
      enterFrom="opacity-0 -top-16"
      enterTo="opacity-100 top-0"
      leave="transform transition ease-in-out duration-100 sm:duration-100"
      leaveFrom="opacity-100"
      leaveTo="opacity-0"
    >
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <template
          v-for="filter in notReportPeriod"
          :key="filter.name"
        >
          <component
            :is="filterInputs[filter.component]"
            :filter="filter"
            @filter-changed="filterChanged"
          />
        </template>
      </div>
      <button
        type="button"
        class="relative inline-flex items-center px-4 py-2 rounded border border-green-300 bg-green-500 text-base font-medium text-white hover:bg-green-60 focus:z-10 focus:outline-none focus:ring-1 focus:ring-green-600 focus:border-green-600 mt-4"
        @click="applyFilters"
      >
        <EyeIcon class="h-6 w-6 mr-2" />
        {{ __('Run Report') }}
      </button>
    </TransitionRoot>

    <button
      v-if="reportPeriod.length > 0 && notReportPeriod.length > 0"
      type="button"
      class="flex justify-center items-center w-7 h-7 bg-white border border-gray-300 rounded absolute -bottom-2 right-2 hover:bg-blue-100 focus:z-10 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
      @click="showNotReportPeriod = !showNotReportPeriod"
    >
      <component
        :is="showNotReportPeriod ? ChevronUpIcon : ChevronDownIcon"
        class="h-6 w-6 text-blue-500"
      />
    </button>
  </div>
</template>
