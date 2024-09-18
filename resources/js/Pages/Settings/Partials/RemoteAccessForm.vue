<script setup>
import { inject } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Alert from '@/Components/Alert.vue';
import FormSection from '@/Components/FormElements/FormSection.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import Checkbox from '@/Components/FormElements/Checkbox.vue';
import Toggle from '@/Components/FormElements/Toggle.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import {__} from '@/Composables/Translate';

const route = inject('route');

const props = defineProps({
  ipAddress: String,
  roles: Array,
  users: Array,
  remoteAccess: Object
});

const form = useForm({
    remoteRestricted: props.remoteAccess.remoteRestricted,
    clinicIp: props.remoteAccess.clinicIp,
    roleRemotePermission: props.remoteAccess.roleRemotePermission,
    userRemotePermission: props.remoteAccess.userRemotePermission,
});

const updateRemoteAccess = () => {
    form.put(route('security.remote-access.update'), {
        preserveScroll: true
    });
};
</script>

<template>
  <FormSection>
    <template #title>
      {{ __('Restrict Remote Access') }}
    </template>
    <template #description>
      <p>{{ __('Restricting remote access will allow users to only sign into Wildlife Rehabilitation MD from a computer at your clinic.') }} <b>{{ __('You should only enable this feature from your clinic.') }}</b> {{ __('Do not turn this feature on from anywhere else. If you do, you may lock yourself out of the database.') }}</p>
    </template>
    <div class="col-span-4">
      <InputLabel for="first-name">
        {{ __('Restrict Remote Access') }}
      </InputLabel>
      <div class="mt-2">
        <Toggle
          v-model="form.remoteRestricted"
          dusk="remote-restricted"
        />
      </div>
    </div>
    <template v-if="form.remoteRestricted">
      <Alert class="col-span-4">
        <div>
          <p>
            {{ __("Don't know what your IPv4 address is? While at your clinic, visit this link to find out:") }}
            <a
              href="http://www.whatismyip.com"
              target="_blank"
              class="underline"
            >www.whatismyip.com</a>
          </p>
          <p class="mt-4">
            <b>{{ __('Right now Wildlife Rehabilitation MD thinks that your IPv4 address is :ipAddress', {ipAddress: ipAddress}) }}.</b> {{ __("If this does not match your clinic's static IPv4 address then you may not want to use this feature.") }}
          </p>
        </div>
      </Alert>
      <div class="col-span-4 md:col-span-2">
        <InputLabel for="clinic_ip">
          {{ __('Your Clinic IPv4 Address') }}
        </InputLabel>
        <TextInput
          v-model="form.clinicIp"
          name="clinic_ip"
          autocomplete="off"
          class="mt-1"
        />
        <InputError :message="form.errors.clinicIp" />
      </div>
      <div class="col-span-4">
        <InputLabel>{{ __('Allow users with these roles to have remote access.') }}</InputLabel>
        <div class="mt-1 flex sapce-between space-x-4">
          <div
            v-for="role in $page.props.options.roles"
            :key="role.value"
            class="flex items-start"
          >
            <div class="flex items-center">
              <Checkbox
                :id="role.value"
                v-model="form.roleRemotePermission"
                name="role_remote_permission[]"
                :value="role.value"
              />
              <InputLabel
                :for="role.value"
                class="ml-2 font-normal"
              >
                {{ role.label }}
              </InputLabel>
            </div>
          </div>
        </div>
      </div>
      <div class="col-span-4">
        <InputLabel>{{ __('Allow these users to have remote access.') }}</InputLabel>
        <div class="mt-1 space-y-2">
          <div
            v-for="user in users"
            :key="user.id"
            class="flex items-start"
          >
            <div class="flex items-center h-5">
              <Checkbox
                :id="user.email"
                v-model="form.userRemotePermission"
                name="user_remote_permission[]"
                :value="user.email"
              />
            </div>
            <div class="ml-3 text-sm">
              <InputLabel
                :for="user.email"
                class="font-normal"
              >
                {{ user.email }}
              </InputLabel>
            </div>
          </div>
        </div>
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
        @click="updateRemoteAccess"
      >
        {{ __('Update Remote Access Settings') }}
      </PrimaryButton>
    </template>
  </FormSection>
</template>
