<script setup>
import Checkbox from '@/Components/FormElements/Checkbox.vue';
import {
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
    TransitionRoot
} from '@headlessui/vue';
import {
    BellIcon,
    MagnifyingGlassIcon,
    QuestionMarkCircleIcon,
    Bars3BottomLeftIcon,
    CogIcon,
    UserGroupIcon,
    AdjustmentsVerticalIcon,
    ArrowsRightLeftIcon,
    ArrowRightOnRectangleIcon,
    CreditCardIcon
} from '@heroicons/vue/24/outline';
import {__} from '@/Composables/Translate';
import {can} from '@/Composables/Can';
import {Abilities} from '@/Enums/Abilities';
</script>

<script>
export default {
    props: {
        noSidebar: {
            type: Boolean,
            default: false,
        },
    },
    emits: ['open'],
    data() {
        return {
            showQuickSearch: false,
            form: this.$inertia.form({
                search: '',
                limitToCurrentYear: true,
                stillPending: true
            })
        };
    },
    computed: {
        user() {
            return this.$page.props.auth.user;
        },
        currentTeam() {
            return this.$page.props.auth.user.current_team;
        },
        unreadNotifications() {
            return this.$page.props.unreadNotifications;
        }
    },
    mounted() {
        document.addEventListener('keydown', this.onKeyDown);
    },
    unmounted() {
         document.removeEventListener('keydown', this.onKeyDown);
    },
    methods: {
        openSideBar() {
            this.$emit('open');
        },
        showHelp () {
          if (window.Beacon) {
            window.Beacon('toggle');
            window.Beacon('navigate', '/ask/');
          }
        },
        slideQuickSearch() {
            this.showQuickSearch = !this.showQuickSearch;
            this.$nextTick(() => {
                if (this.showQuickSearch) this.$refs.quickSearch.focus();
            });
        },
        searchPatients() {
            this.form.post(this.route('search.quick'), {
                preserveState: false
            });
        },
        onKeyDown(event) {
            if (
                (event.key === 'Escape' && this.showQuickSearch) ||
                (event.key === 'k' && (event.metaKey || event.ctrlKey)) ||
                (! this.isEditingContent(event) && event.key === '/' && ! this.showQuickSearch)
            ) {
                event.preventDefault();
                this.slideQuickSearch();
            }
        },
        isEditingContent(event) {
            const element = event.target;
            const tagName = element.tagName;

            return (
                element.isContentEditable ||
                tagName === 'INPUT' ||
                tagName === 'SELECT' ||
                tagName === 'TEXTAREA'
            );
        }
    }
};
</script>

