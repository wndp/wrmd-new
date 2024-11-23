<script setup>
import { inject, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import DialogModal from '@/Components/DialogModal.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import {__} from '@/Composables/Translate';

const route = inject('route');

const props = defineProps({
    vital: {
        type: Object,
        default: null
    },
    show: Boolean,
})

const emit = defineEmits(['close']);

const patient = computed(() => usePage().props.admission.patient);

const form = useForm({
    recorded_at: new Date,
    weight: props.vital?.exam.weight,
    temperature: props.vital?.exam.temperature,
    attitude: props.vital?.exam.attitude,
    pcv: props.vital?.lab.data.pcv,
    tp: props.vital?.lab.data.tp
});

const close = () => emit('close');

const save = () => {
  if (props.vital === null) {
    form.post(route('patients.vital.store', {
      patient: patient.value.id,
    }), {
      preserveScroll: true,
      onSuccess: () => close()
    });
  } else {
    form.put(route('patients.vital.update', {
      patient: patient.value.id,
      vital: props.vital.id,
    }), {
      preserveScroll: true,
      onSuccess: () => close()
    });
  }
};
</script>

<template>
  <DialogModal
    :show="show"
    @close="close"
  >
    <template #title>
      <template v-if="props.vital === null">
        {{ __('Add New Vital') }}
      </template>
      <template v-else>
        {{ __('Update Vital') }}
      </template>
    </template>
    <template #content>
      <div class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-6">
        <FormRow
          id="recorded_at"
          :label="__('Date')"
          :required="true"
          class="col-span-6 md:col-span-3"
        >
          <DatePicker
            id="recorded_at"
            v-model="form.recorded_at"
            time
          />
          <InputError
            :message="form.errors.recorded_at"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="weight"
          :label="__('Weight (g)')"
          class="col-span-6 md:col-span-3"
        >
          <TextInput
            v-model="form.weight"
            name="weight"
            type="number"
            min="0"
            step="0.01"
          />
          <InputError
            :message="form.errors.weight"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="temperature"
          :label="__('Temperature (f)')"
          class="col-span-6 md:col-span-3"
        >
          <TextInput
            v-model="form.temperature"
            name="temperature"
            type="number"
            min="0"
            step="0.01"
          />
          <InputError
            :message="form.errors.temperature"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="attitude"
          :label="__('Attitude')"
          class="col-span-6 md:col-span-3"
        >
          <SelectInput
            v-model="form.attitude"
            name="attitude"
            :options="$page.props.options.examAttitudesOptions"
          />
          <InputError
            :message="form.errors.attitude"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="pvc"
          :label="__('PVC (%)')"
          class="col-span-6 md:col-span-3"
        >
          <TextInput
            v-model="form.pvc"
            name="pvc"
            type="number"
            min="0"
            step="0.01"
          />
          <InputError
            :message="form.errors.pvc"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="ts"
          :label="__('Total Solids (g/dl)')"
          class="col-span-6 md:col-span-3"
        >
          <TextInput
            v-model="form.ts"
            name="ts"
            type="number"
            min="0"
            step="0.01"
          />
          <InputError
            :message="form.errors.ts"
            class="mt-2"
          />
        </FormRow>
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
        {{ __('Save Vitals') }}
      </PrimaryButton>
    </template>
  </DialogModal>
</template>
