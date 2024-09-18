<template>
  <div>
    <Panel>
      <template #heading>
        {{ __('Delete Custom Field') }}
      </template>

      <Alert
        class="col-span-4"
        color="red"
      >
        {{ __('Are you ABSOLUTELY sure you want to DELETE the ":customfieldLabel" field?', {customfieldLabel: customField.label}) }}
      </Alert>
      <p class="my-4 text-sm text-gray-500 font-bold">
        {{ __("Unexpected bad things will happen if you don't read this!") }}
      </p>
      <p class="mt-1 text-sm text-gray-500">
        {{ __('Once you delete a custom field, there is no going back. Please be certain. This action CANNOT be undone.') }} {{ __('This will permanently delete the custom field and all its saved values from your account.') }}
      </p>
      <template #footing>
        <ConfirmsPassword @confirmed="deleteCustomField">
          <DangerButton
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
          >
            {{ __('Delete Custom Field') }}
          </DangerButton>
        </ConfirmsPassword>
      </template>
    </Panel>
  </div>
</template>

<script setup>
import { inject } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Panel from '@/Components/Panel.vue';
import DangerButton from '@/Components/FormElements/DangerButton.vue';
import ConfirmsPassword from '@/Components/ConfirmsPassword.vue';
import Alert from '@/Components/Alert.vue';

const route = inject('route');

const props = defineProps({
  customField: {
    type: Object,
    default: () => ({})
  }
});

const form = useForm({});

const deleteCustomField = () => {
  form.delete(route('maintenance.custom_fields.destroy', props.customField));
};
</script>
