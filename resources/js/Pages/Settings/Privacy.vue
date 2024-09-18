<script setup>
import { inject } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SettingsAside from './Partials/SettingsAside.vue';
import Alert from '@/Components/Alert.vue';
import FormSection from '@/Components/FormElements/FormSection.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import Checkbox from '@/Components/FormElements/Checkbox.vue';
import Toggle from '@/Components/FormElements/Toggle.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import {__} from '@/Composables/Translate';

const route = inject('route');

const props = defineProps({
  users: Array,
  fullPeopleAccess: Boolean,
  authorized: Object
});

const form = useForm({
  fullPeopleAccess: props.fullPeopleAccess,
  displayPeople: props.authorized['display-people'] ?? [],
  displayRescuer: props.authorized['display-rescuer'] ?? [],
  searchRescuers: props.authorized['search-rescuers'] ?? [],
  createPeople: props.authorized['create-people'] ?? [],
  deletePeople: props.authorized['delete-people'] ?? [],
  combinePeople: props.authorized['combine-people'] ?? [],
  exportPeople: props.authorized['export-people'] ?? []
});

const updatePrivacy = () => {
  form.put(route('privacy.update'), {
      preserveScroll: true
  });
};
</script>

<template>
  <AppLayout title="Privacy">
    <div class="lg:grid grid-cols-8 gap-8 mt-4">
      <SettingsAside class="mb-4 lg:mb-0 col-span-2" />
      <div class="col-span-6">
        <FormSection>
          <template #title>
            {{ __('People Privacy') }}
          </template>
          <template #description>
            <p>{{ __('Below are authorization settings designed to protect the privacy of the people (Rescuers, Donors, Volunteers, Members, ...) who are stored in your account.') }}</p>
          </template>
          <div class="col-span-4">
            <InputLabel for="first-name">
              {{ __('Allow All Users Full Access To All People.') }}
            </InputLabel>
            <div class="mt-2">
              <Toggle
                v-model="form.fullPeopleAccess"
                dusk="full-people-access"
              />
            </div>
          </div>
          <div
            v-if="form.fullPeopleAccess"
            class="col-span-4"
          >
            <Alert color="red">
              <p>{{ __('All of your users have the ability to view, edit, create and export the people (Rescuers, Donors, Volunteers, Members, ...) stored in your database.') }}</p>
            </Alert>
          </div>
          <template v-else>
            <Alert class="col-span-4">
              {{ __('By default only users with the Super Admin role can view data related to people which may display their personal identifiable information.') }}
            </Alert>

            <div class="col-span-4">
              <InputLabel>{{ __('Allow These Users to Access People From the Sidebar') }}</InputLabel>
              <div class="mt-2 space-y-2">
                <div
                  v-for="user in users"
                  :key="user.id"
                  class="flex items-start"
                >
                  <div class="flex items-center h-5">
                    <Checkbox
                      :id="`displayPeople-${user.id}`"
                      v-model="form.displayPeople"
                      :value="user.email"
                    />
                  </div>
                  <div class="ml-3 text-sm">
                    <InputLabel
                      :for="`displayPeople-${user.id}`"
                      class="font-normal"
                    >
                      {{ user.email }}
                    </InputLabel>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-span-4">
              <InputLabel>{{ __('Allow These Users to Access a Patients Rescuer') }}</InputLabel>
              <div class="mt-2 space-y-2">
                <div
                  v-for="user in users"
                  :key="user.id"
                  class="flex items-start"
                >
                  <div class="flex items-center h-5">
                    <Checkbox
                      :id="`displayRescuer-${user.id}`"
                      v-model="form.displayRescuer"
                      :value="user.email"
                    />
                  </div>
                  <div class="ml-3 text-sm">
                    <InputLabel
                      :for="`displayRescuer-${user.id}`"
                      class="font-normal"
                    >
                      {{ user.email }}
                    </InputLabel>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-span-4">
              <InputLabel>{{ __('Allow These Users to Search Rescuers When Creating a New Patient') }}</InputLabel>
              <div class="mt-2 space-y-2">
                <div
                  v-for="user in users"
                  :key="user.id"
                  class="flex items-start"
                >
                  <div class="flex items-center h-5">
                    <Checkbox
                      :id="`searchRescuers-${user.id}`"
                      v-model="form.searchRescuers"
                      :value="user.email"
                    />
                  </div>
                  <div class="ml-3 text-sm">
                    <InputLabel
                      :for="`searchRescuers-${user.id}`"
                      class="font-normal"
                    >
                      {{ user.email }}
                    </InputLabel>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-span-4">
              <InputLabel>{{ __('Allow These Users to Create New People') }}</InputLabel>
              <InputLabel class="font-normal italic">
                {{ __('Note: allowing a user to create people will also allow them to see people.') }}
              </InputLabel>
              <div class="mt-2 space-y-2">
                <div
                  v-for="user in users"
                  :key="user.id"
                  class="flex items-start"
                >
                  <div class="flex items-center h-5">
                    <Checkbox
                      :id="`createPeople-${user.id}`"
                      v-model="form.createPeople"
                      :value="user.email"
                    />
                  </div>
                  <div class="ml-3 text-sm">
                    <InputLabel
                      :for="`createPeople-${user.id}`"
                      class="font-normal"
                    >
                      {{ user.email }}
                    </InputLabel>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-span-4">
              <InputLabel>{{ __('Allow These Users to Delete People') }}</InputLabel>
              <InputLabel class="font-normal italic">
                {{ __('Note: allowing a user to delete people will also allow them to see people.') }}
              </InputLabel>
              <div class="mt-2 space-y-2">
                <div
                  v-for="user in users"
                  :key="user.id"
                  class="flex items-start"
                >
                  <div class="flex items-center h-5">
                    <Checkbox
                      :id="`deletePeople-${user.id}`"
                      v-model="form.deletePeople"
                      :value="user.email"
                    />
                  </div>
                  <div class="ml-3 text-sm">
                    <InputLabel
                      :for="`deletePeople-${user.id}`"
                      class="font-normal"
                    >
                      {{ user.email }}
                    </InputLabel>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-span-4">
              <InputLabel>{{ __('Allow These Users to Combine People') }}</InputLabel>
              <InputLabel class="font-normal italic">
                {{ __('Note: allowing a user to combine people will also allow them to see people.') }}
              </InputLabel>
              <div class="mt-2 space-y-2">
                <div
                  v-for="user in users"
                  :key="user.id"
                  class="flex items-start"
                >
                  <div class="flex items-center h-5">
                    <Checkbox
                      :id="`combinePeople-${user.id}`"
                      v-model="form.combinePeople"
                      :value="user.email"
                    />
                  </div>
                  <div class="ml-3 text-sm">
                    <InputLabel
                      :for="`combinePeople-${user.id}`"
                      class="font-normal"
                    >
                      {{ user.email }}
                    </InputLabel>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-span-4">
              <InputLabel>{{ __('Allow These Users to Export People') }}</InputLabel>
              <InputLabel class="font-normal italic">
                {{ __('Note: allowing a user to export people will also allow them to see people.') }}
              </InputLabel>
              <div class="mt-2 space-y-2">
                <div
                  v-for="user in users"
                  :key="user.id"
                  class="flex items-start"
                >
                  <div class="flex items-center h-5">
                    <Checkbox
                      :id="`exportPeople-${user.id}`"
                      v-model="form.exportPeople"
                      :value="user.email"
                    />
                  </div>
                  <div class="ml-3 text-sm">
                    <InputLabel
                      :for="`exportPeople-${user.id}`"
                      class="font-normal"
                    >
                      {{ user.email }}
                    </InputLabel>
                  </div>
                </div>
              </div>
            </div>
          </template>
          <template #actions>
            <ActionMessage
              :on="form.recentlySuccessful"
              class="mr-3"
            >
              {{ __('Saved') }}
            </ActionMessage>
            <PrimaryButton
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
              @click="updatePrivacy"
            >
              {{ __('Update People Privacy Settings') }}
            </PrimaryButton>
          </template>
        </FormSection>
      </div>
    </div>
  </AppLayout>
</template>
