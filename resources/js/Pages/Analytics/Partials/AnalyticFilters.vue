<script setup>
import { ref, computed, watchEffect, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Popover, PopoverButton, PopoverPanel } from "@headlessui/vue";
import { ChevronDownIcon } from '@heroicons/vue/24/solid';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import Checkbox  from '@/Components/FormElements/Checkbox.vue';
import { Float } from '@headlessui-float/vue'
import { datePeriods, dateFromPeriod, compareDateFromPeriod, customDatesFromPeriod } from '@/Composables/DatePeriod';
import { parse, format } from 'date-fns';
import {__} from '@/Composables/Translate';

const props = defineProps({
  filters: {
    type: Object,
    required: true
  }
});

let isApplyDisabled = ref(true);
//let hasSearchedPatients = ref(false);
//let datePeriods = datePeriods;
let comparePeriods = [
  {
      value: 'previousperiod',
      label: 'Previous period',
  },
  {
      value: 'previousyear',
      label: 'Previous year'
  },
  {
      value: 'custom',
      label: 'Custom'
  }
];
let groupByPeriodOptions = [
    'Day', 'Week', 'Month', 'Quarter', 'Year'
];

let form = useForm(props.filters);

let datePeriodText = computed(() => {
  if (form.date_period === 'all-dates') {
    return 'All Dates';
  }

  let from = parse(form.date_from, 'yyyy-MM-dd', new Date());
  let to = parse(form.date_to, 'yyyy-MM-dd', new Date());

  return `${format(from, 'MMM, d yyy')} - ${format(to, 'MMM, d yyy')}`;
});

let dateComparedPeriodText = computed(() => {
    if (form.compare) {
        let from = parse(form.compare_date_from, 'yyyy-MM-dd', new Date());
        let to = parse(form.compare_date_to, 'yyyy-MM-dd', new Date());

        return `${format(from, 'MMM, d yyy')} - ${format(to, 'MMM, d yyy')}`;
    }
    return '';
});

watchEffect(() => form.date_from, () => {
  if (form.date_period === 'custom') {
    onDateSelectChange();
  }
});

watchEffect(() => form.date_to, () => {
  if (form.date_period === 'custom') {
    onDateSelectChange();
  }
});

onMounted(() => {
  onDateSelectChange();
  isApplyDisabled.value = true;
})

let onDateSelectChange = () => {
    let dates = dateFromPeriod(form.date_period);
    if (dates) {
        form.date_from = dates.from;
        form.date_to = dates.to;
    }

    //console.log(form.date_from);
    let compareDates = form.date_period === 'custom' ?
        customDatesFromPeriod(form.date_from, form.date_to, form.compare_period) :
        compareDateFromPeriod(form.date_period, form.compare_period);
    if (compareDates) {
        form.compare_date_from = compareDates.from;
        form.compare_date_to = compareDates.to;
    }

    enableApplyBtn();
};

let customDate = () => {
    form.date_period = 'custom';
    enableApplyBtn();
};

let customCompareDate = () => {
    form.compare_period = 'custom';
    enableApplyBtn();
};

let enableApplyBtn = () => {
    isApplyDisabled.value = false;
};

let applyFilters = (close) => {
    isApplyDisabled.value = true;

    close();

    form.put('/analytics/filters', {
        preserveState: false,
    });
};
</script>

