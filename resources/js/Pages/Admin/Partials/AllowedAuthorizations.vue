<script setup>
import {computed} from 'vue';
import {useForm} from '@inertiajs/vue3';
import Checkbox from '@/Components/FormElements/Checkbox.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import snakeCase from 'lodash/snakeCase';

const props = defineProps({
  roles: {
    type: Array,
    required: true
  },
  abilities: {
    type: Array,
    required: true
  }
});

const obj = {};

for (let role of props.roles) {
  obj[role.name] = role.abilities.filter(
    ability => ability.forbidden === 0
  ).map(role => role.name);
}

const form = useForm(obj);

const width = computed(() => 100 / (props.roles.length + 1));

const saveAuthorizations = () => form.put(route('admin.authorization.update', 'allowed'));
</script>

<template>
  <div class="flex flex-col">
    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
      <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
          <table
            id="allowed-abilities-table"
            class="min-w-full divide-y divide-gray-200"
          >
            <caption class="px-2 py-3 text-left text-base font-bold text-gray-700 bg-white ">
              Default Allowed Authorizations
            </caption>
            <thead class="bg-blue-100">
              <tr>
                <th />
                <th
                  v-for="role in roles"
                  :key="role.id"
                  scope="col"
                  class="px-2 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider"
                  :style="`width: ${width}%`"
                >
                  {{ role.title }}
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr
                v-for="(ability, i) in abilities"
                :key="ability.id"
                :class="i % 2 === 0 ? 'bg-white' : 'bg-gray-50'"
              >
                <th class="px-2 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                  {{ ability.title }}
                </th>
                <td
                  v-for="role in roles"
                  :key="role.id+ability.id"
                  class="px-2 py-2 whitespace-nowrap text-center"
                >
                  <Checkbox
                    :id="snakeCase(`allowed-${ability.name}-${role.name}`)"
                    v-model="form[role.name]"
                    :value="ability.name"
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <PrimaryButton
          class="mt-4"
          @click="saveAuthorizations"
        >
          Save Authorizations
        </PrimaryButton>
      </div>
    </div>
  </div>
</template>
