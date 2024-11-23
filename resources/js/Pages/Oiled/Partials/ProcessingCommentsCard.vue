<script setup>
import Panel from '@/Components/Panel.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
import TextareaInput from '@/Components/FormElements/TextareaInput.vue';
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
      {{ __('Processing Comments') }}
    </template>
    <template #description>
      {{ __('Any other observations, e.g. wing/toe clipping, location of scavenging, which body parts are present for non-whole carcasses, breeding condition, contamination by petroleum products such as plastic or another specimen, additional morphometrics.') }}
    </template>
    <template #content>
      <FormRow
        id="comments"
        class="col-span-6"
      >
        <TextareaInput
          v-model="form.comments"
          name="comments"
        />
        <InputError
          :message="form.errors.comments"
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
          {{ __('Update Processing Comments') }}
        </PrimaryButton>
      </div>
    </template>
  </Panel>
</template>
