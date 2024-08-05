<script setup>
import {ref} from 'vue';
import {useForm, usePage} from '@inertiajs/vue3';
import FormSection from '@/Components/FormElements/FormSection.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import DangerButton from '@/Components/FormElements/DangerButton.vue';
import axios from 'axios';
import {__} from '@/Composables/Translate';

const emit = defineEmits(['country-change']);

const currentTeam = usePage().props.auth.user.current_team;

const form = useForm({
  _method: 'PUT',
  name: currentTeam.name,
  federal_permit_number: currentTeam.federal_permit_number,
  subdivision_permit_number: currentTeam.subdivision_permit_number,
  country: currentTeam.country,
  address: currentTeam.address,
  city: currentTeam.city,
  subdivision: currentTeam.subdivision,
  postal_code: currentTeam.postal_code,
  photo: null,
});

const photoPreview = ref(null);
const photoInput = ref(null);
const mutableSubdivisions = ref(usePage().props.options.subdivisions);
const subdivisionLabel = ref('State');

const updateProfileInformation = () => {
  if (photoInput.value) {
    form.photo = photoInput.value.files[0];
  }

  form.post(route('account.profile.update'), {
    preserveScroll: true,
    onSuccess: () => clearPhotoFileInput(),
  });
}

const selectNewPhoto = () => {
    photoInput.value.click();
};

const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];

    if (! photo) return;

    const reader = new FileReader();

    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };

    reader.readAsDataURL(photo);
};

const deletePhoto = () => {
    router.delete(route('account.profile-photo.destroy'), {
        preserveScroll: true,
        onSuccess: () => {
            photoPreview.value = null;
            clearPhotoFileInput();
        },
    });
};

const clearPhotoFileInput = () => {
    if (photoInput.value?.value) {
        photoInput.value.value = null;
    }
};

const onCountryChange = () => {
  axios.get('/internal-api/locale/' + this.form.country)
    .then(response => {
      subdivisionLabel.value = response.data.subdivision;
      mutableSubdivisions.value = response.data.subdivisions;
      form.subdivision = response.data.subdivisions[0].value;
      emit('country-change', response.data);
    });
}
</script>

<template>
  <FormSection>
    <template #title>
      {{ __('Account Profile') }}
    </template>
    <template #description>
      {{ __('Update your account profile information.') }}
    </template>
    <div class="col-span-4">
      <InputLabel for="photo">
        Photo
      </InputLabel>
      <input
        ref="photoInput"
        type="file"
        class="hidden"
        @change="updatePhotoPreview"
      >

      <!-- Current Profile Photo -->
      <div
        v-show="! photoPreview"
        class="mt-2"
      >
        <img
          :src="currentTeam.profile_photo_url"
          :alt="currentTeam.name"
          class="rounded-full h-20 w-20 object-cover"
        >
      </div>

      <!-- New Profile Photo Preview -->
      <div
        v-show="photoPreview"
        class="mt-2"
      >
        <span
          class="block rounded-full w-20 h-20"
          :style="'background-size: cover; background-repeat: no-repeat; background-position: center center; background-image: url(\'' + photoPreview + '\');'"
        />
      </div>

      <div class="flex">
        <SecondaryButton
          class="mt-2 mr-2"
          type="button"
          @click.prevent="selectNewPhoto"
        >
          {{ __('Select A New Photo') }}
        </SecondaryButton>

        <DangerButton
          v-if="currentTeam.profile_photo_path"
          type="button"
          class="mt-2"
          @click.prevent="deletePhoto"
        >
          {{ __('Remove Photo') }}
        </DangerButton>
      </div>

      <InputError
        :message="form.errors.photo"
        class="mt-2"
      />
    </div>
    <div class="col-span-4">
      <InputLabel for="name">
        {{ __('Organization') }}
      </InputLabel>
      <TextInput
        v-model="form.name"
        name="name"
        autocomplete="organization"
        class="mt-1"
      />
      <InputError
        :message="form.errors.name"
        class="mt-2"
      />
    </div>
    <div class="col-span-4 sm:col-span-2">
      <InputLabel for="country">
        {{ __('Country / Region') }}
      </InputLabel>
      <SelectInput
        v-model="form.country"
        name="country"
        autocomplete="country"
        :options="$page.props.options.countries"
        class="mt-1 block w-full"
        @change="onCountryChange"
      />
      <InputError
        :message="form.errors.country"
        class="mt-2"
      />
    </div>
    <div class="col-span-4 sm:col-span-2">
      <InputLabel
        for="subdivision"
        :value="subdivisionLabel"
      />
      <SelectInput
        v-model="form.subdivision"
        name="subdivision"
        :options="mutableSubdivisions"
        class="mt-1"
      />
      <InputError
        :message="form.errors.subdivision"
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
    <div class="col-span-2 sm:col-span-1">
      <InputLabel for="federal-number">
        {{ __('Federal Permit #') }}
      </InputLabel>
      <TextInput
        v-model="form.federal_permit_number"
        name="federal-number"
        autocomplete="off"
        class="mt-1"
      />
    </div>
    <div class="col-span-2 sm:col-span-1">
      <InputLabel for="subdivision-number">{{ __('State Permit #') }}</InputLabel>
      <TextInput
        v-model="form.subdivision_permit_number"
        name="subdivision-number"
        autocomplete="off"
        class="mt-1"
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
        @click="updateProfileInformation"
      >
        {{ __('Update Profile') }}
      </PrimaryButton>
    </template>
  </FormSection>
</template>
