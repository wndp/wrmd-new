<template>
  <AppLayout title="Paper Form Templates">
    <div class="lg:grid grid-cols-8 gap-8 mt-4">
      <MaintenanceAside class="mb-4 lg:mb-0 col-span-2" />
      <div class="col-span-6">
        <div class="shadow sm:rounded-md sm:overflow-hidden">
          <div class="bg-white py-6 px-4 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
              {{ __('Paper Form Templates') }}
            </h3>
            <p class="mt-2 text-sm text-gray-500">To use your own template as a paper form, you must upload your file as a PDF document. The first page of your document must leave the first 3 inches (8cm) (including the header) blank because WRMD will fill in that space with the patients intake information. Download this example file to see what WRMD will add to your template.</p>
          </div>
        </div>
        <Panel class="mt-4">
          <div class="grid grid-cols-4 gap-6">
            <div class="col-span-4 lg:col-span-2">
              <Label for="year">{{ __("Your PDF Template") }}</Label>
              <FileInput
                name="template"
                accept=".pdf"
                @input="form.template = $event.target.files[0]"
              />
              <InputError
                :message="form.errors.template"
                class="mt-1"
              />
            </div>
            <div class="col-span-4 lg:col-span-2">
              <Label for="name">{{ __("Template Name") }}</Label>
              <Input
                v-model="form.name"
                name="name"
              />
              <InputError
                :message="form.errors.name"
                class="mt-1"
              />
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
                @click="storeNewTemplate()"
              >
                {{ __('Upload PDF Template') }}
              </PrimaryButton>
            </div>
          </template>
        </Panel>
        <div class="flex flex-col mt-4">
          <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
              <div class="shadow overflow-hidden border-b border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-blue-100">
                    <tr>
                      <th
                        scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                      />
                      <th
                        scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                      >
                        {{ __('Name') }}
                      </th>
                      <th
                        scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                      >
                        {{ __('Date Added') }}
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr
                      is="vue:TemplateRow"
                      v-for="template in templates"
                      :key="template.slug"
                      :template="template"
                    />
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { inject } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import MaintenanceAside from '../Partials/MaintenanceAside.vue';
import Panel from '@/Components/Panel.vue';
import Label from '@/Components/FormElements/Label.vue';
import FileInput from '@/Components/FormElements/FileInput.vue';
import Input from '@/Components/FormElements/Input.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import TemplateRow from './Partials/TemplateRow.vue';

const route = inject('route');

defineProps({
  templates: {
    type: Array,
    required: true
  }
});

let form = useForm({
  template: '',
  name: ''
});

let storeNewTemplate = () => {
  form.post(route('maintenance.paper_forms.store'), {
    onSuccess: () => form.reset()
  });
}
</script>
