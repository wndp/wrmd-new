<script setup>
import {ref} from 'vue';
import {useForm} from '@inertiajs/vue3';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import Checkbox from '@/Components/FormElements/Checkbox.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import capitalize from 'lodash/capitalize';
import axios from 'axios';

const props = defineProps({
  options: {
    type: Object,
    required: true
  }
});

const form = useForm({
    organization: '',
    name: '',
    email: '',
    email_confirmation: '',
    password: '',
    password_confirmation: '',
    country: 'US',
    address: '',
    city: '',
    subdivision: '',
    postal_code: '',
    phone_number: '',
    timezone: '',
    terms: false,
});

const mutableSubdivisions = ref(props.options.subdivisions);
const mutableTimezones = ref(props.options.timezones);
const subdivisionLabel = ref('State');

const submit = () => form.post(route('register'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
});

const onCountryChange = () => axios.get('/internal-api/locale/' + form.country)
  .then(response => {
    subdivisionLabel.value = capitalize(response.data.subdivision);
    mutableSubdivisions.value = response.data.subdivisions;
    mutableTimezones.value = response.data.timezones;
    form.subdivision = response.data.subdivisions[0].value;
    form.timezone = response.data.timezones[0].value;
  });
</script>

<template>
  <PublicLayout title="Register">
    <div class="flex justify-center">
      <div class="w-full sm:max-w-3xl mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        <div class="prose prose-lg mx-auto">
          <h1 class="text-3xl leading-8 text-center font-extrabold tracking-tight text-gray-900 sm:text-4xl">
            Register Your Organization
          </h1>
        </div>
        <form
          class="mt-8"
          @submit.prevent="submit"
        >
          <div class="space-y-4 sm:space-y-0 sm:grid sm:grid-cols-2 sm:gap-8">
            <div class="space-y-4">
              <div>
                <InputLabel
                  for="organization"
                  value="Organization"
                />
                <TextInput
                  v-model="form.organization"
                  name="organization"
                  class="mt-1 block w-full"
                  required
                  autofocus
                  autocomplete="organization"
                />
                <InputError
                  :message="form.errors.organization"
                  class="mt-1"
                />
              </div>
              <div>
                <InputLabel
                  for="name"
                  value="Your Name"
                />
                <TextInput
                  v-model="form.name"
                  name="name"
                  type="text"
                  class="mt-1 block w-full"
                  required
                  autocomplete="name"
                />
                <InputError
                  :message="form.errors.name"
                  class="mt-1"
                />
              </div>
              <div>
                <InputLabel
                  for="email"
                  value="Email"
                />
                <TextInput
                  v-model="form.email"
                  name="email"
                  type="email"
                  class="mt-1 block w-full"
                  required
                  autocomplete="username"
                />
                <InputError
                  :message="form.errors.email"
                  class="mt-1"
                />
              </div>
              <div>
                <InputLabel
                  for="email_confirmation"
                  value="Confirm Email"
                />
                <TextInput
                  v-model="form.email_confirmation"
                  name="email_confirmation"
                  type="email"
                  class="mt-1 block w-full"
                  required
                  autocomplete="username"
                />
              </div>
              <div>
                <InputLabel
                  for="password"
                  value="Password"
                />
                <TextInput
                  v-model="form.password"
                  name="password"
                  type="password"
                  class="mt-1 block w-full"
                  required
                  autocomplete="new-password"
                />
                <InputError
                  :message="form.errors.password"
                  class="mt-1"
                />
              </div>
              <div>
                <InputLabel
                  for="password_confirmation"
                  value="Confirm Password"
                />
                <TextInput
                  v-model="form.password_confirmation"
                  name="password_confirmation"
                  type="password"
                  class="mt-1 block w-full"
                  required
                  autocomplete="new-password"
                />
              </div>
            </div>
            <div class="space-y-4">
              <div>
                <InputLabel
                  for="country"
                  value="Country"
                />
                <SelectInput
                  v-model="form.country"
                  name="country"
                  class="mt-1 block w-full"
                  required
                  autocomplete="country"
                  :options="options.countries"
                  @change="onCountryChange"
                />
                <InputError
                  :message="form.errors.country"
                  class="mt-1"
                />
              </div>
              <div>
                <InputLabel
                  for="address"
                  value="Street Address"
                />
                <TextInput
                  v-model="form.address"
                  name="address"
                  class="mt-1 block w-full"
                  required
                  autocomplete="street-address"
                />
                <InputError
                  :message="form.errors.address"
                  class="mt-1"
                />
              </div>
              <div>
                <InputLabel
                  for="city"
                  value="City"
                />
                <TextInput
                  v-model="form.city"
                  name="city"
                  class="mt-1 block w-full"
                  required
                  autocomplete="address-level2"
                />
                <InputError
                  :message="form.errors.city"
                  class="mt-1"
                />
              </div>
              <div>
                <InputLabel
                  for="subdivision"
                  :value="subdivisionLabel"
                />
                <SelectInput
                  v-model="form.subdivision"
                  name="subdivision"
                  class="mt-1 block w-full"
                  required
                  autocomplete="address-level1"
                  :options="mutableSubdivisions"
                />
                <InputError
                  :message="form.errors.subdivision"
                  class="mt-1"
                />
              </div>
              <div>
                <InputLabel
                  for="postal_code"
                  value="Postal Code"
                />
                <TextInput
                  v-model="form.postal_code"
                  name="postal_code"
                  class="mt-1 block w-full"
                  required
                  autocomplete="postal-code"
                />
                <InputError
                  :message="form.errors.postal_code"
                  class="mt-1"
                />
              </div>
              <div>
                <InputLabel
                  for="phone_number"
                  value="Phone Number"
                />
                <TextInput
                  v-model="form.phone_number"
                  name="phone_number"
                  class="mt-1 block w-full"
                  required
                  autocomplete="tel"
                />
                <InputError
                  :message="form.errors.phone_number"
                  class="mt-1"
                />
              </div>
              <div>
                <InputLabel
                  for="timezone"
                  value="Time Zone"
                />
                <SelectInput
                  v-model="form.timezone"
                  name="timezone"
                  class="mt-1 block w-full"
                  required
                  autocomplete="off"
                  :options="mutableTimezones"
                />
                <InputError
                  :message="form.errors.timezone"
                  class="mt-1"
                />
              </div>
            </div>
            <div class="sm:col-span-2">
              <div class="flex items-start mr-4">
                <div class="flex items-center h-5">
                  <Checkbox
                    v-model="form.terms"
                    name="terms"
                  />
                </div>
                <div class="ml-3 text-sm">
                  <InputLabel
                    for="terms"
                  >I agree to the <a
                    :href="route('about.terms')"
                    target="_blank"
                    class="text-blue-600"
                  >Terms and Conditions</a> and <a
                    :href="route('about.privacy')"
                    target="_blank"
                    class="text-blue-600"
                  >Privacy Policy</a>.
                  </InputLabel>
                </div>
              </div>
              <InputError
                :message="form.errors.terms"
                class="mt-1"
              />
            </div>
          </div>

          <div class="flex items-center justify-end mt-4">
            <Link
              :href="route('login')"
              class="underline text-sm text-gray-600 hover:text-gray-900"
            >
              Already registered?
            </Link>

            <PrimaryButton
              class="ml-4"
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
              @click="submit"
            >
              Register
            </PrimaryButton>
          </div>
        </form>
      </div>
    </div>
  </PublicLayout>
</template>
