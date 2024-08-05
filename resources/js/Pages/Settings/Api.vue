<template>
  <AppLayout title="API">
    <div class="lg:grid grid-cols-8 gap-8 mt-4">
      <SettingsAside class="mb-4 lg:mb-0 col-span-2" />
      <div class="col-span-6">
        <FormSection>
          <template #title>
            Wildlife Rehabilitation MD (WRMD) API
          </template>
          <template #description>
            <p>
              {{ __('The Wildlife Rehabilitation MD (WRMD) API allows you to programmatically create and manage your patient resources and related data through a third party application.') }} {{ __('In order to use the API, you must create an authentication token with the appropriate assigned abilities.') }} For more information and to get started <a
                :href="route('apiv3.index')"
                target="_blank"
                class="text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700 transition ease-in-out duration-150"
              >please consult the API documentation</a>.
            </p>
          </template>
          <div class="col-span-4">
            <Label for="token_name">{{ __('Name') }}</Label>
            <Input
              v-model="form.token_name"
              name="token_name"
              autocomplete="off"
              class="mt-1"
            />
            <InputError
              :message="form.errors.token_name"
              class="mt-2"
            />
          </div>
          <div class="col-span-4">
            <Label for="abilities">{{ __('Abilities') }}</Label>
            <InputError
              :message="form.errors.token_abilities"
              class="mt-2"
            />
            <div class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-6">
              <div
                v-for="(abilityGroup, i) in abilities"
                :key="i"
                class="col-span-1"
              >
                <div
                  v-for="ability in abilityGroup"
                  :key="ability"
                  class="flex items-start"
                >
                  <div class="flex items-center h-5">
                    <Checkbox
                      :id="ability"
                      v-model="form.token_abilities"
                      :name="ability"
                      :value="ability"
                    />
                  </div>
                  <div class="ml-3 text-sm">
                    <Label
                      :for="ability"
                      class="font-normal"
                    >{{ ability }}</Label>
                  </div>
                </div>
              </div>
            </div>
          </div>

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
              @click="save"
            >
              {{ __('Create API Token') }}
            </PrimaryButton>
          </template>
        </FormSection>

        <div
          v-if="tokens.length"
          class="mt-8 -my-2 overflow-x-auto"
        >
          <div class="py-2 align-middle inline-block min-w-full">
            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-b-lg">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-blue-100">
                  <tr>
                    <th
                      scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      {{ __('Name') }}
                    </th>
                    <th
                      scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      {{ __('Last Used') }}
                    </th>
                    <th
                      scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      {{ ('Abilities') }}
                    </th>
                    <th
                      scope="col"
                      class="relative py-3.5 pl-3 pr-4 sm:pr-6"
                    />
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="(token, i) in tokens"
                    :key="token.id"
                    :class="i % 2 === 0 ? 'bg-white' : 'bg-gray-50'"
                  >
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ token.name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ token.last_used_at_for_humans }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                      {{ token.abilities.join(', ') }}
                    </td>
                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                      <Link
                        :href="route('api.destroy', token)"
                        method="delete"
                        as="button"
                        class="text-red-600 hover:text-red-900"
                      >
                        {{ __('Delete') }}
                      </Link>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    <DialogModal :show="newTokenModal">
      <template #title>
        {{ __('Your API Token') }}
      </template>

      <template #content>
        <button
          type="button"
          class="absolute top-1 right-1 text-gray-500"
          @click="newTokenModal = false"
        >
          <XCircleIcon class="h-10 w-10" />
        </button>
        <div class="mt-6">
          <p class="text-md text-gray-600">
            {{ __("Please copy your new API token. For your security, it won't be shown again.") }}
          </p>
          <Input
            name="new_token"
            :value="createdToken.plainTextToken"
            autocomplete="off"
            class="mt-4"
          />
        </div>
      </template>

      <template #footer>
        <SecondaryButton
          class="mr-3"
          @click="newTokenModal = false"
        >
          {{ __('Close') }}
        </SecondaryButton>
      </template>
    </DialogModal>
  </AppLayout>
</template>

<script setup>
import { inject, ref, computed, watch } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SettingsAside from './Partials/SettingsAside.vue';
import FormSection from '@/Components/FormElements/FormSection.vue';
import Label from '@/Components/FormElements/Label.vue';
import Input from '@/Components/FormElements/Input.vue';
import Checkbox from '@/Components/FormElements/Checkbox.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import { XCircleIcon } from '@heroicons/vue/24/outline';

const route = inject('route');

defineProps({
  abilities: {
    type: Array,
    required: true
  },
  tokens: {
    type: Array,
    required: true
  }
});

const form = useForm({
  token_name: '',
  token_abilities: []
});

let newTokenModal = ref(false);

const createdToken = computed(() => usePage().props.flash.token);

watch(createdToken, (value) => {
  if (value !== undefined) newTokenModal.value = true
});

const save = () => {
  form.post(route('api.store'), {
    preserveScroll: true,
    onSuccess: () => form.reset()
  });
};
</script>
