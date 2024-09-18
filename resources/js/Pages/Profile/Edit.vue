<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import UserProfileForm from './Partials/UserProfileForm.vue';
import UserPasswordForm from './Partials/UserPasswordForm.vue';
import TwoFactorAuthenticationForm from './Partials/TwoFactorAuthenticationForm.vue';
import LogoutOtherBrowserSessionsForm from './Partials/LogoutOtherBrowserSessionsForm.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import Alert from '@/Components/Alert.vue';
import {__} from '@/Composables/Translate';

defineProps({
    confirmsTwoFactorAuthentication: Boolean
});
</script>

<template>
  <AppLayout
    title="User Profile"
  >
    <Alert
      v-if="$page.props.settings.requireTwoFactor === true"
      color="red"
      class="mb-8"
    >
      {{ __('A super admin has required all users to enable two factor authentication. Please scroll down to enabled your two factor authentication credentials.') }}
    </Alert>
    <div>
      <div class="md:grid md:grid-cols-3 md:gap-6 mt-10 sm:mt-0">
        <div class="md:col-span-1">
          <div class="px-4 sm:px-0">
            <h3 class="text-lg font-medium leading-6 text-gray-900">
              {{ __('Profile Information') }}
            </h3>
            <p class="mt-1 text-sm text-gray-600">
              {{ __('Update your user profile information and email address.') }}
            </p>
          </div>
        </div>
        <div class="mt-5 md:col-span-2 md:mt-0">
          <UserProfileForm />
        </div>
      </div>
    </div>
    <div
      class="hidden sm:block"
      aria-hidden="true"
    >
      <div class="py-5">
        <div class="border-t border-gray-200" />
      </div>
    </div>
    <div>
      <div class="md:grid md:grid-cols-3 md:gap-6 mt-10 sm:mt-0">
        <div class="md:col-span-1">
          <div class="px-4 sm:px-0">
            <h3 class="text-lg font-medium leading-6 text-gray-900">
              {{ __('Update Password') }}
            </h3>
            <p class="mt-1 text-sm text-gray-600">
              {{ __('Ensure your account is using a long, random password to stay secure.') }}
            </p>
          </div>
        </div>
        <div class="mt-5 md:col-span-2 md:mt-0">
          <UserPasswordForm />
        </div>
      </div>
    </div>
    <div
      class="hidden sm:block"
      aria-hidden="true"
    >
      <div class="py-5">
        <div class="border-t border-gray-200" />
      </div>
    </div>
    <div>
      <div class="md:grid md:grid-cols-3 md:gap-6 mt-10 sm:mt-0">
        <div class="md:col-span-1">
          <div class="px-4 sm:px-0">
            <h3 class="text-lg font-medium leading-6 text-gray-900">
              {{ __('Two Factor Authentication') }}
            </h3>
            <p class="mt-1 text-sm text-gray-600">
              {{ __('Add additional security to your account using two factor authentication.') }}
            </p>
          </div>
        </div>
        <div class="mt-5 md:col-span-2 md:mt-0">
          <TwoFactorAuthenticationForm :requires-confirmation="confirmsTwoFactorAuthentication" />
        </div>
      </div>
    </div>
    <div
      class="hidden sm:block"
      aria-hidden="true"
    >
      <div class="py-5">
        <div class="border-t border-gray-200" />
      </div>
    </div>
    <div>
      <div class="md:grid md:grid-cols-3 md:gap-6 mt-10 sm:mt-0">
        <div class="md:col-span-1">
          <div class="px-4 sm:px-0">
            <h3 class="text-lg font-medium leading-6 text-gray-900">
              {{ __('Browser Sessions') }}
            </h3>
            <p class="mt-1 text-sm text-gray-600">
              {{ __('Manage and log out your active sessions on other browsers and devices.') }}
            </p>
          </div>
        </div>
        <div class="mt-5 md:col-span-2 md:mt-0">
          <LogoutOtherBrowserSessionsForm />
        </div>
      </div>
    </div>
    <div
      class="hidden sm:block"
      aria-hidden="true"
    >
      <div class="py-5">
        <div class="border-t border-gray-200" />
      </div>
    </div>
    <div>
      <div class="md:grid md:grid-cols-3 md:gap-6 mt-10 sm:mt-0">
        <div class="md:col-span-1">
          <div class="px-4 sm:px-0">
            <h3 class="text-lg font-medium leading-6 text-gray-900">
              {{ __('Delete User Account') }}
            </h3>
            <p class="mt-1 text-sm text-gray-600">
              {{ __('Permanently delete your user account.') }}
            </p>
          </div>
        </div>
        <div class="mt-5 md:col-span-2 md:mt-0">
          <DeleteUserForm />
        </div>
      </div>
    </div>
    <div
      class="hidden sm:block"
      aria-hidden="true"
    >
      <div class="py-5">
        <div class="border-t border-gray-200" />
      </div>
    </div>
  </AppLayout>
</template>
