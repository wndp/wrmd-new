<template>
  <AppLayout title="Authorizations">
    <div class="max-w-7xl mx-auto">
      <h1 class="text-2xl font-semibold text-gray-900">
        Authorizations
      </h1>
    </div>
    <TabGroup
      class="mt-5"
      as="div"
    >
      <TabList class="border-b border-gray-200 mt-6">
        <nav
          class="-mb-px flex"
          aria-label="Tabs"
        >
          <Tab
            v-for="tab in tabs"
            :key="tab.name"
            v-slot="{ selected }"
            as="template"
          >
            <button
              :class="[selected ? 'bg-white text-gray-700' : 'text-gray-500 hover:text-gray-700', 'px-3 py-2 font-medium text-sm rounded-t-md']"
              :aria-current="selected ? 'page' : undefined"
            >
              {{ tab.name }}
            </button>
          </Tab>
        </nav>
      </TabList>
      <TabPanels>
        <TabPanel
          v-for="tab in tabs"
          :key="tab.name"
        >
          <component
            :is="tab.component"
            v-bind="$props"
          />
        </TabPanel>
      </TabPanels>
    </TabGroup>
  </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import Panel from '@/Components/Panel.vue';
import AllowedAuthorizations from './Partials/AllowedAuthorizations.vue';
import ForbiddenAuthorizations from './Partials/ForbiddenAuthorizations.vue';
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue';

export default {
  components: {
    AppLayout,
    Panel,
    TabGroup,
    TabList,
    Tab,
    TabPanels,
    TabPanel,
    AllowedAuthorizations,
    ForbiddenAuthorizations
  },
  props: {
    roles: Array,
    abilities: Array
  },
  data() {
    return {
      tabs: [
      {name: 'Allowed', component: 'AllowedAuthorizations'},
                // {name: 'Forbidden', component: 'ForbiddenAuthorizations'},
                ]
              };
            }
          };
</script>
