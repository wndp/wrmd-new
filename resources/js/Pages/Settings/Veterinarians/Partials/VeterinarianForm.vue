<template>
  <FormSection>
    <template #title>
      {{ title }}
    </template>
    <template #description>
      {{ __('Adding veterinarians to your account allows them to assign duties and responsibilities (daily tasks, prescriptions, ...) under their authority. You may include address and contact information for your veterinarians if they do not primary work at your organization.') }}
    </template>
    <div class="col-span-4 sm:col-span-2">
      <Label for="name">{{ __('Name') }}</Label>
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
    <div class="col-span-4 sm:col-span-2">
      <Label for="license">{{ __('License Number') }}</Label>
      <Input
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
      <Label for="user_id">{{ __('WRMD User') }}</Label>
      <Select
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
      <Label for="business_name">{{ __('Business Name') }}</label>
      <Input
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
      <Label for="address">{{ __('Address') }}</label>
      <Input
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
      <Label for="city">{{ __('City') }}</label>
      <Input
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
      <Label for="subdivision">{{ __('State') }}</label>
      <Select
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
      <Label for="postal-code">{{ __('ZIP / Postal') }}</label>
      <Input
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
      <Label for="email">{{ __('Email address') }}</Label>
      <Input
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
      <Label for="email">{{ __('Phone Number') }}</Label>
      <Input
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
      users: {
        type: Array,
        required: true
      },
      veterinarian: {
        type: Object,
        default: () => ({})
      }
    },
    emits: ['saved'],
    data() {
      return {
        form: this.$inertia.form({
          name: this.veterinarian.id ? this.veterinarian.name : '',
          license: this.veterinarian.id ? this.veterinarian.license : '',
          business_name: this.veterinarian.id ? this.veterinarian.business_name : '',
          address: this.veterinarian.id ? this.veterinarian.address : '',
          city: this.veterinarian.id ? this.veterinarian.city : '',
          subdivision: this.veterinarian.id ? this.veterinarian.subdivision : '',
          postal_code: this.veterinarian.id ? this.veterinarian.postal_code : '',
          phone: this.veterinarian.id ? this.veterinarian.phone : '',
          email: this.veterinarian.id ? this.veterinarian.email : '',
          user_id: this.veterinarian.id ? this.veterinarian.user?.id : '',
        }),
      };
    },
  };
</script>
