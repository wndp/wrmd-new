<script setup>
import {useForm} from '@inertiajs/vue3';
import Panel from '@/Components/Panel.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import Checkbox from '@/Components/FormElements/Checkbox.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
  patientId: {
    type: String,
    required: true
  },
  morphometric: {
    type: Object,
    default: () => ({})
  },
  enforceRequired: {
    type: Boolean,
    default: true
  }
});

const form = useForm({
  measured_at: props.morphometric?.measured_at,
  bill_length: props.morphometric?.bill_length,
  bill_width: props.morphometric?.bill_width,
  bill_depth: props.morphometric?.bill_depth,
  head_bill_length: props.morphometric?.head_bill_length,
  culmen: props.morphometric?.culmen,
  exposed_culmen: props.morphometric?.exposed_culmen,
  wing_chord: props.morphometric?.wing_chord,
  flat_wing: props.morphometric?.flat_wing,
  tarsus_length: props.morphometric?.tarsus_length,
  middle_toe_length: props.morphometric?.middle_toe_length,
  toe_pad_length: props.morphometric?.toe_pad_length,
  hallux_length: props.morphometric?.hallux_length,
  tail_length: props.morphometric?.tail_length,
  weight: props.morphometric?.weight,
  samples_collected: props.morphometric?.samples_collected || [],
  remarks: props.morphometric?.remarks,
});

