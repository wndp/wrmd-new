<script setup>
import {useForm} from '@inertiajs/vue3';
import FormSection from '@/Components/FormElements/FormSection.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import TextareaInput from '@/Components/FormElements/TextareaInput.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import Toggle from '@/Components/FormElements/Toggle.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';

const props = defineProps({
  team: {
    type: Object,
    required: true
  },
});

const form = useForm({
    status: props.team.status,
    is_master_account: props.team.is_master_account,
    name: props.team.name,
    federal_permit_number: props.team.federal_permit_number,
    subdivision_permit_number: props.team.subdivision_permit_number,
    country: props.team.country,
    address: props.team.address,
    city: props.team.city,
    subdivision: props.team.subdivision,
    postal_code: props.team.postal_code,
    contact_name: props.team.contact_name,
    contact_email: props.team.contact_email,
    phone_number: props.team.phone_number,
    website: props.team.website,
    notes: props.team.notes,
});

const updateProfile = () => {
    form.put(route('teams.update', {
        team: props.team
    }), {
        preserveScroll: true,
    });
};
</script>

<template>
  <FormSection>
    <template #title>
      Account Profile
    </template>
    <div class="col-span-2 sm:col-span-1">
      <InputLabel for="status">
        Status
      </InputLabel>
      <SelectInput
        v-model="form.status"
        :options="$page.props.options.accountStatusOptions"
        name="status"
        required
        class="mt-1"
      />
      <InputError
        :message="form.errors.status"
        class="mt-2"
      />
    </div>
    <div class="col-span-2 sm:col-span-1">
      <div v-if="team.master_account_id === null">
        <InputLabel for="master_account_id">
          Is a Master Account?
        </InputLabel>
        <Toggle
          v-model="form.is_master_account"
          class="mt-3"
        />
        <InputError
          :message="form.errors.is_master_account"
          class="mt-2"
        />
      </div>
    </div>
    <div class="col-span-4 sm:col-span-2">
      <InputLabel for="name">
        Organization
      </InputLabel>
      <TextInput
        v-model="form.name"
        name="name"
        required
        autocomplete="name"
        class="mt-1"
      />
      <InputError
        :message="form.errors.name"
        class="mt-2"
      />
    </div>
    <div class="col-span-4 sm:col-span-2">
      <InputLabel for="first-name">
        Contact name
      </InputLabel>
      <TextInput
        v-model="form.contact_name"
        name="contact_name"
        required
        autocomplete="given-name"
        class="mt-1"
      />
      <InputError
        :message="form.errors.contact_name"
        class="mt-2"
      />
    </div>
    <div class="col-span-4 sm:col-span-2">
      <InputLabel for="country">
        Country / Region
      </InputLabel>
      <SelectInput
        v-model="form.country"
        name="country"
        required
        autocomplete="country"
        :options="$page.props.options.countryOptions"
        class="mt-1 block w-full"
      />
      <InputError
        :message="form.errors.country"
        class="mt-2"
      />
    </div>
    <div class="col-span-4 sm:col-span-2">
      <InputLabel for="address">
        Address
      </InputLabel>
      <TextInput
        v-model="form.address"
        name="address"
        required
        autocomplete="address-line1"
        class="mt-1"
      />
      <InputError
        :message="form.errors.address"
        class="mt-2"
      />
    </div>
    <div class="col-span-4 sm:col-span-2">
      <InputLabel for="city">
        City
      </InputLabel>
      <TextInput
        v-model="form.city"
        name="city"
        required
        autocomplete="address-level2"
        class="mt-1"
      />
      <InputError
        :message="form.errors.city"
        class="mt-2"
      />
    </div>
    <div class="col-span-4 sm:col-span-2">
      <InputLabel for="subdivision">
        State
      </InputLabel>
      <SelectInput
        v-model="form.subdivision"
        name="subdivision"
        :options="$page.props.options.subdivisionOptions"
        required
        class="mt-1"
      />
      <InputError
        :message="form.errors.subdivision"
        class="mt-2"
      />
    </div>
    <div class="col-span-4 sm:col-span-2">
      <InputLabel for="postal-code">
        ZIP / Postal
      </InputLabel>
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
    </div>
    <div class="col-span-4 sm:col-span-2">
      <InputLabel for="email-address">
        Email address
      </InputLabel>
      <TextInput
        v-model="form.contact_email"
        type="email"
        name="contact_email"
        required
        autocomplete="email"
        class="mt-1"
      />
      <InputError
        :message="form.errors.contact_email"
        class="mt-2"
      />
    </div>
    <div class="col-span-4 sm:col-span-2">
      <InputLabel for="phone-number">
        Phone Number
      </InputLabel>
      <TextInput
        v-model="form.phone_number"
        name="phone_number"
        required
        autocomplete="tel"
        class="mt-1"
      />
      <InputError
        :message="form.errors.phone_number"
        class="mt-2"
      />
    </div>
    <div class="col-span-4 sm:col-span-2">
      <InputLabel for="website">
        Website
      </InputLabel>
      <TextInput
        v-model="form.website"
        type="url"
        name="website"
        autocomplete="url"
        class="mt-1"
      />
      <InputError
        :message="form.errors.website"
        class="mt-2"
      />
    </div>
    <div class="col-span-2 sm:col-span-1">
      <InputLabel for="federal-number">
        Federal Permit #
      </InputLabel>
      <TextInput
        v-model="form.federal_permit_number"
        name="federal_permit_number"
        autocomplete="off"
        class="mt-1"
      />
    </div>
    <div class="col-span-2 sm:col-span-1">
      <InputLabel for="subdivision-number">
        State Permit #
      </InputLabel>
      <TextInput
        v-model="form.subdivision_permit_number"
        name="subdivision_permit_number"
        autocomplete="off"
        class="mt-1"
      />
    </div>
    <div class="col-span-4">
      <InputLabel for="notes">
        Notes
      </InputLabel>
      <TextareaInput
        v-model="form.notes"
        type="url"
        name="notes"
        class="mt-1"
      />
    </div>

    <template #actions>
      <ActionMessage
        :on="form.recentlySuccessful"
        class="mr-3"
      >
        Saved.
      </ActionMessage>
      <PrimaryButton
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
        @click="updateProfile"
      >
        Update Account
      </PrimaryButton>
    </template>
  </FormSection>
</template>
