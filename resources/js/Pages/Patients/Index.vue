<script setup>
import {computed} from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import PatientsList from './Partials/PatientsList.vue';
import Paginator from '@/Components/Paginator.vue';
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';
import { ChevronDownIcon } from '@heroicons/vue/24/solid';

const props =defineProps({
  lists: Array,
  list: String,
  listFigures: Object,
  hasQueryCache: Boolean
});

const visibleLists = computed(() => props.lists.filter(listGroup => listGroup.visibility));

const currentListTitle = computed(() => props.lists
  .flatMap(group => group.lists)
  .find(list => list.key === props.list)
  .title
);

// export default {
//     computed: {
//         visibleLists() {
//             return this.lists.filter(listGroup => listGroup.visibility);
//         },
//         currentListTitle() {
//             return this.lists
//                 .flatMap(group => group.lists)
//                 .find(list => list.key === this.list)
//                 .title;
//         }
//     }
// };
</script>

<template>
  <AppLayout title="Patients">
    <template #header>
      <span class="relative z-10 flex mb-8">
        <Menu
          as="span"
          class="-ml-px relative block"
        >
          <MenuButton
            class="relative inline-flex items-center h-100 text-2xl font-semibold text-gray-900 focus:z-10 focus:outline-none"
            dusk="patient-list-button"
          >
            {{ currentListTitle }}
            <ChevronDownIcon
              class="h-6 w-6 flex-shrink-0 ml-3"
              aria-hidden="true"
            />
          </MenuButton>
          <transition
            enterActiveClass="transition ease-out duration-100"
            enterFromClass="transform opacity-0 scale-95"
            enterToClass="transform opacity-100 scale-100"
            leaveActiveClass="transition ease-in duration-75"
            leaveFromClass="transform opacity-100 scale-100"
            leaveToClass="transform opacity-0 scale-95"
          >
            <MenuItems class="origin-top-left absolute left-0 mt-2 -mr-1 w-72 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none">
              <div
                v-for="group in visibleLists"
                :key="group.title"
                class="py-2"
              >
                <h4 class="px-4 py-2 font-bold text-gray-700 text-lg">{{ group.title }}</h4>
                <MenuItem
                  v-for="loopList in group.lists"
                  :key="loopList.key"
                  v-slot="{ active }"
                >
                  <Link
                    :href="route('patients.index', {list: loopList.key})"
                    :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block px-6 py-2 text-sm']"
                  >
                    {{ loopList.title }}
                  </Link>
                </MenuItem>
              </div>
            </MenuItems>
          </transition>
        </Menu>
      </span>
    </template>
    <PatientsList
      :hasQueryCache="hasQueryCache"
      :data="listFigures"
    />
    <Paginator
      v-if="listFigures.rows.first_page_url"
      :properties="listFigures.rows"
      class="mt-8"
      dusk="patient-paginator"
    />
  </AppLayout>
</template>
