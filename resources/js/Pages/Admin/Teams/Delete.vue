<script setup>
import {ref} from 'vue';
import {useForm} from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import TeamHeader from './Partials/TeamHeader.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import DangerButton from '@/Components/FormElements/DangerButton.vue';

const props = defineProps({
  team: Object
})

const password = ref(null);

const form = useForm({
  organization: '',
  password: '',
  password_confirmation: '',
});

const deleteTeam = () => {
    form.delete(route('accounts.destroy', {
        team: props.team
    }), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            form.reset();
            password.value.focus();
        }
    });
};
</script>

<template>
  <AppLayout title="Accounts">
    <TeamHeader :team="team" />
    <div class="bg-white overflow-hidden shadow rounded-lg divide-y divide-gray-200 mt-8">
      <div class="px-4 py-5 sm:px-6">
        <h3 class="text-xl leading-6 font-medium text-red-600">
          Are you ABSOLUTELY sure you want to DELETE the account {{ team.name }}?
        </h3>
      </div>
      <div class="px-4 py-5 sm:p-6 prose max-w-none">
        <p><strong>Unexpected bad things will happen if you do not read this!</strong></p>
        <p>Once you delete an account, there is no going back. Please be certain. This action <span class="text-red-600">CANNOT be undone</span>. This will permanently delete the account and all of its associated database records.</p>
      </div>
    </div>

    <div class="md:grid md:grid-cols-3 md:gap-6 mt-8">
      <div class="md:col-span-1">
        <div class="px-4 sm:px-0 prose">
          <p>
            Provide your password and the name of the account to confirm that you want to <strong>delete</strong> this account.
          </p>
        </div>
      </div>
      <div class="mt-5 md:mt-0 md:col-span-2">
        <form
          action="#"
          method="POST"
        >
          <div class="shadow sm:rounded-md sm:overflow-hidden">
            <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
              <div class="grid grid-cols-3 gap-6">
                <div class="col-span-3 sm:col-span-2">
                  <InputLabel
                    value="Account Organization Name"
                    for="organization"
                  />
                  <TextInput
                    v-model="form.organization"
                    name="organization"
                    autofill="off"
                  />
                  <InputError
                    :message="form.errors.organization"
                    class="mt-2"
                  />
                </div>
              </div>
              <div class="grid grid-cols-3 gap-6">
                <div class="col-span-3 sm:col-span-2">
                  <InputLabel
                    value="Your Password"
                    for="password"
                  />
                  <TextInput
                    ref="password"
                    v-model="form.password"
                    name="password"
                    type="password"
                  />
                  <InputError
                    :message="form.errors.password"
                    class="mt-2"
                  />
                </div>
              </div>
              <div class="grid grid-cols-3 gap-6">
                <div class="col-span-3 sm:col-span-2">
                  <InputLabel
                    value="Confirm Your Password"
                    for="password_confirmation"
                  />
                  <TextInput
                    v-model="form.password_confirmation"
                    name="password_confirmation"
                    type="password"
                  />
                  <InputError
                    :message="form.errors.password_confirmation"
                    class="mt-2"
                  />
                </div>
              </div>
            </div>
            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
              <DangerButton
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                @click="deleteTeam"
              >
                I Understand the Consequences, Delete {{ team.name }}
              </DangerButton>
            </div>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