<template>
  <div>
    <Popover
      as="div"
      class="relative block"
    >
      <Float
        placement="bottom-end"
        strategy="fixed"
        :offset="4"
        :flip="{crossAxis: true}"
      >
        <PopoverButton
          ref="trigger"
          class="inline-flex items-center text-right w-full py-4 md:pt-0 text-base text-black-500 border-b-2 border-gray-300 hover:border-gray-400 focus:outline-none min-w-min"
        >
          <div class="md:whitespace-nowrap">
            <div>{{ datePeriodText }}</div>
            <div v-show="form.compare">
              <span class="text-gray-400">{{ __('Compare to') }}</span> {{ dateComparedPeriodText }}
            </div>
          </div>
          <ChevronDownIcon
            class="w-5 h-5 ml-2"
            aria-hidden="true"
          />
        </PopoverButton>
        <PopoverPanel
          ref="container"
          v-slot="{ close }"
          :unmount="false"
          class="bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-20 w-full ml-4 md:w-[600px] lg:w-[800px]"
        >
          <div class="grid sm:grid-cols-2 gap-8 px-4 pt-4">
            <div>
              <label
                for="date_period"
                class="block text-sm font-bold text-gray-700"
              >{{ __('Dates') }}</label>
              <div class="mt-1 relative rounded-md">
                <SelectInput
                  id="date_period"
                  v-model="form.date_period"
                  :options="datePeriods"
                  @change="onDateSelectChange"
                />
              </div>
              <div class="md:flex space-between items-center mt-2">
                <DatePicker
                  v-model="form.date_from"
                  name="date_from"
                  @open="customDate"
                />
                <span class="hidden md:inline mx-2">&ndash;</span>
                <DatePicker
                  v-model="form.date_to"
                  name="date_to"
                  @open="customDate"
                />
              </div>
              <div
                v-show="form.date_period !== 'all-dates'"
                class="flex space-between items-center mt-2"
              >
                <div class="relative flex items-start mr-4">
                  <div class="flex items-center h-5">
                    <Checkbox
                      id="compare"
                      v-model="form.compare"
                      name="compare"
                      @change="enableApplyBtn()"
                    />
                  </div>
                  <div class="ml-3 text-sm">
                    <label
                      for="compare"
                      class="font-bold text-gray-700 whitespace-nowrap"
                    >{{ __('Compare to') }}:</label>
                  </div>
                </div>
                <SelectInput
                  id="compare_period"
                  v-model="form.compare_period"
                  :disabled="!form.compare"
                  :readonly="!form.compare"
                  :options="comparePeriods"
                  class="disabled:bg-gray-100"
                  @change="onDateSelectChange"
                />
              </div>
              <div
                v-show="form.compare"
                class="md:flex space-between items-center mt-2"
              >
                <DatePicker
                  v-model="form.compare_date_from"
                  name="compare_date_from"
                  @open="customCompareDate"
                />
                <span class="hidden md:inline mx-2">&ndash;</span>
                <DatePicker
                  v-model="form.compare_date_to"
                  name="compare_date_to"
                  @open="customCompareDate"
                />
              </div>
            </div>
            <div>
              <label
                for="group_by_period"
                class="block text-sm font-bold text-gray-700"
              >{{ __('Group By') }}</label>
              <div class="mt-1 relative rounded-md">
                <SelectInput
                  id="group_by_period"
                  v-model="form.group_by_period"
                  :options="groupByPeriodOptions"
                  @change="enableApplyBtn"
                />
              </div>
            </div>
          </div>
          <div class="px-4 pt-2 pb-4">
            <button
              type="button"
              class="relative inline-flex items-center px-4 py-2 rounded border border-green-300 bg-green-500 text-base font-medium text-white hover:bg-green-60 focus:z-10 focus:outline-none focus:ring-1 focus:ring-green-600 focus:border-green-600 disabled:bg-gray-200"
              :disabled="isApplyDisabled"
              @click="applyFilters(close)"
            >
              {{ __('Apply') }}
            </button>
          </div>
          <div class="bg-gray-200 border-t-1 border-gray-300 px-4 py-4 text-sm">
            <ul class="list-disc list-outside pl-4 space-y-2">
              <li>{{ __('All analytics exclude voided patients.') }}</li>
              <li><strong>{{ __('Survival Rate Including First 24 Hours') }}</strong> = (Pending + Released + Transferred) / (All patients - Dead on arrival).</li>
              <li><strong>{{ __('Survival Rate After First 24 Hours') }}</strong> = (Pending + Released + Transferred) / (All patients - Dead on arrival - Died in 24hr - Euthanized in 24hr).</li>
            </ul>
          </div>
        </PopoverPanel>
      </Float>
    </Popover>
  </div>
</template>
