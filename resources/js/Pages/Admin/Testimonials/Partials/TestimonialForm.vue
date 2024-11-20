<template>
  <FormSection>
    <template #title>
      {{ title }}
    </template>
    <div class="col-span-4 sm:col-span-2">
      <Label for="account_id">Account</Label>
      <Select
        v-model="form.account_id"
        name="account_id"
        :options="accounts"
        class="mt-1"
      />
      <InputError
        :message="form.errors.account_id"
        class="mt-2"
      />
    </div>
    <div class="col-span-4 sm:col-span-2">
      <Label for="name">Name</Label>
      <Input
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
    <div class="col-span-4">
      <Label for="text">Testimonial</Label>
      <Textarea
        v-model="form.text"
        name="text"
        rows="20"
        autocomplete="off"
        class="mt-1"
      />
      <InputError
        :message="form.errors.text"
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
        @click="$emit('saved')"
      >
        {{ action }}
      </PrimaryButton>
    </template>
  </FormSection>
</template>

<script setup>
import FormSection from '@/Components/FormElements/FormSection.vue';
import Label from '@/Components/FormElements/Label.vue';
import Input from '@/Components/FormElements/Input.vue';
import Textarea from '@/Components/FormElements/Textarea.vue';
import Select from '@/Components/FormElements/Select.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import hoistForm from '@/Mixins/HoistForm';
</script>

<script>
  export default {
    mixins: [hoistForm],
    props: {
      title: {
        type: String,
        required: true
      },
      action: {
        type: String,
        required: true
      },
      accounts: {
        type: Array,
        required: true
      },
      testimonial: {
        type: Object,
        default: () => ({})
      }
    },
    emits: ['saved'],
    data() {
      return {
        form: this.$inertia.form({
          name: this.testimonial.id ? this.testimonial.name : '',
          text: this.testimonial.id ? this.testimonial.text : '',
          account_id: this.testimonial.id ? this.testimonial.account_id : '',
        }),
      };
    },
  };
</script>
