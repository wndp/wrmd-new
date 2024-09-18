<script setup>
import { formatISO9075 } from 'date-fns';
import DialogModal from '@/Components/DialogModal.vue';
import Alert from '@/Components/Alert.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import TextareaInput from '@/Components/FormElements/TextareaInput.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
</script>

<script>
export default {
    props: {
        patient: {
            type: Object,
            required: true
        },
        nutrition: {
            type: Object,
            default: () => { return {} }
        },
        title: {
            type: String,
            required: true
        },
        show: Boolean
    },
    emits: ['close'],
    data() {
      return {
        form: this.$inertia.form({
            plan_start_at: this.nutrition.id ? this.nutrition.plan_start_at : formatISO9075(new Date()),
            plan_end_at: this.nutrition.id ? this.nutrition.plan_end_at : null,
            frequency: this.nutrition.id ? this.nutrition.frequency : 'sid',
            route: this.nutrition.id ? this.nutrition.route : '',
            name: this.nutrition.id ? this.nutrition.name : '',
            description: this.nutrition.id ? this.nutrition.description : ''
        }),
      };
    },
    computed: {
      frequencies() {
        return this.$page.props.frequencies;
      },
      // assignments() {
      //   return this.$page.props.assignments;
      // }
    },
    methods: {
        close() {
            this.$emit('close');
        },
        save() {
            if (this.nutrition.id) {
                this.update();
                return;
            }

            this.store();
        },
        store() {
            this.form.post(this.route('patients.nutrition.store', {
                patient: this.patient
            }), {
                preserveScroll: true,
                onSuccess: () => {
                    this.form.reset();
                    this.close();
                }
            });
        },
        update() {
            this.form.put(this.route('patients.nutrition.update', {
                patient: this.patient,
                nutrition: this.nutrition
            }), {
                preserveScroll: true,
                onSuccess: () => this.close()
            });
        },
    }
};
</script>

<template>
  <DialogModal
    :show="show"
    @close="close"
  >
    <template #title>
      {{ title }}
    </template>
    <template #content>
      <Alert
        v-if="nutrition.id"
        color="red"
        class="mb-4"
      >
        {{ __("Warning: Altering a nutrition plan's dates or description may delete any marked-off tasks.") }}
      </Alert>
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-6">
        <FormRow
          id="name"
          class="sm:col-span-4"
          :label="__('Name')"
        >
          <TextInput
            v-model="form.name"
            name="name"
          />
          <InputError
            :message="form.errors.name"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="plan_start_at"
          class="sm:col-span-3"
          :label="__('Start Date')"
        >
          <DatePicker
            id="plan_start_at"
            v-model="form.plan_start_at"
          />
          <InputError
            :message="form.errors.plan_start_at"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="plan_end_at"
          class="sm:col-span-3"
          :label="__('End Date')"
        >
          <DatePicker
            id="plan_end_at"
            v-model="form.plan_end_at"
          />
          <InputError
            :message="form.errors.plan_end_at"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="frequency"
          class="sm:col-span-3"
          :label="__('Frequency')"
        >
          <SelectInput
            v-model="form.frequency"
            name="frequency"
            :options="$page.props.options.frequencies"
          />
          <InputError
            :message="form.errors.frequency"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="route"
          class="sm:col-span-3"
          :label="__('Route')"
        >
          <SelectInput
            v-model="form.route"
            name="route"
            :options="$page.props.options.nutritionRoutes"
          />
          <InputError
            :message="form.errors.route"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="description"
          class="sm:col-span-6"
          :label="__('Description')"
        >
          <TextareaInput
            v-model="form.description"
            name="description"
          />
          <InputError
            :message="form.errors.description"
            class="mt-2"
          />
        </FormRow>
      </div>
    </template>
    <template #footer>
      <SecondaryButton @click="close">
        {{ __('Nevermind') }}
      </SecondaryButton>
      <PrimaryButton
        class="ml-2"
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
        @click="save"
      >
        {{ __('Save Nutrition Plan') }}
      </PrimaryButton>
    </template>
  </DialogModal>
</template>
