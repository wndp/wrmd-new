<script setup>
import { inject, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import DialogModal from '@/Components/DialogModal.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import TextareaInput from '@/Components/FormElements/TextareaInput.vue';
import Checkbox from '@/Components/FormElements/Checkbox.vue';
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
  conditioning: {
    type: Object,
    default: null
  },
  show: Boolean,
})

const emit = defineEmits(['close']);

const form = useForm({
    evaluated_at: props.conditioning?.evaluated_at || new Date,
    buoyancy_id: props.conditioning?.buoyancy_id,
    hauled_out_id: props.conditioning?.hauled_out_id,
    preening_id: props.conditioning?.preening_id,
    self_feeding_id: props.conditioning?.self_feeding_id,
    flighted_id: props.conditioning?.flighted_id,
    areas_wet_to_skin: props.conditioning?.areas_wet_to_skin || [],
    comments: props.conditioning?.comments,
    examiner: props.conditioning?.examiner,
});

const close = () => {
  emit('close');
};

const save = () => {
  if (props.conditioning === null) {
    form.post(route('oiled.conditioning.store', {
      patient: props.patientId,
    }), {
      preserveScroll: true,
      onSuccess: () => close()
    });
  } else {
    form.put(route('oiled.conditioning.update', {
      patient: props.patientId,
      conditioning: props.conditioning.id,
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
      <template v-if="props.conditioning === null">
        {{ __('Add New Conditioning') }}
      </template>
      <template v-else>
        {{ __('Update Conditioning') }}
      </template>
    </template>
    <template #content>
      <div class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-6">
        <div class="col-span-6 md:col-span-3 grid grid-cols-1 gap-y-4">
          <FormRow
            id="evaluated_at"
            :label="__('Date Evaluated')"
            :required="true"
          >
            <DatePicker
              id="evaluated_at"
              v-model="form.evaluated_at"
              time
            />
            <InputError
              :message="form.errors.evaluated_at"
              class="mt-2"
            />
          </FormRow>
          <FormRow
            id="examiner"
            :label="__('Examiner')"
            :required="true"
          >
            <TextInput
              v-model="form.examiner"
              name="examiner"
            />
            <InputError
              :message="form.errors.examiner"
              class="mt-2"
            />
          </FormRow>
          <FormRow
            id="buoyancy_id"
            :label="__('Buoyancy')"
          >
            <SelectInput
              v-model="form.buoyancy_id"
              name="buoyancy_id"
              :options="$page.props.options.oiledConditioningBuoyanciesOptions"
              hasBlankOption
            />
            <InputError
              :message="form.errors.buoyancy_id"
              class="mt-2"
            />
          </FormRow>
          <FormRow
            id="hauled_out_id"
            :label="__('Hauled Out')"
          >
            <SelectInput
              v-model="form.hauled_out_id"
              name="hauled_out_id"
              :options="$page.props.options.oiledConditioningHauledOutsOptions"
              hasBlankOption
            />
            <InputError
              :message="form.errors.hauled_out_id"
              class="mt-2"
            />
          </FormRow>
        </div>
        <div class="col-span-6 md:col-span-3 grid grid-cols-1 gap-y-4">
          <FormRow
            id="preening_id"
            :label="__('Preening / Grooming')"
          >
            <SelectInput
              v-model="form.preening_id"
              name="preening_id"
              :options="$page.props.options.oiledConditioningPreeningsOptions"
              hasBlankOption
            />
            <InputError
              :message="form.errors.preening_id"
              class="mt-2"
            />
          </FormRow>
          <FormRow
            id="self_feeding_id"
            :label="__('Self Feeding')"
          >
            <SelectInput
              v-model="form.self_feeding_id"
              name="self_feeding_id"
              :options="$page.props.options.oiledConditioningUnknownBoolOptions"
              hasBlankOption
            />
            <InputError
              :message="form.errors.self_feeding_id"
              class="mt-2"
            />
          </FormRow>
          <FormRow
            id="flighted_id"
            :label="__('Flighted')"
          >
            <SelectInput
              v-model="form.flighted_id"
              name="flighted_id"
              :options="$page.props.options.oiledConditioningUnknownBoolOptions"
              hasBlankOption
            />
            <InputError
              :message="form.errors.flighted_id"
              class="mt-2"
            />
          </FormRow>
        </div>
        <FormRow
          id="areas_wet_to_skin"
          :label="__('Area Wet To Skin')"
          class="col-span-6"
        >
          <div class="flex flex-wrap gap-x-6 gap-y-4">
            <div
              v-for="area in $page.props.options.oiledConditioningAreasWetToSkinOptions"
              :key="area.value"
              class="relative flex items-start"
            >
              <div class="flex items-center h-5">
                <Checkbox
                  :id="`${area.value}_id`"
                  v-model="form.areas_wet_to_skin"
                  name="areas_wet_to_skin"
                  :value="area.value"
                />
              </div>
              <div class="ml-2 text-sm">
                <label
                  :for="`${area.value}_id`"
                  class="font-medium text-gray-700"
                >{{ area.label }}</label>
              </div>
            </div>
          </div>
          <InputError
            :message="form.errors.areas_wet_to_skin"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="comments"
          :label="__('Comments')"
          class="col-span-6"
        >
          <TextareaInput
            v-model="form.comments"
            name="comments"
          />
          <InputError
            :message="form.errors.comments"
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
        {{ __('Save Conditioning') }}
      </PrimaryButton>
    </template>
  </DialogModal>
</template>
