<script setup>
import {computed} from 'vue';
import {useForm, router} from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import HotlineTabs from './Partials/HotlineTabs.vue';
import IncidentsList from './Partials/IncidentsList.vue';
import Paginator from '@/Components/Paginator.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import { MagnifyingGlassIcon, PlusIcon } from '@heroicons/vue/24/outline';
import upperFirst from 'lodash/upperFirst';
import debounce from 'lodash/debounce';
import URI from 'urijs';
import {__} from '@/Composables/Translate';

const props = defineProps({
  group: {
    type: String,
    required: true
  },
  incidents: Object
})

const form = useForm({
    search: new URI().query(true).search
});

const groupUpper = computed(() => upperFirst(props.group)+' Incidents');

const search = () => debounce(function() {
    if (form.search === '') {
        router.get(route(route().current()));
    } else {
        form.get(route(`hotline.${props.group}.index`));
    }
}, 500)();
</script>

<template>
  <AppLayout title="Hotline">
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900">
        {{ __('Hotline') }}
      </h1>
    </template>
    <HotlineTabs class="mt-4" />
    <div class="mt-8 sm:flex sm:items-center sm:justify-between">
      <div>
        <h3 class="text-lg leading-6 font-medium text-gray-900">
          {{ groupUpper }}
        </h3>
        <h4 class="mt-1 text-sm text-gray-500">
          {{ __('Total') }}: {{ incidents.total }}
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
              id="mobile-search-hotline"
              v-model="form.search"
              type="text"
              name="mobile-search-hotline"
              class="focus:ring-blue-500 focus:border-blue-500 block w-full rounded-none rounded-l-md pl-10 sm:hidden border-gray-300"
              :placeholder="__('Search')"
              @keyup="search"
            >
            <input
              id="desktop-search-hotline"
              v-model="form.search"
              type="text"
              name="desktop-search-hotline"
              class="hidden focus:ring-blue-500 focus:border-blue-500 w-full rounded-none rounded-l-md pl-10 sm:block sm:text-sm border-gray-300"
              :placeholder="`Search incidents...`"
              @keyup="search"
            >
          </div>
          <PrimaryButton
            class="rounded-l-none"
            @click="$inertia.get(route('hotline.incident.create'))"
          >
            <PlusIcon
              class="h-5 w-5"
              aria-hidden="true"
            />
            <span class="ml-2 whitespace-nowrap">{{ __('New Incident') }}</span>
          </PrimaryButton>
        </div>
      </div>
    </div>
    <IncidentsList
      :incidents="incidents"
      :group="group"
      class="mt-2"
    />
    <Paginator
      :properties="incidents"
      class="mt-8"
    />
  </AppLayout>
</template>
