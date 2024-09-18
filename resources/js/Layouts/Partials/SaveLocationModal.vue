<script setup>
import {ref, computed, onMounted} from 'vue';
import {useForm, usePage} from '@inertiajs/vue3';
import { formatISO9075 } from 'date-fns';
import DialogModal from '@/Components/DialogModal.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import Autocomplete from '@/Components/FormElements/Autocomplete.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import LocalStorage from '@/Composables/LocalStorage';
import axios from 'axios';
import {__} from '@/Composables/Translate';

const localStorage = LocalStorage();

const props = defineProps({
  patientId: {
      type: Number,
      required: true
  },
  location: {
      type: Object,
      default: () => { return {} }
  },
  show: Boolean,
  title: {
      type: String,
      default: ''
  }
});

const emit = defineEmits(['close']);

const form = useForm({
    moved_in_at: props.location.patient_location_id ? props.location.moved_in_at_local : formatISO9075(new Date()),
    facility_id: props.location.patient_location_id ? props.location.facility_id : usePage().props.locationOptionUiBehaviorIds.clinicFacilityId,
    area: props.location.patient_location_id ? props.location.area : '',
    enclosure: props.location.patient_location_id ? props.location.enclosure : '',
    comments: props.location.patient_location_id ? props.location.comments : ''
});

const volunteers = ref([]);

const facilityIsHomecare = computed(() => Number(form.facility_id) === usePage().props.locationOptionUiBehaviorIds.homecareFacilityId);
const areas = computed(() => facilityIsHomecare.value ? volunteers.value : usePage().props.options.areaOptions || []);
const enclosures = computed(() => facilityIsHomecare.value ? [] : usePage().props.options.enclosureOptions || []);

onMounted(() => {
  if (localStorage.status('volunteers')) {
      volunteers.value = localStorage.get('volunteers');
  } else  {
      axios.get(route('people.search', {is_volunteer: true})).then(response => {
        let names = response.data.map(person => person.full_name);
        localStorage.store('volunteers', names, 7200); // 2 hours
        volunteers.value = names;
      });
  }
});

const close = () => emit('close');

const save = () => {
  if (props.location.patient_location_id) {
      update();
      return;
  }
  store();
};

const store = () => {
    form.post(route('patients.location.store', {
        patient: props.patientId
    }), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            close();
        }
    });
};

const update = () => {
    form.put(route('patients.location.update', {
        patient: props.patientId,
        location: props.location.patient_location_id
    }), {
        preserveScroll: true,
        onSuccess: () => close()
    });
};
</script>

<template>
  <DialogModal
    :show="show"
    @close="close"
  >
    <template #title>
      {{ title }}
    </template>
    <template #content>
      <div
        id="SaveLocationModal"
        class="space-y-2"
      >
        <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center">
          <InputLabel
            for="moved_in_at"
            class="sm:text-right"
          >
            {{ __('Date Moved') }}
          </InputLabel>
          <div class="col-span-2 mt-1 sm:mt-0">
            <DatePicker
              id="moved_in_at"
              v-model="form.moved_in_at"
              time
            />
            <InputError
              :message="form.errors.moved_in_at"
              class="mt-2"
            />
          </div>
        </div>
        <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center">
          <InputLabel
            for="facility_id"
            class="sm:text-right"
          >
            {{ __('Facility') }}
          </InputLabel>
          <div class="col-span-2 mt-1 sm:mt-0">
            <SelectInput
              v-model="form.facility_id"
              name="facility_id"
              :options="$page.props.options.patientLocationFacilitiesOptions"
            />
            <InputError
              :message="form.errors.facility_id"
              class="mt-2"
            />
          </div>
        </div>
        <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center">
          <InputLabel
            for="area"
            class="sm:text-right"
          >
            <template v-if="facilityIsHomecare">
              {{ __('Caregiver') }}
            </template>
            <template v-else>
              {{ __('Area / Room') }}
            </template>
          </InputLabel>
          <div class="col-span-2 mt-1 sm:mt-0">
            <Autocomplete
              v-model="form.area"
              name="area"
              :source="areas"
            />
            <InputError
              :message="form.errors.area"
              class="mt-2"
            />
          </div>
          <InputLabel
            for="enclosure"
            class="sm:text-right"
          >
            <template v-if="facilityIsHomecare">
              {{ __('Address') }}
            </template>
            <template v-else>
              {{ __('Enclosure') }}
            </template>
          </InputLabel>
          <div class="col-span-2 mt-1 sm:mt-0">
            <Autocomplete
              v-model="form.enclosure"
              name="enclosure"
              :source="enclosures"
            />
          </div>
        </div>
        <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center">
          <InputLabel
            for="comments"
            class="sm:text-right"
          >
            {{ __('Comments') }}
          </InputLabel>
          <div class="col-span-5 mt-1 sm:mt-0">
            <TextInput
              v-model="form.comments"
              name="comments"
            />
          </div>
        </div>
      </div>
    </template>
    <template #footer>
      <SecondaryButton @click="close">
        {{ __('Nevermind') }}
      </SecondaryButton>
      <PrimaryButton
        class="ml-2"
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
        @click="save"
      >
        {{ __('Save Location') }}
      </PrimaryButton>
    </template>
  </DialogModal>
</template>
