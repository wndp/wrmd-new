<script setup>
import {ref, onMounted} from 'vue';
import {useForm} from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PeopleTabs from '../Partials/PeopleTabs.vue';
import Panel from '@/Components/Panel.vue';
import Alert from '@/Components/Alert.vue';
import DangerButton from '@/Components/FormElements/DangerButton.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import ValidationErrors from '@/Components/FormElements/ValidationErrors.vue';
import PersonCard from '@/Components/FormCards/PersonCard.vue';
import merge from 'lodash/merge';
import forEach from 'lodash/forEach';
import isNull from 'lodash/isNull';
import {__} from '@/Composables/Translate';

const props = defineProps({
  people: Array,
});

const oldPeople = ref(props.people);

const getMatchingProperties = () => {
    const [firstPerson, ...rest] = props.people;

    return rest.reduce((carry, person) => {
      Object.keys(carry).forEach(key => {
        if (!(key in person) || person[key] !== carry[key]) {
          delete carry[key];
        }
      });
      return carry;
    }, {...firstPerson});
};

const form = useForm({
    oldPeople: [],
    newPerson: merge({
        entity_id: '',
        organization: '',
        first_name: '',
        last_name: '',
        phone: '',
        alt_phone: '',
        email: '',
        subdivision: '',
        city: '',
        address: '',
        county: '',
        notes: '',
        no_solicitations: '',
        is_volunteer: '',
        is_member: '',
    }, getMatchingProperties())
});

onMounted(() => {
  highlightSelectedInputs();

  document.querySelectorAll(".js-person-card input:not([type=hidden]), .js-person-card select, .js-person-card textarea").forEach(item => {
    item.addEventListener('click', setPersonValue, false);
    item.addEventListener('change', setPersonValue, false);
  })
});

const highlightSelectedInputs = () => {
  forEach(form.newPerson, (value, key) => {
    if (! isNull(value) && value !== '') {
      highlightInputs(key)
    }
  })
};

const highlightInputs = (key, color = 'green') => {
  let inputs = document.querySelectorAll(`.js-person-card input:not([type=hidden])[name="${key}"], .js-person-card select[name="${key}"], .js-person-card textarea[name="${key}"]`);
  for (const input of inputs) {
    input.classList.add(`bg-${color}-400`);
  }
};

const setPersonValue = (e) => {
  form.newPerson[e.target.name] = e.target.value;
  highlightInputs(e.target.name, 'red');

  e.target.classList.remove('bg-red-400')
  e.target.classList.add('bg-green-400');
};

const removeOldPerson = (person) => {
  oldPeople.value = oldPeople.value.filter(p => {
      return p.id !== person.id;
  });
};

const mergePeople = () => {
  form.transform(data => ({
      ...data,
      oldPeople: oldPeople.value.map(p => p.id)
  })).post(route('people.combine.merge'));
};
</script>

<template>
  <AppLayout title="Combine People">
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900">
        {{ __('People') }}
      </h1>
    </template>
    <PeopleTabs class="mt-4" />
    <ValidationErrors />
    <Panel class="mt-8">
      <template #title>
        {{ __('Choose the Correct Values') }}
      </template>
      <template #description>
        <strong class="font-bold text-green-700">{{ __('Fields highlighted in green will be used to create the new person.') }}</strong> {{ __('WRMD has already guessed which values are most likely correct. To choose a value from a field that has not already been chosen, click that field and it will be highlighted in green as well.') }} {{ __('Once you have carefully reviewed these selections, click Merge Duplicate People button to combine them together. Any patients or donations that belong to any of these people will be re-associated to the new person.') }}
      </template>
      <template #content>
        <div class="col-span-6 flex justify-between">
          <div class="flex flex-wrap gap-x-8 gap-y-4">
            <div class="inline-flex items-center">
              <span class="h-8 w-8 mr-2 bg-green-400" />
              = {{ __('Selected') }}
            </div>
            <div class="inline-flex items-center">
              <span class="h-8 w-8 mr-2 bg-red-400" />
              = {{ __('Not selected') }}
            </div>
          </div>
          <div class="ml-4 flex-shrink-0">
            <Link
              :href="route('people.combine.search')"
              class="relative inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
            >
              {{ __('Start Over') }}
            </Link>
          </div>
        </div>
      </template>
    </Panel>
    <ol class="list-decimal list-outside pl-8 mt-8 space-y-8">
      <li
        v-for="person in oldPeople"
        :key="person.id"
      >
        <DangerButton @click="removeOldPerson(person)">
          {{ __('Do Not Include This Person') }}
        </DangerButton>
        <PersonCard
          :id="`person-form-${person.id}`"
          :form="person"
          :canSubmit="false"
          class="mt-2 js-person-card"
        />
      </li>
    </ol>

    <Panel class="mt-8">
      <template #title>
        {{ __('Ready to Merge These People?') }}
      </template>
      <template #content>
        <Alert class="col-span-6" color="red">
          {{ __('A new person will be created using the green values from above. The people listed above will be deleted and any patients, donations or other data that belongs to any of these people will be re-associated to the new person.') }}
        </Alert>
      </template>
      <template #actions>
        <div class="flex items-center justify-end text-right">
          <ActionMessage
            :on="form.recentlySuccessful"
            class="mr-3"
          >
            {{ __('Saved.') }}
          </ActionMessage>
          <PrimaryButton
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
            @click="mergePeople"
          >
            {{ __('Merge Duplicate People') }}
          </PrimaryButton>
        </div>
      </template>
    </Panel>
  </AppLayout>
</template>
