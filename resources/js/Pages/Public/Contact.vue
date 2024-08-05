<script setup>
import {useForm} from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import TextareaInput from '@/Components/FormElements/TextareaInput.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import InputError from '@/Components/FormElements/InputError.vue';

const props = defineProps({
  honeypot: {
    type: Object,
    required: true
  },
  status: {
    type: String,
    default: ''
  }
});

const form = useForm({
  name: '',
  email: '',
  organization: '',
  subject: '',
  message: '',
  [props.honeypot.nameFieldName]: '',
  [props.honeypot.validFromFieldName]: props.honeypot.encryptedValidFrom,
})

const submit = () => {
  form.post(route('contact.store'), {
    onSuccess: () => form.reset()
  });
};
</script>

<template>
  <PublicLayout title="Contact Us">
    <div class="flex justify-center">
      <div class="w-full sm:max-w-xl mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        <div class="mb-4 prose prose-lg prose-blue text-gray-500 mx-auto">
          <h2>Contact Us</h2>
          <p>
            Looking for some more information? Interested in supporting Wildlife Rehabilitation MD's development? Or maybe you just want to tell us how excited you are about the database? Send us a message and we'll be glad to hear from you!
          </p>
          <h4>Want a Quick Answer?</h4>
          <p><a href="https://help.wrmd.org/frequently-asked-questions/">See our Frequently Asked Questions</a></p>
          <h4>Our Address</h4>
          <address>
            The Wild Neighbors Database Project<br>
            PO Box 421<br>
            Middletown, CA 95461<br>
            United States of America
          </address>
          <h4>Our Phone Number</h4>
          <a href="tel:+17072161848">+1 (707) 216-1848</a>
          <h4>Have a Question?</h4>
        </div>
        <div
          v-if="status"
          class="mb-4 font-medium text-sm text-green-600"
        >
          {{ status }}
        </div>
        <form @submit.prevent="submit">
          <div>
            <InputLabel
              for="name"
              value="Name"
            />
            <TextInput
              v-model="form.name"
              name="name"
              class="mt-1"
              required
              autocomplete="name"
            />
            <InputError
              :message="form.errors.name"
              class="mt-2"
            />
          </div>
          <div class="mt-4">
            <InputLabel
              for="email"
              value="Email"
            />
            <TextInput
              v-model="form.email"
              name="email"
              type="email"
              class="mt-1"
              required
              autocomplete="email"
            />
            <InputError
              :message="form.errors.email"
              class="mt-2"
            />
          </div>
          <div class="mt-4">
            <InputLabel
              for="organization"
              value="Organization"
            />
            <TextInput
              v-model="form.organization"
              name="organization"
              class="mt-1"
              required
              autocomplete="organization"
            />
            <InputError
              :message="form.errors.organization"
              class="mt-2"
            />
          </div>
          <div class="mt-4">
            <InputLabel
              for="subject"
              value="Subject"
            />
            <TextInput
              v-model="form.subject"
              name="subject"
              class="mt-1"
              required
              autocomplete="subject"
            />
            <InputError
              :message="form.errors.subject"
              class="mt-2"
            />
          </div>
          <div class="mt-4">
            <InputLabel
              for="message"
              value="Message"
            />
            <TextareaInput
              v-model="form.message"
              name="message"
              class="mt-1"
              required
              autocomplete="message"
            />
            <InputError
              :message="form.errors.message"
              class="mt-2"
            />
          </div>
          <div
            v-if="honeypot.enabled"
            :name="`${honeypot.nameFieldName}_wrap`"
            style="display:none;"
          >
            <input
              :id="honeypot.nameFieldName"
              v-model="form[honeypot.nameFieldName]"
              type="text"
              :name="honeypot.nameFieldName"
            >
            <input
              v-model="form[honeypot.validFromFieldName]"
              type="text"
              :name="honeypot.validFromFieldName"
            >
          </div>
          <div class="flex justify-end mt-4">
            <PrimaryButton
              type="submit"
              class="ml-4"
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
            >
              Send Message
            </PrimaryButton>
          </div>
        </form>
      </div>
    </div>
  </PublicLayout>
</template>
