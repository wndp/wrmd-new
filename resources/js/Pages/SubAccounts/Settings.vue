<script setup>
import {useForm} from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ArrowLongLeftIcon } from '@heroicons/vue/24/outline';
import SubAccountTabs from './Partials/SubAccountTabs.vue';
import Panel from '@/Components/Panel.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
import Toggle from '@/Components/FormElements/Toggle.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
  subAccount: {
    type: Object,
    required: true
  },
  subAccountSettings: {
    type: Object,
    required: true
  },
  settings: {
    type: Object,
    required: true
  },
});

const form = useForm({
  sub_account_allow_manage_settings: props.subAccountSettings.subAccountAllowManageSettings,
  sub_account_allow_transfer_patients: props.subAccountSettings.subAccountAllowTransferPatients
});

const save = () => {
  form.put(route('sub_accounts.settings.update', {
    subAccount: props.subAccount
  }), {
    preserveScroll: true,
  });
};
</script>

<template>
  <AppLayout title="Sub-Accounts">
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900">
        {{ subAccount.name }}
      </h1>
    </template>
    <Link
      :href="route('sub_accounts.index')"
      class="inline-flex items-center text-base leading-5 text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700 transition ease-in-out duration-150"
    >
      <ArrowLongLeftIcon class="h-6 w-6 mr-2" />
      {{ __('Return to Sub-Accounts List') }}
    </Link>
    <SubAccountTabs
      :subAccount="subAccount"
      class="mt-4"
    />
    <Panel class="mt-8">
      <template #title>
        {{ __('Sub-Account Settings') }}
      </template>
      <template #description>
        {{ __('Your master account can limit some functionality of this sub-account. These settings will take precedent over any settings set directly in the sub-account.') }}
      </template>
      <template #content>
        <FormRow
          id="sub_account_allow_manage_settings"
          class="col-span-6"
        >
          <Toggle
            v-model="form.sub_account_allow_manage_settings"
            :label="__('Allow this sub-account to manage their own settings.')"
            dusk="sub_account_allow_manage_settings"
          />
          <InputError
            :message="form.errors.sub_account_allow_manage_settings"
            class="mt-1"
          />
        </FormRow>
        <FormRow
          id="sub_account_allow_transfer_patients"
          class="col-span-6"
        >
          <Toggle
            v-model="form.sub_account_allow_transfer_patients"
            :label="__('Allow this sub-account to transfer their patients to other accounts.')"
            dusk="sub_account_allow_transfer_patients"
          />
          <InputError
            :message="form.errors.sub_account_allow_transfer_patients"
            class="mt-1"
          />
        </FormRow>
      </template>
      <template #actions>
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
            @click="save"
          >
            {{ __('Update Settings') }}
          </PrimaryButton>
        </div>
      </template>
    </Panel>
    <div class="flex flex-col mt-8">
      <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
          <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
              <caption class="px-4 py-5 sm:px-6 text-left text-base bg-white">
                <span class="font-bold text-gray-700">{{ __('Settings') }}</span>
                <p class="mt-1 text-sm text-gray-500">
                  {{ __("Below are the current settings applied to the sub-account. To mange this sub-account's settings please log into the account and navigate to the settings area.") }}
                </p>
              </caption>
              <thead class="bg-blue-100">
                <tr>
                  <th
                    scope="col"
                    class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                  >
                    Name
                  </th>
                  <th
                    scope="col"
                    class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                  >
                    Value
                  </th>
                  <th
                    scope="col"
                    class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                  >
                    Last Updated
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr
                  v-for="(setting, i) in settings"
                  :key="setting.id"
                  :class="i % 2 === 0 ? 'bg-white' : 'bg-gray-50'"
                >
                  <td class="px-4 py-4 whitespace-nowrap">
                    {{ setting.label }}
                  </td>
                  <td class="px-4 py-4 text-sm text-gray-900">
                    {{ setting.value }}
                  </td>
                  <td class="px-4 py-4 text-sm text-gray-900">
                    {{ setting.updated_at_for_humans }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
