<script setup>
import Panel from '@/Components/Panel.vue';
import Alert from '@/Components/Alert.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
import TextareaAutosize from '@/Components/FormElements/TextareaAutosize.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import Toggle from '@/Components/FormElements/Toggle.vue';
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
</script>

<template>
  <Panel>
    <template #title>
      {{ __('Resolution') }}
    </template>
    <template #content>
      <FormRow
        id="resolved_at"
        :label="__('Date Resolved')"
        class="col-span-6 md:col-span-2"
      >
        <DatePicker
          id="resolved_at"
          v-model="form.resolved_at"
          time
          :required="enforceRequired"
        />
        <InputError :message="form.errors?.resolved_at" />
      </FormRow>
      <FormRow
        id="given_information"
        class="col-span-6 md:col-span-3"
      >
        <Toggle
          v-model="form.given_information"
          dusk="given_information"
          class="md:mt-7"
          :label="__('Given Information')"
        />
        <InputError :message="form.errors?.given_information" />
      </FormRow>
      <FormRow
        id="resolution"
        :label="__('Description')"
        class="col-span-6"
      >
        <TextareaAutosize
          v-model="form.resolution"
          name="resolution"
        />
        <InputError :message="form.errors?.resolution" />
      </FormRow>
    </template>
    <div class="col-span-6 mt-4">
      <Alert>
        {{ __('Saving a resolution date will automatically change the incident status to Resolved.') }}
      </Alert>
    </div>
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
          {{ __('Update Resolution') }}
        </PrimaryButton>
      </div>
    </template>
  </Panel>
</template>
