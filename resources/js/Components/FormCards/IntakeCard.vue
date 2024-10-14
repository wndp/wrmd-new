<script setup>
import {computed} from 'vue';
import {usePage} from '@inertiajs/vue3';
import Panel from '@/Components/Panel.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import TextareaAutosize from '@/Components/FormElements/TextareaAutosize.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import TagsInput from '@/Components/FormElements/TagsInput.vue';
import CustomFields from '@/Components/FormElements/CustomFields.vue';
import {__} from '@/Composables/Translate';
import {SettingKey} from '@/Enums/SettingKey';

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
  }
});

const emit = defineEmits(['submitted']);

const settings = computed(() => usePage().props.settings);
const showTags = computed(() => settings.value[SettingKey.SHOW_TAGS] === '1' && props.form.patient.id);

const saveIntake = () => emit('submitted');
</script>

<template>
  <Panel>
    <template #title>
      {{ __('Intake') }}
    </template>
    <template #content>
      <CustomFields
        v-if="false"
        v-model:customValues="form.custom_values"
        group="Patient"
        panel="Intake"
        location="Top"
        :enforceRequired="enforceRequired"
        class="col-span-6"
      />
      <FormRow
        id="admitted_by"
        :label="__('Admitted By')"
        :required="enforceRequired"
        class="col-span-6 md:col-span-2"
      >
        <TextInput
          v-model="form.admitted_by"
          name="admitted_by"
          :required="enforceRequired"
          autoComplete="patients.admitted_by"
        />
        <InputError
          class="mt-2"
          :message="form.errors?.admitted_by"
        />
      </FormRow>
      <FormRow
        id="transported_by"
        :label="__('Transported By')"
        class="col-span-6 md:col-span-2"
      >
        <TextInput
          v-model="form.transported_by"
          name="transported_by"
          autoComplete="patients.transported_by"
        />
      </FormRow>
      <FormRow
        id="found_at"
        :label="__('Date Found')"
        :required="enforceRequired"
        class="col-span-6 md:col-span-2"
      >
        <DatePicker
          id="found_at"
          v-model="form.found_at"
          :required="enforceRequired"
        />
        <InputError
          class="mt-2"
          :message="form.errors?.found_at"
        />
      </FormRow>
      <FormRow
        id="address_found"
        :label="__('Address Found')"
        :required="enforceRequired"
        class="col-span-6"
      >
        <TextInput
          v-model="form.address_found"
          name="address_found"
          :required="enforceRequired"
          autoComplete="patients.address_found"
        />
        <InputError
          class="mt-2"
          :message="form.errors?.address_found"
        />
      </FormRow>
      <FormRow
        id="city_found"
        :label="__('City Found')"
        :required="enforceRequired"
        class="col-span-6 md:col-span-2"
      >
        <TextInput
          v-model="form.city_found"
          name="city_found"
          class="mr-2"
          :required="enforceRequired"
          autoComplete="patients.city_found"
        />
        <InputError
          class="mt-2"
          :message="form.errors?.city_found"
        />
      </FormRow>
      <FormRow
        id="subdivision_found"
        :label="__('State Found')"
        :required="enforceRequired"
        class="col-span-6 md:col-span-2"
      >
        <SelectInput
          v-model="form.subdivision_found"
          name="subdivision_found"
          :options="$page.props.options.subdivisionOptions"
          :required="enforceRequired"
        />
        <InputError
          class="mt-2"
          :message="form.errors?.subdivision_found"
        />
      </FormRow>
      <FormRow
        id="postal_code_found"
        :label="__('Postal Code Found')"
        class="col-span-6 md:col-span-2"
      >
        <TextInput
          v-model="form.postal_code_found"
          name="postal_code_found"
        />
        <InputError
          class="mt-2"
          :message="form.errors?.postal_code_found"
        />
      </FormRow>
      <FormRow
        v-if="settings[SettingKey.SHOW_GEOLOCATION_FIELDS]"
        id="lat_found"
        :label="__('Latitude Found')"
        class="col-span-6 md:col-span-2"
      >
        <TextInput
          v-model="form.lat_found"
          name="lat_found"
          type="number"
          step="any"
          min="-90"
          max="90"
        />
        <InputError
          class="mt-2"
          :message="form.errors?.lat_found"
        />
      </FormRow>
      <FormRow
        v-if="settings[SettingKey.SHOW_GEOLOCATION_FIELDS]"
        id="lng_found"
        :label="__('Longitude Found')"
        class="col-span-6 md:col-span-2"
      >
        <TextInput
          v-model="form.lng_found"
          name="lng_found"
          type="number"
          step="any"
          min="-180"
          max="180"
        />
        <InputError
          class="mt-2"
          :message="form.errors?.lng_found"
        />
      </FormRow>
      <FormRow
        id="reason_for_admission"
        :label="__('Reasons For Admission')"
        :required="enforceRequired"
        class="col-span-6"
      >
        <TextareaAutosize
          v-model="form.reason_for_admission"
          name="reason_for_admission"
          :required="enforceRequired"
          autoComplete="patients.reason_for_admission"
        />
        <InputError
          class="mt-2"
          :message="form.errors?.reason_for_admission"
        />
      </FormRow>
      <FormRow
        v-if="showTags"
        id="circumstances_of_admission"
        :label="__('Circumstances of Admission')"
        class="col-span-6"
      >
        <TagsInput
          :options="$page.props.options.circumstancesOfAdmissionOptions"
          category="CircumstancesOfAdmission"
          :patientId="patient.id"
        />
      </FormRow>
      <FormRow
        id="care_by_rescuer"
        :label="__('Care by Rescuer')"
        class="col-span-6"
      >
        <TextareaAutosize
          v-model="form.care_by_rescuer"
          name="care_by_rescuer"
          autoComplete="patients.care_by_rescuer"
        />
      </FormRow>
      <FormRow
        id="notes_about_rescue"
        :label="__('Notes About Rescue')"
        class="col-span-6"
      >
        <TextareaAutosize
          v-model="form.notes_about_rescue"
          name="notes_about_rescue"
          autoComplete="patients.notes_about_rescue"
        />
      </FormRow>
      <CustomFields
        v-if="false"
        v-model:customValues="form.custom_values"
        group="Patient"
        panel="Intake"
        location="Bottom"
        :enforceRequired="enforceRequired"
        class="col-span-6"
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
          @click="saveIntake"
        >
          {{ __('Update Intake') }}
        </PrimaryButton>
      </div>
    </template>
  </Panel>
</template>
