<script setup>
import {computed} from 'vue';
import {usePage} from '@inertiajs/vue3';
import Panel from '@/Components/Panel.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextareaAutosize from '@/Components/FormElements/TextareaAutosize.vue';
import TagsInput from '@/Components/FormElements/TagsInput.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import CustomFields from '@/Components/FormElements/CustomFields.vue';
import {__} from '@/Composables/Translate';
// import autoSave from '@/Mixins/AutoSave';
// import hoistForm from '@/Mixins/HoistForm';

const props = defineProps({
  form: {
    type: Object,
    default: () => ({})
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

const emit = defineEmits(['submitted']);

const showTags = computed(() => usePage().props.settings.showTags);

const doSubmit = () => emit('submitted');

//export default {
  //mixins: [autoSave, hoistForm],
  // props: {
  //   patient: {
  //     type: Object,
  //     default: () => ({})
  //   },
  //   enforceRequired: {
  //     type: Boolean,
  //     default: true
  //   }
  // },
  // data() {
  //   return {
  //     form: this.$inertia.form({
  //       custom_values: this.patient.custom_values || {},
  //       diagnosis: this.patient.diagnosis,
  //     })
  //   };
  // },
//   computed: {
//     showTags() {
//       return this.$page.props.settings.showTags && this.patient.id;
//     }
//   },
//   methods: {
//     save() {
//       this.update();
//     },
//     update() {
//       if (this.canSubmit) {
        // this.form.put(this.route('patients.diagnosis.update', {
        //   patient: this.patient,
        // }), {
        //   preserveScroll: true,
        //   onError: () => this.stopAutoSave()
        // });
//       }
//     },
//   },
// };
</script>

<template>
  <Panel>
    <template #title>
      {{ __('Diagnosis') }}
    </template>
    <template #content>
      <CustomFields
        v-if="false"
        v-model:customValues="form.custom_values"
        group="Patient"
        panel="Diagnosis"
        location="Top"
        :enforceRequired="enforceRequired"
        class="col-span-6"
      />
      <FormRow class="col-span-6">
        <TextareaAutosize
          v-model="form.diagnosis"
          name="diagnosis"
          autoComplete="patients.diagnosis"
        />
      </FormRow>
      <!-- <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center">
        <InputLabel
          v-if="showTags"
          for="disposition"
          class="sm:text-right"
        >
          {{ __('Diagnosis') }}
        </InputLabel>
        <div
          :class="[showTags ? 'col-span-5' : 'col-span-6']"
          class="mt-1 sm:mt-0"
        >
          <TextareaAutosize
            v-model="form.diagnosis"
            name="diagnosis"
            autoComplete="patients.diagnosis"
          />
        </div>
      </div> -->
      <template v-if="showTags">
        <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center">
          <InputLabel
            for="clinical_classifications"
            class="sm:text-right"
          >
            {{ __('Clinical Classifications') }}
          </InputLabel>
          <div class="col-span-5 mt-1 sm:mt-0">
            <TagsInput
              :options="$page.props.options.clinicalClassifications"
              category="ClinicalClassifications"
              :patientId="patient.id"
            />
          </div>
        </div>
        <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center">
          <InputLabel
            for="categorization_of_clinical_signs"
            class="sm:text-right"
          >
            {{ __('Categorization of Clinical Signs') }}
          </InputLabel>
          <div class="col-span-5 mt-1 sm:mt-0">
            <TagsInput
              :options="$page.props.options.categorizationOfClinicalSigns"
              category="CategorizationOfClinicalSigns"
              :patientId="patient.id"
            />
          </div>
        </div>
      </template>
      <CustomFields
        v-if="false"
        v-model:customValues="form.custom_values"
        group="Patient"
        panel="Diagnosis"
        location="Bottom"
        :enforceRequired="enforceRequired"
      />
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
          @click="doSubmit"
        >
          {{ __('Update Diagnosis') }}
        </PrimaryButton>
      </div>
    </template>
  </Panel>
</template>
