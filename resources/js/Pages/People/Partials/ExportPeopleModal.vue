<script setup>
import {computed} from 'vue';
import {useForm} from '@inertiajs/vue3';
import DialogModal from '@/Components/DialogModal.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import {subDays, formatISO} from 'date-fns';
import {__} from '@/Composables/Translate';
import upperFirst from 'lodash/upperFirst';

const props = defineProps({
  group: String,
  show: Boolean,
});

const emit = defineEmits(['close']);

const form = useForm({
  format: 'xlsx',
  group: props.group,
  date_from: formatISO(subDays(new Date(), 7)),
  date_to: formatISO(new Date()),
});

const groupUpper = computed(() => upperFirst(props.group));

const close = () => emit('close');

const exportPeople = () => form.post(route('people.export'), {
  preserveState: true,
  onSuccess: () => close()
});
</script>

<template>
  <DialogModal
    :show="show"
    maxWidth="2xl"
    @close="close"
  >
    <template #title>
      {{ __('Export :group', {group: groupUpper}) }}
    </template>
    <template #content>
      <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center">
        <InputLabel
          for="date_from"
          class="block font-medium text-sm text-gray-700 sm:text-right"
        >
          {{ __('Date From') }}
        </InputLabel>
        <div class="col-span-2 mt-1 sm:mt-0">
          <DatePicker
            id="date_from"
            v-model="form.date_from"
          />
        </div>
        <InputLabel
          for="date_to"
          class="block font-medium text-sm text-gray-700 sm:text-right"
        >
          {{ __('Date To') }}
        </InputLabel>
        <div class="col-span-2 mt-1 sm:mt-0">
          <DatePicker
            id="date_to"
            v-model="form.date_to"
          />
        </div>
        <div class="col-span-6">
          <InputError
            :message="form.errors.date_from"
            class="mt-2"
          />
          <InputError
            :message="form.errors.date_to"
            class="mt-2"
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
        @click="exportPeople"
      >
        {{ __('Export') }}
      </PrimaryButton>
    </template>
  </DialogModal>
</template>
