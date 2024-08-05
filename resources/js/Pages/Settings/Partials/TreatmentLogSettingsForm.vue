<template>
  <FormSection>
    <template #title>
      {{ __('Treatment Log Settings') }}
    </template>
    <template #description>
      {{ __('Update these settings to adjust how the Treatment Log will perform for your account.') }}
    </template>
    <div class="col-span-4 sm:col-span-2">
      <Label for="first-name">{{ __('Show Treatment Log in this Order') }}</label>
      <div class="mt-1">
        <RadioGroup
          v-model="form.logOrder"
          :options="orderOptions"
          name="logOrder"
        />
      </div>
    </div>
    <div class="col-span-4 sm:col-span-2">
      <Label for="first-name">{{ __('Allow Authors to Edit Entries?') }}</label>
      <div class="mt-2">
        <Toggle
          v-model="form.logAllowAuthorEdit"
          dusk="logAllowAuthorEdit"
        />
      </div>
    </div>
    <div class="col-span-4 sm:col-span-2">
      <Label>{{ __('Allow These Users to Edit Entries.') }}</label>
      <div class="mt-1 flex sapce-between space-x-4">
        <div
          v-for="role in $page.props.options.roles"
          :key="role.value"
          class="flex items-start"
        >
          <div class="flex items-center">
            <Checkbox
              :id="'logAllowEdit-'+role.value"
              v-model="form.logAllowEdit"
              :value="role.value"
              name="logAllowEdit"
            />
            <Label
              :for="'logAllowEdit-'+role.value"
              class="ml-2 font-normal"
            >{{ role.label }}</Label>
          </div>
        </div>
      </div>
    </div>
    <div class="col-span-4 sm:col-span-2">
      <Label>{{ __('Allow These Users to Delete Entries.') }}</label>
      <div class="mt-1 flex sapce-between space-x-4">
        <div
          v-for="role in $page.props.options.roles"
          :key="role.value"
          class="flex items-start"
        >
          <div class="flex items-center">
            <Checkbox
              :id="'logAllowDelete-'+role.value"
              v-model="form.logAllowDelete"
              :value="role.value"
              name="logAllowDelete"
            />
            <Label
              :for="'logAllowDelete-'+role.value"
              class="ml-2 font-normal"
            >{{ role.label }}</Label>
          </div>
        </div>
      </div>
    </div>
    <div class="col-span-4 sm:col-span-2">
      <Label for="first-name">{{ __('Create a Treatment Log Entry When a Patient is Shared?') }}</label>
      <div class="mt-2">
        <Toggle
          v-model="form.logShares"
          dusk="logShares"
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
        @click="updateTreatmentLog"
      >
        {{ __('Update Treatment Log Settings') }}
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
        generalSettings: Object
    },
    data() {
        return {
            orderOptions: [
                {
                    value: 'desc',
                    name: this.__('Newest -> Oldest')
                },
                {
                    value: 'asc',
                    name: this.__('Oldest -> Newest')
                },
            ],
            form: this.$inertia.form({
                logOrder: this.generalSettings.logOrder,
                logAllowAuthorEdit: this.generalSettings.logAllowAuthorEdit,
                logAllowEdit: this.generalSettings.logAllowEdit,
                logAllowDelete: this.generalSettings.logAllowDelete,
                logShares: this.generalSettings.logShares,
            }),
        };
    },
    methods: {
        updateTreatmentLog() {
            this.form.put(this.route('general-settings.update.treatment-log'), {
                preserveScroll: true
            });
        },
    },
};
</script>
