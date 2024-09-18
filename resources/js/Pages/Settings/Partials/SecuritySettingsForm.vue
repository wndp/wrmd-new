<script setup>
import {useForm} from '@inertiajs/vue3';
import FormSection from '@/Components/FormElements/FormSection.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import Toggle from '@/Components/FormElements/Toggle.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
  security: Object
});

const form = useForm({
    requireTwoFactor: props.security.requireTwoFactor,
});

const updateSecuritySetings = () => {
    form.put(route('security.update'), {
        preserveScroll: true
    });
};
</script>

<template>
  <FormSection>
    <template #title>
      {{ __('Security Settings') }}
    </template>
    <template #description>
      {{ __('Update these settings to adjust your accounts security settings preferences.') }}
    </template>
    <div class="col-span-4 sm:col-span-2">
      <InputLabel for="first-name">
        {{ __('Require two-factor authentication for all users?') }}
      </InputLabel>
      <div class="mt-2">
        <Toggle
          v-model="form.requireTwoFactor"
          dusk="requireTwoFactor"
        />
      </div>
    </div>
    <template #actions>
      <ActionMessage
        :on="form.recentlySuccessful"
        class="mr-3"
      >
        {{ __('Saved.') }}
      </ActionMessage>
      <PrimaryButton
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
        @click="updateSecuritySetings"
      >
        {{ __('Update Security Settings') }}
      </PrimaryButton>
    </template>
  </FormSection>
</template>
