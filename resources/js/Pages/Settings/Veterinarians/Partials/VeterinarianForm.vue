<script setup>
import FormSection from '@/Components/FormElements/FormSection.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import {__} from '@/Composables/Translate';

defineProps({
  title: {
    type: String,
    required: true
  },
  form: {
    type: Object,
    default: () => ({})
  },
  action: {
    type: String,
    required: true
  },
  users: {
    type: Array,
    required: true
  }
});

defineEmits(['actionClicked']);
</script>

<template>
  <FormSection>
    <template #title>
      {{ title }}
    </template>
    <template #description>
      {{ __('Adding veterinarians to your account allows them to assign duties and responsibilities (daily tasks, prescriptions, ...) under their authority. You may include address and contact information for your veterinarians if they do not primary work at your organization.') }}
    </template>
    <div class="col-span-4 sm:col-span-2">
      <InputLabel for="name">
        {{ __('Name') }}
      </InputLabel>
      <TextInput
        v-model="form.name"
        name="name"
        autocomplete="given-name"
        class="mt-1"
      />
      <InputError
        :message="form.errors.name"
        class="mt-2"
      />
    </div>
    <div class="col-span-4 sm:col-span-2">
      <InputLabel for="license">
        {{ __('License Number') }}
      </InputLabel>
      <TextInput
        v-model="form.license"
        name="license"
        autocomplete="off"
        class="mt-1"
      />
      <InputError
        :message="form.errors.license"
        class="mt-2"
      />
    </div>
    <div class="col-span-4 sm:col-span-2">
      <InputLabel for="user_id">
        {{ __('WRMD User') }}
      </InputLabel>
      <SelectInput
        v-model="form.user_id"
        name="user_id"
        :options="$page.props.users"
        class="mt-1"
      />
      <InputError
        :message="form.errors.user_id"
        class="mt-2"
      />
    </div>
    <div class="col-span-4 sm:col-span-2">
      <InputLabel for="business_name">
        {{ __('Business Name') }}
      </InputLabel>
      <TextInput
        v-model="form.business_name"
        name="business_name"
        autocomplete="organization"
        class="mt-1"
      />
      <InputError
        :message="form.errors.business_name"
        class="mt-2"
      />
    </div>
    <div class="col-span-4 sm:col-span-2">
      <InputLabel for="address">
        {{ __('Address') }}
      </InputLabel>
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
    </div>
    <div class="col-span-4 sm:col-span-2">
      <InputLabel for="city">
        {{ __('City') }}
      </InputLabel>
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
    </div>
    <div class="col-span-4 sm:col-span-2">
      <InputLabel for="subdivision">
        {{ __('State') }}
      </InputLabel>
      <SelectInput
        v-model="form.subdivision"
        name="subdivision"
        :options="$page.props.options.subdivisions"
        class="mt-1"
      />
      <InputError
        :message="form.errors.subdivision"
        class="mt-2"
      />
    </div>
    <div class="col-span-4 sm:col-span-2">
      <InputLabel for="postal-code">
        {{ __('ZIP / Postal') }}
      </InputLabel>
      <TextInput
        v-model="form.postal_code"
        name="postal-code"
        autocomplete="postal-code"
        class="mt-1"
      />
      <InputError
        :message="form.errors.postal_code"
        class="mt-2"
      />
    </div>
    <div class="col-span-4 sm:col-span-2">
      <InputLabel for="email">
        {{ __('Email address') }}
      </InputLabel>
      <TextInput
        v-model="form.email"
        type="email"
        name="email"
        autocomplete="email"
        class="mt-1"
      />
      <InputError
        :message="form.errors.email"
        class="mt-2"
      />
    </div>
    <div class="col-span-4 sm:col-span-2">
      <InputLabel for="email">
        {{ __('Phone Number') }}
      </InputLabel>
      <TextInput
        v-model="form.phone"
        name="phone"
        autocomplete="tel"
        class="mt-1"
      />
      <InputError
        :message="form.errors.phone"
        class="mt-2"
      />
    </div>
    <template #actions>
      <ActionMessage
        :on="form.recentlySuccessful"
        class="mr-3"
      >
        {{ __('Saved.') }}
      </ActionMessage>
      <PrimaryButton
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
        @click="$emit('actionClicked')"
      >
        {{ action }}
      </PrimaryButton>
    </template>
  </FormSection>
</template>
