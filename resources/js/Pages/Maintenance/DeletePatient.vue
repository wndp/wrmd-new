<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import MaintenanceAside from './Partials/MaintenanceAside.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import DangerButton from '@/Components/FormElements/DangerButton.vue';
</script>

<script>
export default {
    props: {
        yearsInAccount: Array
    },
    data() {
        return {
            form: this.$inertia.form({
                year: '',
                password: '',
                password_confirmation: '',
            }),
        };
    },
    methods: {
        deletePatient() {
            this.form.delete(this.route('patient.delete.destroy'), {
                preserveScroll: true,
                onSuccess: () => this.form.reset(),
                onError: () => {
                    if (this.form.errors.password) {
                        this.form.reset('password', 'password_confirmation');
                        this.$refs.password.focus();
                    }
                }
            });
        },
    },
};
</script>

<template>
  <AppLayout title="Maintenance">
    <div class="lg:grid grid-cols-8 gap-8 mt-4">
      <MaintenanceAside class="mb-4 lg:mb-0 col-span-2" />
      <div class="col-span-6">
        <div class="bg-white overflow-hidden shadow rounded-lg divide-y divide-gray-200">
          <div class="px-4 py-5 sm:px-6">
            <h3 class="text-xl leading-6 font-medium text-red-600">
              {{ __('Are you ABSOLUTELY sure you want to DELETE a patient?') }}
            </h3>
          </div>
          <div class="px-4 py-5 sm:p-6 prose max-w-none">
            <p><strong>{{ __('Unexpected bad things will happen if you do not read this!') }}</strong></p>
            <p>{{ __('Once you delete a patient, there is no going back. Please be certain. This action CANNOT be undone.') }} {{ __('This will permanently delete the patient from your account.') }}</p>
          </div>
        </div>

        <div class="md:grid md:grid-cols-3 md:gap-6 mt-8">
          <div class="md:col-span-1">
            <div class="px-4 sm:px-0 prose">
              <h3 class="text-lg font-medium leading-6 text-gray-900">
                {{ __('Only the last patient from the year you choose can be deleted.') }}
              </h3>
              <p>
                {{ __('Provide your password and indicate from which year you want to delete the last patient admitted in.') }}
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
                      <Label for="year">{{ __("Patient's Year") }}</Label>
                      <Select
                        v-model="form.year"
                        name="year"
                        :options="yearsInAccount"
                      />
                      <InputError
                        :message="form.errors.year"
                        class="mt-2"
                      />
                    </div>
                  </div>

                  <div class="grid grid-cols-3 gap-6">
                    <div class="col-span-3 sm:col-span-2">
                      <Label for="password">{{ __('Your Password') }}</Label>
                      <Input
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
                      <Label for="password_confirmation">{{ __('Confirm Your Password') }}</Label>
                      <Input
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
                    @click="deletePatient"
                  >
                    {{ __('I Understand the Consequences, Delete the Patient') }}
                  </DangerButton>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
