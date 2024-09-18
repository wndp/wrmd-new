<template>
  <Panel>
    <template #heading>
      {{ __('Summary') }}
    </template>
    <div class="space-y-4 md:space-y-2">
      <div class="lg:grid lg:grid-cols-6 lg:gap-x-2 lg:items-center">
        <Label
          for="samples_collected"
          class="lg:text-right"
        >{{ __('Samples Collected') }}</Label>
        <div class="col-span-5 mt-1 lg:mt-0 flex gap-4 flex-wrap">
          <div
            v-for="sample in $page.props.options.samples"
            :key="sample.value"
            class="relative flex items-start"
          >
            <div class="flex items-center h-5">
              <Checkbox
                :id="`samples_collected_${sample.value}`"
                v-model="form.samples_collected"
                :name="`samples_collected_${sample.value}`"
                :value="sample.value"
              />
            </div>
            <div class="ml-1 text-sm">
              <label
                :for="`samples_collected_${sample.value}`"
                class="font-medium text-gray-700 whitespace-nowrap"
              >{{ sample.label }}</label>
            </div>
          </div>
          <div class="relative flex items-start">
            <div class="flex items-center h-5">
              <Checkbox
                id="samples_collected_Other"
                v-model="form.samples_collected"
                name="samples_collected_Other"
                value="other"
                @change="showOtherSample = !showOtherSample"
              />
            </div>
            <div class="ml-1 text-sm">
              <label
                for="samples_collected_Other"
                class="font-medium text-gray-700 whitespace-nowrap"
              >{{ __('Other') }}</label>
            </div>
          </div>
        </div>
      </div>
      <div
        v-if="showOtherSample"
        class="lg:grid lg:grid-cols-6 lg:gap-x-2 lg:items-center"
      >
        <Label
          for="other_sample"
          class="lg:text-right"
        >{{ __('Other Sample') }}</Label>
        <div class="col-span-5 mt-1 xl:mt-0 flex">
          <Input
            v-model="form.other_sample"
            name="other_sample"
          />
        </div>
      </div>
      <div class="lg:grid lg:grid-cols-6 lg:gap-x-2 lg:items-center">
        <Label
          for="morphologic_diagnosis"
          class="lg:text-right"
        >{{ __('Morphologic Diagnosis') }}</Label>
        <div class="col-span-5 mt-1 xl:mt-0 flex">
          <Textarea
            v-model="form.morphologic_diagnosis"
            name="morphologic_diagnosis"
          />
        </div>
      </div>
      <div class="lg:grid lg:grid-cols-6 lg:gap-x-2 lg:items-center">
        <Label
          for="gross_summary_diagnosis "
          class="lg:text-right"
        >{{ __('Gross Summary Diagnosis') }}</Label>
        <div class="col-span-5 mt-1 xl:mt-0 flex">
          <Textarea
            v-model="form.gross_summary_diagnosis"
            name="gross_summary_diagnosis"
          />
        </div>
      </div>
    </div>
    <template
      v-if="canSubmit"
      #footing
    >
      <div class="flex items-center justify-end text-right">
        <ActionMessage
          :on="form.isDirty"
          class="mr-3"
        >
          <span class="text-red-600">{{ __('There are unsaved changes') }}</span>
        </ActionMessage>
        <ActionMessage
          :on="form.recentlySuccessful"
          class="mr-3"
        >
          {{ __('Saved') }}
        </ActionMessage>
        <PrimaryButton
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
          @click="save"
        >
          {{ __('Update Summary Details') }}
        </PrimaryButton>
      </div>
    </template>
  </Panel>
</template>

<script setup>
import Panel from '@/Components/Panel.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import Textarea from '@/Components/FormElements/Textarea.vue';
import Checkbox from '@/Components/FormElements/Checkbox.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import autoSave from '@/Mixins/AutoSave';
import hoistForm from '@/Mixins/HoistForm';
</script>

<script>
  export default {
    mixins: [autoSave, hoistForm],
    props: {
      necropsy: {
        type: Object,
        default: () => ({})
      },
      enforceRequired: {
        type: Boolean,
        default: true
      }
    },
    data() {
      return {
        showOtherSample: (this.necropsy?.samples_collected || []).includes('other'),
        form: this.$inertia.form({
          samples_collected: this.necropsy?.samples_collected || [],
          other_sample: this.necropsy?.other_sample,
          morphologic_diagnosis: this.necropsy?.morphologic_diagnosis,
          gross_summary_diagnosis: this.necropsy?.gross_summary_diagnosis,
        })
      }
    },
    methods: {
      save() {
        if (this.canSubmit) {
          this.form.put(this.route('patients.necropsy.summary.update', {
            patient: this.$page.props.admission.patient
          }), {
            preserveScroll: true,
            onError: () => this.stopAutoSave()
          });
        }
      }
    }
  }
</script>
