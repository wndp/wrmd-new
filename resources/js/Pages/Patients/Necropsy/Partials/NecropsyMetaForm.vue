<script setup>
import {useForm} from '@inertiajs/vue3';
import Panel from '@/Components/Panel.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import Toggle from '@/Components/FormElements/Toggle.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
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
  necropsy: {
    type: Object,
    required: true
  },
  enforceRequired: {
    type: Boolean,
    default: false
  }
});

const form = useForm({
  necropsied_at: props.necropsy.necropsied_at,
  prosector: props.necropsy.prosector,
  carcass_condition: props.necropsy.carcass_condition,
  is_photos_collected: props.necropsy.is_photos_collected,
  is_carcass_radiographed: props.necropsy.is_carcass_radiographed,
  is_previously_frozen: props.necropsy.is_previously_frozen,
  is_scavenged: props.necropsy.is_scavenged,
  is_discarded: props.necropsy.is_discarded,
});

const save = () => {
  form.put(route('patients.necropsy.update', {
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
      {{ __('Necropsy') }}
    </template>
    <template #content>
      <FormRow
        id="necropsied_at"
        :label="__('Date')"
        :required="enforceRequired"
        class="col-span-6 md:col-span-2"
      >
        <DatePicker
          id="necropsied_at"
          v-model="form.necropsied_at"
          time
          :required="enforceRequired"
        />
        <InputError :message="form.errors.necropsied_at" />
      </FormRow>
      <FormRow
        id="prosector"
        :label="__('Prosector')"
        :required="enforceRequired"
        class="col-span-6 md:col-span-2"
      >
        <TextInput
          v-model="form.prosector"
          name="prosector"
          :required="enforceRequired"
        />
        <InputError :message="form.errors.prosector" />
      </FormRow>
      <FormRow
        id="carcass_condition_id"
        :label="__('Carcass Condition')"
        class="col-span-6 md:col-span-2"
      >
        <SelectInput
          v-model="form.carcass_condition_id"
          name="carcass_condition_id"
          :options="$page.props.options.necropsyCarcassConditionsOptions"
        />
        <InputError :message="form.errors.carcass_condition_id" />
      </FormRow>
      <FormRow
        id="is_photos_collected"
        class="col-span-6 md:col-span-2 mt-4"
      >
        <Toggle
          v-model="form.is_photos_collected"
          dusk="is_photos_collected"
        >
          {{ __('Photos Collected?') }}
        </Toggle>
      </FormRow>
      <FormRow
        id="is_carcass_radiographed"
        class="col-span-6 md:col-span-2 mt-4"
      >
        <Toggle
          v-model="form.is_carcass_radiographed"
          dusk="is_carcass_radiographed"
        >
          {{ __('Carcass Radiographed?') }}
        </Toggle>
      </FormRow>
      <FormRow
        id="is_previously_frozen"
        class="col-span-6 md:col-span-2 mt-4"
      >
        <Toggle
          v-model="form.is_previously_frozen"
          dusk="is_previously_frozen"
        >
          {{ __('Carcass Frozen?') }}
        </Toggle>
      </FormRow>
      <FormRow
        id="is_scavenged"
        class="col-span-6 md:col-span-2 mt-4"
      >
        <Toggle
          v-model="form.is_scavenged"
          dusk="is_scavenged"
        >
          {{ __('Carcass Scavenged?') }}
        </Toggle>
      </FormRow>
      <FormRow
        id="is_scavenged"
        class="col-span-6 md:col-span-2 mt-4"
      >
        <Toggle
          v-model="form.is_discarded"
          dusk="is_discarded"
        >
          {{ __('Carcass Discarded After Necropsy?') }}
        </Toggle>
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
          {{ __('Update Necropsy') }}
        </PrimaryButton>
      </div>
    </template>
  </Panel>
</template>
