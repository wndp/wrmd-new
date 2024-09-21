<template>
  <Panel class="mt-4">
    <template #heading>
      {{ title }}
    </template>
    <div class="space-y-4 sm:space-y-2">
      <Alert>
        <strong class="font-bold">{{ __('The Group and Type of a custom field can not be updated.') }}</strong> {{ __('Make sure you are choosing the correct Group and Type for your custom field.') }} {{ __('If you must update either of these values then please contact support and request our assistance.') }}
      </Alert>
      <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center">
        <Label
          for="label"
          class="sm:text-right"
        >{{ __('Label') }} <RequiredInput /></Label>
        <div class="col-span-5 mt-1 sm:mt-0">
          <Input
            v-model="form.label"
            name="label"
          />
          <InputError
            :message="form.errors.label"
            class="mt-1"
          />
        </div>
      </div>
      <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center">
        <Label
          for="group"
          class="sm:text-right"
        >{{ __('Group') }} <RequiredInput /></Label>
        <div class="col-span-2 mt-1 sm:mt-0">
          <Select
            v-if="!isUpdating"
            v-model="form.group"
            name="group"
            :options="$page.props.options.groups"
            @change="onGroupChange"
          />
          <span v-else>
            {{ form.group }}
          </span>
          <InputError
            :message="form.errors.group"
            class="mt-1"
          />
        </div>
        <Label
          for="type"
          class="sm:text-right mt-4 sm:mt-0"
        >{{ __('Type') }} <RequiredInput /></Label>
        <div class="col-span-2 mt-1 sm:mt-0">
          <Select
            v-if="!isUpdating"
            v-model="form.type"
            name="type"
            :options="$page.props.options.fieldTypes"
          />
          <span v-else>
            {{ $page.props.options.fieldTypes.find(t => t.value === form.type).label }}
          </span>
          <InputError
            :message="form.errors.type"
            class="mt-1"
          />
        </div>
      </div>
      <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center">
        <template v-if="form.group === 'Patient'">
          <Label
            for="panel"
            class="sm:text-right"
          >{{ __('Panel') }} <RequiredInput /></Label>
          <div class="col-span-2 mt-1 sm:mt-0">
            <Select
              v-model="form.panel"
              name="panel"
              :options="$page.props.options.patientPanels"
            />
            <InputError
              :message="form.errors.panel"
              class="mt-1"
            />
          </div>
        </template>
        <Label
          for="location"
          class="sm:text-right mt-4 sm:mt-0"
        >{{ __('Location') }} <RequiredInput /></Label>
        <div class="col-span-2 mt-1 sm:mt-0">
          <Select
            v-model="form.location"
            name="location"
            :options="$page.props.options.locations"
          />
          <InputError
            :message="form.errors.location"
            class="mt-1"
          />
        </div>
      </div>
      <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center">
        <Label
          for="is_required"
          class="sm:text-right"
        >{{ __('Required') }}</Label>
        <div class="col-span-2 mt-1 sm:mt-0">
          <Toggle
            v-model="form.is_required"
            dusk="is_required"
          />
        </div>
        <template v-if="['select', 'checkbox-group'].includes(form.type)">
          <Label
            for="options"
            class="sm:text-right"
          >{{ __('List Options') }} <RequiredInput /></Label>
          <div class="col-span-2 mt-1 sm:mt-0">
            <Textarea
              v-model="form.options"
              name="options"
            />
          </div>
        </template>
      </div>
    </div>
    <template #footing>
      <div class="flex items-center justify-end text-right">
        <ActionMessage
          :on="form.isDirty"
          class="mr-3"
        >
          <span class="text-red-600">{{ __('There are unsaved changes.') }}</span>
        </ActionMessage>
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
      </div>
    </template>
  </Panel>
</template>

<script setup>
import Alert from '@/Components/Alert.vue';
import Panel from '@/Components/Panel.vue';
import Label from '@/Components/FormElements/Label.vue';
import Input from '@/Components/FormElements/Input.vue';
import Select from '@/Components/FormElements/Select.vue';
import Textarea from '@/Components/FormElements/Textarea.vue';
import Toggle from '@/Components/FormElements/Toggle.vue';
import RequiredInput from '@/Components/FormElements/RequiredInput.vue';
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
      customField: {
        type: Object,
        default: () => ({})
      },
    },
    emits: ['saved'],
    data() {
      return {
        form: this.$inertia.form({
          group: this.customField.id ? this.customField.group : '',
          panel: this.customField.id ? this.customField.panel : '',
          location: this.customField.id ? this.customField.location : '',
          type: this.customField.id ? this.customField.type : '',
          label: this.customField.id ? this.customField.label : '',
          options: this.customField.id ? this.customField.options : '',
          is_required: this.customField.id ? this.customField.is_required : false,
        })
      };
    },
    computed: {
      isUpdating() {
        return this.customField.id !== undefined;
      }
    },
    methods: {
      onGroupChange() {
        if (this.form.group !== 'Patient') {
          this.form.panel = null;
        }
      }
    }
  };
</script>
