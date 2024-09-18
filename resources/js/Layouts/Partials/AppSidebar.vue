<script setup>
import { Dialog, DialogOverlay, TransitionChild, TransitionRoot } from '@headlessui/vue';
import {
  HeartIcon,
    ComputerDesktopIcon,
    PlusIcon,
    ListBulletIcon,
    MagnifyingGlassIcon,
    CalendarIcon,
    DocumentTextIcon,
    ChartBarIcon,
    UsersIcon,
    ChatBubbleOvalLeftEllipsisIcon,
    PhoneIcon,
    CommandLineIcon,
    XMarkIcon
} from '@heroicons/vue/24/outline';
import RecentPatients from './RecentPatients.vue';
import {__} from '@/Composables/Translate';
import {can} from '@/Composables/Can';
import {Abilities} from '@/Enums/Abilities';
</script>

<script>
export default {
    props: {
        sidebarOpen: Boolean
    },
    emits: ['close'],
    data() {
        return {
            navigation: [
                { name: __('Donate $25 Today'), route: 'donate.index', icon: HeartIcon, can: this.$page.props.showDonateHeader },
                { name: __('Dashboard'), route: 'dashboard', icon: ComputerDesktopIcon, can: true },
                { name: __('Add New Patient'), route: 'patients.create', icon: PlusIcon, can: can(Abilities.CREATE_PATIENTS) },
                { name: __('List Patients'), route: 'patients.index', routeParams: {
                  page: this.$page.props.listPaginationPage,
                  y: this.$page.props.caseYear
                }, icon: ListBulletIcon, can: true },
                { name: __('Search Patients'), route: 'search.simple.create', icon: MagnifyingGlassIcon, can: can(Abilities.SEARCH_PATIENTS) },
                { name: __('Daily Tasks'), route: 'daily-tasks.index', icon: CalendarIcon, can: can(Abilities.VIEW_DAILY_TASKS) },
                { name: __('Hotline'), route: 'hotline.open.index', icon: PhoneIcon, can: can(Abilities.VIEW_HOTLINE) },
                { name: __('Reports'), route: 'reports.index', icon: DocumentTextIcon, can: can(Abilities.VIEW_REPORTS) },
                { name: __('Analytics'), route: 'analytics.index', icon: ChartBarIcon, can: can(Abilities.VIEW_ANALYTICS) },
                { name: __('People'), route: 'people.rescuers.index', icon: UsersIcon, can: can('displayPeople') },
                { name: __('Forum'), route: 'forum.index', icon: ChatBubbleOvalLeftEllipsisIcon, can: true },
                { name: __('Admin'), route: 'admin.dashboard', icon: CommandLineIcon, can: can(Abilities.VIEW_WRMD_ADMIN) },
            ]
        };
    },
    computed: {
        recentUpdatedPatients() {
            return this.$page.props.recentUpdatedPatients;
        },
        recentAdmittedPatients() {
            return this.$page.props.recentAdmittedPatients;
        }
        // {page: $page.props.listPaginationPage}
    },
    methods: {
        closeSideBar() {
            this.$emit('close');
        },
    }
};
</script>

