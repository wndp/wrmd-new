<script setup>
import {ref, computed} from 'vue';
import AppSidebar from '@/Layouts/Partials/AppSidebar.vue';
import AppHeader from '@/Layouts/Partials/AppHeader.vue';
import { Head, usePage } from '@inertiajs/vue3';
import ReportRenderer from '@/Layouts/Partials/ReportRenderer.vue';
import Alert from '@/Components/Alert.vue';
import AlertAction from '@/Components/AlertAction.vue';
import ToastNotifications from '@/Components/ToastNotifications.vue';
import DonateHeader from '@/Layouts/Partials/DonateHeader.vue';
import {__} from '@/Composables/Translate';
import {can} from '@/Composables/Can';
import {Abilities} from '@/Enums/Abilities';

defineProps({
  title: String,
  noSidebar: {
      type: Boolean,
      default: false
  }
});

const sidebarOpen = ref(false);

const showDonateHeader = computed(() => usePage().props.showDonateHeader || false);
const timezone = computed(() => usePage().props.settings.timezone);
const differentTimeZone = computed(() => window.Intl.DateTimeFormat().resolvedOptions().timeZone !== timezone.value);

if (typeof window !== 'undefined' && typeof window.Beacon !== 'undefined') {
  window.Beacon('prefill', {
    name: usePage().props.auth.user.name,
    email: usePage().props.auth.user.email,
  })
}
</script>

<template>
  <Head :title="title" />
  <div class="h-screen flex overflow-hidden bg-gray-100">
    <AppSidebar
      v-if="!noSidebar"
      :sidebarOpen="sidebarOpen"
      @close="sidebarOpen = false"
    />
    <div class="flex flex-col w-0 flex-1 overflow-hidde">
      <DonateHeader v-if="showDonateHeader" />
      <AppHeader
        :sidebarOpen="sidebarOpen"
        :noSidebar="noSidebar"
        @open="sidebarOpen = true"
      />
      <Alert
        v-if="usePage().props.subscription.genericTrialEndsAt !== null"
        color="yellow"
      >
        {{ __('Your trial period will expire on :genericTrialEndsAt. Subscribe to a FREE account to continue using WRMD.', {
          genericTrialEndsAt: usePage().props.subscription.genericTrialEndsAt
        }) }}
        <AlertAction v-if="can(Abilities.COMPUTED_VIEW_BILLING)" color="yellow">
          <Link :href="route('spark.portal')">
            {{ __('Start a WRMD subscription') }}
          </Link>
        </AlertAction>
      </Alert>
      <Alert
        v-if="differentTimeZone"
        color="red"
      >
        {{ __('You appear to be in a different timezone than what is used by this organization. Dates will display in the organizations chosen timezone: :timezone.', {timezone}) }}
        <AlertAction color="red">
          <Link :href="route('account.profile.edit')">
            {{ __('If this is not correct, please update your localization settings.') }}
          </Link>
        </AlertAction>
      </Alert>
      <main
        id="main"
        scroll-region
        class="flex-1 relative overflow-y-scroll focus:outline-none pb-16 md:pb-0"
      >
        <div class="py-6">
          <header v-if="$slots.header">
            <div class="max-w-7xl mx-auto px-4 sm:px-6">
              <slot name="header" />
            </div>
          </header>
          <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <slot />
          </div>
        </div>
      </main>
    </div>
  </div>
  <ReportRenderer />
  <ToastNotifications :notification="usePage().props.notification" />
</template>
