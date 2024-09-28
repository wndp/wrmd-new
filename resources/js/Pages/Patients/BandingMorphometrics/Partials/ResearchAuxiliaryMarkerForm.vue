<script setup>
import {useForm} from '@inertiajs/vue3';
import Panel from '@/Components/Panel.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
  patientId: {
    type: String,
    required: true
  },
  banding: {
    type: Object,
    default: () => ({})
  },
  enforceRequired: {
    type: Boolean,
    default: true
  }
});

const form = useForm({
  auxiliary_marker: props.banding?.auxiliary_marker,
  auxiliary_marker_color_id: props.banding?.auxiliary_marker_color_id,
  auxiliary_side_of_bird_id: props.banding?.auxiliary_side_of_bird_id,
  auxiliary_marker_type_id: props.banding?.auxiliary_marker_type_id,
  auxiliary_marker_code_color_id: props.banding?.auxiliary_marker_code_color_id,
  auxiliary_placement_on_leg_id: props.banding?.auxiliary_placement_on_leg_id,
});

const save = () => {
  form.put(route('patients.banding-morphometrics.auxiliary_marker.update', {
    patient: props.patientId
  }), {
    preserveScroll: true,
    //onError: () => this.stopAutoSave()
  });
};
</script>

<template>
  <Panel>
    <template #title>
      {{ __('Auxiliary Marker') }}
    </template>
    <template #content>
      <FormRow
        id="auxiliary_marker"
        :label="__('Marker Code')"
        :required="enforceRequired"
        class="col-span-6 md:col-span-2"
      >
        <TextInput
          v-model="form.auxiliary_marker"
          name="auxiliary_marker"
          :required="enforceRequired"
        />
        <InputError :message="form.errors.auxiliary_marker" />
      </FormRow>
      <FormRow
        id="auxiliary_marker_color_id"
        :label="__('Marker Color')"
        class="col-span-6 md:col-span-2"
      >
        <SelectInput
          v-model="form.auxiliary_marker_color_id"
          name="auxiliary_marker_color_id"
          :options="$page.props.options.bandingAuxillaryColorCodesOptions"
          hasBlankOption
        />
        <InputError :message="form.errors.auxiliary_marker_color_id" />
      </FormRow>
      <FormRow
        id="auxiliary_side_of_bird_id"
        :label="__('Side of Bird')"
        class="col-span-6 md:col-span-2"
      >
        <SelectInput
          v-model="form.auxiliary_side_of_bird_id"
          name="auxiliary_side_of_bird_id"
          :options="$page.props.options.bandingAuxillarySideOfBirdOptions"
          hasBlankOption
        />
        <InputError :message="form.errors.auxiliary_side_of_bird_id" />
      </FormRow>
      <FormRow
        id="auxiliary_marker_type_id"
        :label="__('Marker Type')"
        class="col-span-6 md:col-span-2"
      >
        <SelectInput
          v-model="form.auxiliary_marker_type_id"
          name="auxiliary_marker_type_id"
          :options="$page.props.options.bandingAuxillaryMarkerTypeCodesOptions"
          hasBlankOption
        />
        <InputError :message="form.errors.auxiliary_marker_type_id" />
      </FormRow>
      <FormRow
        id="auxiliary_marker_code_color_id"
        :label="__('Marker Code Color')"
        class="col-span-6 md:col-span-2"
      >
        <SelectInput
          v-model="form.auxiliary_marker_code_color_id"
          name="auxiliary_marker_code_color_id"
          :options="$page.props.options.bandingAuxillaryColorCodesOptions"
          hasBlankOption
        />
        <InputError :message="form.errors.auxiliary_marker_code_color_id" />
      </FormRow>
      <FormRow
        id="auxiliary_placement_on_leg_id"
        :label="__('Placement on Leg')"
        class="col-span-6 md:col-span-2"
      >
        <SelectInput
          v-model="form.auxiliary_placement_on_leg_id"
          name="auxiliary_placement_on_leg_id"
          :options="$page.props.options.bandingPlacementOnLegOptions"
          hasBlankOption
        />
        <InputError :message="form.errors.auxiliary_placement_on_leg_id" />
      </FormRow>
    </template>
    <template #actions>
      <div class="flex items-center justify-end text-right">
        <ActionMessage
          :on="form.isDirty"
          class="mr-3"
        >
          <span class="text-red-600">{{ __('There are unsaved changes') }}</span>
        </ActionMessage>
        <ActionMessage
          :on="form.recentlySuccessful"
          class="mr-3"
        >
          {{ __('Saved') }}
        </ActionMessage>
        <PrimaryButton
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
          @click="save"
        >
          {{ __('Update Auxiliary Marker Data') }}
        </PrimaryButton>
      </div>
    </template>
  </Panel>
</template>
