<script setup>
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue';
import { SparklesIcon, CreditCardIcon, LifebuoyIcon, Bars3Icon, XMarkIcon, NewspaperIcon } from '@heroicons/vue/24/outline';
import { HeartIcon } from '@heroicons/vue/24/solid';
import {__} from '@/Composables/Translate';
</script>

<template>
  <Disclosure
    v-slot="{ open }"
    as="nav"
    class="bg-white shadow"
  >
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <div class="flex">
          <div class="-ml-2 mr-2 flex items-center md:hidden">
            <!-- Mobile menu button -->
            <DisclosureButton class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
              <span class="sr-only">Open main menu</span>
              <Bars3Icon
                v-if="!open"
                class="block h-6 w-6"
                aria-hidden="true"
              />
              <XMarkIcon
                v-else
                class="block h-6 w-6"
                aria-hidden="true"
              />
            </DisclosureButton>
          </div>
          <div class="flex items-center">
            <Link
              :href="route('home')"
              class="flex-shrink-0 flex items-center"
            >
              <img
                class="block h-10 w-auto"
                src="../../../images/logo-48x48.png"
                :alt="$page.props.appName"
              >
              <span class="text-2xl text-gray-800 font-normal uppercase ml-3">wrmd</span>
            </Link>
          </div>
          <div class="hidden md:ml-6 md:flex md:space-x-8">
            <Link
              :href="route('about.features')"
              :class="[route().current('about.features') ? 'border-green-500 text-gray-900' : 'border-transparent text-gray-500']"
              class="hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
            >
              Features
            </Link>
            <Link
              :href="route('about.pricing')"
              :class="[route().current('about.pricing') ? 'border-green-500 text-gray-900' : 'border-transparent text-gray-500']"
              class="hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
            >
              Pricing
            </Link>
            <a
              href="https://wrmd.helpscoutdocs.com/collection/11-wrmd"
              class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
              target="_blank"
            >
              Knowledge Base
            </a>
            <Link
              :href="route('about.new')"
              :class="[route().current('about.new') ? 'border-green-500 text-gray-900' : 'border-transparent text-gray-500']"
              class="hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
            >
              What's New
            </Link>
            <Link
              :href="route('donate.index')"
              :class="[route().current('donate.index') ? 'border-red-300 text-red-500' : 'border-transparent text-red-500']"
              class="hover:border-red-300 hover:text-red-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
            >
              Make a Donation
            </Link>
          </div>
        </div>
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <Link
              v-if="$page.props.auth.user"
              :href="route('dashboard')"
              class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring focus:ring-green-300 disabled:bg-green-400 transition"
            >
              {{ __('Dashboard') }}
            </Link>
            <template v-else>
              <Link
                :href="route('login')"
                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring focus:ring-green-300 disabled:bg-green-400 transition"
              >
                {{ __('Sign in') }}
              </Link>
            </template>
          </div>
        </div>
      </div>
    </div>
    <DisclosurePanel class="md:hidden">
      <div class="pt-2 pb-3 space-y-1">
        <Link
          :href="route('about.features')"
          :class="[route().current('about.features') ? 'bg-blue-50 border-blue-500 text-blue-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700']"
          class="pl-3 pr-4 py-2 border-l-4 flex items-center text-base font-medium sm:pl-5 sm:pr-6"
        >
          <SparklesIcon class="w-6 h-6 mr-3" />
          Features
        </Link>
        <Link
          :href="route('about.pricing')"
          :class="[route().current('about.pricing') ? 'bg-blue-50 border-blue-500 text-blue-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700']"
          class="pl-3 pr-4 py-2 border-l-4 flex items-center text-base font-medium sm:pl-5 sm:pr-6"
        >
          <CreditCardIcon class="w-6 h-6 mr-3" />
          Pricing
        </Link>
        <a
          href="https://wrmd.helpscoutdocs.com/collection/11-wrmd"
          class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 pl-3 pr-4 py-2 border-l-4 flex items-center text-base font-medium sm:pl-5 sm:pr-6"
          target="_blank"
        >
          <LifebuoyIcon class="w-6 h-6 mr-3" />
          Knowledge Base
        </a>
        <Link
          :href="route('about.new')"
          :class="[route().current('about.new') ? 'bg-blue-50 border-blue-500 text-blue-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700']"
          class="pl-3 pr-4 py-2 border-l-4 flex items-center text-base font-medium sm:pl-5 sm:pr-6"
        >
          <NewspaperIcon class="w-6 h-6 mr-3" />
          What's New
        </Link>
        <Link
          :href="route('donate.index')"
          :class="[route().current('donate.index') ? 'bg-red-50 border-red-500 text-red-700' : 'border-transparent text-red-500 hover:bg-red-50 hover:border-red-300 hover:text-red-700']"
          class="flex items-center pl-3 pr-4 py-2 border-l-4 text-base font-medium sm:pl-5 sm:pr-6"
        >
          <HeartIcon class="w-6 h-6 mr-3" />
          Make a Donation
        </Link>
      </div>
    </DisclosurePanel>
  </Disclosure>
</template>
