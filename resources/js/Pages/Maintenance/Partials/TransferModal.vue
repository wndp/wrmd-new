<script setup>
import {ref, computed} from 'vue';
import {useForm, usePage} from '@inertiajs/vue3';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import Alert from '@/Components/Alert.vue';
import AlertAction from '@/Components/AlertAction.vue';
import Badge from '@/Components/Badge.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import DangerButton from '@/Components/FormElements/DangerButton.vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
  transfer: {
    type: Object,
    required: true
  },
  show: Boolean,
});

const emit = defineEmits(['close']);

//const location = ref({});
const form = useForm({});

const isTransferToAuthAccount = computed(() => props.transfer.to_team_id === usePage().props.auth.team.id);

const close = () => emit('close');

const uncollaborate = () => form.post(route('maintenance.transfers.uncollaborate', {uuid: props.transfer.uuid}));

const caseNumberFrom = (transfer) => {
  return transfer.patient.admissions.find(admission =>
    admission.team_id === transfer.from_team_id
  ).case_number;
};

const caseNumberTo = (transfer) => {
  let patient = transfer.is_collaborative ? transfer.patient : transfer.cloned_patient;

  return patient.admissions.find(admission =>
    admission.team_id === transfer.to_team_id
  )?.case_number;
};

const getStatus = (transfer) => {
  if (transfer.responded_at === null) return __('No Response');
  return transfer.is_accepted ? __('Accepted') : __('Denied');
};

const getStatusColor = (transfer) => {
  if (transfer.responded_at === null) return 'orange';
  return transfer.is_accepted ? 'green' : 'red';
};

const acceptTransfer = () => {
  form.post(route('share.transfer.accept', {uuid: props.transfer.uuid}), {
    onSuccess: () => close(),
  })
};

const denyTransfer = () => {
  form.delete(route('share.transfer.deny', {uuid: props.transfer.uuid}), {
    onSuccess: () => close(),
  })
};
</script>

<template>
  <DialogModal
    :show="show"
    maxWidth="2xl"
    @close="close"
  >
    <template #title>
      {{ __('Transfer Request') }}
    </template>
    <template #content>
      <Alert
        v-if="transfer.is_collaborative && transfer.is_accepted"
        color="red"
      >
        <p><strong class="font-bold">{{ __('This is a collaborative transfer.') }}</strong> {{ __('All updates to the patient can be seen by both organizations.') }}</p>
        <p class="mt-2">
          {{ __('This patient can be un-collaborated by either organization. Once you un-collaborate a patient, there is no going back. Please be certain. It CANNOT be undone.') }}
        </p>
        <AlertAction
          color="red"
          @click="uncollaborate"
        >
          {{ __('Un-collaborate this patient') }}
        </AlertAction>
      </Alert>
      <div class="border-t border-gray-200 mt-4">
        <dl class="sm:divide-y sm:divide-gray-200">
          <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
            <dt class="text-sm font-medium text-gray-500">
              {{ __('Common Name') }}
            </dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              {{ transfer.patient.common_name }}
            </dd>
          </div>
          <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
            <dt class="text-sm font-medium text-gray-500">
              {{ __('Transfer From') }}
            </dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              <div class="flex">
                <div class="mr-4 flex-shrink-0 self-center">
                  <img
                    class="h-10 w-10 rounded-full"
                    :src="transfer.from_account.profile_photo_url"
                    alt=""
                  >
                </div>
                <div>
                  <h4 class="text-sm text-gray-900">
                    {{ transfer.from_account.organization }}
                  </h4>
                  <p class="text-gray-500 mt-1">
                    {{ __('Patient') }}: <span class="text-gray-900">{{ caseNumberFrom(transfer) }}</span>
                  </p>
                </div>
              </div>
            </dd>
          </div>
          <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
            <dt class="text-sm font-medium text-gray-500">
              {{ __('Transfer To') }}
            </dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              <div class="flex">
                <div class="mr-4 flex-shrink-0 self-center">
                  <img
                    class="h-10 w-10 rounded-full"
                    :src="transfer.to_account.profile_photo_url"
                    alt=""
                  >
                </div>
                <div>
                  <h4 class="text-sm text-gray-900">
                    {{ transfer.to_account.organization }}
                  </h4>
                  <p
                    v-if="transfer.responded_at !== null && transfer.is_accepted"
                    class="text-gray-500 mt-1"
                  >
                    {{ __('Patient') }}: <span class="text-gray-900">{{ caseNumberTo(transfer) }}</span>
                  </p>
                </div>
              </div>
            </dd>
          </div>
          <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
            <dt class="text-sm font-medium text-gray-500">
              {{ __('Date Sent') }}
            </dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              {{ transfer.created_at_for_humans }}
            </dd>
          </div>
          <div
            v-if="transfer.responded_at !== null"
            class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4"
          >
            <dt class="text-sm font-medium text-gray-500">
              {{ __('Date Responded') }}
            </dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              {{ transfer.responded_at_for_humans }}
            </dd>
          </div>
          <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
            <dt class="text-sm font-medium text-gray-500">
              {{ __('Status') }}
            </dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              <Badge :color="getStatusColor(transfer)">
                {{ getStatus(transfer) }}
              </Badge>
            </dd>
          </div>
          <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
            <dt class="text-sm font-medium text-gray-500">
              {{ __('Collaborative') }}
            </dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              <Badge color="blue">
                {{ transfer.is_collaborative ? __('Yes') : __('No') }}
              </Badge>
              <div
                v-if="transfer.responded_at === null && transfer.is_collaborative && ! transfer.is_accepted"
                class="text-gray-500 mt-1"
              >
                <span class="font-bold">*</span> {{ __('This patient can be un-collaborated after the transfer request is accepted.') }}
              </div>
            </dd>
          </div>
          <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
            <dt class="text-sm font-medium text-gray-500">
              {{ __('Transfer ID') }}
            </dt>
            <dd class="mt-1 text-sm text-gray-500 sm:mt-0 sm:col-span-2">
              {{ transfer.uuid }}
            </dd>
          </div>
        </dl>
      </div>
      <div
        v-if="isTransferToAuthAccount && transfer.responded_at === null && can('create-patients')"
        class="flex justify-between items-center border-t border-gray-200 pt-4"
      >
        <PrimaryButton @click="acceptTransfer">
          {{ __('Accept this Transfer') }}
        </PrimaryButton>
        <DangerButton @click="denyTransfer">
          {{ __('Deny this Transfer') }}
        </DangerButton>
      </div>
    </template>
    <template #footer>
      <SecondaryButton @click="close">
        {{ isTransferToAuthAccount && transfer.responded_at === null ? __("Don't Answer Yet") : __('Close') }}
      </SecondaryButton>
    </template>
  </DialogModal>
</template>
