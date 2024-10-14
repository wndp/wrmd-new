<script setup>
import {useForm} from '@inertiajs/vue3';
import FormSection from '@/Components/FormElements/FormSection.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import RadioGroup from '@/Components/FormElements/RadioGroup.vue';
import Checkbox from '@/Components/FormElements/Checkbox.vue';
import Toggle from '@/Components/FormElements/Toggle.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import {__} from '@/Composables/Translate';
import {SettingKey} from '@/Enums/SettingKey';

const props = defineProps({
  generalSettings: {
    type: Object,
    required: true
  }
});

const form = useForm({
  logOrder: props.generalSettings[SettingKey.LOG_ORDER],
  logAllowAuthorEdit: props.generalSettings[SettingKey.LOG_ALLOW_AUTHOR_EDIT],
  logAllowEdit: props.generalSettings[SettingKey.LOG_ALLOW_EDIT],
  logAllowDelete: props.generalSettings[SettingKey.LOG_ALLOW_DELETE],
  logShares: props.generalSettings[SettingKey.LOG_SHARES],
});

const orderOptions = [
  {
    value: 'desc',
    name: __('Newest -> Oldest')
  },
  {
    value: 'asc',
    name: __('Oldest -> Newest')
  },
];

const updateTreatmentLog = () => {
  form.put(route('general-settings.update.treatment-log'), {
    preserveScroll: true
  });
};
</script>

<template>
  <FormSection>
    <template #title>
      {{ __('Treatment Log Settings') }}
    </template>
    <template #description>
      {{ __('Update these settings to adjust how the Treatment Log will perform for your account.') }}
    </template>
    <div class="col-span-4 sm:col-span-2">
      <InputLabel for="first-name">{{ __('Show Treatment Log in this Order') }}</InputLabel>
      <div class="mt-1">
        <RadioGroup
          v-model="form.logOrder"
          :options="orderOptions"
          name="logOrder"
        />
      </div>
    </div>
    <div class="col-span-4 sm:col-span-2">
      <InputLabel for="first-name">{{ __('Allow Authors to Edit Entries?') }}</InputLabel>
      <div class="mt-2">
        <Toggle
          v-model="form.logAllowAuthorEdit"
          dusk="logAllowAuthorEdit"
        />
      </div>
    </div>
    <div class="col-span-4 sm:col-span-2">
      <InputLabel>{{ __('Allow These Users to Edit Entries.') }}</InputLabel>
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
            <InputLabel
              :for="'logAllowEdit-'+role.value"
              class="ml-2 font-normal"
            >{{ role.label }}</InputLabel>
          </div>
        </div>
      </div>
    </div>
    <div class="col-span-4 sm:col-span-2">
      <InputLabel>{{ __('Allow These Users to Delete Entries.') }}</InputLabel>
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
            <InputLabel
              :for="'logAllowDelete-'+role.value"
              class="ml-2 font-normal"
            >{{ role.label }}</InputLabel>
          </div>
        </div>
      </div>
    </div>
    <div class="col-span-4 sm:col-span-2">
      <InputLabel for="first-name">{{ __('Create a Treatment Log Entry When a Patient is Shared?') }}</InputLabel>
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
