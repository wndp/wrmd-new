<template>
  <Panel>
    <template #heading>
      {{ __('Morphometrics') }}
    </template>
    <div class="space-y-4 md:space-y-2">
      <div class="grid lg:grid-cols-6 gap-2 md:items-center mb-6">
        <div class="col-span-3 md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
          <Label
            for="weight"
            class="md:text-right"
          >{{ __('Weight') }}</Label>
          <div class="col-span-2 mt-1 md:mt-0">
            <InputWithUnit
              v-model:text="form.weight"
              v-model:unit="form.weight_unit"
              name="weight"
              type="number"
              step="any"
              min="0"
              :units="$page.props.options.weightUnits"
            />
          </div>
        </div>
        <div class="col-span-3 md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
          <Label
            for="age"
            class="md:text-right"
          >{{ __('Age') }}</Label>
          <div class="col-span-2 mt-1 md:mt-0">
            <InputWithUnit
              v-model:text="form.age"
              v-model:unit="form.age_unit"
              name="age"
              :units="$page.props.options.ageUnits"
            />
          </div>
        </div>
        <div class="col-span-3 md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
          <Label
            for="sex"
            class="md:text-right"
          >{{ __('Sex') }}</Label>
          <div class="col-span-2 mt-1 md:mt-0">
            <Select
              v-model="form.sex"
              name="sex"
              :options="$page.props.options.sexes"
            />
          </div>
        </div>
        <div class="col-span-3 md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
          <Label
            for="bcs"
            class="md:text-right"
          >{{ __('Body Condition') }}</Label>
          <div class="col-span-2 mt-1 md:mt-0">
            <Select
              v-model="form.bcs"
              name="bcs"
              :options="$page.props.options.bodyConditions"
            />
          </div>
        </div>
        <div class="col-span-3 md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
          <Label
            for="wing"
            class="md:text-right"
          >{{ __('Unflat Wing') }}</Label>
          <div class="col-span-2 mt-1 md:mt-0 flex items-center">
            <Input
              v-model="form.wing"
              name="wing"
              type="number"
            />
            <span class="text-gray-600 text-sm ml-2">(mm)</span>
          </div>
        </div>
        <div class="col-span-3 md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
          <Label
            for="tarsus"
            class="md:text-right"
          >{{ __('Tarsus') }}</Label>
          <div class="col-span-2 mt-1 md:mt-0 flex items-center">
            <Input
              v-model="form.tarsus"
              name="tarsus"
              type="number"
            />
            <span class="text-gray-600 text-sm ml-2">(mm)</span>
          </div>
        </div>
        <div class="col-span-3 md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
          <Label
            for="culmen"
            class="md:text-right"
          >{{ __('Culmen') }}</Label>
          <div class="col-span-2 mt-1 md:mt-0 flex items-center">
            <Input
              v-model="form.culmen"
              name="culmen"
              type="number"
            />
            <span class="text-gray-600 text-sm ml-2">(mm)</span>
          </div>
        </div>
        <div class="col-span-3 md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
          <Label
            for="exposed_culmen"
            class="md:text-right"
          >{{ __('Exposed Culmen') }}</Label>
          <div class="col-span-2 mt-1 md:mt-0 flex items-center">
            <Input
              v-model="form.exposed_culmen"
              name="exposed_culmen"
              type="number"
            />
            <span class="text-gray-600 text-sm ml-2">(mm)</span>
          </div>
        </div>
        <div class="col-span-3 md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
          <Label
            for="bill_depth"
            class="md:text-right"
          >{{ __('Bill Depth') }}</Label>
          <div class="col-span-2 mt-1 md:mt-0 flex items-center">
            <Input
              v-model="form.bill_depth"
              name="bill_depth"
              type="number"
            />
            <span class="text-gray-600 text-sm ml-2">(mm)</span>
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
          {{ __('Update Morphometrics Details') }}
        </PrimaryButton>
      </div>
    </template>
  </Panel>
</template>

<script setup>
import Panel from '@/Components/Panel.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import InputWithUnit from '@/Components/FormElements/InputWithUnit.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import autoSave from '@/Mixins/AutoSave';
import hoistForm from '@/Mixins/HoistForm';
</script>

<script>
  export default {
    mixins: [autoSave, hoistForm],
    props: {
      necropsy: {
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
          weight: this.necropsy?.weight,
          weight_unit: this.necropsy?.weight_unit,
          age: this.necropsy?.age,
          age_unit: this.necropsy?.age_unit,
          sex: this.necropsy?.sex,
          bcs: this.necropsy?.bcs,
          wing: this.necropsy?.wing,
          tarsus: this.necropsy?.tarsus,
          culmen: this.necropsy?.culmen,
          exposed_culmen: this.necropsy?.exposed_culmen,
          bill_depth: this.necropsy?.bill_depth,
        })
      }
    },
    methods: {
      save() {
        if (this.canSubmit) {
          this.form.put(this.route('patients.necropsy.morphometrics.update', {
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
