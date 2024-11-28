<script setup>
import {inject} from 'vue';
import {useForm} from '@inertiajs/vue3';
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
  assessment: {
    type: Object,
    default: null
  },
  show: Boolean,
})

const emit = defineEmits(['close']);

const form = useForm({
    evaluated_at: props.assessment?.evaluated_at || new Date,
    buoyancy_id: props.assessment?.buoyancy_id,
    hauled_out_id: props.assessment?.hauled_out_id,
    preening_id: props.assessment?.preening_id,
    areas_wet_to_skin: props.assessment?.areas_wet_to_skin || [],
    areas_surface_wet: props.assessment?.areas_surface_wet || [],
    comments: props.assessment?.comments,
    examiner: props.assessment?.examiner,
});

const close = () => {
  emit('close');
};

const save = () => {
  if (props.assessment === null) {
    form.post(route('oiled.waterproofing_assessment.store', {
      patient: props.patientId,
    }), {
      preserveScroll: true,
      onSuccess: () => close()
    });
  } else {
    form.put(route('oiled.waterproofing_assessment.update', {
      patient: props.patientId,
      assessment: props.assessment.id,
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
      <template v-if="props.assessment === null">
        {{ __('Add New Waterproofing Assessment') }}
      </template>
      <template v-else>
        {{ __('Update Waterproofing Assessment') }}
      </template>
    </template>
    <template #content>
      <div class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-6">
        <FormRow
          id="evaluated_at"
          :label="__('Date Evaluated')"
          :required="true"
          class="col-span-6 md:col-span-3"
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
          class="col-span-6 md:col-span-3"
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
          class="col-span-6 md:col-span-3"
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
          class="col-span-6 md:col-span-3"
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
        <FormRow
          id="preening_id"
          :label="__('Preening / Grooming')"
          class="col-span-6 md:col-span-3"
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
          id="areas_wet_to_skin"
          :label="__('Areas Wet To Skin')"
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
                  :id="`${area.value}_areas_wet_to_skin_id`"
                  v-model="form.areas_wet_to_skin"
                  name="areas_wet_to_skin"
                  :value="area.value"
                />
              </div>
              <div class="ml-2 text-sm">
                <label
                  :for="`${area.value}_areas_wet_to_skin_id`"
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
          id="areas_surface_wet"
          :label="__('Areas Surface Wet')"
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
                  :id="`${area.value}_areas_surface_wet_id`"
                  v-model="form.areas_surface_wet"
                  name="areas_surface_wet"
                  :value="area.value"
                />
              </div>
              <div class="ml-2 text-sm">
                <label
                  :for="`${area.value}_areas_surface_wet_id`"
                  class="font-medium text-gray-700"
                >{{ area.label }}</label>
              </div>
            </div>
          </div>
          <InputError
            :message="form.errors.areas_surface_wet"
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
        {{ __('Save Assessment') }}
      </PrimaryButton>
    </template>
  </DialogModal>
</template>
