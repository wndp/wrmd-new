<template>
  <Panel>
    <template #heading>
      {{ __('Morphometrics') }}
    </template>
    <template #description>
      {{ __('See ID guides/resources for appropriate species-specific measurements. Describe measurement locations (e.g., distal vs proximal culmen; bill depth location) in Remarks.') }}
    </template>
    <div class="space-y-4 sm:space-y-2">
      <div class="grid grid-cols-6 gap-2 md:items-center">
        <div class="col-span-6 md:grid md:grid-cols-6 md:gap-x-2 md:items-center">
          <Label
            for="remarks"
            class="md:text-right"
          >
            {{ __('Date Measured') }}
            <RequiredInput v-if="enforceRequired" />
          </Label>
          <div class="col-span-5 mt-1 md:mt-0">
            <DatePicker
              id="measured_at"
              v-model="form.measured_at"
              :required="enforceRequired"
            />
            <InputError :message="form.errors.measured_at" />
          </div>
        </div>
        <div class="col-span-3 md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
          <Label
            for="wing_chord"
            class="md:text-right"
          >
            {{ __('Wing Chord') }} <i class="ml-1 italic">(mm)</i>
          </Label>
          <div class="col-span-2 mt-1 md:mt-0">
            <Input
              v-model="form.wing_chord"
              name="wing_chord"
              type="number"
              step="any"
              min="0"
            />
            <InputError :message="form.errors.wing_chord" />
          </div>
        </div>
        <div class="col-span-3 md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
          <Label
            for="weight"
            class="md:text-right"
          >
            {{ __('Weight') }} <i class="ml-1 italic">(g)</i>
          </Label>
          <div class="col-span-2 mt-1 md:mt-0">
            <Input
              v-model="form.weight"
              name="weight"
              type="number"
              step="any"
              min="0"
            />
            <InputError :message="form.errors.weight" />
          </div>
        </div>
        <div class="col-span-3 md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
          <Label
            for="flat_wing"
            class="md:text-right"
          >
            {{ __('Flat Wing') }} <i class="ml-1 italic">(mm)</i>
          </Label>
          <div class="col-span-2 mt-1 md:mt-0">
            <Input
              v-model="form.flat_wing"
              name="flat_wing"
              type="number"
              step="any"
              min="0"
            />
            <InputError :message="form.errors.flat_wing" />
          </div>
        </div>
        <div class="col-span-3 md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
          <Label
            for="bill_length"
            class="md:text-right"
          >
            {{ __('Bill Length') }} <i class="ml-1 italic">(mm)</i>
          </Label>
          <div class="col-span-2 mt-1 md:mt-0">
            <Input
              v-model="form.bill_length"
              name="bill_length"
              type="number"
              step="any"
              min="0"
            />
            <InputError :message="form.errors.bill_length" />
          </div>
        </div>
        <div class="col-span-3 md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
          <Label
            for="tail_length"
            class="md:text-right"
          >
            {{ __('Tail Length') }} <i class="ml-1 italic">(mm)</i>
          </Label>
          <div class="col-span-2 mt-1 md:mt-0">
            <Input
              v-model="form.tail_length"
              name="tail_length"
              type="number"
              step="any"
              min="0"
            />
            <InputError :message="form.errors.tail_length" />
          </div>
        </div>
        <div class="col-span-3 md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
          <Label
            for="bill_width"
            class="md:text-right"
          >
            {{ __('Bill Width') }} <i class="ml-1 italic">(mm)</i>
          </Label>
          <div class="col-span-2 mt-1 md:mt-0">
            <Input
              v-model="form.bill_width"
              name="bill_width"
              type="number"
              step="any"
              min="0"
            />
            <InputError :message="form.errors.bill_width" />
          </div>
        </div>
        <div class="col-span-3 md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
          <Label
            for="tarsus_length"
            class="md:text-right"
          >
            {{ __('Tarsus Length') }} <i class="ml-1 italic">(mm)</i>
          </Label>
          <div class="col-span-2 mt-1 md:mt-0">
            <Input
              v-model="form.tarsus_length"
              name="tarsus_length"
              type="number"
              step="any"
              min="0"
            />
            <InputError :message="form.errors.tarsus_length" />
          </div>
        </div>
        <div class="col-span-3 md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
          <Label
            for="bill_depth"
            class="md:text-right"
          >
            {{ __('Bill Depth') }} <i class="ml-1 italic">(mm)</i>
          </Label>
          <div class="col-span-2 mt-1 md:mt-0">
            <Input
              v-model="form.bill_depth"
              name="bill_depth"
              type="number"
              step="any"
              min="0"
            />
            <InputError :message="form.errors.bill_depth" />
          </div>
        </div>
        <div class="col-span-3 md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
          <Label
            for="middle_toe_length"
            class="md:text-right"
          >
            {{ __('Middle Toe Length') }} <i class="ml-1 italic">(mm)</i>
          </Label>
          <div class="col-span-2 mt-1 md:mt-0">
            <Input
              v-model="form.middle_toe_length"
              name="middle_toe_length"
              type="number"
              step="any"
              min="0"
            />
            <InputError :message="form.errors.middle_toe_length" />
          </div>
        </div>
        <div class="col-span-3 md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
          <Label
            for="head_bill_length"
            class="md:text-right"
          >
            {{ __('Head-Bill Length') }} <i class="ml-1 italic">(mm)</i>
          </Label>
          <div class="col-span-2 mt-1 md:mt-0">
            <Input
              v-model="form.head_bill_length"
              name="head_bill_length"
              type="number"
              step="any"
              min="0"
            />
            <InputError :message="form.errors.head_bill_length" />
          </div>
        </div>
        <div class="col-span-3 md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
          <Label
            for="hallux_length"
            class="md:text-right"
          >
            {{ __('Hallux Length') }} <i class="ml-1 italic">(mm)</i>
          </Label>
          <div class="col-span-2 mt-1 md:mt-0">
            <Input
              v-model="form.hallux_length"
              name="hallux_length"
              type="number"
              step="any"
              min="0"
            />
            <InputError :message="form.errors.hallux_length" />
          </div>
        </div>
        <div class="col-span-3 md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
          <Label
            for="culmen"
            class="md:text-right"
          >
            {{ __('Culmen') }} <i class="ml-1 italic">(mm)</i>
          </Label>
          <div class="col-span-2 mt-1 md:mt-0">
            <Input
              v-model="form.culmen"
              name="culmen"
              type="number"
              step="any"
              min="0"
            />
            <InputError :message="form.errors.culmen" />
          </div>
        </div>
        <div class="col-span-3 md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
          <Label
            for="toe_pad_length"
            class="md:text-right"
          >
            {{ __('Toe Pad Length') }} <i class="ml-1 italic">(mm)</i>
          </Label>
          <div class="col-span-2 mt-1 md:mt-0">
            <Input
              v-model="form.toe_pad_length"
              name="toe_pad_length"
              type="number"
              step="any"
              min="0"
            />
            <InputError :message="form.errors.toe_pad_length" />
          </div>
        </div>
        <div class="col-span-3 md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
          <Label
            for="exposed_culmen"
            class="md:text-right"
          >
            {{ __('Exposed Culmen') }} <i class="ml-1 italic">(mm)</i>
          </Label>
          <div class="col-span-2 mt-1 md:mt-0">
            <Input
              v-model="form.exposed_culmen"
              name="exposed_culmen"
              type="number"
              step="any"
              min="0"
            />
            <InputError :message="form.errors.exposed_culmen" />
          </div>
        </div>
        <div class="col-span-6 md:grid md:grid-cols-6 md:gap-x-2 md:items-center">
          <Label
            for="samples_collected"
            class="md:text-right"
          >
            {{ __('Samples Collected') }}
          </Label>
          <div class="col-span-5 mt-1 md:mt-0">
            <div class="flex flex-wrap gap-x-4 gap-y-3 sapce-between ">
              <div
                v-for="sample in $page.props.options.samplesCollected"
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
                  <Label
                    :for="sample.value"
                    class="ml-2 font-normal whitespace-nowrap"
                  >{{ sample.label }}</Label>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-span-6 md:grid md:grid-cols-6 md:gap-x-2 md:items-center">
          <Label
            for="morphometric_remarks"
            class="md:text-right"
          >
            {{ __('Remarks') }}
          </Label>
          <div class="col-span-5 mt-1 md:mt-0">
            <Input
              v-model="form.remarks"
              name="morphometric_remarks"
            />
          </div>
        </div>
      </div>
    </div>
    <template
      v-if="canSubmit"
      #footing
    >
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

