<script setup>
import {computed} from 'vue';
import {usePage} from '@inertiajs/vue3';
import Panel from '@/Components/Panel.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import Toggle from '@/Components/FormElements/Toggle.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import CommonName from '@/Components/FormElements/CommonName.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import {__} from '@/Composables/Translate';

defineProps({
  form: {
    type: Object,
    default: () => ({})
  },
  canSubmit: {
    type: Boolean,
    default: true
  },
  enforceRequired: {
    type: Boolean,
    default: true
  }
});

const emit = defineEmits(['submitted']);

const categoryOptions = computed(() => {
  return [
    {
      label: __('Wildlife'),
      group: usePage().props.options.hotlineWildlifeCategoriesOptions,
    },
    {
      label: __('Administrative'),
      group: usePage().props.options.hotlineAdministrativeCategoriesOptions,
    },
    {
      label: __('Other'),
      group: usePage().props.options.hotlineOtherCategoriesOptions
    }
  ];
});
</script>

<template>
  <Panel>
    <template #content>
      <FormRow
        id="reported_at"
        :label="__('Date Reported')"
        class="col-span-6 md:col-span-2"
        :required="enforceRequired"
      >
        <DatePicker
          id="reported_at"
          v-model="form.reported_at"
          time
          :required="enforceRequired"
        />
        <InputError :message="form.errors?.reported_at" />
      </FormRow>
      <FormRow
        id="occurred_at"
        :label="__('Date Occurred')"
        class="col-span-6 md:col-span-2"
        :required="enforceRequired"
      >
        <DatePicker
          id="occurred_at"
          v-model="form.occurred_at"
          time
          :required="enforceRequired"
        />
        <InputError :message="form.errors?.occurred_at" />
      </FormRow>
      <FormRow
        id="recorded_by"
        :label="__('Recorded By')"
        class="col-span-6 md:col-span-2"
        :required="enforceRequired"
      >
        <TextInput
          v-model="form.recorded_by"
          name="recorded_by"
        />
        <InputError :message="form.errors?.recorded_by" />
      </FormRow>
      <FormRow
        id="duration_of_call"
        :label="__('Duration of Call')"
        class="col-span-6 md:col-span-2"
      >
        <TextInput
          v-model="form.duration_of_call"
          name="duration_of_call"
          type="number"
          min="1"
          step="0.5"
        />
        <InputError :message="form.errors?.duration_of_call" />
      </FormRow>
      <FormRow
        id="suspected_species"
        :label="__('Suspected Species')"
        class="col-span-6 md:col-span-2"
      >
        <CommonName
          v-model="form.suspected_species"
          name="suspected_species"
        />
        <InputError :message="form.errors?.suspected_species" />
      </FormRow>
      <FormRow
        id="number_of_animals"
        :label="__('Number of Animals')"
        class="col-span-6 md:col-span-2"
      >
        <TextInput
          v-model="form.number_of_animals"
          name="number_of_animals"
          type="number"
          min="1"
          step="0.5"
        />
        <InputError :message="form.errors?.number_of_animals" />
      </FormRow>
      <FormRow
        id="category_id"
        :label="__('Category')"
        class="col-span-6 md:col-span-2"
        :required="enforceRequired"
      >
        <SelectInput
          v-model="form.category_id"
          name="category_id"
          :options="categoryOptions"
        />
        <InputError :message="form.errors?.category_id" />
      </FormRow>
      <FormRow
        id="incident_status_id"
        :label="__('Status')"
        class="col-span-6 md:col-span-2"
        :required="enforceRequired"
      >
        <SelectInput
          v-model="form.incident_status_id"
          name="incident_status_id"
          :options="$page.props.options.hotlineStatusesOptions"
        />
        <InputError :message="form.errors?.incident_status_id" />
      </FormRow>
      <FormRow
        id="is_priority"
        class="col-span-6 md:col-span-2"
      >
        <Toggle
          v-model="form.is_priority"
          :label="__('Is this a priority?')"
          class="md:mt-8"
          dusk="is_priority"
        />
      </FormRow>
    </template>
    <template
      v-if="canSubmit"
      #actions
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
          @click="emit('submitted')"
        >
          {{ __('Update Incident') }}
        </PrimaryButton>
      </div>
    </template>
  </Panel>
</template>