const save = () => {
  form.put(route('patients.banding-morphometrics.morphometrics.update', {
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
      {{ __('Morphometrics') }}
    </template>
    <template #description>
      {{ __('See ID guides/resources for appropriate species-specific measurements. Describe measurement locations (e.g., distal vs proximal culmen; bill depth location) in Remarks.') }}
    </template>
    <template #content>
      <FormRow
        id="measured_at"
        :label="__('Date Measured')"
        :required="enforceRequired"
        class="col-span-6 md:col-span-2"
      >
        <DatePicker
          id="measured_at"
          v-model="form.measured_at"
          :required="enforceRequired"
        />
        <InputError :message="form.errors.measured_at" />
      </FormRow>
      <FormRow
        id="wing_chord"
        :label="__('Wing Chord')"
        class="col-span-6 md:col-span-2"
      >
        <div class="flex items-center">
          <TextInput
            v-model="form.wing_chord"
            name="wing_chord"
            type="number"
            step="any"
            min="0"
          />
          <span class="text-gray-600 text-sm ml-2">(mm)</span>
        </div>
        <InputError :message="form.errors.wing_chord" />
      </FormRow>
      <FormRow
        id="weight"
        :label="__('Weight')"
        class="col-span-6 md:col-span-2"
      >
        <div class="flex items-center">
          <TextInput
            v-model="form.weight"
            name="weight"
            type="number"
            step="any"
            min="0"
          />
          <span class="text-gray-600 text-sm ml-2">(g)</span>
        </div>
        <InputError :message="form.errors.weight" />
      </FormRow>
      <FormRow
        id="flat_wing"
        :label="__('Flat Wing')"
        class="col-span-6 md:col-span-2"
      >
        <div class="flex items-center">
          <TextInput
            v-model="form.flat_wing"
            name="flat_wing"
            type="number"
            step="any"
            min="0"
          />
          <span class="text-gray-600 text-sm ml-2">(mm)</span>
        </div>
        <InputError :message="form.errors.flat_wing" />
      </FormRow>
      <FormRow
        id="bill_length"
        :label="__('Bill Length')"
        class="col-span-6 md:col-span-2"
      >
        <div class="flex items-center">
          <TextInput
            v-model="form.bill_length"
            name="bill_length"
            type="number"
            step="any"
            min="0"
          />
          <span class="text-gray-600 text-sm ml-2">(mm)</span>
        </div>
        <InputError :message="form.errors.bill_length" />
      </FormRow>
      <FormRow
        id="tail_length"
        :label="__('Tail Length')"
        class="col-span-6 md:col-span-2"
      >
        <div class="flex items-center">
          <TextInput
            v-model="form.tail_length"
            name="tail_length"
            type="number"
            step="any"
            min="0"
          />
          <span class="text-gray-600 text-sm ml-2">(mm)</span>
        </div>
        <InputError :message="form.errors.tail_length" />
      </FormRow>
      <FormRow
        id="bill_width"
        :label="__('Bill Width')"
        class="col-span-6 md:col-span-2"
      >
        <div class="flex items-center">
          <TextInput
            v-model="form.bill_width"
            name="bill_width"
            type="number"
            step="any"
            min="0"
          />
          <span class="text-gray-600 text-sm ml-2">(mm)</span>
        </div>
        <InputError :message="form.errors.bill_width" />
      </FormRow>
      <FormRow
        id="tarsus_length"
        :label="__('Tarsus Length')"
        class="col-span-6 md:col-span-2"
      >
        <div class="flex items-center">
          <TextInput
            v-model="form.tarsus_length"
            name="tarsus_length"
            type="number"
            step="any"
            min="0"
          />
          <span class="text-gray-600 text-sm ml-2">(mm)</span>
        </div>
        <InputError :message="form.errors.tarsus_length" />
      </FormRow>
      <FormRow
        id="bill_depth"
        :label="__('Bill Depth')"
        class="col-span-6 md:col-span-2"
      >
        <div class="flex items-center">
          <TextInput
            v-model="form.bill_depth"
            name="bill_depth"
            type="number"
            step="any"
            min="0"
          />
          <span class="text-gray-600 text-sm ml-2">(mm)</span>
        </div>
        <InputError :message="form.errors.bill_depth" />
      </FormRow>
      <FormRow
        id="middle_toe_length"
        :label="__('Middle Toe Length')"
        class="col-span-6 md:col-span-2"
      >
        <div class="flex items-center">
          <TextInput
            v-model="form.middle_toe_length"
            name="middle_toe_length"
            type="number"
            step="any"
            min="0"
          />
          <span class="text-gray-600 text-sm ml-2">(mm)</span>
        </div>
        <InputError :message="form.errors.middle_toe_length" />
      </FormRow>
      <FormRow
        id="head_bill_length"
        :label="__('Head-Bill Length')"
        class="col-span-6 md:col-span-2"
      >
        <div class="flex items-center">
          <TextInput
            v-model="form.head_bill_length"
            name="head_bill_length"
            type="number"
            step="any"
            min="0"
          />
          <span class="text-gray-600 text-sm ml-2">(mm)</span>
        </div>
        <InputError :message="form.errors.head_bill_length" />
      </FormRow>
      <FormRow
        id="hallux_length"
        :label="__('Hallux Length')"
        class="col-span-6 md:col-span-2"
      >
        <div class="flex items-center">
          <TextInput
            v-model="form.hallux_length"
            name="hallux_length"
            type="number"
            step="any"
            min="0"
          />
          <span class="text-gray-600 text-sm ml-2">(mm)</span>
        </div>
        <InputError :message="form.errors.hallux_length" />
      </FormRow>
      <FormRow
        id="culmen"
        :label="__('Culmen')"
        class="col-span-6 md:col-span-2"
      >
        <div class="flex items-center">
          <TextInput
            v-model="form.culmen"
            name="culmen"
            type="number"
            step="any"
            min="0"
          />
          <span class="text-gray-600 text-sm ml-2">(mm)</span>
        </div>
        <InputError :message="form.errors.culmen" />
      </FormRow>
      <FormRow
        id="toe_pad_length"
        :label="__('Toe Pad Length')"
        class="col-span-6 md:col-span-2"
      >
        <div class="flex items-center">
          <TextInput
            v-model="form.toe_pad_length"
            name="toe_pad_length"
            type="number"
            step="any"
            min="0"
          />
          <span class="text-gray-600 text-sm ml-2">(mm)</span>
        </div>
        <InputError :message="form.errors.toe_pad_length" />
      </FormRow>
      <FormRow
        id="exposed_culmen"
        :label="__('Exposed Culmen')"
        class="col-span-6 md:col-span-2"
      >
        <div class="flex items-center">
          <TextInput
            v-model="form.exposed_culmen"
            name="exposed_culmen"
            type="number"
            step="any"
            min="0"
          />
          <span class="text-gray-600 text-sm ml-2">(mm)</span>
        </div>
        <InputError :message="form.errors.exposed_culmen" />
      </FormRow>
      <FormRow
        id="samples_collected"
        :label="__('Exposed Culmen')"
        class="col-span-6"
      >
        <div class="flex flex-wrap gap-x-4 gap-y-3 sapce-between ">
          <div
            v-for="sample in $page.props.options.bandingSamplesCollectedOptions"
            :key="sample.value"
            class="flex items-start"
          >
            <div class="flex items-center">
              <Checkbox
                :id="sample.value"
                v-model="form.samples_collected"
                name="samples_collected[]"
                :value="sample.value"
              />
              <InputLabel
                :for="sample.value"
                class="ml-2 font-normal whitespace-nowrap"
              >{{ sample.label }}</InputLabel>
            </div>
          </div>
        </div>
        <InputError :message="form.errors.samples_collected" />
      </FormRow>
      <FormRow
        id="morphometric_remarks"
        :label="__('Remarks')"
        class="col-span-6"
      >
        <TextInput
          v-model="form.remarks"
          name="morphometric_remarks"
        />
        <InputError :message="form.errors.morphometric_remarks" />
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
          {{ __('Update Morphometrics Data') }}
        </PrimaryButton>
      </div>
    </template>
  </Panel>
</template>
