<script setup>
import {useForm} from '@inertiajs/vue3';
import DialogModal from '@/Components/DialogModal.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import TextareaInput from '@/Components/FormElements/TextareaInput.vue';
import Checkbox from '@/Components/FormElements/Checkbox.vue';
import CheckboxCombobox from '@/Components/FormElements/CheckboxCombobox.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
  show: Boolean,
  patient: {
      type: Object,
      default: () => ({})
  }
});

const emit = defineEmits(['close']);

const form = useForm({
  to: '',
  bcc_me: false,
  subject: '',
  body: '',
  include: []
});

const close = () => emit('close');

const send = () => form.post(route('share.email.store', {
  patient: props.patient?.id
}), {
  preserveScroll: true,
  onSuccess: () => {
    form.reset();
    close();
  }
});
</script>

<template>
  <DialogModal
    :show="show"
    @close="close"
  >
    <template #title>
      {{ __('Email Patient Record') }}
    </template>
    <template #content>
      <div class="sm:grid sm:grid-cols-2 sm:gap-4 sm:items-start sm:pt-5 md:grid-cols-4">
        <InputLabel class="mt-3 sm:mt-0">
          {{ __('Attachment') }}
        </InputLabel>
        <p class="sm:col-span-3 max-w-2xl text-base text-gray-500 mt-1 sm:mt-0">
          patients.pdf
        </p>
        <InputLabel class="mt-3 sm:mt-0">
          {{ __('From') }}
        </InputLabel>
        <p class="sm:col-span-3 mt-1 max-w-2xl text-base text-gray-500 mt-1 sm:mt-0">
          {{ $page.props.auth.user.email }}
        </p>
        <InputLabel
          for="to"
          class="mt-3 sm:mt-0"
        >
          {{ __('To') }}
        </InputLabel>
        <div class="mt-1 sm:mt-0 sm:col-span-3">
          <TextInput
            id="to"
            v-model="form.to"
            name="to"
            type="email"
            required
            autocomplete="email"
          />
          <InputError
            :message="form.errors.to"
            class="mt-2"
          />
        </div>
        <div class="mt-3 md:mt-0 md:col-start-2 md:col-span-3">
          <div class="relative flex items-start mr-8">
            <div class="flex items-center h-5">
              <Checkbox
                id="bcc_me"
                v-model="form.bcc_me"
                name="bcc_me"
              />
            </div>
            <div class="ml-3 text-sm">
              <InputLabel for="bcc_me">{{ __('Send me a copy') }}</InputLabel>
            </div>
          </div>
          <InputError
            :message="form.errors.bcc_me"
            class="mt-2"
          />
        </div>
        <InputLabel
          for="subject"
          class="mt-3 sm:mt-0"
        >
          {{ __('Subject') }}
        </InputLabel>
        <div class="mt-1 sm:mt-0 sm:col-span-3">
          <TextInput
            id="subject"
            v-model="form.subject"
            name="subject"
            type="text"
            required
            autocomplete="subject"
          />
          <InputError
            :message="form.errors.subject"
            class="mt-2"
          />
        </div>
        <InputLabel
          for="message"
          class="mt-3 sm:mt-0"
        >
          {{ __('Message') }}
        </InputLabel>
        <div class="mt-1 sm:mt-0 sm:col-span-3">
          <TextareaInput
            id="body"
            v-model="form.body"
            name="body"
            rows="3"
            required
          />
          <p class="mt-2 text-sm text-gray-500">
            {{ __('Brief description of your report.') }}
          </p>
          <InputError
            :message="form.errors.body"
            class="mt-2"
          />
        </div>
        <InputLabel
          for="subject"
          class="mt-3 sm:mt-0"
        >
          {{ __('Choose what to include in the emailed patient medical record.') }}
        </InputLabel>
        <div class="mt-1 sm:mt-0 sm:col-span-3">
          <CheckboxCombobox
            id="include"
            v-model="form.include"
            :options="$page.props.options.includableOptions"
            class="mt-1"
          />
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
        @click="send"
      >
        {{ __('Email Patient Record') }}
      </PrimaryButton>
    </template>
  </DialogModal>
</template>
