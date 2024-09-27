<script setup>
import { inject } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import MaintenanceAside from './Partials/MaintenanceAside.vue';
import FormSection from '@/Components/FormElements/FormSection.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import Checkbox from '@/Components/FormElements/Checkbox.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';

const route = inject('route');

const props = defineProps({
  users: {
    type: Array,
    required: true
  },
  notifyOfIoa: {
    type: Array,
    required: true
  }
});

const form = useForm({
  notifyOfIoa: props.notifyOfIoa
})

const updatePrivacy = () => {
  form.put(route('maintenance.owcn_ioa.update'), {
    preserveScroll: true
  });
};
</script>

<template>
  <AppLayout title="Maintenance">
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900">
        {{ __('Maintenance') }}
      </h1>
    </template>
    <div class="lg:grid grid-cols-8 gap-8 mt-4">
      <MaintenanceAside class="mb-4 lg:mb-0 col-span-2" />
      <div class="col-span-6">
        <FormSection>
          <template #title>
            {{ __('OWCN Individual Oiled Animal') }}
          </template>
          <div class="col-span-4">
            <Label>{{ __('These Users Should be Notified When an IOA Patient is Processed.') }}</Label>
            <div class="mt-2 space-y-2">
              <div
                v-for="user in users"
                :key="user.id"
                class="flex items-start"
              >
                <div class="flex items-center h-5">
                  <Checkbox
                    :id="`notifyOfIoa-${user.id}`"
                    v-model="form.notifyOfIoa"
                    :value="user.email"
                  />
                </div>
                <div class="ml-3 text-sm">
                  <Label
                    :for="`notifyOfIoa-${user.id}`"
                    class="font-normal"
                  >{{ user.email }}</Label>
                </div>
              </div>
            </div>
          </div>
          <template #actions>
            <ActionMessage
              :on="form.recentlySuccessful"
              class="mr-3"
            >
              {{ __('Saved') }}
            </ActionMessage>
            <PrimaryButton
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
              @click="updatePrivacy"
            >
              {{ __('Update Individual Oiled Animal Settings') }}
            </PrimaryButton>
          </template>
        </FormSection>
      </div>
    </div>
  </AppLayout>
</template>
