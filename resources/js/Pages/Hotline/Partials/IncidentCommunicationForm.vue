<script setup>
import Panel from '@/Components/Panel.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import TextareaAutosize from '@/Components/FormElements/TextareaAutosize.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import {__} from '@/Composables/Translate';

defineProps({
  form: {
    type: Object,
    default: () => ({})
  },
  canSubmit: {
    type: Boolean,
    default: true
  },
  enforceRequired: {
    type: Boolean,
    default: true
  }
});
</script>

<script>
  export default {
    //mixins: [autoSave, hoistForm],
    // props: {
    //   enforceRequired: {
    //     type: Boolean,
    //     default: true
    //   }
    // },
    // data() {
    //   return {
    //     form: this.$inertia.form({
    //       communication_at: '',
    //       communication_by: '',
    //       communication: ''
    //     }),
    //   };
    // },
    methods: {
      // save() {
      //   this.update();
      // },
      // update() {
      //   if (this.canSubmit) {
      //     this.form.put(this.route('patients.intake.update', {
      //       patient: this.patient
      //     }), {
      //       preserveScroll: true,
      //       onError: () => this.stopAutoSave()
      //     });
      //   }
      // },
    },
  };
</script>

<template>
  <Panel>
    <template #title>
      {{ __('Communication') }}
    </template>
    <template #content>
      <FormRow
        id="communication_at"
        :label="__('Date')"
        class="col-span-6 md:col-span-2"
      >
        <DatePicker
          id="communication_at"
          v-model="form.communication_at"
          time
        />
        <InputError :message="form.errors?.communication_at" />
      </FormRow>
      <FormRow
        id="communication_by"
        :label="__('By')"
        class="col-span-6 md:col-span-2"
      >
        <TextInput
          id="communication_by"
          v-model="form.communication_by"
        />
        <InputError :message="form.errors?.communication_by" />
      </FormRow>
      <FormRow
        id="communication"
        :label="__('Communication')"
        class="col-span-6"
      >
        <TextareaAutosize
          id="communication"
          v-model="form.communication"
        />
        <InputError :message="form.errors?.communication" />
      </FormRow>
    </template>
  </Panel>
</template>
