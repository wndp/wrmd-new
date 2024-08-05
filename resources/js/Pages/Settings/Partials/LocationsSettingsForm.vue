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
      <Label for="first-name">{{ __('Areas / Rooms') }}</label>
      <p class="mt-1 text-sm text-gray-500">
        <em class="italic">{{ __('example: Treatment Room, Nursery, Aviary, ...') }}</em>
      </p>
      <Textarea
        v-model="form.areas"
        name="areas"
        autocomplete="off"
        class="mt-1"
      />
    </div>
    <div class="col-span-4">
      <Label for="first-name">{{ __('Enclosures') }}</label>
      <p class="mt-1 text-sm text-gray-500">
        <em class="italic">example: Flight Cage, Incubator, Mammal Cage, ...</em>
      </p>
      <Textarea
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

<script>
import LocalStorage from '@/Utilities/LocalStorage';
import FormSection from '@/Components/FormElements/FormSection.vue';
import Label from '@/Components/FormElements/Label.vue';
import Textarea from '@/Components/FormElements/Textarea.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';

export default {
    components: {
        FormSection,
        Label,
        Textarea,
        PrimaryButton,
        ActionMessage
    },
    props: {
        generalSettings: Object
    },
    data() {
        return {
            form: this.$inertia.form({
                areas: this.generalSettings.areas,
                enclosures: this.generalSettings.enclosures,
            }),
        };
    },
    methods: {
        updateLocations() {
            this.form.put(this.route('general-settings.update.locations'), {
                preserveScroll: true,
                onSuccess: () => {
                    LocalStorage.remove('areas');
                    LocalStorage.remove('enclosures');
                },
            });
        },
    },
};
</script>
