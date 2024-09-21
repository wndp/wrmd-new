<template>
  <Panel>
    <template #heading>
      {{ __('Banding') }}
    </template>
    <div class="space-y-4 sm:space-y-2">
      <div class="sm:grid sm:grid-cols-6 sm:gap-2 md:items-center">
        <div class="col-span-3 md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
          <Label
            for="band_number"
            class="md:text-right"
          >
            {{ __('Band Number') }}
            <RequiredInput v-if="enforceRequired" />
          </Label>
          <div class="col-span-2 mt-1 md:mt-0">
            <Input
              v-model="form.band_number"
              name="band_number"
              :required="enforceRequired"
            />
            <InputError :message="form.errors.band_number" />
          </div>
        </div>
        <div class="col-span-3 md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
          <Label
            for="banded_at"
            class="md:text-right"
          >
            {{ __(' Banding Date') }}
            <RequiredInput v-if="enforceRequired" />
          </Label>
          <div class="col-span-2 mt-1 md:mt-0">
            <DatePicker
              id="banded_at"
              v-model="form.banded_at"
              :required="enforceRequired"
            />
            <InputError :message="form.errors.banded_at" />
          </div>
        </div>
        <div class="col-span-3 space-y-2">
          <div class="md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
            <Label
              for="age_code"
              class="md:text-right"
            >
              {{ __('Age Code') }}
            </Label>
            <div class="col-span-2 mt-1 md:mt-0">
              <Select
                v-model="form.age_code"
                name="age_code"
                :options="$page.props.options.ageCodes"
              />
            </div>
          </div>
          <div class="md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
            <Label
              for="how_aged"
              class="md:text-right"
            >
              {{ __('How Aged') }}
            </Label>
            <div class="col-span-2 mt-1 md:mt-0">
              <Select
                v-model="form.how_aged"
                name="how_aged"
                :options="$page.props.options.howAgedCodes"
              />
            </div>
          </div>
          <div class="md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
            <Label
              for="sex_code"
              class="md:text-right"
            >
              {{ __('Sex Code') }}
            </Label>
            <div class="col-span-2 mt-1 md:mt-0">
              <Select
                v-model="form.sex_code"
                name="sex_code"
                :options="$page.props.options.sexCodes"
              />
            </div>
          </div>
          <div class="md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
            <Label
              for="how_sexed"
              class="md:text-right"
            >
              {{ __('How Sexed') }}
            </Label>
            <div class="col-span-2 mt-1 md:mt-0">
              <Select
                v-model="form.how_sexed"
                name="how_sexed"
                :options="$page.props.options.howSexedCodes"
              />
            </div>
          </div>
          <div class="md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
            <Label
              for="status_code"
              class="md:text-right"
            >
              {{ __('Status Code') }}
            </Label>
            <div class="col-span-2 mt-1 md:mt-0">
              <Select
                v-model="form.status_code"
                name="status_code"
                :options="$page.props.options.statusCodes"
              />
            </div>
          </div>
          <div class="md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
            <Label
              for="additional_status_code"
              class="md:text-right"
            >
              {{ __('Additional Status Code') }}
            </Label>
            <div class="col-span-2 mt-1 md:mt-0">
              <Select
                v-model="form.additional_status_code"
                name="additional_status_code"
                :options="$page.props.options.additionalInformationStatusCodes"
              />
            </div>
          </div>
        </div>
        <div class="col-span-3 space-y-2">
          <div class="md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
            <Label
              for="band_size"
              class="md:text-right"
            >
              {{ __('Band Size') }}
            </Label>
            <div class="col-span-2 mt-1 md:mt-0">
              <Select
                v-model="form.band_size"
                name="band_size"
                :options="$page.props.options.bandSizes"
              />
            </div>
          </div>
          <div class="md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
            <Label
              for="master_bander_id"
              class="md:text-right"
            >
              {{ __('Master Bander ID') }}
            </Label>
            <div class="col-span-2 mt-1 md:mt-0">
              <Input
                v-model="form.master_bander_id"
                name="master_bander_id"
              />
            </div>
          </div>
          <div class="md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
            <Label
              for="banded_by"
              class="md:text-right"
            >
              {{ __('Banded By') }}
            </Label>
            <div class="col-span-2 mt-1 md:mt-0">
              <Input
                v-model="form.banded_by"
                name="banded_by"
              />
            </div>
          </div>
          <div class="md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
            <Label
              for="location_id"
              class="md:text-right"
            >
              {{ __('Location ID') }}
            </Label>
            <div class="col-span-2 mt-1 md:mt-0">
              <Input
                v-model="form.location_id"
                name="location_id"
              />
            </div>
          </div>
          <div class="md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
            <Label
              for="band_disposition"
              class="md:text-right"
            >
              {{ __('Band Disposition') }}
            </Label>
            <div class="col-span-2 mt-1 md:mt-0">
              <Select
                v-model="form.band_disposition"
                name="band_disposition"
                :options="$page.props.options.dispositionCodes"
              />
            </div>
          </div>
        </div>
        <div class="col-span-6 md:grid md:grid-cols-6 md:gap-x-2 md:items-center">
          <Label
            for="remarks"
            class="md:text-right"
          >
            {{ __('Remarks') }}
          </Label>
          <div class="col-span-5 mt-1 md:mt-0">
            <Input
              v-model="form.remarks"
              name="remarks"
            />
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
          {{ __('Update Banding Data') }}
        </PrimaryButton>
      </div>
    </template>
  </Panel>
</template>

<script setup>
import Panel from '@/Components/Panel.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import RequiredInput from '@/Components/FormElements/RequiredInput.vue';
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
          band_number: this.banding?.band_number,
          banded_at: this.banding?.banded_at,
          age_code: this.banding?.age_code,
          how_aged: this.banding?.how_aged,
          sex_code: this.banding?.sex_code,
          how_sexed: this.banding?.how_sexed,
          status_code: this.banding?.status_code,
          additional_status_code: this.banding?.additional_status_code,
          band_size: this.banding?.band_size,
          master_bander_id: this.banding?.master_bander_id,
          banded_by: this.banding?.banded_by,
          location_id: this.banding?.location_id,
          band_disposition: this.banding?.band_disposition,
          remarks: this.banding?.remarks,
        })
      }
    },
    methods: {
      save() {
        if (this.canSubmit) {
          this.form.put(this.route('patients.research.banding.update', {
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
