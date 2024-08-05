<template>
  <div
    :id="id"
    class="relative"
  >
    <span
      ref="container"
      class="rounded-md shadow-sm"
    >
      <div
        class="absolute inset-y-0 left-0 z-10 pl-3 flex items-center"
        @click="now"
      >
        <CalendarIcon
          class="h-5 w-5 text-gray-400"
          aria-hidden="true"
        />
      </div>
      <FlatPickr
        ref="datePickerWrap"
        v-model="mutableValue"
        :config="config"
        class="focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md pl-10 py-1.5"
        :name="name"
        @on-change="onChange"
      />
    </span>
  </div>
</template>

<script>
import { CalendarIcon } from '@heroicons/vue/24/outline';
import FlatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import merge from 'lodash/merge';
import { parseISO } from 'date-fns';
import { utcToZonedTime } from 'date-fns-tz';
import { usePage } from '@inertiajs/vue3';

export default {
    components: {
        FlatPickr,
        CalendarIcon
    },
    props: {
        modelValue: {
            type: [String, Object],
            default: null
        },
        configs: {
            type: Object,
            default: () => ({})
        },
        time: {
            type: Boolean,
            default: false
        },
        id: {
            type: String,
            default: null
        }
    },
    emits: ['update:modelValue', 'change', 'open'],
    data() {
        return {
            mutableValue: this.modelValue,
            flatPickrInstance: null,
            dateConfig: {
                altInput: true,
                defaultHour: this.time ? new Date().getHours() : '',
                defaultMinute: this.time ? new Date().getMinutes() : '',
                altFormat: this.time ? 'M j, Y h:i K' : 'M j, Y',
                dateFormat: this.time ? 'Y-m-d H:i' : 'Y-m-d',
                enableTime: this.time,
                minuteIncrement: 1,
                onOpen: this.onOpen,
                static: true,
                parseDate: (dateString) => {
                    let timezone = usePage().props.settings.timezone;
                    return utcToZonedTime(
                        parseISO(dateString),
                        (this.time ? timezone : null)
                    );
                }
            },
        };
    },
    computed: {
        config() {
            return merge(this.dateConfig, this.configs);
        },
        name() {
            return this.id || '';
        }
    },
    watch: {
        modelValue (newValue) {
            this.mutableValue = newValue;
        },

        mutableValue (newValue) {
            this.$emit('update:modelValue', newValue);
        }
    },
    mounted() {
        this.flatPickrInstance = this.$refs.datePickerWrap.fp;
    },
    methods: {
        now() {
            if (! this.mutableValue) {
                this.mutableValue = new Date();
            }
            this.flatPickrInstance.open();
        },
        onOpen: function (selectedDates, dateStr, instance) {
            this.$emit('open', selectedDates, dateStr, instance);
        },
        onChange(selectedDates, dateStr, instance) {
            if (dateStr === '') {
                instance.close();
            }

            this.$emit('change', selectedDates, dateStr, instance);
        }
    }
};
</script>

