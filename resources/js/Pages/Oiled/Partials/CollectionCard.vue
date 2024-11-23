<script setup>
import Panel from '@/Components/Panel.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import {__} from '@/Composables/Translate';

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

const doSubmit = () => emit('submitted');
</script>

<template>
  <Panel>
    <template #title>
      {{ __('Oiled Animal Collection and Processing') }}
    </template>
    <template #content>
      <FormRow
        id="collected_at"
        :label="__('Date Collected')"
        :required="true"
        class="col-span-6 md:col-span-3"
      >
        <DatePicker
          id="collected_at"
          v-model="form.collected_at"
          time
        />
        <InputError
          :message="form.errors.collected_at"
          class="mt-2"
        />
      </FormRow>
      <FormRow
        id="collection_condition_id"
        :label="__('Collection Condition')"
        :required="true"
        class="col-span-6 md:col-span-3"
      >
        <SelectInput
          v-model="form.collection_condition_id"
          name="collection_condition_id"
          :options="$page.props.options.oiledProcessingConditionsOptions"
        />
        <InputError
          :message="form.errors.collection_condition_id"
          class="mt-2"
        />
      </FormRow>
      <FormRow
        id="collector_name"
        :label="__('Collector(s) Full Name')"
        class="col-span-6 md:col-span-3"
      >
        <TextInput
          v-model="form.collector_name"
          name="collector_name"
        />
        <InputError
          :message="form.errors.collector_name"
          class="mt-2"
        />
      </FormRow>
      <FormRow
        id="address_found"
        :label="__('Location Name')"
        class="col-span-6 md:col-span-3"
      >
        <TextInput
          v-model="form.address_found"
          name="address_found"
        />
        <InputError
          :message="form.errors.address_found"
          class="mt-2"
        />
      </FormRow>
      <FormRow
        id="lat_found"
        :label="__('Latitude')"
        class="col-span-6 md:col-span-3"
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
          :message="form.errors.lat_found"
          class="mt-2"
        />
      </FormRow>
      <FormRow
        id="lng_found"
        :label="__('Longitude')"
        class="col-span-6 md:col-span-3"
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
          :message="form.errors.lng_found"
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
          @click="doSubmit"
        >
          {{ __('Update Collection') }}
        </PrimaryButton>
      </div>
    </template>
  </Panel>
</template>
