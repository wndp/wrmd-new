<script setup>
import { inject } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Alert from '@/Components/Alert.vue';
import FormSection from '@/Components/FormElements/FormSection.vue';
import Toggle from '@/Components/FormElements/Toggle.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import SettingsAside from './Partials/SettingsAside.vue';
import {__} from '@/Composables/Translate';
import {SettingKey} from '@/Enums/SettingKey';

const route = inject('route');

const props = defineProps({
  sharingSettings: Boolean
});

const form = useForm({
  wildAlertSharing: props.sharingSettings[SettingKey.WILD_ALERT_SHARING],
  exportSharing: props.sharingSettings[SettingKey.EXPORT_SHARING],
});

const update = () => {
  form.put(route('data-sharing.update'), {
    preserveScroll: true
  });
};
</script>

<template>
  <AppLayout title="Data Sharing">
    <div class="lg:grid grid-cols-8 gap-8 mt-4">
      <SettingsAside class="mb-4 lg:mb-0 col-span-2" />
      <div class="col-span-6">
        <FormSection>
          <template #title>
            {{ __('Data Sharing') }}
          </template>
          <template #description>
            <p>{{ __('The wildlife data your organization collects is an enormous asset to you, universities, research institutions and your government agencies. You can partner with wildlife rehabilitators around the world to help advance wildlife sciences by sharing that data.') }}</p>
          </template>
          <Alert
            color="yellow"
            class="col-span-4"
          >
            <p>{{ __('We will never share your people data (Rescuers, Donors, Volunteers, Members, ...) with any third-party entity.') }}</p>
          </Alert>
          <div class="col-span-4">
            <h4 class="text-lg leading-6 font-medium text-gray-900">
              {{ __('WildAlert') }}
            </h4>
            <p class="mt-1 text-sm text-gray-500">
              {{ __('WildAlert is a system used for early detection of wildlife morbidity and mortality events. It provides timely and accurate information on where wildlife disease events are occurring or have occurred to facilitate decision making and improve our understanding of health threats.') }} {{ __('WildAlert uses Wildlife Rehabilitation MD data to predict when the number of patients exceeds an establish threshold for a given diseases.') }} <strong class="text-bold">{{ __('As a benefit to contributing to WildAlert, you will also receive notifications in WRMD on your patients that may be part of an ongoing wildlife health event.') }}</strong>
            </p>
            <div class="mt-2">
              <Toggle
                v-model="form.wildAlertSharing"
                dusk="wildAlertSharing"
              >
                {{ __('Yes! We want to participate in WildAlert.') }}
              </Toggle>
            </div>
          </div>
          <div class="col-span-4">
            <h4 class="text-lg leading-6 font-medium text-gray-900">
              {{ __('General Data Requests') }}
            </h4>
            <p class="mt-1 text-sm text-gray-500">
              {{ __('We often receive data requests from researchers, agencies and reporters on a variety of topics.') }} {{ __('Often the requests are for aggregate data or counts of a specific group of patients. For example: "How many pelicans were cared for in 2023?"') }} {{ __('If we receive a request that may include data your organization collected, can we include that data in our response?') }} {{ __('When data from your organization is shared we will make efforts to notify you for your records.') }} <strong class="text-bold">{{ __('We will not fulfill requests for data that only your organization collected, without your written consent.') }}</strong>
            </p>
            <div class="mt-2">
              <Toggle
                v-model="form.exportSharing"
                dusk="exportSharing"
              >
                {{ __('Yes! Please include our data') }}
              </Toggle>
            </div>
          </div>
          <template #actions>
            <ActionMessage
              :on="form.recentlySuccessful"
              class="mr-3"
            >
              {{ __('Saved') }}
            </ActionMessage>
            <PrimaryButton
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
              @click="update"
            >
              {{ __('Update Data Sharing Settings') }}
            </PrimaryButton>
          </template>
        </FormSection>
      </div>
    </div>
  </AppLayout>
</template>