<script setup>
import Panel from '@/Components/Panel.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import Checkbox from '@/Components/FormElements/Checkbox.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import RequiredInput from '@/Components/FormElements/RequiredInput.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import autoSave from '@/Mixins/AutoSave';
import hoistForm from '@/Mixins/HoistForm';
</script>

<script>
  export default {
    mixins: [autoSave, hoistForm],
    props: {
      morphometric: {
        type: Object,
        default: () => ({})
      },
      enforceRequired: {
        type: Boolean,
        default: true
      }
    },
    data() {
      return {
        form: this.$inertia.form({
          measured_at: this.morphometric?.measured_at,
          bill_length: this.morphometric?.bill_length,
          bill_width: this.morphometric?.bill_width,
          bill_depth: this.morphometric?.bill_depth,
          head_bill_length: this.morphometric?.head_bill_length,
          culmen: this.morphometric?.culmen,
          exposed_culmen: this.morphometric?.exposed_culmen,
          wing_chord: this.morphometric?.wing_chord,
          flat_wing: this.morphometric?.flat_wing,
          tarsus_length: this.morphometric?.tarsus_length,
          middle_toe_length: this.morphometric?.middle_toe_length,
          toe_pad_length: this.morphometric?.toe_pad_length,
          hallux_length: this.morphometric?.hallux_length,
          tail_length: this.morphometric?.tail_length,
          weight: this.morphometric?.weight,
          samples_collected: this.morphometric?.samples_collected || [],
          remarks: this.morphometric?.remarks,
        })
      }
    },
    methods: {
      save() {
        if (this.canSubmit) {
          this.form.put(this.route('patients.research.morphometrics.update', {
            patient: this.$page.props.admission.patient
          }), {
            preserveScroll: true,
            onError: () => this.stopAutoSave()
          });
        }
      }
    }
  }
</script>
