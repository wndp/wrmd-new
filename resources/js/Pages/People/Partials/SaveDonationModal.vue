<script setup>
import {useForm} from '@inertiajs/vue3';
import {formatISO} from 'date-fns';
import DialogModal from '@/Components/DialogModal.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import TextareaInput from '@/Components/FormElements/TextareaInput.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
  person: {
    type: Object,
    required: true
  },
  donation: {
    type: Object,
    default: () => ({})
  },
  donationMethodIsCashId: {
    type: Number,
    required: true
  },
  show: Boolean,
});

const emit = defineEmits(['close']);

const form = useForm({
    donated_at: props.donation.id ? props.donation.donated_at : formatISO(new Date()),
    method_id: props.donation.id ? props.donation.method_id : props.donationMethodIsCashId,
    value: props.donation.id ? props.donation.value_for_input : '',
    comments: props.donation.id ? props.donation.comments : ''
});

const close = () => emit('close');

const save = () => {
    if (props.donation.id) {
        update();
        return;
    }
    store();
};

const store = () => {
    form.post(route('people.donations.store', {
        person: props.person
    }), {
        preserveScroll: true,
        onSuccess: () => {
          form.reset();
          close();
        }
    });
};

const update = () => {
    form.put(route('people.donations.update', {
        person: props.person,
        donation: props.donation
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
      {{ donation.id ? __('Update Donation') : __('Create Donation') }}
    </template>
    <template #content>
      <div class="space-y-2">
        <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center">
          <InputLabel
            for="donated_at"
            class="sm:text-right"
          >
            {{ __('Donation Date') }}
          </InputLabel>
          <div class="col-span-2 mt-1 sm:mt-0">
            <DatePicker
              id="donated_at"
              v-model="form.donated_at"
            />
            <InputError
              :message="form.errors.donated_at"
              class="mt-2"
            />
          </div>
        </div>
        <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center">
          <InputLabel
            for="method_id"
            class="sm:text-right"
          >
            {{ __('Method') }}
          </InputLabel>
          <div class="col-span-2 mt-1 sm:mt-0">
            <SelectInput
              v-model="form.method_id"
              name="method_id"
              :options="$page.props.options.donationMethodsOptions"
            />
            <InputError
              :message="form.errors.method_id"
              class="mt-2"
            />
          </div>
          <InputLabel
            for="value"
            class="sm:text-right"
          >
            {{ __('Value') }}
          </InputLabel>
          <div class="col-span-2 mt-1 sm:mt-0">
            <TextInput
              v-model="form.value"
              name="value"
              type="number"
            />
            <InputError
              :message="form.errors.value"
              class="mt-2"
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
            <TextareaInput
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
        {{ __('Save Donation') }}
      </PrimaryButton>
    </template>
  </DialogModal>
</template>
