<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { ArrowRightOnRectangleIcon } from '@heroicons/vue/24/outline';
</script>

<template>
  <PublicLayout title="No Accounts">
    <div class="flex justify-center">
      <div class="flex w-full sm:max-w-3xl mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        <img
          src="../../../images/rafiki.png"
          class="pr-8"
        >
        <div class="prose mx-auto">
          <h1>{{ __('No Active Accounts') }}</h1>
          <p>
            <strong>You are not a user of any active accounts.</strong> If you believe this is a mistake then please contact us at <a
              href="mailto:support@wildneighborsdp.org?subject=No Active WRMD Accounts"
              class="text-blue-600 hover:text-blue-700"
            >support@wildneighborsdp.org</a> and we'll help to resolve this.
          </p>
          <blockquote>
            <p>
              <em>Per the <Link
                :href="route('about.inactive-account')"
                class="text-blue-600 hover:text-blue-700"
              >Inactive Account Policy</Link>, we may terminate an account that has not been used within a 2-year period. The patients associated to those accounts are not permanently removed from the Wildlife Rehabilitation MD's database unless requested by the Account Profile contact.</em>
            </p>
          </blockquote>
          <p>
            If your account was terminated by mistake or you want your account reactivated, please contact us at <a
              href="mailto:support@wildneighborsdp.org?subject=Reactivate WRMD Account"
              class="text-blue-600 hover:text-blue-700"
            >support@wildneighborsdp.org</a>
          </p>
          <div class="mt-8">
            <Link
              :href="route('logout')"
              method="post"
              as="button"
              class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest uppercase hover:bg-green-700 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring focus:ring-green-300 disabled:bg-green-400 transition"
            >
              <ArrowRightOnRectangleIcon
                class="w-5 h-5 mr-2"
                aria-hidden="true"
              />
              {{ __('Sign Out') }}
            </Link>
          </div>
        </div>
      </div>
    </div>
  </PublicLayout>
</template>
