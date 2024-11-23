<script setup>
import FormSection from '@/Components/FormElements/FormSection.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import TextareaInput from '@/Components/FormElements/TextareaInput.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import {__} from '@/Composables/Translate';

defineProps({
  title: {
    type: String,
    required: true
  },
  action: {
    type: String,
    required: true
  },
  teams: {
    type: Array,
    required: true
  },
  form: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['saved']);
</script>

<script>
  export default {
    //mixins: [hoistForm],
    // props: {
    //   title: {
    //     type: String,
    //     required: true
    //   },
    //   action: {
    //     type: String,
    //     required: true
    //   },
    //   teams: {
    //     type: Array,
    //     required: true
    //   },
    //   testimonial: {
    //     type: Object,
    //     default: () => ({})
    //   }
    // },
    //emits: ['saved'],
    // data() {
    //   return {
    //     form: this.$inertia.form({
          // name: this.testimonial.id ? this.testimonial.name : '',
          // text: this.testimonial.id ? this.testimonial.text : '',
          // team_id: this.testimonial.id ? this.testimonial.team_id : '',
    //     }),
    //   };
    // },
  };
</script>

<template>
  <FormSection>
    <template #title>
      {{ title }}
    </template>
    <div class="col-span-4 sm:col-span-2">
      <InputLabel for="team_id">
        Account
      </InputLabel>
      <SelectInput
        v-model="form.team_id"
        name="team_id"
        :options="teams"
        class="mt-1"
      />
      <InputError
        :message="form.errors.team_id"
        class="mt-2"
      />
    </div>
    <div class="col-span-4 sm:col-span-2">
      <InputLabel for="name">
        Name
      </InputLabel>
      <TextInput
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
      <InputLabel for="text">
        Testimonial
      </InputLabel>
      <TextareaInput
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
        @click="emit('saved')"
      >
        {{ action }}
      </PrimaryButton>
    </template>
  </FormSection>
</template>
