<script setup>
import {inject} from 'vue';
import {useForm} from '@inertiajs/vue3';
import DialogModal from '@/Components/DialogModal.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import TextareaInput from '@/Components/FormElements/TextareaInput.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import {__} from '@/Composables/Translate';

const route = inject('route');

const props = defineProps({
  patientId: {
    type: String,
    required: true
  },
  wash: {
    type: Object,
    default: null
  },
  preTreatmentIsNoneId: {
    type: Number,
    required: true
  },
  washTypeIsInitialId: {
    type: Number,
    required: true
  },
  show: Boolean,
})

const emit = defineEmits(['close']);

const form = useForm({
    washed_at: props.wash?.washed_at || new Date,
    pre_treatment_id: props.wash?.pre_treatment_id || props.preTreatmentIsNoneId,
    pre_treatment_duration: props.wash?.pre_treatment_duration,
    wash_type_id: props.wash?.wash_type_id || props.washTypeIsInitialId,
    wash_duration: props.wash?.wash_duration,
    detergent_id: props.wash?.detergent_id,
    rinse_duration: props.wash?.rinse_duration,
    washer: props.wash?.washer,
    handler: props.wash?.handler,
    rinser: props.wash?.rinser,
    dryer: props.wash?.dryer,
    drying_method_id: props.wash?.drying_method_id,
    drying_duration: props.wash?.drying_duration,
    observations: props.wash?.observations,
});

const close = () => emit('close');

const save = () => {
  if (props.wash === null) {
    form.post(route('oiled.wash.store', {
      patient: props.patientId,
    }), {
      preserveScroll: true,
      onSuccess: () => close()
    });
  } else {
    form.put(route('oiled.wash.update', {
      patient: props.patientId,
      wash: props.wash.id,
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
      <template v-if="props.wash === null">
        {{ __('New Wash Record') }}
      </template>
      <template v-else>
        {{ __('Update Wash Record') }}
      </template>
    </template>
    <template #content>
      <div class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-6">
        <div class="col-span-6 md:col-span-3 grid grid-cols-1 gap-y-4">
          <FormRow
            id="washed_at"
            :label="__('Date Washed')"
            :required="true"
          >
            <DatePicker
              id="washed_at"
              v-model="form.washed_at"
              time
            />
            <InputError
              :message="form.errors.washed_at"
              class="mt-2"
            />
          </FormRow>
          <FormRow
            id="pre_treatment_id"
            :label="__('Pre-treatment / Duration')"
          >
            <div class="flex space-x-2">
              <SelectInput
                v-model="form.pre_treatment_id"
                name="pre_treatment_id"
                :options="$page.props.options.oiledWashPreTreatmentsOptions"
                hasBlankOption
              />
              <TextInput
                v-model="form.pre_treatment_duration"
                name="pre_treatment_duration"
                type="number"
                min="0"
                step="0.25"
                placeholder="minutes"
              />
            </div>
            <InputError
              :message="form.errors.pre_treatment_id"
              class="mt-2"
            />
            <InputError
              :message="form.errors.pre_treatment_duration"
              class="mt-2"
            />
          </FormRow>
          <FormRow
            id="wash_type_id"
            :label="__('Wash Type / Duration')"
          >
            <div class="flex space-x-2">
              <SelectInput
                v-model="form.wash_type_id"
                name="wash_type_id"
                :options="$page.props.options.oiledWashTypesOptions"
                hasBlankOption
              />
              <TextInput
                v-model="form.wash_duration"
                name="wash_duration"
                type="number"
                min="0"
                step="0.25"
                placeholder="minutes"
              />
            </div>
            <InputError
              :message="form.errors.wash_type_id"
              class="mt-2"
            />
            <InputError
              :message="form.errors.wash_duration"
              class="mt-2"
            />
          </FormRow>
          <FormRow
            id="detergent_id"
            :label="__('Detergent')"
          >
            <SelectInput
              v-model="form.detergent_id"
              name="detergent_id"
              :options="$page.props.options.oiledWashDetergentsOptions"
              hasBlankOption
            />
            <InputError
              :message="form.errors.detergent_id"
              class="mt-2"
            />
          </FormRow>
          <FormRow
            id="rinse_duration"
            :label="__('Rinse Duration')"
          >
            <TextInput
              v-model="form.rinse_duration"
              name="rinse_duration"
              type="number"
              min="0"
              step="0.25"
              placeholder="minutes"
            />
            <InputError
              :message="form.errors.rinse_duration"
              class="mt-2"
            />
          </FormRow>
        </div>
        <div class="col-span-6 md:col-span-3 grid grid-cols-1 gap-y-4">
          <FormRow
            id="washer"
            :label="__('Washer(s)')"
          >
            <TextInput
              v-model="form.washer"
              name="washer"
            />
            <InputError
              :message="form.errors.washer"
              class="mt-2"
            />
          </FormRow>
          <FormRow
            id="handler"
            :label="__('Handler(s)')"
          >
            <TextInput
              v-model="form.handler"
              name="handler"
            />
            <InputError
              :message="form.errors.handler"
              class="mt-2"
            />
          </FormRow>
          <FormRow
            id="rinser"
            :label="__('Rinser(s)')"
          >
            <TextInput
              v-model="form.rinser"
              name="rinser"
            />
            <InputError
              :message="form.errors.rinser"
              class="mt-2"
            />
          </FormRow>
          <FormRow
            id="dryer"
            :label="__('Dryer(s)')"
          >
            <TextInput
              v-model="form.dryer"
              name="dryer"
            />
            <InputError
              :message="form.errors.dryer"
              class="mt-2"
            />
          </FormRow>
          <FormRow
            id="drying_method_id"
            :label="__('Drying Method / Duration')"
          >
            <div class="flex space-x-2">
              <SelectInput
                v-model="form.drying_method_id"
                name="drying_method_id"
                :options="$page.props.options.oiledWashDryingMethodsOptions"
                hasBlankOption
              />
              <TextInput
                v-model="form.drying_duration"
                name="drying_duration"
                type="number"
                min="0"
                step="0.25"
                placeholder="minutes"
              />
            </div>
            <InputError
              :message="form.errors.drying_method_id"
              class="mt-2"
            />
            <InputError
              :message="form.errors.drying_duration"
              class="mt-2"
            />
          </FormRow>
        </div>
        <FormRow
          id="observations"
          :label="__('Observations')"
          class="col-span-6"
        >
          <TextareaInput
            v-model="form.observations"
            name="observations"
          />
          <InputError
            :message="form.errors.observations"
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
        {{ __('Save Wash') }}
      </PrimaryButton>
    </template>
  </DialogModal>
</template>
