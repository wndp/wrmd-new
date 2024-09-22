<script setup>
import {ref, computed} from 'vue';
import {useForm, usePage} from '@inertiajs/vue3';
import Badge from '@/Components/Badge.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import Toggle from '@/Components/FormElements/Toggle.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import { BookOpenIcon } from '@heroicons/vue/24/outline';
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';
import isNil from 'lodash/isNil';
import {__} from '@/Composables/Translate';
import {can} from '@/Composables/Can';
import {Abilities} from '@/Enums/Abilities';

const props = defineProps({
  patientMeta: {
    type: Object,
    default: () => {}
  }
});

const showModal = ref(false);

const form = useForm({
  is_locked: ! isNil(props.patientMeta.locked_at),
  is_voided: ! isNil(props.patientMeta.voided_at),
  is_criminal_activity: props.patientMeta.is_criminal_activity || false,
  is_resident: props.patientMeta.is_resident,
  keywords: props.patientMeta.keywords
});

const resources = ref([
    props.patientMeta.taxon?.class === 'Aves' && props.patientMeta.taxon?.bow_code ? {
        name: 'Birds of the World',
        logo: new URL('../../../images/birds-of-the-world-logo.svg', import.meta.url).href,
        url: props.patientMeta.taxon?.bow_url
    } : false,
    props.patientMeta.taxon?.inaturalist_taxon_id ? {
        name: 'iNaturalist',
        logo: new URL('../../../images/inaturalist-logo.svg', import.meta.url).href,
        url: props.patientMeta.taxon?.inaturalist_url
    } : false,
    props.patientMeta.taxon?.class === 'Reptilia' ? {
        name: 'The Reptile Database',
        logo: new URL('../../../images/the-reptile-database-logo.svg', import.meta.url).href,
        url: `http://reptile-database.reptarium.cz/species?genus=${props.patientMeta.taxon?.genus}&species=${props.patientMeta.taxon?.species}`
    } : false,
    props.patientMeta.taxon?.iucn_id ? {
        name: 'IUCN Red List',
        logo: new URL('../../../images/iucn-red-list-logo.svg', import.meta.url).href,
        url: props.patientMeta.taxon?.iucn_url
    } : false,
    {
        name: 'Wikipedia',
        logo: new URL('../../../images/wikipedia-logo.svg', import.meta.url).href,
        url: `https://en.wikipedia.org/wiki/${encodeURI(props.patientMeta.taxon?.genus+' '+props.patientMeta.taxon?.species)}`
    },
    {
        name: 'Animal Diversity Web',
        logo: new URL('../../../images/animal-diversity-web-logo.svg', import.meta.url).href,
        url: `https://animaldiversity.org/accounts/${encodeURI(props.patientMeta.taxon?.genus+' '+props.patientMeta.taxon?.species)}`
    },
].filter(Boolean));

const caseQueryString = ref({
  y: usePage().props.admission.case_year,
  c: usePage().props.admission.case_id,
});

// const iucnStatus = computed(() => {
//   let statuses = props.patientMeta.taxon?.metas.filter(meta => meta.key === 'conservation_status') || [];
//   let iucn = statuses.find(status => status.authority === 'IUCN');

//   return iucn || {};
// });

const conservationStatusColor = computed(() => {
  switch (props.patientMeta.taxon?.iucn_conservation_status) {
    case 'EX':
        return 'gray';
    case "EW":
    case "EN":
    case "CR":
        return 'red';
    case "VU":
    case "NT":
        return 'yellow';
    case "LC":
    case "DD":
    case "NE":
    default:
        return 'green';
  }
});

const daysInCareColor = computed(() => {
  if (props.patientMeta.days_in_care < 90) {
      return 'gray';
  } else if (props.patientMeta.days_in_care >= 91 && props.patientMeta.days_in_care <= 120) {
      return 'yellow';
  }
  return 'red';
});

const criminalActivityColor = computed(() => props.patientMeta.is_criminal_activity ? 'yellow' : 'gray');

// const hasTasksDueToday = com