<template>
  <TransitionRoot
    as="template"
    :show="sidebarOpen"
  >
    <Dialog
      as="div"
      class="fixed inset-0 flex z-40 md:hidden"
      @close="closeSideBar"
    >
      <TransitionChild
        as="template"
        enter="transition-opacity ease-linear duration-300"
        enterFrom="opacity-0"
        enterTo="opacity-100"
        leave="transition-opacity ease-linear duration-300"
        leaveFrom="opacity-100"
        leaveTo="opacity-0"
      >
        <DialogOverlay class="fixed inset-0 bg-gray-600 bg-opacity-75" />
      </TransitionChild>
      <TransitionChild
        as="template"
        enter="transition ease-in-out duration-300 transform"
        enterFrom="-translate-x-full"
        enterTo="translate-x-0"
        leave="transition ease-in-out duration-300 transform"
        leaveFrom="translate-x-0"
        leaveTo="-translate-x-full"
      >
        <div class="relative flex-1 flex flex-col max-w-xs w-full pt-5 pb-4 bg-white">
          <TransitionChild
            as="template"
            enter="ease-in-out duration-300"
            enterFrom="opacity-0"
            enterTo="opacity-100"
            leave="ease-in-out duration-300"
            leaveFrom="opacity-100"
            leaveTo="opacity-0"
          >
            <div class="absolute top-0 right-0 -mr-12 pt-2">
              <button
                type="button"
                class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                @click="closeSideBar"
              >
                <span class="sr-only">{{ __('Close sidebar') }}</span>
                <XMarkIcon
                  class="h-6 w-6 text-white"
                  aria-hidden="true"
                />
              </button>
            </div>
          </TransitionChild>
          <Link
            :href="route('dashboard')"
            class="flex-shrink-0 flex items-center px-4"
          >
            <img
              src="../../../images/logo-48x48.png"
              class="h-12 w-auto mr-4"
              :alt="$page.props.appName"
            >
            <h4 class="text-3xl leading-6 font-medium text-gray-900">
              WRMD
            </h4>
          </Link>
          <div class="mt-5 flex-1 h-0 overflow-y-auto">
            <nav class="px-2 space-y-1">
              <template
                v-for="item in navigation"
                :key="item.name"
              >
                <Link
                  v-if="item.can"
                  :href="route(item.route, item.routeParams || {})"
                  :class="[route().current(item.route) ? 'bg-blue-100 text-blue-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900', 'group flex items-center px-2 py-2 text-base font-medium rounded-md']"
                >
                  <component
                    :is="item.icon"
                    :class="[route().current(item.route) ? 'text-gray-500' : 'text-gray-400 group-hover:text-gray-500', 'mr-4 flex-shrink-0 h-6 w-6']"
                    aria-hidden="true"
                  />
                  {{ item.name }}
                </Link>
              </template>
            </nav>
          </div>
          <div class="border-t border-gray-200 p-4 pt-0">
            <RecentPatients
              id="recent-patients-mobile"
              :updated="recentUpdatedPatients"
              :admitted="recentAdmittedPatients"
            />
          </div>
        </div>
      </TransitionChild>
      <div
        class="flex-shrink-0 w-14"
        aria-hidden="true"
      >
        <!-- Dummy element to force sidebar to shrink to fit close icon -->
      </div>
    </Dialog>
  </TransitionRoot>
  <!-- Static sidebar for desktop -->
  <div class="hidden md:flex md:flex-shrink-0">
    <div class="flex flex-col w-64">
      <!-- Sidebar component, swap this element with another sidebar if you like -->
      <div class="flex flex-col flex-grow border-r border-gray-200 pt-5 bg-white overflow-y-auto">
        <Link
          :href="route('dashboard')"
          class="flex items-center flex-shrink-0 px-4"
        >
          <img
            src="../../../images/logo-48x48.png"
            class="h-12 w-auto mr-4"
            :alt="$page.props.appName"
          >
          <h4 class="text-3xl leading-6 font-medium text-gray-900">
            WRMD
          </h4>
        </Link>
        <div class="mt-5 flex-grow flex flex-col">
          <nav class="flex-1 px-2 bg-white space-y-1">
            <template
              v-for="item in navigation"
              :key="item.name"
            >
              <Link
                v-if="item.can"
                :href="route(item.route, item.routeParams || {})"
                :class="[route().current(item.route) ? 'bg-blue-100 text-blue-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900', 'group flex items-center px-2 py-2 text-sm font-medium rounded-md']"
              >
                <component
                  :is="item.icon"
                  :class="[route().current(item.route) ? 'text-gray-500' : 'text-gray-400 group-hover:text-gray-500', 'mr-3 flex-shrink-0 h-6 w-6']"
                  aria-hidden="true"
                />
                {{ item.name }}
              </Link>
            </template>
          </nav>

          <div class="border-t border-gray-200 p-4 pt-0">
            <RecentPatients
              id="recent-patients-desktop"
              :updated="recentUpdatedPatients"
              :admitted="recentAdmittedPatients"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
