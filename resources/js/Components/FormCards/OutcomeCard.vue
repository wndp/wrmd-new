<script setup>
import {ref, computed} from 'vue';
import {usePage} from '@inertiajs/vue3';
import Panel from '@/Components/Panel.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
import Alert from '@/Components/Alert.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import Toggle from '@/Components/FormElements/Toggle.vue';
import TextareaAutosize from '@/Components/FormElements/TextareaAutosize.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import CustomFields from '@/Components/FormElements/CustomFields.vue';
// import autoSave from '@/Mixins/AutoSave';
// import hoistForm from '@/Mixins/HoistForm';
import {__} from '@/Composables/Translate';
import {AttributeOptionUiBehavior} from '@/Enums/AttributeOptionUiBehavior';

const props = defineProps({
  teamIsInPossession: {
    type: Boolean,
    required: true
  },
  form: {
    type: Object,
    default: () => ({})
  },
  attributeOptionUiBehaviors: {
    type: Object,
    required: true
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

const outcomeBody = ref(null);

//const teamIsInPossession = computed(() => usePage().props.teamPossessionId === usePage().props.auth.user.current_team.id);

const dispositionIsPending = computed(() => {
    return props.attributeOptionUiBehaviors[AttributeOptionUiBehavior.PATIENT_DISPOSITION_IS_PENDING].includes(
        Number(props.form.disposition_id)
    );
});

const dispositionIsReleased = computed(() => {
    return props.attributeOptionUiBehaviors[AttributeOptionUiBehavior.PATIENT_DISPOSITION_IS_RELEASED].includes(
        Number(props.form.disposition_id)
    );
});

const dispositionIsTransferred = computed(() => {
    return props.attributeOptionUiBehaviors[AttributeOptionUiBehavior.PATIENT_DISPOSITION_IS_TRANSFERRED].includes(
        Number(props.form.disposition_id)
    );
});

const dispositionIsDeadOnArrival = computed(() => {
    return props.attributeOptionUiBehaviors[AttributeOptionUiBehavior.PATIENT_DISPOSITION_IS_DOA].includes(
        Number(props.form.disposition_id)
    );
});

const scrollDown = () => {
  if (! dispositionIsPending.value) {
    console.log(outcomeBody.value)
    //outcomeBody.value.scrollIntoView({alignToTop: true, behavior: 'smooth'});
  }
};

const doSubmit = () => emit('submitted');
</script>

<template>
  <Panel ref="outcomeBody">
    <template #title>
      {{ __('Outcome') }}
    </template>
    <template #content>
      <FormRow
        id="disposition_id"
        :label="__('Disposition')"
        class="col-span-6 md:col-span-2"
      >
        <template v-if="teamIsInPossession">
          <SelectInput
            v-model="form.disposition_id"
            name="disposition_id"
            :options="$page.props.options.patientDispositionsOptions"
            @change="scrollDown"
          />
          <InputError
            :message="form.errors?.disposition_id"
            class="mt-2"
          />
        </template>
        <div v-else>
          {{ __('Transferred') }}
        </div>
      </FormRow>
      <Alert
        v-if="! teamIsInPossession"
        class="col-span-6"
      >
        {{ __('This patient is in the possession of :organization and they will record the final outcome details.', {
          organization: patient.possession.organization
        }) }}
      </Alert>
      <template v-if="! dispositionIsPending">
        <CustomFields
          v-if="false"
          v-model:customValues="form.custom_values"
          group="Patient"
          panel="Outcome"
          location="Top"
          :enforceRequired="enforceRequired"
          class="col-span-6"
        />
        <FormRow
          id="dispositioned_at"
          :label="__('Date')"
          class="col-span-6 md:col-span-2"
        >
          <DatePicker
            id="dispositioned_at"
            v-model="form.dispositioned_at"
          />
          <InputError
            :message="form.errors?.dispositioned_at"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          v-if="dispositionIsReleased"
          id="release_type_id"
          :label="__('Release Type')"
          class="col-span-6 md:col-span-2"
        >
          <SelectInput
            v-model="form.release_type_id"
            name="release_type_id"
            :options="$page.props.options.patientReleaseTypesOptions"
          />
          <InputError
            :message="form.errors?.release_type_id"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          v-if="dispositionIsTransferred"
          id="transfer_type_id"
          :label="__('Transfer Type')"
          class="col-span-6 md:col-span-2"
        >
          <SelectInput
            v-model="form.transfer_type_id"
            name="transfer_type_id"
            :options="$page.props.options.patientTransferTypesOptions"
          />
          <InputError
            :message="form.errors?.transfer_type_id"
            class="mt-2"
          />
        </FormRow>
        <template v-if="dispositionIsReleased || dispositionIsTransferred">
          <FormRow
            id="disposition_address"
            :label="__('Address')"
            class="col-span-6"
          >
            <TextInput
              v-model="form.disposition_address"
              name="disposition_address"
              autoComplete="patients.disposition_location"
            />
            <InputError
              :message="form.errors?.disposition_address"
              class="mt-2"
            />
          </FormRow>
          <FormRow
            id="disposition_city"
            :label="__('City')"
            class="col-span-6 md:col-span-2"
          >
            <TextInput
              v-model="form.disposition_city"
              name="disposition_city"
              class="mr-2"
            />
          </FormRow>
          <FormRow
            id="disposition_subdivision"
            :label="__('State')"
            class="col-span-6 md:col-span-2"
          >
            <SelectInput
              v-model="form.disposition_subdivision"
              name="disposition_subdivision"
              :options="$page.props.options.subdivisionOptions"
            />
          </FormRow>
          <FormRow
            id="disposition_postal_code"
            :label="__('Postal Code')"
            class="col-span-6 md:col-span-2"
          >
            <TextInput
              v-model="form.disposition_postal_code"
              name="disposition_postal_code"
            />
          </FormRow>
          <template v-if="$page.props.settings.showGeolocationFields">
            <FormRow
              id="disposition_lat"
              :label="__('Latitude')"
              class="col-span-6 md:col-span-2"
            >
              <TextInput
                v-model="form.disposition_lat"
                name="disposition_lat"
                type="number"
                step="any"
                min="-90"
                max="90"
              />
              <InputError
                class="mt-2"
                :message="form.errors?.disposition_lat"
              />
            </FormRow>
            <FormRow
              id="disposition_lng"
              :label="__('Longitude')"
              class="col-span-6 md:col-span-2"
            >
              <TextInput
                v-model="form.disposition_lng"
                name="disposition_lng"
                type="number"
                step="any"
                min="-180"
                max="180"
              />
              <InputError
                class="mt-2"
                :message="form.errors?.disposition_lng"
              />
            </FormRow>
          </template>
        </template>
        <FormRow
          v-if="! dispositionIsDeadOnArrival"
          id="reason_for_disposition"
          :label="__('Reason for Disposition')"
          class="col-span-6"
        >
          <TextareaAutosize
            v-model="form.reason_for_disposition"
            name="reason_for_disposition"
            autoComplete="patients.reason_for_disposition"
          />
        </FormRow>
        <FormRow
          id="dispositioned_by"
          :label="__('Dispositioned By')"
          class="col-span-6 md:col-span-3 md:col-start-1"
        >
          <TextInput
            v-model="form.dispositioned_by"
            name="dispositioned_by"
            autoComplete="patients.dispositioned_by"
          />
        </FormRow>
        <FormRow
          v-if="!dispositionIsReleased && !dispositionIsTransferred"
          id="is_carcass_saved"
          class="col-span-6 md:col-span-2"
        >
          <div class="flex items-center mt-2 md:mt-7">
            <Toggle
              v-model="form.is_carcass_saved"
              dusk="is_carcass_saved"
              class="mr-1"
            >
              {{ __('Saved Carcass?') }}
            </Toggle>
          </div>
        </FormRow>
      </template>
    </template>
    <template
      v-if="canSubmit && teamIsInPossession"
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
          {{ __('Update Outcome') }}
        </PrimaryButton>
      </div>
    </template>
  </Panel>
</template>
