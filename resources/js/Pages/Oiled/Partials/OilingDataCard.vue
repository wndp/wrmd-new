<script setup>
import Panel from '@/Components/Panel.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
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
      {{ __('Oiling Data') }}
    </template>
    <template #content>
      <FormRow
        id="processor"
        :label="__('Processor Full Name')"
        :required="true"
        class="col-span-6 md:col-span-3"
      >
        <TextInput
          v-model="form.processor"
          name="processor"
        />
        <InputError
          :message="form.errors.processor"
          class="mt-2"
        />
      </FormRow>
      <FormRow
        id="type_of_oil_id"
        :label="__('Oiling Type')"
        class="col-span-6 md:col-span-3"
      >
        <SelectInput
          v-model="form.type_of_oil_id"
          name="type_of_oil_id"
          :options="$page.props.options.oiledProcessingOilTypesOptions"
        />
        <InputError
          :message="form.errors.type_of_oil_id"
          class="mt-2"
        />
      </FormRow>
      <FormRow
        id="oiling_depth_id"
        :label="__('Oiling Depth')"
        class="col-span-6 md:col-span-3"
      >
        <SelectInput
          v-model="form.oiling_depth_id"
          name="oiling_depth_id"
          :options="$page.props.options.oiledProcessingOilingDepthsOptions"
        />
        <p class="col-start-2 col-span-2 mt-1 text-sm text-gray-500">{{ __('Select greatest depth observed') }}</p>
        <InputError
          :message="form.errors.oiling_depth_id"
          class="mt-2"
        />
      </FormRow>
      <FormRow
        id="oiling_status_id"
        :label="__('Oiling Status')"
        class="col-span-6 md:col-span-3"
      >
        <SelectInput
          v-model="form.oiling_status_id"
          name="oiling_status_id"
          :options="$page.props.options.oiledProcessingStatusesOptions"
        />
        <p class="col-start-2 col-span-2 mt-1 text-sm text-gray-500">{{ __('Hierarchical (choose first option that applies)') }}</p>
        <InputError
          :message="form.errors.oiling_status_id"
          class="mt-2"
        />
      </FormRow>
      <FormRow
        id="oiling_location_id"
        :label="__('Oiling Location')"
        class="col-span-6 md:col-span-3"
      >
        <SelectInput
          v-model="form.oiling_location_id"
          name="oiling_location_id"
          :options="$page.props.options.oiledProcessingOilingLocationsOptions"
        />
        <InputError
          :message="form.errors.oiling_location_id"
          class="mt-2"
        />
      </FormRow>
      <FormRow
        id="oiling_percentage_id"
        :label="__('Oiling Percentage')"
        class="col-span-6 md:col-span-3"
      >
        <SelectInput
          v-model="form.oiling_percentage_id"
          name="oiling_percentage_id"
          :options="$page.props.options.oiledProcessingOilingPercentagesOptions"
        />
        <InputError
          :message="form.errors.oiling_percentage_id"
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
          {{ __('Update Oiling Data') }}
        </PrimaryButton>
      </div>
    </template>
  </Panel>
</template>
