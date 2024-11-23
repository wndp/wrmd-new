<script setup>
import Panel from '@/Components/Panel.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
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
      {{ __('Carcass Condition') }}
    </template>
    <template #description>
      {{ __('Record location of scavenging and which body parts present in Processing Comments') }}
    </template>
    <template #content>
      <FormRow
        id="carcass_condition_id"
        :label="__('Carcass Condition')"
        class="col-span-6 md:col-span-3"
      >
        <SelectInput
          v-model="form.carcass_condition_id"
          name="carcass_condition_id"
          :options="$page.props.options.oiledProcessingCarcassConditionsOptions"
        />
        <InputError
          :message="form.errors.carcass_condition_id"
          class="mt-2"
        />
      </FormRow>
      <FormRow
        id="extent_of_scavenging_id"
        :label="__('Extent of Scavenging')"
        class="col-span-6 md:col-span-3"
      >
        <SelectInput
          v-model="form.extent_of_scavenging_id"
          name="extent_of_scavenging_id"
          :options="$page.props.options.oiledProcessingExtentOfScavengingsOptions"
        />
        <InputError
          :message="form.errors.extent_of_scavenging_id"
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
          {{ __('Update Carcass Condition') }}
        </PrimaryButton>
      </div>
    </template>
  </Panel>
</template>
