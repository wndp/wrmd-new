<script setup>
import {useForm} from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import DangerButton from '@/Components/FormElements/DangerButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
  incident: {
    type: Object,
    required: true
  },
  show: Boolean,
});

const emit = defineEmits(['close']);

const form = useForm({
  incident_number: '',
  password: '',
});

const close = () => emit('close');

const doDelete = () => {
   let routexx = props.incident.deleted_at === null
      ? 'hotline.incident.destroy'
      : 'hotline.deleted.destroy';
    form.delete(route(routexx, {
        incident: props.incident,
    }), {
        preserveState: false,
        //onSuccess: () => this.close(),
        onError: () => form.reset('password'),
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
      {{ __('Delete Incident') }}
    </template>
    <template #content>
      <template v-if="incident.deleted_at === null">
        <strong>{{ __('Are you sure you want to delete this incident?') }}</strong>
        <p class="text-sm text-gray-600 mt-4">
          {{ __('Deleted incidents will be hidden from your incident list and will keep their incident number reserved to maintain a "paper trail" of incident numbers.') }} <strong class="font-bold">{{ __('Deleted incidents can be restored if needed.') }}</strong>
        </p>
      </template>
      <template v-else>
        <strong>{{ __('Are you sure you want to restore this incident?') }}</strong>
        <p class="text-sm text-gray-600 mt-4">
          {{ __('Restored incidents will keep their incident number to maintain a "paper trail" of incident numbers.') }} <strong class="font-bold">{{ __('Restored incidents can be deleted again if needed.') }}</strong>
        </p>
      </template>
      <p class="text-sm text-gray-600 mt-4">
        {{ __('Please provide your password and type in the incident number to confirm which incident you want to :action', {action: incident.deleted_at === null ? __('delete') : __('restore')}) }}.
      </p>
      <div class="space-y-4 sm:space-y-2 text-left mt-4">
        <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center">
          <InputLabel
            for="incident_number"
            class="col-span-2 sm:text-right"
          >
            {{ __('Incident Number') }}
          </InputLabel>
          <div class="col-span-2 mt-1 sm:mt-0">
            <TextInput
              v-model="form.incident_number"
              name="incident_number"
            />
            <InputError
              :message="form.errors.incident_number"
              class="mt-2"
            />
          </div>
        </div>
        <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center">
          <InputLabel
            for="password"
            class="col-span-2 sm:text-right"
          >
            {{ __('Password') }}
          </InputLabel>
          <div class="col-span-2 mt-1 sm:mt-0">
            <TextInput
              v-model="form.password"
              name="password"
              type="password"
            />
            <InputError
              :message="form.errors.password"
              class="mt-2"
            />
          </div>
        </div>
      </div>
    </template>
    <template #footer>
      <SecondaryButton @click="close">
        {{ __('Nevermind') }}
      </SecondaryButton>
      <DangerButton
        class="ml-2"
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
        @click="doDelete"
      >
        {{ incident.deleted_at === null ? __('Delete Incident') : __('Restore Incident') }}
      </DangerButton>
    </template>
  </ConfirmationModal>
</template>