const update = () => {
    form.put(route('patients.meta.update', props.patientMeta.patient_id), {
        preserveScroll: true,
        onSuccess: () => {
            showModal.value = false;
        },
    });
};
</script>

<template>
  <section class="mt-4 flex flex-wrap gap-2">
    <button
      v-if="patientMeta.is_resident"
      class="inline-flex items-center bg-gray-200 rounded-full p-0.5"
      @click="showModal = true"
    >
      <Badge color="green">
        {{ __('Resident?') }}
      </Badge>
      <span class="mx-2 text-xs font-bold text-gray-600">{{ patientMeta.is_resident ? 'Yes' : 'No' }}</span>
    </button>
    <button
      v-if="patientMeta.locked_at"
      class="inline-flex items-center bg-gray-200 rounded-full p-0.5"
      @click="showModal = true"
    >
      <Badge color="blue">
        {{ __('Locked?') }}
      </Badge>
      <span class="mx-2 text-xs font-bold text-gray-600">{{ patientMeta.locked_at ? 'Yes' : 'No' }}</span>
    </button>
    <button
      v-if="patientMeta.incident.id"
      class="inline-flex items-center bg-gray-200 rounded-full p-0.5"
      @click="$inertia.get(route('hotline.incident.edit', patientMeta.incident.id))"
    >
      <Badge color="yellow">
        {{ __('Hotline incident') }}
      </Badge>
      <span class="mx-2 text-xs font-bold text-gray-600">{{ patientMeta.incident.incident_number }}</span>
    </button>
    <button
      v-if="patientMeta.taxon?.iucn_conservation_status"
      class="inline-flex items-center bg-gray-200 rounded-full p-0.5"
      @click="showModal = true"
    >
      <Badge :color="conservationStatusColor">
        {{ __('Conservation Status') }}
      </Badge>
      <span class="mx-2 text-xs font-bold text-gray-600">{{ patientMeta.taxon?.iucn_conservation_status }}</span>
    </button>
    <button
      class="inline-flex items-center bg-gray-200 rounded-full p-0.5"
      @click="showModal = true"
    >
      <Badge :color="daysInCareColor">
        {{ __('Days in care') }}
      </Badge>
      <span class="mx-2 text-xs font-bold text-gray-600">{{ patientMeta.days_in_care }}</span>
    </button>
    <button
      v-if="patientMeta.taxon?.alpha_code"
      class="inline-flex items-center bg-gray-200 rounded-full p-0.5"
      @click="showModal = true"
    >
      <Badge color="gray">
        {{ __('Alpha code') }}
      </Badge>
      <span class="mx-2 text-xs font-bold text-gray-600">{{ patientMeta.taxon?.alpha_code }}</span>
    </button>
    <button
      class="inline-flex items-center bg-gray-200 rounded-full p-0.5"
      @click="showModal = true"
    >
      <Badge :color="criminalActivityColor">
        {{ __('Criminal activity?') }}
      </Badge>
      <span class="mx-2 text-xs font-bold text-gray-600">{{ patientMeta.is_criminal_activity ? 'Yes' : 'No' }}</span>
    </button>
    <Link
      v-if="patientMeta.numberOfTasksDueToday"
      :href="route('patients.daily-tasks.edit', caseQueryString)"
      class="inline-flex items-center bg-gray-200 rounded-full p-0.5"
    >
      <Badge color="red">
        {{ __('Tasks Due Today') }}
      </Badge>
      <span class="mx-2 text-xs font-bold text-gray-600">{{ patientMeta.numberOfTasksDueToday }}</span>
    </Link>
    <Menu
      v-if="patientMeta.taxon?.class"
      as="span"
      class="relative"
    >
      <MenuButton  class="relative inline-flex items-center bg-gray-200 rounded-full p-0.5">
        <Badge color="gree">
          {{ __('Resources') }}
        </Badge>
        <BookOpenIcon class="mx-2 w-4 h-4 text-gray-600" />
      </MenuButton>
      <transition
        enterActiveClass="transition ease-out duration-100"
        enterFromClass="transform opacity-0 scale-95"
        enterToClass="transform opacity-100 scale-100"
        leaveActiveClass="transition ease-in duration-75"
        leaveFromClass="transform opacity-100 scale-100"
        leaveToClass="transform opacity-0 scale-95"
      >
        <MenuItems class="origin-top-right absolute right-0 z-10 mt-2 -mr-1 w-72 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none">
          <MenuItem
            v-for="resource in resources"
            :key="resource.name"
            v-slot="{ active }"
          >
            <a
              :href="resource.url"
              target="_blank"
              :class="[active ? 'bg-gray-100' : '', 'group flex px-2 py-2 text-sm text-gray-800']"
            >
              <img
                :src="resource.logo"
                class="w-5 h-5 mr-2 text-gray-400"
                aria-hidden="true"
              >
              {{ resource.name }}
            </a>
          </MenuItem>
        </MenuItems>
      </transition>
    </Menu>
    <div>
      <button
        v-if="patientMeta.keywords"
        class="inline-flex items-center bg-gray-200 rounded-full p-0.5"
        @click="showModal = true"
      >
        <Badge color="gray">
          {{ __('Keywords') }}
        </Badge>
        <span class="mx-2 text-xs font-bold text-gray-600">{{ patientMeta.keywords }}</span>
      </button>
    </div>
  </section>
  <DialogModal :show="can(Abilities.UPDATE_PATIENT_META) && showModal">
    <template #title>
      {{ __('Other Patient Information') }}
    </template>
    <template #content>
      <div class="mt-6 grid grid-cols-4 gap-6">
        <div class="col-span-4 sm:col-span-2">
          <InputLabel for="is_locked">
            {{ __('Lock patient to prevent any changes?') }}
          </InputLabel>
          <div class="mt-2">
            <Toggle
              v-model="form.is_locked"
              dusk="is_locked"
              :label="__('Locked?')"
            />
            <InputError
              :message="form.errors.is_locked"
              class="mt-2"
            />
          </div>
        </div>
        <div class="col-span-4 sm:col-span-2">
          <InputLabel for="is_voided">
            {{ __('Void patient to prevent any reporting?') }}
          </InputLabel>
          <div class="mt-2">
            <Toggle
              v-model="form.is_voided"
              dusk="is_voided"
              :label="__('Voided?')"
            />
            <InputError
              :message="form.errors.is_voided"
              class="mt-2"
            />
          </div>
        </div>
        <div class="col-span-4 sm:col-span-2">
          <InputLabel for="is_criminal_activity">
            {{ __('Patient has reportable criminal activity?') }}
          </InputLabel>
          <div class="mt-2">
            <Toggle
              v-model="form.is_criminal_activity"
              dusk="is_criminal_activity"
              :label="__('Criminal Activity?')"
            />
            <InputError
              :message="form.errors.is_criminal_activity"
              class="mt-2"
            />
          </div>
        </div>
        <div class="col-span-4 sm:col-span-2">
          <InputLabel for="is_resident">
            {{ __('Is a resident animal?') }}
          </InputLabel>
          <div class="mt-2">
            <Toggle
              v-model="form.is_resident"
              dusk="is_resident"
              :label="__('Resident?')"
            />
            <InputError
              :message="form.errors.is_resident"
              class="mt-2"
            />
          </div>
        </div>
        <div class="col-span-4">
          <InputLabel for="keywords">
            {{ __('Patient Keywords') }}
          </InputLabel>
          <div class="mt-2">
            <TextInput
              v-model="form.keywords"
              name="keywords"
              autoComplete="patients.keywords"
            />
          </div>
        </div>
      </div>
    </template>
    <template #footer>
      <SecondaryButton @click="showModal = false">
        {{ __('Nevermind') }}
      </SecondaryButton>
      <PrimaryButton
        class="ml-2"
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
        @click="update"
      >
        {{ __('Update Patient') }}
      </PrimaryButton>
    </template>
  </DialogModal>
</template>
