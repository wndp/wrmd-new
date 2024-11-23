<script setup>
import {ref} from 'vue';
import {usePage} from '@inertiajs/vue3';
import Panel from '@/Components/Panel.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import TextareaInput from '@/Components/FormElements/TextareaInput.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import axios from 'axios';
import {__} from '@/Composables/Translate';

const props = defineProps({
  form: {
    type: Object,
    required: true
  },
  canSubmit: {
    type: Boolean,
    default: true
  },
});

const emit = defineEmits(['country-change', 'submitted']);

const subdivisionLabel = ref('State');
const mutableSubdivisions = ref(usePage().props.options.subdivisionOptions);

const onCountryChange = () => {
  axios.get('/internal-api/locale/' + props.form.country)
    .then(response => {
      subdivisionLabel.value = response.data.subdivisionType;
      mutableSubdivisions.value = response.data.subdivisions;
      props.form.subdivision = response.data.subdivisions[0].value;
      emit('country-change', response.data);
    });
}

const doSubmit = () => emit('submitted');
</script>

<script>
  // export default {
  //   data() {
  //     return {

  //     };
  //   },
  //   computed: {
  //     subdivisions() {
  //       return this.$page.props.options.subdivisions;
  //     },
  //     countries() {
  //       return this.$page.props.options.countries;
  //     }
  //   },
  //   methods: {
  //     save() {
  //       if (this.canSubmit) {
  //         this.form.put(this.route('sub_accounts.update', {
  //           form: this.form
  //         }), {
  //           preserveScroll: true,
  //           onError: () => this.stopAutoSave()
  //         });
  //       }
  //     },
  //   },
  // };
</script>

<template>
  <Panel>
    <template #title>
      {{ __('Sub-Account Details') }}
    </template>
    <template #content>
      <FormRow
        id="name"
        :label="__('Organization')"
        :required="true"
        class="col-span-6"
      >
        <TextInput
          v-model="form.name"
          name="name"
        />
        <InputError
          :message="form.errors.name"
          class="mt-1"
        />
      </FormRow>
      <FormRow
        id="contact_name"
        :label="__('Contact Name')"
        :required="true"
        class="col-span-6 md:col-span-2"
      >
        <TextInput
          v-model="form.contact_name"
          name="contact_name"
          autocomplete="off"
        />
        <InputError
          :message="form.errors.contact_name"
          class="mt-2"
        />
      </FormRow>
      <FormRow
        id="contact_email"
        :label="__('Contact Email')"
        :required="true"
        class="col-span-6 md:col-span-2"
      >
        <TextInput
          v-model="form.contact_email"
          name="contact_email"
          autocomplete="off"
          type="email"
          class="mt-1"
        />
        <InputError
          :message="form.errors.contact_email"
          class="mt-2"
        />
      </FormRow>
      <FormRow
        id="phone_number"
        :label="__('Phone Number')"
        :required="true"
        class="col-span-6 md:col-span-2"
      >
        <TextInput
          v-model="form.phone_number"
          name="phone_number"
          autocomplete="off"
          class="mt-1"
        />
        <InputError
          :message="form.errors.phone_number"
          class="mt-2"
        />
      </FormRow>
      <FormRow
        id="address"
        :label="__('Address')"
        :required="true"
        class="col-span-6"
      >
        <TextInput
          v-model="form.address"
          name="address"
          autocomplete="address-line1"
          class="mt-1"
        />
        <InputError
          :message="form.errors.address"
          class="mt-2"
        />
      </FormRow>
      <FormRow
        id="country"
        :label="__('Country / Region')"
        :required="true"
        class="col-span-6 md:col-span-2"
      >
        <SelectInput
          v-model="form.country"
          name="country"
          autocomplete="country"
          :options="$page.props.options.countryOptions"
          class="mt-1 block w-full"
          @change="onCountryChange"
        />
        <InputError
          :message="form.errors.country"
          class="mt-2"
        />
      </FormRow>
      <FormRow
        id="subdivision"
        :label="subdivisionLabel"
        :required="true"
        class="col-span-6 md:col-span-2"
      >
        <SelectInput
          v-model="form.subdivision"
          name="subdivision"
          :options="mutableSubdivisions"
          class="mt-1"
        />
        <InputError
          :message="form.errors.subdivision"
          class="mt-2"
        />
      </FormRow>
      <FormRow
        id="city"
        :label="__('City')"
        :required="true"
        class="col-span-6 md:col-span-2"
      >
        <TextInput
          v-model="form.city"
          name="city"
          autocomplete="address-level2"
          class="mt-1"
        />
        <InputError
          :message="form.errors.city"
          class="mt-2"
        />
      </FormRow>
      <FormRow
        id="postal_code"
        :label="__('ZIP / Postal')"
        :required="true"
        class="col-span-6 md:col-span-2"
      >
        <TextInput
          v-model="form.postal_code"
          name="postal_code"
          autocomplete="postal-code"
          class="mt-1"
        />
        <InputError
          :message="form.errors.postal_code"
          class="mt-2"
        />
      </FormRow>
      <FormRow
        id="federal_permit_number"
        :label="__('Federal Permit #')"
        class="col-span-6 md:col-span-2"
      >
        <TextInput
          v-model="form.federal_permit_number"
          name="federal_permit_number"
          autocomplete="off"
          class="mt-1"
        />
        <InputError
          :message="form.errors.federal_permit_number"
          class="mt-2"
        />
      </FormRow>
      <FormRow
        id="subdivision_permit_number"
        :label="__('State Permit #')"
        class="col-span-6 md:col-span-2"
      >
        <TextInput
          v-model="form.subdivision_permit_number"
          name="subdivision_permit_number"
          autocomplete="off"
          class="mt-1"
        />
        <InputError
          :message="form.errors.subdivision_permit_number"
          class="mt-2"
        />
      </FormRow>
      <FormRow
        id="notes"
        :label="__('Notes')"
        class="col-span-6"
      >
        <TextareaInput
          v-model="form.notes"
          name="notes"
          autocomplete="off"
          class="mt-1"
        />
        <InputError
          :message="form.errors.notes"
          class="mt-2"
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
          @click="doSubmit"
        >
          {{ __('Update Sub-Account') }}
        </PrimaryButton>
      </div>
    </template>
  </Panel>
</template>
