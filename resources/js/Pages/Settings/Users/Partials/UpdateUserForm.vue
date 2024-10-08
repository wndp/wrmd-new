<script setup>
import { inject, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import FormSection from '@/Components/FormElements/FormSection.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import Alert from '@/Components/Alert.vue';
import {__} from '@/Composables/Translate';

const route = inject('route');

const props = defineProps({
  user: {
    type: Object,
    required: true
  }
})

const form = useForm({
  name: props.user.name,
  email: props.user.email,
  email_confirmation: '',
  role: props.user.role_name,
  password: '',
  password_confirmation: ''
});

// Was this user created by the usePage().props.auth.team.id?
const belongsToTeam = computed(() => true);
// props.user.parent_account_id === usePage().props.auth.team.id

const updateUser = () => {
  form.put(route('users.update', props.user.id), {
    preserveScroll: true,
  });
};
</script>

<template>
  <FormSection>
    <template #title>
      {{ __('Update :user', {user: user.name}) }}
    </template>
    <Alert
      color="red"
      class="col-span-4"
    >
      {{ __('Updating the users role will also reset their permissions to the new roles defaults.') }}
    </Alert>
    <template v-if="belongsToTeam">
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
        <InputLabel for="email_confirmation">
          {{ __('Email address confirmation') }}
        </InputLabel>
        <TextInput
          v-model="form.email_confirmation"
          type="email"
          name="email_confirmation"
          autocomplete="off"
          class="mt-1"
        />
      </div>
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
        <InputLabel for="role">
          {{ __('Role') }}
        </InputLabel>
        <SelectInput
          ref="role"
          v-model="form.role"
          name="role"
          :options="$page.props.options.roles"
          class="mt-1"
        />
        <InputError
          :message="form.errors.role"
          class="mt-2"
        />
      </div>
      <Alert class="col-span-4">
        {{ __("Only fill in the password fields if you are changing :user's password", {user: user.name}) }}.
      </Alert>
      <div class="col-span-4 sm:col-span-2">
        <InputLabel for="password">
          {{ __('Password') }}
        </InputLabel>
        <TextInput
          v-model="form.password"
          type="password"
          name="password"
          autocomplete="off"
          class="mt-1"
        />
        <InputError
          :message="form.errors.password"
          class="mt-2"
        />
      </div>
      <div class="col-span-4 sm:col-span-2">
        <InputLabel for="password_confirmation">
          {{ __('Password Confirmation') }}
        </InputLabel>
        <TextInput
          v-model="form.password_confirmation"
          type="password"
          name="password_confirmation"
          autocomplete="off"
          class="mt-1"
        />
      </div>
    </template>
    <template v-else>
      <Alert class="col-span-4">
        {{ __('This email belongs to a user of a different account. You can update their role on your account but you can not update their email address, name or password.') }}
      </Alert>
      <div class="col-span-4 sm:col-span-2">
        <InputLabel for="name">
          {{ __('Email address') }}
        </InputLabel>
        <p class="text-sm mt-1">
          {{ user.email }}
        </p>
      </div>
      <div class="col-span-4 sm:col-span-2">
        <InputLabel for="role">
          {{ __('Role') }}
        </InputLabel>
        <SelectInput
          ref="role"
          v-model="form.role"
          name="role"
          :options="$page.props.options.roles"
          class="mt-1"
        />
        <InputError
          :message="form.errors.role"
          class="mt-2"
        />
      </div>
      <div class="col-span-4 sm:col-span-2">
        <InputLabel for="name">
          {{ __('Name') }}
        </InputLabel>
        <p class="text-sm mt-1">
          {{ user.name }}
        </p>
      </div>
    </template>
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
        @click="updateUser"
      >
        {{ __('Update User') }}
      </PrimaryButton>
    </template>
  </FormSection>
</template>
