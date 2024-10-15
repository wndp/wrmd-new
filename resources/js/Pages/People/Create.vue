<script setup>
import {computed} from 'vue';
import {useForm} from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import {ArrowLongLeftIcon} from '@heroicons/vue/24/outline';
import PersonCard from '@/Components/FormCards/PersonCard.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import LocalStorage from '@/Composables/LocalStorage';
import {__} from '@/Composables/Translate';

const localStorage = LocalStorage();

const form = useForm({
  custom_values: {},
  entity_id: '',
  organization: '',
  first_name: '',
  last_name: '',
  phone: '',
  alternate_phone: '',
  email: '',
  subdivision: '',
  city: '',
  address: '',
  county: '',
  postal_code: '',
  notes: '',
  no_solicitations: true,
  is_volunteer: '',
  is_member: '',
});

const uri = computed(() => localStorage.get('peopleFilters'));

const store = () => {
    form.post(route('people.store'));
};
</script>

<template>
  <AppLayout title="Create New Person">
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900">
        {{ __('Create New Person') }}
      </h1>
      <Link
        :href="route('people.rescuers.index', uri)"
        class="inline-flex items-center text-base leading-5 text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700 transition ease-in-out duration-150 mb-8"
      >
        <ArrowLongLeftIcon class="h-6 w-6 mr-2" />
        {{ __('Return to People') }}
      </Link>
    </template>
    <PersonCard
      :form="form"
      :canSubmit="false"
      class="rounded-b-none"
    />
    <div
      class="px-4 py-2 sm:px-6 bg-gray-50 rounded-b-lg shadow"
    >
      <div class="flex items-center justify-end text-right">
        <ActionMessage
          :on="form.isDirty"
          class="mr-3"
        >
          <span class="text-red-600">{{ __('There are unsaved changes.') }}</span>
        </ActionMessage>
        <PrimaryButton
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
          @click="store"
        >
          {{ __('Create New Person') }}
        </PrimaryButton>
      </div>
    </div>
  </AppLayout>
</template>
