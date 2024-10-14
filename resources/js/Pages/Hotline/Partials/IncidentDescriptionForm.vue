<script setup>
import Panel from '@/Components/Panel.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import TextareaAutosize from '@/Components/FormElements/TextareaAutosize.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import {__} from '@/Composables/Translate';
import {SettingKey} from '@/Enums/SettingKey';

defineProps({
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
</script>

<template>
  <Panel>
    <template #title>
      {{ __('Description') }}
    </template>
    <template #content>
      <FormRow
        id="incident_address"
        :label="__('Address')"
        class="col-span-6"
      >
        <TextInput
          v-model="form.incident_address"
          name="incident_address"
          autoComplete="incidents.incident_address"
        />
        <InputError
          :message="form.errors?.incident_address"
          class="mt-2"
        />
      </FormRow>
      <FormRow
        id="incident_city"
        :label="__('City')"
        class="col-span-6 md:col-span-2"
      >
        <TextInput
          v-model="form.incident_city"
          name="incident_city"
          class="mr-2"
        />
      </FormRow>
      <FormRow
        id="incident_subdivision"
        :label="__('State')"
        class="col-span-6 md:col-span-2"
      >
        <SelectInput
          v-model="form.incident_subdivision"
          name="incident_subdivision"
          :options="$page.props.options.subdivisionOptions"
        />
      </FormRow>
      <FormRow
        id="incident_postal_code"
        :label="__('Postal Code')"
        class="col-span-6 md:col-span-2"
      >
        <TextInput
          v-model="form.incident_postal_code"
          name="incident_postal_code"
        />
      </FormRow>
      <template v-if="$page.props.settings[SettingKey.SHOW_GEOLOCATION_FIELDS]">
        <FormRow
          id="incident_latitude"
          :label="__('Latitude')"
          class="col-span-6 md:col-span-2"
        >
          <TextInput
            v-model="form.incident_latitude"
            name="incident_latitude"
            type="number"
            step="any"
            min="-90"
            max="90"
          />
          <InputError
            class="mt-2"
            :message="form.errors?.incident_latitude"
          />
        </FormRow>
        <FormRow
          id="incident_longitude"
          :label="__('Longitude')"
          class="col-span-6 md:col-span-2"
        >
          <TextInput
            v-model="form.incident_longitude"
            name="incident_longitude"
            type="number"
            step="any"
            min="-180"
            max="180"
          />
          <InputError
            class="mt-2"
            :message="form.errors?.incident_longitude"
          />
        </FormRow>
      </template>
      <FormRow
        id="description"
        :label="__('Reason for Call')"
        class="col-span-6"
      >
        <TextareaAutosize
          v-model="form.description"
          name="description"
        />
        <InputError
          :message="form.errors?.description"
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
          @click="emit('submitted')"
        >
          {{ __('Update Description') }}
        </PrimaryButton>
      </div>
    </template>
  </Panel>
</template>
