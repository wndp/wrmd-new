<template>
  <DialogModal
    :show="show"
    @close="close"
  >
    <template #title>
      {{ __('Update Transaction') }}
    </template>
    <template #content>
      <div class="space-y-2">
        <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center">
          <Label
            for="update_transacted_at"
            class="sm:text-right"
          >{{ __('Transaction Date') }}</Label>
          <div class="col-span-2 mt-1 sm:mt-0">
            <DatePicker
              id="update_transacted_at"
              v-model="form.transacted_at"
            />
            <InputError
              :message="form.errors.transacted_at"
              class="mt-2"
            />
          </div>
          <Label
            for="update_category"
            class="sm:text-right"
          >{{ __('Category') }}</Label>
          <div class="col-span-2 mt-1 sm:mt-0">
            <Select
              v-model="form.category"
              name="update_category"
              :options="categories"
            />
            <InputError
              :message="form.errors.category"
              class="mt-2"
            />
          </div>
        </div>
        <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center">
          <Label
            for="update_debit"
            class="sm:text-right"
          >{{ __('Debit') }}</Label>
          <div class="col-span-2 mt-1 sm:mt-0">
            <Input
              v-model="form.debit"
              name="update_debit"
              type="number"
              min="0"
              step="0.01"
            />
            <InputError
              :message="form.errors.debit"
              class="mt-2"
            />
          </div>
          <Label
            for="update_credit"
            class="sm:text-right"
          >{{ __('Credit') }}</Label>
          <div class="col-span-2 mt-1 sm:mt-0">
            <Input
              v-model="form.credit"
              name="update_credit"
              type="number"
              min="0"
              step="0.01"
            />
            <InputError
              :message="form.errors.credit"
              class="mt-2"
            />
          </div>
        </div>
        <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center">
          <Label
            for="update_memo"
            class="sm:text-right"
          >{{ __('Memo') }}</Label>
          <div class="col-span-5 mt-1 sm:mt-0">
            <Input
              v-model="form.memo"
              name="update_memo"
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
        @click="update"
      >
        {{ __('Update Transaction') }}
      </PrimaryButton>
    </template>
  </DialogModal>
</template>

<script setup>
import { inject, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import DialogModal from '@/Components/DialogModal.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';

const route = inject('route');

const props = defineProps({
    transaction: {
        type: Object,
        required: true
    },
    show: Boolean,
})

const emit = defineEmits(['close']);
const form = useForm({
  transacted_at: props.transaction.transacted_at,
  category: props.transaction.category.name,
  debit: props.transaction.debit ? props.transaction.debit_for_humans : '',
  credit: props.transaction.credit ? props.transaction.credit_for_humans : '',
  memo: props.transaction.memo
});

let patient = computed(() => usePage().props.admission.patient);
let categories = computed(() => usePage().props.options.categories);

const close = () => {
  emit('close');
};

const update = () => {
  form.put(route('patients.expenses.update', {
    patient: patient.value.id,
    transaction: props.transaction.id
  }), {
    preserveScroll: true,
    onSuccess: () => close()
  });
};

</script>
