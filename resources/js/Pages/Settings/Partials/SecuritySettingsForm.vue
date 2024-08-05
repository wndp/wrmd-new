<template>
  <FormSection>
    <template #title>
      {{ __('Security Settings') }}
    </template>
    <template #description>
      {{ __('Update these settings to adjust your accounts security settings preferences.') }}
    </template>
    <div class="col-span-4 sm:col-span-2">
      <Label for="first-name">{{ __('Require two-factor authentication for all users?') }}</label>
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

<script>
import FormSection from '@/Components/FormElements/FormSection.vue';
import Label from '@/Components/FormElements/Label.vue';
import RadioGroup from '@/Components/FormElements/RadioGroup.vue';
import Checkbox from '@/Components/FormElements/Checkbox.vue';
import Toggle from '@/Components/FormElements/Toggle.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';

export default {
    components: {
        FormSection,
        Label,
        RadioGroup,
        Checkbox,
        Toggle,
        PrimaryButton,
        ActionMessage
    },
    props: {
        security: Object
    },
    data() {
        return {
            form: this.$inertia.form({
                requireTwoFactor: this.security.requireTwoFactor,
            }),
        };
    },
    methods: {
        updateSecuritySetings() {
            this.form.put(this.route('security.update'), {
                preserveScroll: true
            });
        },
    },
};
</script>