<template>
  <div id="app-header">
    <div class="relative z-20 flex-shrink-0 flex h-16 bg-blue-600">
      <button
        v-if="!noSidebar"
        type="button"
        class="px-4 border-r border-gray-200 text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-green-500 md:hidden"
        @click="openSideBar"
      >
        <span class="sr-only">{{ __('Open sidebar') }}</span>
        <Bars3BottomLeftIcon
          class="h-6 w-6"
          aria-hidden="true"
        />
      </button>

      <div class="flex-1 px-4 flex justify-between">
        <h1
          class="sm:block text-sm sm:text-2xl md:text-3xl text-blue-50 line-clamp-1 break-all overflow-hidden overflow-ellipsis"
          style="line-height: 4rem;"
        >
          {{ currentTeam.name }}
        </h1>
        <div class="ml-4 flex items-center md:ml-6 space-x-2 sm:space-x-4">
          <button
            type="button"
            class="bg-blue-600 rounded-full text-white hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
            dusk="quick-search"
            @click="slideQuickSearch"
          >
            <span class="sr-only">{{ __('Quick Search') }}</span>
            <MagnifyingGlassIcon
              class="h-6 w-6"
              aria-hidden="true"
            />
          </button>
          <button
            type="button"
            class="bg-blue-600 rounded-full text-white hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
            @click="showHelp"
          >
            <span class="sr-only">{{ __('Get Help') }}</span>
            <div class="flex items-center">
              <QuestionMarkCircleIcon
                class="h-6 w-6"
                aria-hidden="true"
              />
            </div>
          </button>
          <Link
            :href="route('notifications.index')"
            class="relative flex flex-col items-center bg-blue-600 rounded-full text-white hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
          >
            <span class="sr-only">{{ __('View notifications') }}</span>
            <BellIcon
              class="h-6 w-6"
              aria-hidden="true"
            />
            <div
              v-if="unreadNotifications.length"
              class="absolute top-full flex flex-col items-center animate-bounce"
            >
              <span
                class="w-0 h-0 border-8 border-red-500"
                style="border-left-color: transparent; border-right-color: transparent; border-top-color: transparent;"
              />
              <span class="text-white bg-red-500 rounded p-1 shadow">{{ unreadNotifications.length }}</span>
            </div>
          </Link>

          <!-- Profile dropdown -->
          <Menu
            as="div"
            class="relative"
          >
            <div>
              <MenuButton
                class="max-w-xs bg-white flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 w-8"
                dusk="user-button"
              >
                <span class="sr-only">{{ __('Open user menu') }}</span>
                <img
                  class="h-8 w-8 rounded-full flex-shrink-0"
                  :src="currentTeam.profile_photo_url"
                  alt="account profile photo"
                >
              </MenuButton>
            </div>
            <TransitionRoot
              enterActive="transition ease-out duration-100"
              enterFrom="transform opacity-0 scale-95"
              enterTo="transform opacity-100 scale-100"
              leaveActive="transition ease-in duration-75"
              leaveFrom="transform opacity-100 scale-100"
              leaveTo="transform opacity-0 scale-95"
            >
              <MenuItems class="origin-top-right absolute right-0 z-40 mt-2 w-72 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none divide-y-4 divide-gray-100">
                <div>
                  <MenuItem v-slot="{ active }">
                    <Link
                      :href="route('profile.edit')"
                      class="font-semibold block py-3 px-2"
                      :class="[active ? 'bg-gray-100' : '']"
                    >
                      <p class="truncate">
                        <span class="block mb-0.5 text-xs text-gray-500">{{ __('Signed in as') }}</span>
                        {{ user.email }}
                      </p>
                    </Link>
                  </MenuItem>
                </div>
                <div v-if="can(Abilities.COMPUTED_VIEW_SETTINGS) || can(Abilities.COMPUTED_VIEW_MAINTENANCE)">
                  <MenuItem
                    v-if="can(Abilities.COMPUTED_VIEW_SETTINGS)"
                    v-slot="{ active }"
                  >
                    <Link
                      :href="route('spark.portal')"
                      :class="[active ? 'bg-gray-100' : '', 'group flex items-center px-2 py-2 text-base text-gray-800']"
                    >
                      <CreditCardIcon
                        :active="active"
                        class="w-5 h-5 mr-2 text-gray-400"
                        aria-hidden="true"
                      />
                      {{ __('Billing') }}
                    </Link>
                  </MenuItem>
                  <MenuItem
                    v-if="can(Abilities.COMPUTED_VIEW_SETTINGS)"
                    v-slot="{ active }"
                  >
                    <Link
                      :href="route('account.profile.edit')"
                      :class="[active ? 'bg-gray-100' : '', 'group flex items-center px-2 py-2 text-base text-gray-800']"
                    >
                      <CogIcon
                        :active="active"
                        class="w-5 h-5 mr-2 text-gray-400"
                        aria-hidden="true"
                      />
                      {{ __('Settings') }}
                    </Link>
                  </MenuItem>
                  <MenuItem
                    v-if="can(Abilities.COMPUTED_VIEW_MAINTENANCE)"
                    v-slot="{ active }"
                  >
                    <Link
                      :href="route('maintenance.unrecognized-patients')"
                      :class="[active ? 'bg-gray-100' : '', 'group flex items-center px-2 py-2 text-base text-gray-800']"
                    >
                      <AdjustmentsVerticalIcon
                        :active="active"
                        class="w-5 h-5 mr-2 text-gray-400"
                        aria-hidden="true"
                      />
                      {{ __('Maintenance') }}
                    </Link>
                  </MenuItem>
                </div>
                <div>
                  <MenuItem
                    v-if="currentTeam.is_master_account && can(Abilities.COMPUTED_VIEW_SUB_ACCOUNTS)"
                    v-slot="{ active }"
                  >
                    <Link
                      :href="route('sub_accounts.index')"
                      :class="[active ? 'bg-gray-100' : '', 'group flex items-center px-2 py-2 text-base text-gray-800']"
                    >
                      <UserGroupIcon
                        :active="active"
                        class="w-5 h-5 mr-2 text-gray-400"
                        aria-hidden="true"
                      />
                      {{ __('Sub-Accounts') }}
                    </Link>
                  </MenuItem>
                  <template v-if="$page.props.auth.user.all_teams.length > 1">
                    <MenuItem v-slot="{ active }">
                      <div class="block px-4 py-2 text-xs text-gray-400">
                        {{ __('Switch Accounts') }}
                      </div>
                      <template
                        v-for="team in $page.props.auth.user.all_teams"
                        :key="team.id"
                      >
                        <form @submit.prevent="switchToTeam(team)">
                          <Link as="button">
                            <div class="flex items-center">
                              <svg
                                v-if="team.id == $page.props.auth.user.current_team_id"
                                class="me-2 h-5 w-5 text-green-400"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                              >
                                <path
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                              </svg>

                              <div>{{ team.name }}</div>
                            </div>
                          </Link>
                        </form>
                      </template>
                      <Link
                        :href="route('choose_account.index')"
                        :class="[active ? 'bg-gray-100' : '', 'group flex items-center px-2 py-2 text-base text-gray-800']"
                      >
                        <ArrowsRightLeftIcon
                          :active="active"
                          class="w-5 h-5 mr-2 text-gray-400"
                          aria-hidden="true"
                        />
                        {{ __('Switch Accounts') }}
                      </Link>
                    </MenuItem>
                  </template>
                  <MenuItem v-slot="{ active }">
                    <Link
                      :href="route('logout')"
                      method="post"
                      as="button"
                      :class="[active ? 'bg-gray-100' : '', 'group flex items-center px-2 py-2 text-base text-gray-800 w-full']"
                    >
                      <ArrowRightOnRectangleIcon
                        :active="active"
                        class="w-5 h-5 mr-2 text-gray-400"
                        aria-hidden="true"
                      />
                      {{ __('Sign Out') }}
                    </Link>
                  </MenuItem>
                </div>
              </MenuItems>
            </TransitionRoot>
          </Menu>
        </div>
      </div>
    </div>

    <!-- Quick Search -->
    <TransitionRoot
      as="template"
      :show="showQuickSearch"
      enter="transform transition ease-in-out duration-500 sm:duration-700"
      enterFrom="opacity-0 -top-16"
      enterTo="opacity-100 top-0"
      leave="transform transition ease-in-out duration-100 sm:duration-100"
      leaveFrom="opacity-100"
      leaveTo="opacity-0"
    >
      <div class="relative z-10 flex-shrink-0 flex h-16 border-t border-gray-200 bg-white shadow">
        <div class="flex-1 flex px-4">
          <form
            class="w-full md:ml-0"
            action="#"
            method="GET"
            @submit.prevent="searchPatients"
          >
            <label
              for="search-field"
              class="sr-only"
            >{{ __('Search') }}</label>
            <div class="relative w-full text-gray-400 focus-within:text-gray-600">
              <div class="absolute inset-y-0 left-0 flex items-center pointer-events-none">
                <span
                  style="opacity: 1;"
                  class="block text-gray-400 text-sm leading-5 py-0.5 px-1.5 border border-gray-300 rounded-md"
                ><span class="sr-only">Press </span><kbd class="font-sans"><span class="no-underline">⌘</span></kbd><span class="sr-only"> and </span><kbd class="font-sans">K</kbd><span class="sr-only"> to search</span></span>
              </div>
              <input
                ref="quickSearch"
                v-model="form.search"
                class="block w-full h-full pl-10 pr-3 py-2 border-transparent text-gray-900 placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-0 focus:border-transparent sm:text-sm"
                :placeholder="__('Quick Search Patients')"
                type="search"
                name="quick-search"
              >
            </div>
            <div class="flex">
              <div class="relative flex items-start mr-8">
                <div class="flex items-center h-5">
                  <Checkbox
                    id="limitToCurrentYear"
                    v-model="form.limitToCurrentYear"
                    name="limitToCurrentYear"
                  />
                </div>
                <div class="ml-3 text-sm">
                  <label
                    for="limitToCurrentYear"
                    class="font-medium text-gray-700"
                  >{{ __('Limit to this year') }}</label>
                </div>
              </div>
              <div class="relative flex items-start">
                <div class="flex items-center h-5">
                  <Checkbox
                    id="stillPending"
                    v-model="form.stillPending"
                    name="stillPending"
                  />
                </div>
                <div class="ml-3 text-sm">
                  <label
                    for="stillPending"
                    class="font-medium text-gray-700"
                  >{{ __('Disposition pending') }}</label>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </TransitionRoot>
  </div>
</template>
