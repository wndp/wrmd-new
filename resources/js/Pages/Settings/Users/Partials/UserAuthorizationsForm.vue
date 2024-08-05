<template>
  <FormSection>
    <template #title>
      {{ __(":user's Authorizations", {user: user.name}) }}
    </template>
    <Alert class="col-span-4">
      {{ __("Default authorizations are set by the user's role. If you wish to broadly change :user's authorizations then first update their role to a higher or lower level.", {user: user.name }) }}
    </Alert>

    <div class="flex flex-col col-span-4">
      <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
          <div class="overflow-hidden border-b border-gray-200 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-blue-100">
                <tr>
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    {{ __('Ability') }}
                  </th>
                  <th
                    scope="col"
                    class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    {{ __('Allowed') }}
                  </th>
                  <th
                    scope="col"
                    class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    {{ __('Forbidden') }}
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="ability in abilities"
                  :key="ability.id"
                  class="bg-white"
                >
                  <td class="px-2 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ ability.title }}
                  </td>
                  <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">
                    <Radio
                      v-model="form.userAbilities[ability.name]"
                      name="allowed[]"
                      value="allowed"
                    />
                  </td>
                  <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">
                    <Radio
                      v-model="form.userAbilities[ability.name]"
                      name="forbidden[]"
                      value="forbidden"
                    />
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <template #actions>
      <ActionMessage
        :on="form.recentlySuccessful"
        class="mr-3"
      >
        {{ __('Saved.') }}
      </ActionMessage>
      <PrimaryButton
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
        @click="updateAuthorizations"
      >
        {{ __('Update Authorizations') }}
      </PrimaryButton>
    </template>
  </FormSection>
</template>

<script setup>
import { inject } from 'vue';
import { useForm } from '@inertiajs/vue3';
import FormSection from '@/Components/FormElements/FormSection.vue';
import Radio from '@/Components/FormElements/Radio.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import Alert from '@/Components/Alert.vue';
import merge from 'lodash/merge';

const route = inject('route');

const props = defineProps({
  user: Object,
  abilities: Array,
  allowedAbilities: Array,
  forbiddenAbilities: Array,
  unAllowedAbilities: Array
});

const allowed = props.allowedAbilities.reduce((carry, ability) => {
    carry[ability.name] = 'allowed';
    return carry;
}, {});

const forbidden = props.forbiddenAbilities.reduce((carry, ability) => {
    carry[ability.name] = 'forbidden';
    return carry;
}, {});

const form = useForm({
  userAbilities: merge(allowed, forbidden)
});

const updateAuthorizations = () => {
    form.put(route('users.authorizations.update', props.user.id), {
        preserveScroll: true
    });
};
</script>
