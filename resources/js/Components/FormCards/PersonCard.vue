<script setup>
import {computed} from 'vue';
import {usePage} from '@inertiajs/vue3';
import Panel from '@/Components/Panel.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import Toggle from '@/Components/FormElements/Toggle.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import CustomFields from '@/Components/FormElements/CustomFields.vue';
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
  },
  affiliation: {
    type: String,
    default: ''
  },
});

const emit = defineEmits(['submitted']);

const subdivisions = computed(() => usePage().props.options.subdivisionOptions);

const doSubmit = () => emit('submitted');
</script>

<template>
  <Panel>
    <template #heading>
      {{ affiliation }}
    </template>
    <template #content>
      <CustomFields
        v-if="false"
        v-model:customValues="form.custom_values"
        group="Person"
        location="Top"
        :enforceRequired="enforceRequired"
        class="col-span-6"
      />
      <FormRow
        id="entity_id"
        :label="__('Entity')"
        class="col-span-6 md:col-span-3"
      >
        <SelectInput
          v-model="form.entity_id"
          name="entity_id"
          :options="$page.props.options.personEntityTypesOptions"
          hasBlankOption
        />
        <InputError
          :message="form.errors?.entity_id"
          class="mt-2"
        />
      </FormRow>
      <FormRow
        id="organization"
        :label="__('Organization')"
        class="col-span-6 md:col-span-3"
      >
        <TextInput
          v-model="form.organization"
          name="organization"
          autoComplete="people.organization"
        />
        <InputError
          :message="form.errors?.organization"
          class="mt-2"
        />
      </FormRow>
      <FormRow
        id="first_name"
        :label="__('First Name')"
        class="col-span-6 md:col-span-3"
      >
        <TextInput
          v-model="form.first_name"
          name="first_name"
        />
        <InputError
          :message="form.errors?.first_name"
          class="mt-2"
        />
      </FormRow>
      <FormRow
        id="last_name"
        :label="__('Last Name')"
        class="col-span-6 md:col-span-3"
      >
        <TextInput
          v-model="form.last_name"
          name="last_name"
        />
        <InputError
          :message="form.errors?.last_name"
          class="mt-2"
        />
      </FormRow>
      <FormRow
        id="phone"
        :label="__('Phone Number')"
        class="col-span-6 md:col-span-2"
      >
        <TextInput
          v-model="form.phone"
          name="phone"
        />
        <InputError
          :message="form.errors?.phone"
          class="mt-2"
        />
      </FormRow>
      <FormRow
        id="alt_phone"
        :label="__('Alt. Phone Number')"
        class="col-span-6 md:col-span-2"
      >
        <TextInput
          v-model="form.alt_phone"
          name="alt_phone"
        />
        <InputError
          :message="form.errors?.alt_phone"
          class="mt-2"
        />
      </FormRow>
      <FormRow
        id="email"
        :label="__('Email')"
        class="col-span-6 md:col-span-2"
      >
        <TextInput
          v-model="form.email"
          name="email"
          type="email"
        />
        <InputError
          :message="form.errors?.email"
          class="mt-2"
        />
      </FormRow>
      <FormRow
        id="address"
        :label="__('Address')"
        class="col-span-6"
      >
        <TextInput
          v-model="form.address"
          name="address"
        />
      </FormRow>
      <FormRow
        id="city"
        :label="__('City')"
        class="col-span-6  md:col-span-2"
      >
        <TextInput
          v-model="form.city"
          name="city"
          class="mr-2"
          autoComplete="people.city"
        />
      </FormRow>
      <FormRow
        id="subdivision"
        :label="__('State')"
        class="col-span-6  md:col-span-2"
      >
        <SelectInput
          v-model="form.subdivision"
          name="subdivision"
          :options="subdivisions"
        />
      </FormRow>
      <FormRow
        id="postal_code"
        :label="__('Postal Code')"
        class="col-span-6  md:col-span-2"
      >
        <TextInput
          v-model="form.postal_code"
          name="postal_code"
          autoComplete="people.postal_code"
        />
      </FormRow>
      <FormRow
        id="notes"
        :label="__('Notes')"
        class="col-span-6"
      >
        <TextInput
          v-model="form.notes"
          name="notes"
        />
      </FormRow>
      <FormRow class="col-span-6 md:col-span-2">
        <Toggle
          v-model="form.is_volunteer"
          dusk="is_volunteer"
          class="mr-2"
          :label="__('Is a Volunteer?')"
        />
      </FormRow>
      <FormRow class="col-span-6 md:col-span-2">
        <Toggle
          v-model="form.is_member"
          dusk="is_member"
          class="mr-2"
          :label="__('Is a Member?')"
        />
      </FormRow>
      <FormRow class="col-span-6 md:col-span-2">
        <Toggle
          v-model="form.no_solicitations"
          dusk="no_solicitations"
          class="mr-2"
          :label="__('No Solicitations?')"
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
          <span class="text-red-600">{{ __('There are unsaved changes.') }}</span>
        </ActionMessage>
        <ActionMessage
          :on="form.recentlySuccessful"
          class="mr-3"
        >
          {{ __('Saved.') }}
        </ActionMessage>
        <PrimaryButton
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
          @click="doSubmit"
        >
          {{ __('Update :affiliation', {affiliation: affiliation}) }}
        </PrimaryButton>
      </div>
    </template>
  </Panel>
</template>
