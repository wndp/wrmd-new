<script setup>
import {ref, computed, nextTick} from 'vue';
import {useForm, usePage, router} from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Bars3BottomLeftIcon, Bars2Icon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline';
import Paginator from '@/Components/Paginator.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import URI from 'urijs';
import SaveGroupModal from './Partials/SaveGroupModal.vue';
import DeleteGroupModal from './Partials/DeleteGroupModal.vue';
import ExitGroupModal from './Partials/ExitGroupModal.vue';
import ThreadExcerpt from './Partials/ThreadExcerpt.vue';
import ForumAside from './Partials/ForumAside.vue';
import merge from 'lodash.merge';
import debounce from 'lodash/debounce';
import LocalStorage from '@/Composables/LocalStorage';
import {__} from '@/Composables/Translate';

const localStorage = LocalStorage();

const props = defineProps({
  threads: {
      type: Object,
      required: true
    },
    channels: {
      type: Array,
      required: true
    },
    teams: {
      type: Array,
      required: true
    },
    groups: {
      type: Array,
      required: true
    },
    currentGroup: {
      type: Object,
      default: () => ({})
    },
});

const uri = new URI();
const useCurrentGroup = ref(false);
const groupModal = ref(false);
const groupModalHeading = ref('');
const groupModalButton = ref('');
const deleteGroupModal = ref(false);
const exitGroupModal = ref(false);
const excerpt = ref(localStorage.get('forum.excerpt') || false);
const form = useForm({
  channel: uri.query(true).channel || 'all',
  search: uri.query(true).search
});


const channelFilter = computed(() =>
  [{
    value: 'all',
    label: 'All Channels'
  }, ...props.channels]
);

const isGroupAdmin = computed(() => {
  let team = usePage().props.auth.team;
  return props.currentGroup.members.find(m => m.id === team.id).pivot.role === 'admin';
});

const showExcerpt = (boolean) => {
  excerpt.value = boolean;
  localStorage.store('forum.excerpt', boolean)
};

const queryString = (obj) => {
  return merge(uri.query(true), obj);
};

// const redirectToTab = () => {
//   router.get(route('forum.index', {[this.currentNav]: 1}));
// }

const filterThreads = () => {
  debounceSearch();
};

const debounceSearch = debounce(function() {
    if (form.search === '') {
        router.get(route(route().current()));
    } else {
        form.get(route('forum.index', queryString()));
    }
}, 500);

const editGroup = () => {
  groupModalHeading.value = __('Update Group');
  groupModalButton.value = __('Update Group');
  useCurrentGroup.value = true;
  nextTick(() => {
    groupModal.value = true;
  });
};

const deleteGroup = () => {
  useCurrentGroup.value = true;
  deleteGroupModal.value = true
};

const exitGroup = () => {
  useCurrentGroup.value = true;
  exitGroupModal.value = true
}
</script>

<template>
  <AppLayout title="Forum">
    <div class="lg:grid grid-cols-8 gap-8">
      <ForumAside
        :channels="channels"
        :groups="groups"
        :teams="teams"
        class="mb-4 lg:mb-0 col-span-2"
      />
      <div class="col-span-6">
        <div class="flex justify-center md:justify-between mb-8">
          <div class="flex flex-1 mr-4">
            <SelectInput
              v-model="form.channel"
              :options="channelFilter"
              class="w-36 md:w-44"
              name="search-forum"
              @keyup="filterThreads"
            />
          </div>
          <div class="flex items-center md:pl-4 pr-4 space-x-3">
            <button
              class="px-2 py-2 sm:py-1 rounded"
              :class="{'shadow-sm bg-blue-300': !excerpt}"
              @click="showExcerpt(false)"
            >
              <Bars2Icon class="h-5 w-5" />
            </button>
            <button
              class="px-2 py-2 sm:py-1 rounded"
              :class="{'shadow-sm bg-blue-300': excerpt}"
              @click="showExcerpt(true)"
            >
              <Bars3BottomLeftIcon class="h-5 w-5" />
            </button>
          </div>
          <div class="relative rounded-md shadow-sm">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <MagnifyingGlassIcon
                class="h-5 w-5 text-gray-400"
                aria-hidden="true"
              />
            </div>
            <TextInput
              v-model="form.search"
              class="pl-10"
              :placeholder="__('Search Discussions')+'...'"
              name="search-forum"
              @keyup="filterThreads"
            />
          </div>
        </div>
        <div
          v-if="currentGroup"
          class="bg-gray-200 overflow-hidden rounded-lg mb-8"
        >
          <div class="px-4 py-5 sm:p-6">
            <div class="flex items-end">
              <h2 class="text-lg leading-6 font-medium text-gray-900">
                {{ currentGroup.name }}
              </h2>
              <button
                v-if="isGroupAdmin"
                type="button"
                class="text-sm text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700 transition ease-in-out duration-150 ml-4"
                @click="editGroup()"
              >
                {{ __('Settings') }}
              </button>
              <button
                v-if="isGroupAdmin"
                type="button"
                class="text-sm text-red-500 hover:text-red-700 focus:outline-none focus:text-red-700 transition ease-in-out duration-150 ml-4"
                @click="deleteGroup()"
              >
                {{ __('Delete Group') }}
              </button>
              <button
                v-if="!isGroupAdmin"
                type="button"
                class="text-sm text-red-600 hover:text-red-700 focus:outline-none focus:text-red-700 transition ease-in-out duration-150 ml-4"
                @click="exitGroup()"
              >
                {{ __('Exit Group') }}
              </button>
            </div>
            <p class="mt-1 text-sm text-gray-500">
              {{ currentGroup.description }}
            </p>
          </div>
        </div>
        <div class="space-y-4">
          <ThreadExcerpt
            v-for="thread in threads.data"
            :key="thread.id"
            :thread="thread"
            :showExcerpt="excerpt"
          />
        </div>
      </div>
    </div>
    <Paginator
      :properties="threads"
      class="mt-8"
    />
    <SaveGroupModal
      :group="useCurrentGroup ? currentGroup : {}"
      :teams="teams"
      :heading="groupModalHeading"
      :button="groupModalButton"
      :show="groupModal"
      @close="groupModal = false"
    />
    <DeleteGroupModal
      :group="useCurrentGroup ? currentGroup : {}"
      :show="deleteGroupModal"
      @close="deleteGroupModal = false"
    />
    <ExitGroupModal
      :group="useCurrentGroup ? currentGroup : {}"
      :show="exitGroupModal"
      @close="exitGroupModal = false"
    />
  </AppLayout>
</template>
