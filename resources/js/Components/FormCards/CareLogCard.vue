<script setup>
import {ref} from 'vue';
import {useForm, usePage, router} from '@inertiajs/vue3';
import Panel from '@/Components/Panel.vue';
import Badge from '@/Components/Badge.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import TextareaAutosize from '@/Components/FormElements/TextareaAutosize.vue';
import InputWithUnit from '@/Components/FormElements/InputWithUnit.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import DeleteCareLogModal from './DeleteCareLogModal.vue';
import { ArrowsPointingInIcon, ArrowsPointingOutIcon, PresentationChartLineIcon, TrashIcon } from '@heroicons/vue/24/outline';
import { formatISO9075 } from 'date-fns';
import EditCareLogModal from './EditCareLogModal.vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
  patient: {
    type: Object,
    required: true
  },
  logs: {
    type: Array,
    required: true
  },
  canSubmit: {
    type: Boolean,
    default: true
  },
  enforceRequired: {
    type: Boolean,
    default: true
  },
});

const expanded = ref(false);
const showDeleteLog = ref(false);
const showEditLog = ref(false);
const activeLog = ref(null);

const form = useForm({
  date_care_at: formatISO9075(new Date()),
  weight: '',
  weight_unit_id: '',
  temperature: '',
  temperature_unit_id: '',
  comments: ''
});

const caseQueryString = ref({
  y: usePage().props.admission.case_year,
  c: usePage().props.admission.case_id,
});

const store = () => {
  if (props.canSubmit) {
    form.post(route('patients.care_log.store', {
      patient: props.patient,
    }), {
      preserveScroll: true,
      onSuccess: () => {
        form.reset()
        router.reload({ only: ['logs'] });
      }
    });
  }
};

const deleteLog = (log) => {
  activeLog.value = log;
  showDeleteLog.value = true;
};

const editLog = (log) => {
  if (log.can_edit) {
    activeLog.value = log;
    showEditLog.value = true;
  } else if (isUrl(log.edit_action)) {
    router.visit(log.edit_action);
  }
};

const isUrl = (string) => {
  try {
    new URL(string);
    return true;
  } catch (err) {
    return false;
  }
}

  // export default {
  //   mixins: [hoistForm],
  //   props: {
      // patient: {
      //   type: Object,
      //   required: true
      // },
      // logs: {
      //   type: Array,
      //   required: true
      // }
  //   },
  //   data() {
  //     return {
        // form: this.$inertia.form({
        //   date_care_at: formatISO9075(new Date()),
        //   weight: '',
        //   weight_unit_id: 'g',
        //   comments: ''
        // }),
  //       expanded: false,
  //       caseQueryString: {
  //         y: this.$page.props.admission.case_year,
  //         c: this.$page.props.admission.case_id
  //       },
        // showDeleteLog: false,
        // showEditLog: false,
        // activeLog: null
  //     };
  //   },
  //   methods: {
      // store() {
      //   if (this.canSubmit) {
      //     this.form.post(this.route('patients.care_log.store', {
      //       patient: this.patient,
      //     }), {
      //       preserveScroll: true,
      //       onSuccess: () => this.form.reset()
      //     });
      //   }
      // },
  //     deleteLog(log) {
  //       this.activeLog = log;
  //       this.showDeleteLog = true;
  //     },
  //     editLog(log) {
  //       if (log.can_edit) {
  //         this.activeLog = log;
  //         this.showEditLog = true;
  //       } else if (this.isUrl(log.edit_action)) {
  //         this.$inertia.get(log.edit_action);
  //       }
  //     },
  //     isUrl(string) {
  //       try {
  //         new URL(string);
  //         return true;
  //       } catch (err) {
  //         return false;
  //       }
  //     }
  //   },
  // };
</script>

