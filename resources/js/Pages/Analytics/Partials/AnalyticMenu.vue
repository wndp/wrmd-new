<template>
  <Menu
    as="span"
    class="-ml-px relative block"
  >
    <MenuButton class="relative inline-flex items-center h-100 text-xl font-semibold text-gray-900 focus:z-10 focus:outline-none whitespace-nowrap">
      {{ button }}
      <ChevronDownIcon
        class="h-6 w-6 flex-shrink-0 ml-1"
        aria-hidden="true"
      />
    </MenuButton>
    <TransitionRoot
      enterActive="transition ease-out duration-100"
      enterFrom="transform opacity-0 scale-95"
      enterTo="transform opacity-100 scale-100"
      leaveActive="transition ease-in duration-75"
      leaveFrom="transform opacity-100 scale-100"
      leaveTo="transform opacity-0 scale-95"
    >
      <MenuItems
        class="origin-top-left absolute left-0 mt-2 -mr-1 py-2 w-80 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none divide-y-4 space-y-1 divide-gray-100"
        style="z-index: 1010"
      >
        <div
          v-for="group in list"
          :key="group.heading"
        >
          <h6
            v-if="group.heading"
            class="text-gray-700 font-medium px-4 pt-4 pb-1 text-sm"
          >
            {{ group.heading }}
          </h6>
          <MenuItem
            v-for="link in group.links"
            :key="link.name"
            v-slot="{ active }"
          >
            <Link
              :href="link.href"
              :class="[active ? 'bg-blue-100 text-gray-900' : 'text-gray-700', 'block px-6 py-1 text-sm']"
            >
              {{ link.name }}
            </Link>
          </MenuItem>
        </div>
      </MenuItems>
    </TransitionRoot>
  </Menu>
</template>

<script setup>
import { ChevronDownIcon } from '@heroicons/vue/24/solid';
import { TransitionRoot, Menu, MenuButton, MenuItems, MenuItem } from "@headlessui/vue";
</script>

<script>
export default {
    props: {
        button: String,
        list: Array
    }
};
</script>
