<script setup>
import {ref} from 'vue';
import {useForm, router} from '@inertiajs/vue3';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import { MagnifyingGlassIcon, ArrowDownTrayIcon, PlusCircleIcon } from '@heroicons/vue/24/solid';
import ExportPeopleModal from './ExportPeopleModal.vue';
import URI from 'urijs';
import debounce from 'lodash/debounce';
import {__} from '@/Composables/Translate';
import {can} from '@/Composables/Can';
import {Abilities} from '@/Enums/Abilities';

defineProps({
  group: {
    type: String,
    required: true
  },
  people: {
    type: Object,
    required: true
  },
});

const showExportModal = ref(false);

const form = useForm({
  search: new URI().query(true).search
});

const search = () => debounce(function() {
    if (form.search === '') {
        router.get(route(route().current()));
    } else {
        form.get(route(`people.${this.group}.index`));
    }
}, 500)();
</script>

<template>
  <div>
    <div class="mt-8 sm:flex sm:items-center sm:justify-between">
      <div>
        <label
          for="mobile-search-candidate"
          class="sr-only"
        >{{ __('Search') }}</label>
        <label
          for="desktop-search-candidate"
          class="sr-only"
        >{{ __('Search') }}</label>
        <div class="relative flex-grow focus-within:z-10">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <MagnifyingGlassIcon
              class="h-5 w-5 text-gray-400"
              aria-hidden="true"
            />
          </div>
          <input
            id="mobile-search-person"
            type="text"
            name="mobile-search-person"
            class="focus:ring-blue-500 focus:border-blue-500 block w-full rounded-md pl-10 sm:hidden border-gray-300"
            :placeholder="__('Search')"
          >
          <input
            id="desktop-search-person"
            v-model="form.search"
            type="text"
            name="desktop-search-person"
            class="hidden focus:ring-blue-500 focus:border-blue-500 w-full rounded-md pl-10 sm:block sm:text-sm border-gray-300"
            :placeholder="__('Search :group...', {group})"
            @keyup="search"
          >
        </div>
      </div>
      <div class="mt-3 sm:mt-0 sm:ml-4">
        <div class="flex space-x-2">
          <Link
            v-if="can(Abilities.CREATE_PEOPLE)"
            :href="route('people.create')"
            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring focus:ring-green-300 disabled:bg-green-400 transition"
          >
            <PlusCircleIcon
              class="mr-3 h-5 w-5 text-white"
              aria-hidden="true"
            />
            <span>{{ __('Create') }}</span>
          </Link>
          <SecondaryButton
            v-if="can(Abilities.EXPORT_PEOPLE)"
            @click="showExportModal = true"
          >
            <ArrowDownTrayIcon
              class="mr-3 h-5 w-5 text-gray-400"
              aria-hidden="true"
            />
            <span>{{ __('Export') }}</span>
          </SecondaryButton>
        </div>
        <ExportPeopleModal
          v-if="showExportModal"
          :group="group"
          :show="true"
          @close="showExportModal = false"
        />
      </div>
    </div>
  </div>
</template>
