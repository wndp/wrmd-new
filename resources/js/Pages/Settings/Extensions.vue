<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import SettingsAside from './Partials/SettingsAside.vue';
import ExtensionToggler from '@/Components/ExtensionToggler.vue';
import {__} from '@/Composables/Translate';

defineProps({
  standardExtensions: {
    type: Array,
    required: true
  },
  proExtensions: {
    type: Array,
    required: true
  }
});
</script>

<template>
  <AppLayout title="Extensions">
    <div class="lg:grid grid-cols-8 gap-8 mt-4">
      <SettingsAside class="mb-4 lg:mb-0 col-span-2" />
      <div class="col-span-6">
        <section class="shadow sm:rounded-md sm:overflow-hidden">
          <div class="bg-white py-6 px-4 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
              {{ __('Extensions') }}
            </h3>
            <p class="mt-1 text-sm text-gray-500">
              {{ __('Extensions are features in WRMD that you can activate which will give you more power and ability as it relates to your patient records. While most features relate to patient records, not all of them do.') }}
            </p>
          </div>
        </section>

        <template v-if="$page.props.subscription.isProPlan">
          <h3 class="text-lg leading-6 font-medium text-gray-700 mt-8">
            Pro Extensions
          </h3>
          <p class="mt-1 text-sm text-gray-500 border-b border-gray-300 pb-2">
            {{ __('Extensions available to WRMD Pro accounts.') }}
          </p>
          <ul
            role="list"
            class="grid grid-cols-1 gap-6 md:grid-cols-2 mt-2"
          >
            <ExtensionToggler
              v-for="extension in proExtensions"
              :key="extension.namespace"
              :extension="extension"
            />
          </ul>
        </template>

        <h3 class="text-lg leading-6 font-medium text-gray-900 mt-8">
          Standard Extensions
        </h3>
        <p class="mt-1 text-sm text-gray-500 border-b border-gray-300 pb-2">
          {{ __('Extensions available to all accounts.') }}
        </p>
        <ul
          role="list"
          class="grid grid-cols-1 gap-6 md:grid-cols-2 mt-2"
        >
          <ExtensionToggler
            v-for="extension in standardExtensions"
            :key="extension.namespace"
            :extension="extension"
          />
        </ul>
      </div>
    </div>
  </AppLayout>
</template>
