<script setup>
import {useForm, router} from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import MaintenanceAside from '../Partials/MaintenanceAside.vue';
import FormulasList from '../Partials/FormulasList.vue';
import Paginator from '@/Components/Paginator.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import { MagnifyingGlassIcon, PlusIcon } from '@heroicons/vue/24/outline';
import debounce from 'lodash/debounce';
import URI from 'urijs';
import {__} from '@/Composables/Translate';

defineProps({
  recipes: {
    type: Object,
    required: true
  }
});

const form = useForm({
    search: new URI().query(true).search
});

const search = () => debounce(function() {
    if (form.search === '') {
        router.get(route(route().current()));
    } else {
        form.get(route('maintenance.cookbook.index'));
    }
}, 700)();
</script>

<template>
  <AppLayout title="Cookbook">
    <div class="lg:grid grid-cols-8 gap-8 mt-4">
      <MaintenanceAside class="mb-4 lg:mb-0 col-span-2" />
      <div class="col-span-6">
        <div class="shadow sm:rounded-md sm:overflow-hidden">
          <div class="bg-white py-6 px-4 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
              {{ __('Nutrition Cookbook') }}
            </h3>
            <p class="mt-2 text-sm text-gray-500">{{ __("Use the nutrition cookbook to create blueprint nutrition plans. This will help your organization's users have a faster and more reliable way to write your most common nutrition plans.") }}</p>
          </div>
        </div>
        <div class="sm:flex sm:items-center sm:justify-between mt-8">
          <div>
            <h3 class="text-lg leading-6 font-medium text-gray-900">
              {{ __('Recipes') }}
            </h3>
            <h4 class="mt-1 text-sm text-gray-500">
              {{ __('Total') }}: {{ recipes.total }}
            </h4>
          </div>
          <div class="mt-3 sm:mt-0 sm:ml-4">
            <label
              for="mobile-search-candidate"
              class="sr-only"
            >{{ __('Search') }}</label>
            <label
              for="desktop-search-candidate"
              class="sr-only"
            >{{ __('Search') }}</label>
            <div class="flex rounded-md shadow-sm">
              <div class="relative flex-grow focus-within:z-10">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <MagnifyingGlassIcon
                    class="h-5 w-5 text-gray-400"
                    aria-hidden="true"
                  />
                </div>
                <input
                  id="mobile-search-candidate"
                  type="text"
                  name="mobile-search-candidate"
                  class="focus:ring-blue-500 focus:border-blue-500 block w-full rounded-none rounded-l-md pl-10 sm:hidden border-gray-300"
                  :placeholder="__('Search')"
                >
                <input
                  id="desktop-search-candidate"
                  v-model="form.search"
                  type="text"
                  name="desktop-search-candidate"
                  class="hidden focus:ring-blue-500 focus:border-blue-500 w-full rounded-none rounded-l-md pl-10 sm:block sm:text-sm border-gray-300"
                  :placeholder="`${__('Search recipes')}...`"
                  @keyup="search"
                >
              </div>
              <PrimaryButton
                class="rounded-l-none"
                @click="$inertia.get(route('maintenance.cookbook.create'))"
              >
                <PlusIcon
                  class="h-5 w-5"
                  aria-hidden="true"
                />
                <span class="ml-2 whitespace-nowrap">{{ __('New Recipe') }}</span>
              </PrimaryButton>
            </div>
          </div>
        </div>
        <FormulasList
          :formulas="recipes"
          editRoute="maintenance.cookbook.edit"
          class="mt-2"
        />
        <Paginator
          :properties="recipes"
          class="mt-8"
        />
      </div>
    </div>
  </AppLayout>
</template>
