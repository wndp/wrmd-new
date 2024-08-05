<script setup>
import URI from 'urijs';
import NewDiscussionModal from './NewDiscussionModal.vue';
import SaveGroupModal from './SaveGroupModal.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import {
    ChatBubbleOvalLeftEllipsisIcon,
    QuestionMarkCircleIcon,
    MegaphoneIcon,
    ArrowUturnLeftIcon,
    StarIcon,
    CheckIcon,
    XMarkIcon,
    UserGroupIcon,
    ArrowLongRightIcon
} from '@heroicons/vue/24/outline';
//import { hasURLParameter, missingURLParameter } from '@/Utilities/Helpers';
import pick from 'lodash/pick';
import {__} from '@/Composables/Translate';
</script>

<script>
export default {
  props: {
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
  },
  data() {
    let uri = new URI();
    return {
      newDiscussion: false,
      uri: uri,
      groupModal: false,
      groupModalHeading: __('Create A New Group'),
      groupModalButton: __('Create Group'),
      currentNav: [
        'mine',
        'participant',
        'following',
        'solved',
        'unsolved',
        'unanswered'
      ].filter(key => Object.keys(uri.query(true)).includes(key))[0] || '',
      navigation: [
      {
        name: __('All Discussions'),
        uri: '',
        icon: ChatBubbleOvalLeftEllipsisIcon,
      },
      {
        name: __('My Questions'),
        uri: 'mine',
        icon: QuestionMarkCircleIcon,
      },
      {
        name: __('My Participation'),
        uri: 'participant',
        icon: MegaphoneIcon,
      },
      {
        name: __('Following'),
        uri: 'following',
        icon: StarIcon,
      },
      {
        name: __('Solved'),
        uri: 'solved',
        icon: CheckIcon,
      },
      {
        name: __('Not Solved'),
        uri: 'unsolved',
        icon: XMarkIcon,
      },
      {
        name: __('No Replies Yet'),
        uri: 'unanswered',
        icon: ArrowUturnLeftIcon,
      },
      ]
    }
  },
  computed: {
    currentSubSet() {
      let key = pick(this.uri.query(true), [
        'mine',
        'participant',
        'following',
        'solved',
        'unsolved',
        'unanswered',
        'group',
      ]);

      return Object.keys(key)[0] || '';
    },
    currentGroupUri() {
      return this.uri.query(true).group;
    },
  },
  methods: {
    newGroup() {
      this.$nextTick(() => {
        this.groupModal = true;
      });
    },
  }
}
</script>

<template>
  <aside>
    <PrimaryButton @click="newDiscussion = true">
      {{ __('New Discussion') }}
    </PrimaryButton>
    <div class="md:hidden my-4">
      <label
        for="tabs"
        class="sr-only"
      >{{ __('Select a tab') }}</label>
      <select
        id="tabs"
        v-model="currentNav"
        name="tabs"
        class="block w-full focus:ring-blue-500 focus:border-blue-500 border-gray-300 rounded-md"
        @change="redirectToTab"
      >
        <option
          v-for="item in navigation"
          :key="item.name"
          :value="item.uri"
        >
          {{ item.name }}
        </option>
      </select>
    </div>
    <div class="hidden md:block">
      <nav class="space-y-1 mt-8">
        <Link
          v-for="item in navigation"
          :key="item.name"
          :href="route('forum.index', {[item.uri]: 1})"
          :class="[currentSubSet === item.uri ? 'bg-blue-300 text-gray-900 hover:bg-blue-300' : 'text-gray-700 hover:text-gray-900 hover:bg-blue-300', 'group rounded-md px-3 py-2 flex items-center text-sm font-medium']"
          :aria-current="currentSubSet === item.uri ? 'page' : undefined"
        >
          <component
            :is="item.icon"
            :class="[currentSubSet === item.uri ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500', 'flex-shrink-0 -ml-1 mr-3 h-6 w-6']"
            aria-hidden="true"
          />
          <span class="truncate">
            {{ item.name }}
          </span>
        </Link>
      </nav>
      <div class="flex justify-between items-center mt-8">
        <h5 class="text-gray-700 group rounded-md px-3 py-2 flex items-center font-medium">
          <UserGroupIcon
            class="text-gray-400 flex-shrink-0 -ml-1 mr-3 h-6 w-6"
            aria-hidden="true"
          />
          <span class="truncate">
            {{ __('Groups') }}
          </span>
        </h5>
        <button
          type="button"
          class="text-sm text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700 transition ease-in-out duration-150 truncate"
          @click="newGroup()"
        >
          {{ __('New Group') }}
        </button>
      </div>
      <nav class="space-y-1">
        <Link
          v-for="group in groups"
          :key="group.id"
          :href="route('forum.index', {group: group.slug})"
          :class="[group.slug === currentGroupUri ? 'bg-blue-300 text-gray-900 hover:bg-blue-300' : 'text-gray-700 hover:text-gray-900 hover:bg-blue-300', 'group rounded-md px-3 py-2 flex items-center text-sm font-medium']"
          :aria-current="group.slug === currentGroupUri ? 'page' : undefined"
        >
          <ArrowLongRightIcon
            :class="[group.slug === currentGroupUri ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500', 'flex-shrink-0 -ml-1 mr-3 h-6 w-6']"
            aria-hidden="true"
          />
          <span class="truncate">
            {{ group.name }}
          </span>
        </Link>
      </nav>
    </div>
    <NewDiscussionModal
      :channels="channels"
      :groups="groups"
      :show="newDiscussion"
      @close="newDiscussion = false"
    />
    <SaveGroupModal
      :teams="teams"
      :heading="groupModalHeading"
      :button="groupModalButton"
      :show="groupModal"
      @close="groupModal = false"
    />
  </aside>
</template>
