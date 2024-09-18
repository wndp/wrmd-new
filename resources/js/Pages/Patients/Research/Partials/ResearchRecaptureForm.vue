<template>
  <Panel>
    <template #heading>
      {{ __('Recapture') }}
    </template>
    <div class="space-y-4 sm:space-y-2">
      <div class="sm:grid sm:grid-cols-6 sm:gap-2 md:items-center">
        <div class="col-span-3 space-y-2">
          <div class="md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
            <Label
              for="recaptured_at"
              class="md:text-right"
            >
              {{ __('Recapture Date') }}
            </Label>
            <div class="col-span-2 mt-1 md:mt-0">
              <DatePicker
                id="recaptured_at"
                v-model="form.recaptured_at"
                :required="enforceRequired"
              />
              <InputError :message="form.errors.recaptured_at" />
            </div>
          </div>
          <div class="md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
            <Label
              for="recapture_disposition"
              class="md:text-right"
            >
              {{ __('Recapture Disposition') }}
            </Label>
            <div class="col-span-2 mt-1 md:mt-0">
              <Select
                v-model="form.recapture_disposition"
                name="recapture_disposition"
                :options="$page.props.options.recaptureDispositionCodes"
              />
            </div>
          </div>
        </div>
        <div class="col-span-3 space-y-2">
          <div class="md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
            <Label
              for="present_condition"
              class="md:text-right"
            >
              {{ __('Present Condition') }}
            </Label>
            <div class="col-span-2 mt-1 md:mt-0">
              <Select
                v-model="form.present_condition"
                name="present_condition"
                :options="$page.props.options.presentConditionCodes"
              />
            </div>
          </div>
          <div class="md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
            <Label
              for="how_present_condition"
              class="md:text-right"
            >
              {{ __('How Obtained Present Condition') }}
            </Label>
            <div class="col-span-2 mt-1 md:mt-0">
              <Select
                v-model="form.how_present_condition"
                name="how_present_condition"
                :options="$page.props.options.howPresentConditionCodes"
              />
            </div>
          </div>
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
          {{ __('Update Recapture Data') }}
        </PrimaryButton>
      </div>
    </template>
  </Panel>
</template>

<script setup>
import Panel from '@/Components/Panel.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import autoSave from '@/Mixins/AutoSave';
import hoistForm from '@/Mixins/HoistForm';
</script>

<script>
  export default {
    mixins: [autoSave, hoistForm],
    props: {
      banding: {
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
        form: this.$inertia.form({
          recaptured_at: this.banding?.recaptured_at,
          recapture_disposition: this.banding?.recapture_disposition,
          present_condition: this.banding?.present_condition,
          how_present_condition: this.banding?.how_present_condition,
        })
      }
    },
    methods: {
      save() {
        if (this.canSubmit) {
          this.form.put(this.route('patients.research.recapture.update', {
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
