<script setup>
import {ref} from 'vue';
import {useForm} from '@inertiajs/vue3';
import { ArrowsPointingOutIcon, PrinterIcon, AtSymbolIcon, ArrowDownTrayIcon } from '@heroicons/vue/24/outline';
import DialogModal from '@/Components/DialogModal.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import TextareaInput from '@/Components/FormElements/TextareaInput.vue';
import Checkbox from '@/Components/FormElements/Checkbox.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import axios from 'axios';
import {__} from '@/Composables/Translate';

const props = defineProps({
  url: String,
  filters64: String,
  report: Object
});

const emit = defineEmits(['creating', 'fullScreen']);

const emailReportModal = ref(false);

const form = useForm({
    to: '',
    bcc_me: false,
    subject: '',
    body: ''
});

const generatePdf = () => {
    emit('creating');
    axios.post(`${props.url}&format=pdf`);
};

const generateEmail = () => emailReportModal.value = true;

const generateXlsx = () => {
    emit('creating');
    axios.post(`${props.url}&format=xlsx`);
};

const emailReport = () => {
    form.post(`/reports/email/${props.report.key}?format=pdf&filters=${props.filters64}`, {
        preserveScroll: true,
        onSuccess: () => {
            emailReportModal.value = false;
            form.reset();
        }
    });
};
</script>

<template>
  <span class="relative z-0 inline-flex shadow-sm rounded-md">
    <button
      type="button"
      class="relative inline-flex items-center px-4 py-2 rounded-l-md border border-blue-300 bg-blue-500 text-base font-medium text-white hover:bg-blue-60 focus:z-10 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
      @click="$emit('fullScreen')"
    >
      <ArrowsPointingOutIcon class="h-6 w-6 mr-2" />
      {{ __('Full Screen') }}
    </button>
    <button
      type="button"
      class="relative inline-flex items-center px-4 py-2 border border-blue-300 bg-blue-500 text-base font-medium text-white hover:bg-blue-60 focus:z-10 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
      @click="generatePdf"
    >
      <PrinterIcon class="h-6 w-6 mr-2" />
      {{ __('Print Report') }}
    </button>
    <button
      v-if="report.canExport"
      type="button"
      class="-ml-px relative inline-flex items-center px-4 py-2 border border-blue-300 bg-blue-500 text-base font-medium text-white hover:bg-blue-60 focus:z-10 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
      @click="generateXlsx"
    >
      <ArrowDownTrayIcon class="h-6 w-6 mr-2" />
      {{ __('Export Report') }}
    </button>
    <button
      type="button"
      class="-ml-px relative inline-flex items-center px-4 py-2 rounded-r-md border border-blue-300 bg-blue-500 text-base font-medium text-white hover:bg-blue-60 focus:z-10 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
      @click="generateEmail"
    >
      <AtSymbolIcon class="h-6 w-6 mr-2" />
      {{ __('Email Report') }}
    </button>
  </span>
  <DialogModal
    :show="emailReportModal"
    @close="emailReportModal = false"
  >
    <template #title>
      {{ __('Email Report as PDF') }}
    </template>

    <template #content>
      <div class="grid grid-cols-4 gap-4 sm:items-start sm:pt-5">
        <InputLabel class="sm:mt-px sm:pt-2">
          {{ __('Attachment') }}
        </InputLabel>
        <p class="col-span-3 max-w-2xl text-base text-gray-500 sm:mt-px sm:pt-2">
          {{ `${report.titleSlug}.pdf` }}
        </p>
        <InputLabel class="sm:mt-px sm:pt-2">
          {{ __('From') }}
        </InputLabel>
        <p class="col-span-3 mt-1 max-w-2xl text-base text-gray-500 sm:mt-px sm:pt-2">
          {{ $page.props.auth.user.email }}
        </p>
      </div>
      <div class="md:grid sm:grid-cols-4 md:gap-4 md:items-start sm:pt-5">
        <InputLabel
          for="to"
          class="mt-4 sm:mt-px sm:pt-2"
        >
          {{ __('To') }}
        </InputLabel>
        <div class="mt-1 sm:mt-0 md:col-span-3">
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
        <div class="mt-4 md:mt-0 md:col-start-2 md:col-span-3">
          <div class="relative flex items-start mr-8">
            <div class="flex items-center h-5">
              <Checkbox
                id="bcc_me"
                v-model="form.bcc_me"
              />
            </div>
            <div class="ml-3 text-sm">
              <InputLabel
                for="bcc_me"
                class="text-gray-500"
              >{{ __('Send me a copy') }}</InputLabel>
            </div>
          </div>
        </div>
        <InputLabel
          for="subject"
          class="mt-4 sm:mt-px sm:pt-2"
        >
          {{ __('Subject') }}
        </InputLabel>
        <div class="mt-1 sm:mt-0 md:col-span-3">
          <TextInput
            id="subject"
            v-model="form.subject"
            name="subject"
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
          class="mt-4 sm:mt-px sm:pt-2"
        >
          {{ __('Message') }}
        </InputLabel>
        <div class="mt-1 sm:mt-0 md:col-span-3">
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
      </div>
    </template>
    <template #footer>
      <SecondaryButton @click="emailReportModal = false">
        {{ __('Nevermind') }}
      </SecondaryButton>
      <PrimaryButton
        class="ml-2"
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
        @click="emailReport"
      >
        {{ __('Send Email') }}
      </PrimaryButton>
    </template>
  </DialogModal>
</template>
