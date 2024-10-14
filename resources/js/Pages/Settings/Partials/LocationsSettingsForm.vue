<script setup>
import {useForm} from '@inertiajs/vue3';
import FormSection from '@/Components/FormElements/FormSection.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextareaInput from '@/Components/FormElements/TextareaInput.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import {__} from '@/Composables/Translate';
import LocalStorage from '@/Composables/LocalStorage';
import {SettingKey} from '@/Enums/SettingKey';

const localStorage = LocalStorage();

const props = defineProps({
  generalSettings: {
    type: Object,
    required: true
  }
});

const form = useForm({
    areas: props.generalSettings[SettingKey.AREAS],
    enclosures: props.generalSettings[SettingKey.ENCLOSURES],
});

const updateLocations = () => {
  form.put(route('general-settings.update.locations'), {
    preserveScroll: true,
    onSuccess: () => {
      localStorage.remove('areas');
      localStorage.remove('enclosures');
    },
  });
};
</script>

<template>
  <FormSection>
    <template #title>
      {{ __("Your Facility's Locations") }}
    </template>
    <template #description>
      <div class="space-y-4">
        <p>{{ __('Provide a comma separated list of all the areas and rooms where you may house your patients and a comma separated list of all your enclosures.') }}</p>

        <p>If you have related enclosures numbered sequentially (<em class="italic">ex: Incubator 1, Incubator 2, ...</em>) then we recommend that you just save the enclosure name here. Then when you create a location record on a patient you can write in the number after choosing the enclosure.</p>
      </div>
    </template>
    <div class="col-span-4">
      <InputLabel for="first-name">{{ __('Areas / Rooms') }}</InputLabel>
      <p class="mt-1 text-sm text-gray-500">
        <em class="italic">{{ __('example: Treatment Room, Nursery, Aviary, ...') }}</em>
      </p>
      <TextareaInput
        v-model="form.areas"
        name="areas"
        autocomplete="off"
        class="mt-1"
      />
    </div>
    <div class="col-span-4">
      <InputLabel for="first-name">{{ __('Enclosures') }}</InputLabel>
      <p class="mt-1 text-sm text-gray-500">
        <em class="italic">example: Flight Cage, Incubator, Mammal Cage, ...</em>
      </p>
      <TextareaInput
        v-model="form.enclosures"
        name="enclosures"
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
        @click="updateLocations"
      >
        {{ __('Update Locations') }}
      </PrimaryButton>
    </template>
  </FormSection>
</template>
