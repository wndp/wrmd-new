<script setup>
import {ref} from 'vue';
import {useForm} from '@inertiajs/vue3';
import Panel from '@/Components/Panel.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import { UserPlusIcon } from '@heroicons/vue/24/outline';
import debounce from 'lodash/debounce';

const emit = defineEmits(['use']);

const people = ref([])

const form = useForm({
    organization: null,
    full_name: null,
    phone: null,
    email: null,
});

const search = (e) => {
  if (e.keyCode === 13) {
      e.preventDefault();
      e.stopPropagation();
      return;
  }

  people.value = [];

  if (form.isDirty()) {
    debounceSearch();
  }
};

const debounceSearch = debounce(function() {
  window.axios.get(route('people.search'), {
      params: form
  }).then(response => {
      people.value = response.data.slice(0, 5);
  });
}, 500);

const usePerson = (person) => {
  emit('use', person);
  form.reset();
  people.value = ref([]);
};

//const shouldDoSearch = () => [form.organization, form.full_name, form.phone, form.email].join('').trim().length > 2;
</script>

<template>
  <Panel class="rounded-t-none">
    <template #title>
      {{ __('Search For Known People') }}
    </template>
    <div class="flex flex-col">
      <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
          <div class="overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-blue-100">
                <tr>
                  <th
                    scope="col"
                    class="px-3 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                    style="width: 75px"
                  />
                  <th
                    scope="col"
                    class="px-3 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                  >
                    <Input
                      v-model="form.organization"
                      :placeholder="__('Organization')"
                      name="search-organization"
                      @keyup="search"
                    />
                  </th>
                  <th
                    scope="col"
                    class="px-3 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                  >
                    <TextInput
                      v-model="form.full_name"
                      :placeholder="__('Name')"
                      name="search-name"
                      @keyup="search"
                    />
                  </th>
                  <th
                    scope="col"
                    class="px-3 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                  >
                    <TextInput
                      v-model="form.phone"
                      :placeholder="__('Phone number')"
                      name="search-phone"
                      @keyup="search"
                    />
                  </th>
                  <th
                    scope="col"
                    class="px-3 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                  >
                    <TextInput
                      v-model="form.email"
                      :placeholder="__('Email')"
                      name="search-email"
                      @keyup="search"
                    />
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr
                  v-for="person in people"
                  :key="person.id"
                >
                  <td class="px-3 py-3 whitespace-nowrap text-right text-sm">
                    <PrimaryButton @click="usePerson(person)">
                      <UserPlusIcon class="w-4 h-4" />
                    </PrimaryButton>
                  </td>
                  <td class="px-3 py-3 whitespace-nowrap text-sm">
                    {{ person.organization }}
                  </td>
                  <td class="px-3 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ person.full_name }}
                  </td>
                  <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-500">
                    {{ person.phone }}
                  </td>
                  <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-500">
                    {{ person.email }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </Panel>
</template>
