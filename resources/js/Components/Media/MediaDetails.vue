<script setup>
import { inject, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { ArrowLeftIcon, ArrowRightIcon, XMarkIcon, ArrowDownTrayIcon } from '@heroicons/vue/24/outline'
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import TextareaInput from '@/Components/FormElements/TextareaInput.vue';
import Checkbox from '@/Components/FormElements/Checkbox.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import DangerButton from '@/Components/FormElements/DangerButton.vue';
import DeleteMedia from './DeleteMedia.vue';
import {__} from '@/Composables/Translate';

const route = inject('route');

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  media: {
    type: Object,
    required: true
  },
  resource: {
    type: String,
    required: true
  },
  resourceId: {
    type: Number,
    required: true
  }
});

const emit = defineEmits(['close', 'previous', 'next']);

const showDeleteMedia = ref(false);

let form = useForm({
  resource: props.resource,
  resource_id: props.resourceId,
  name: props.media.name,
  obtained_at: props.media.custom_properties.obtained_at,
  source: props.media.custom_properties.source || '',
  description: props.media.custom_properties.description || '',
  is_evidence: props.media.custom_properties.is_evidence || false,
  is_correction: props.media.custom_properties.is_correction || false,
});

const close = () => emit('close');
const previous = () => emit('previous');
const next = () => emit('next');

const updateMedia = () => {
  form.put(route('media.update', {media: props.media.id}), {
      preserveScroll: true,
      onSuccess: () => close()
  })
};

</script>

<template>
  <TransitionRoot
    as="template"
    :show="show"
  >
    <Dialog
      as="div"
      class="relative z-50"
      @close="close()"
    >
      <TransitionChild
        as="template"
        enter="ease-in-out duration-500"
        enterFrom="opacity-0"
        enterTo="opacity-100"
        leave="ease-in-out duration-500"
        leaveFrom="opacity-100"
        leaveTo="opacity-0"
      >
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
      </TransitionChild>

      <div class="fixed inset-0 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
          <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
            <TransitionChild
              as="template"
              enter="transform transition ease-in-out duration-500 sm:duration-700"
              enterFrom="translate-x-full"
              enterTo="translate-x-0"
              leave="transform transition ease-in-out duration-500 sm:duration-700"
              leaveFrom="translate-x-0"
              leaveTo="translate-x-full"
            >
              <DialogPanel class="pointer-events-auto w-screen max-w-2xl">
                <div class="flex h-full flex-col overflow-y-scroll bg-white py-6 shadow-xl">
                  <div class="px-4 sm:px-6">
                    <div class="flex items-start justify-between">
                      <div class="flex items-center">
                        <SecondaryButton @click="close()">
                          <span class="sr-only">Close panel</span>
                          <XMarkIcon
                            class="h-4 w-4"
                            aria-hidden="true"
                          />
                        </SecondaryButton>
                      </div>
                      <div class="ml-3 flex items-center">
                        <SecondaryButton
                          class="mr-4"
                          @click="previous()"
                        >
                          <span class="sr-only">Previous attachment</span>
                          <ArrowLeftIcon
                            class="h-4 w-4"
                            aria-hidden="true"
                          />
                        </SecondaryButton>
                        <SecondaryButton @click="next()">
                          <span class="sr-only">Next attachment</span>
                          <ArrowRightIcon
                            class="h-4 w-4"
                            aria-hidden="true"
                          />
                        </SecondaryButton>
                      </div>
                    </div>
                  </div>
                  <div class="relative mt-6 flex-1 px-4 sm:px-6">
                    <div class="flex flex-1 justify-center overflow-hidden">
                      <img
                        :src="media.medium_url"
                        :alt="media.file_name"
                        class="object-cover"
                      >
                    </div>
                    <div class="mt-4 flex items-start justify-between">
                      <div>
                        <DialogTitle>
                          <h2 class="text-lg font-medium text-gray-900">
                            <span class="sr-only">Details for </span>{{ media.file_name }}
                          </h2>
                        </DialogTitle>
                        <p class="text-sm font-medium text-gray-500">
                          {{ media.human_readable_size }}
                        </p>
                      </div>
                      <a
                        :href="media.original_url"
                        target="attachment"
                        download
                        class="ml-4 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                      >
                        <ArrowDownTrayIcon
                          class="h-6 w-6"
                          aria-hidden="true"
                        />
                        <span class="sr-only">Full Screen</span>
                      </a>
                    </div>
                    <div class="grid grid-cols-6 gap-6 mt-4">
                      <div class="col-span-6 sm:col-span-3">
                        <InputLabel for="name">
                          Name
                        </InputLabel>
                        <TextInput
                          id="name"
                          v-model="form.name"
                          autocomplete="off"
                          class="mt-1"
                        />
                      </div>
                      <div class="col-span-6 sm:col-span-3">
                        <InputLabel for="obtained_at">
                          Date
                        </InputLabel>
                        <DatePicker
                          id="obtained_at"
                          v-model="form.obtained_at"
                          class="mt-1"
                        />
                      </div>
                      <div class="col-span-6">
                        <InputLabel for="source">
                          Source
                        </InputLabel>
                        <TextInput
                          v-model="form.source"
                          name="source"
                          autocomplete="off"
                          class="mt-1"
                        />
                      </div>
                      <div class="col-span-6">
                        <InputLabel for="description">
                          Description
                        </InputLabel>
                        <TextareaInput
                          v-model="form.description"
                          name="description"
                          autocomplete="off"
                          class="mt-1"
                        />
                      </div>
                      <div class="col-span-6 flex items-start">
                        <div class="flex items-center h-5">
                          <Checkbox
                            id="is_evidence"
                            v-model="form.is_evidence"
                            name="is_evidence"
                          />
                        </div>
                        <div class="ml-3 text-sm">
                          <InputLabel
                            for="is_evidence"
                            class="font-medium text-gray-700"
                          >
                            {{ __('This attachment is considered evidence.') }}
                          </InputLabel>
                        </div>
                      </div>
                      <div class="col-span-6 flex items-start">
                        <div class="flex items-center h-5">
                          <Checkbox
                            id="is_correction"
                            v-model="form.is_correction"
                            name="is_correction"
                          />
                        </div>
                        <div class="ml-3 text-sm">
                          <InputLabel
                            for="is_correction"
                            class="font-medium text-gray-700"
                          >
                            {{ __('This attachment is a correction for a previous one.') }}
                          </InputLabel>
                        </div>
                      </div>
                    </div>
                    <div class="mt-4 flex justify-between space-y-3 sm:space-y-0 sm:space-x-3">
                      <PrimaryButton @click="updateMedia">
                        {{ __('Update Details') }}
                      </PrimaryButton>
                      <DangerButton @click="showDeleteMedia = true">
                        {{ __('Delete') }}
                      </DangerButton>
                    </div>
                  </div>
                </div>
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
  <DeleteMedia
    v-if="showDeleteMedia"
    :resource="resource"
    :resourceId="resourceId"
    :media="media"
    :show="true"
    @close="showDeleteMedia = false"
  />
</template>
