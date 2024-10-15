<script setup>
import {useForm} from '@inertiajs/vue3';
import { CalendarDaysIcon, CreditCardIcon, BanknotesIcon, ChatBubbleBottomCenterTextIcon } from '@heroicons/vue/24/outline';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/FormElements/DangerButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
  person: {
    type: Object,
    required: true
  },
  donation: {
    type: Object,
    required: true
  },
  show: Boolean,
});

const emit = defineEmits(['close']);

const form = useForm({});

const close = () => emit('close');

const deleteDonation = () => {
    form.delete(route('people.donations.destroy', {
        person: props.person,
        donation: props.donation
    }), {
        preserveState: false,
        onSuccess: () => close()
    });
};
</script>

<template>
  <ConfirmationModal
    :show="show"
    maxWidth="2xl"
    @close="close"
  >
    <template #title>
      {{ __('Delete Donation') }}
    </template>
    <template #content>
      <strong>{{ __('Are you sure you want to delete this donation?') }}</strong>
      <dl>
        <div class="mt-4 flex w-full flex-none gap-x-4">
          <dt class="flex-none">
            <span class="sr-only">{{ __('Date') }}</span>
            <CalendarDaysIcon
              class="h-6 w-5 text-gray-600"
              aria-hidden="true"
            />
          </dt>
          <dd class="text-sm leading-6 text-gray-700">
            <time datetime="2023-01-31">{{ donation.donated_at_for_humans }}</time>
          </dd>
        </div>
        <div class="mt-2 flex w-full flex-none gap-x-4">
          <dt class="flex-none">
            <span class="sr-only">{{ __('Method') }}</span>
            <CreditCardIcon
              class="h-6 w-5 text-gray-600"
              aria-hidden="true"
            />
          </dt>
          <dd class="text-sm leading-6 text-gray-700">
            <time datetime="2023-01-31">{{ donation.method }}</time>
          </dd>
        </div>
        <div class="mt-2 flex w-full flex-none gap-x-4">
          <dt class="flex-none">
            <span class="sr-only">{{ __('Value') }}</span>
            <BanknotesIcon
              class="h-6 w-5 text-gray-600"
              aria-hidden="true"
            />
          </dt>
          <dd class="text-sm leading-6 text-gray-700">
            <time datetime="2023-01-31">{{ donation.value_for_humans }}</time>
          </dd>
        </div>
        <div class="mt-2 flex w-full flex-none gap-x-4">
          <dt class="flex-none">
            <span class="sr-only">{{ __('Comments') }}</span>
            <ChatBubbleBottomCenterTextIcon
              class="h-6 w-5 text-gray-600"
              aria-hidden="true"
            />
          </dt>
          <dd class="text-sm leading-6 text-gray-700">
            {{ donation.comments }}
          </dd>
        </div>
      </dl>
    </template>
    <template #footer>
      <SecondaryButton @click="close">
        {{ __('Nevermind') }}
      </SecondaryButton>
      <DangerButton
        class="ml-2"
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
        @click="deleteDonation"
      >
        {{ __('Delete Donation') }}
      </DangerButton>
    </template>
  </ConfirmationModal>
</template>