<template>
  <div>
    <div :class="[expanded ? 'fixed z-30 w-screen h-screen top-0 left-0 mt-0 overflow-y-auto bg-gray-800 bg-opacity-60 rounded-none' : 'shadow rounded-lg']">
      <Panel
        class="shadow-none rounded-b-none"
        :class="[expanded ? 'rounded-none sticky z-20 top-0' : 'rounded-t-lg']"
      >
        <template #title>
          <div class="flex justify-between">
            <span>{{ __('Care Log') }}</span>
            <div class="flex items-center">
              <button
                type="button"
                @click.stop="expanded = !expanded"
              >
                <ArrowsPointingInIcon
                  v-if="expanded"
                  class="h-6 w-6 text-blue-600 mr-4"
                />
                <ArrowsPointingOutIcon
                  v-else
                  class="h-6 w-6 text-blue-600 mr-4"
                />
              </button>
              <Link :href="route('patients.analytics', caseQueryString)">
                <PresentationChartLineIcon class="h-6 w-6 text-blue-600" />
              </Link>
            </div>
          </div>
        </template>
        <template
          v-if="canSubmit"
          #content
        >
          <FormRow
            id="date_care_at"
            :label="__('Date')"
            class="col-span-6 md:col-span-2"
          >
            <DatePicker
              id="date_care_at"
              v-model="form.date_care_at"
            />
            <InputError
              :message="form.errors?.date_care_at"
              class="mt-2"
            />
          </FormRow>
          <FormRow
            id="weight"
            :label="__('Weight')"
            class="col-span-6 md:col-span-2"
          >
            <InputWithUnit
              v-model:text="form.weight"
              v-model:unit="form.weight_unit_id"
              name="weight"
              step="any"
              min="0"
              type="number"
              :units="$page.props.options.examWeightUnitsOptions"
            />
            <InputError
              :message="form.errors?.weight"
              class="mt-2"
            />
            <InputError
              :message="form.errors?.weight_unit_id"
              class="mt-2"
            />
          </FormRow>
          <FormRow
            id="temperature"
            :label="__('Temperature')"
            class="col-span-6 md:col-span-2"
          >
            <InputWithUnit
              v-model:text="form.temperature"
              v-model:unit="form.temperature_unit_id"
              name="temperature"
              step="any"
              min="0"
              type="number"
              :units="$page.props.options.examTemperatureUnitsOptions"
            />
            <InputError
              :message="form.errors?.temperature"
              class="mt-2"
            />
            <InputError
              :message="form.errors?.temperature_unit_id"
              class="mt-2"
            />
          </FormRow>
          <FormRow
            id="comments"
            :label="__('Comments')"
            class="col-span-6"
          >
            <TextareaAutosize
              id="comments"
              v-model="form.comments"
            />
            <InputError
              :message="form.errors?.comments"
              class="mt-2"
            />
          </FormRow>
        </template>
        <template
          v-if="canSubmit"
          #actions
        >
          <div class="flex items-center justify-end text-right">
            <ActionMessage
              :on="form.isDirty"
              class="mr-3"
            >
              <span class="text-red-600">{{ __('There are unsaved changes.') }}</span>
            </ActionMessage>
            <ActionMessage
              :on="form.recentlySuccessful"
              class="mr-3"
            >
              {{ __('Saved.') }}
            </ActionMessage>
            <PrimaryButton
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
              @click="store"
            >
              {{ __('Save New Care Log') }}
            </PrimaryButton>
          </div>
        </template>
      </Panel>
      <div>
        <div class="overflow-x-auto bg-white rounded-b-lg">
          <div
            class="px-2 align-middle inline-block min-w-full max-h-80"
            :class="{'max-h-full pb-6': expanded}"
          >
            <table class="min-w-full divide-y divide-gray-300">
              <tbody class="divide-y divide-gray-200 bg-white">
                <tr
                  v-for="log in logs"
                  :key="log.id"
                >
                  <td class="py-4 pl-4 pr-3 sm:w-auto sm:max-w-none sm:pl-2">
                    <div class="flex justify-between items-center text-sm font-medium text-gray-900 whitespace-nowrap">
                      <div class="flex items-center">
                        <button
                          v-if="log.can_delete"
                          type="button"
                          dusk="delete-care-log"
                          @click="deleteLog(log)"
                        >
                          <TrashIcon class="w-5 h-5 text-red-600 mr-2" />
                        </button>
                        <span>
                          {{ log.logged_at_for_humans }}
                        </span>
                      </div>
                      <Badge class="lg:hidden mt-1">
                        {{ log.type }}
                      </Badge>
                    </div>
                    <div class="lg:hidden mt-2 text-sm text-gray-600 whitespace-pre-line">
                      {{ log.body }}
                    </div>
                  </td>
                  <td class="hidden px-3 py-4 text-sm lg:table-cell">
                    <button
                      type="button"
                      dusk="edit-care-log"
                      @click="editLog(log)"
                    >
                      <Badge>
                        {{ log.type }}
                      </Badge>
                    </button>
                  </td>
                  <td class="hidden px-3 py-4 text-sm text-gray-600 w-full lg:table-cell whitespace-pre-line">
                    {{ log.body }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <DeleteCareLogModal
      v-if="showDeleteLog"
      :patient="patient"
      :log="activeLog"
      :show="true"
      @close="showDeleteLog = false"
    />
    <EditCareLogModal
      v-if="showEditLog"
      :patient="patient"
      :log="activeLog.model"
      :show="true"
      @close="showEditLog = false"
    />
  </div>
</template>
