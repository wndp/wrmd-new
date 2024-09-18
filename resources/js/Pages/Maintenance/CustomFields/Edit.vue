<template>
  <AppLayout title="Custom Fields">
    <div class="lg:grid grid-cols-8 gap-8 mt-4">
      <MaintenanceAside class="mb-4 lg:mb-0 col-span-2" />
      <div class="col-span-6">
        <Link
          :href="route('maintenance.custom_fields.index')"
          class="mt-1 inline-flex items-center text-base leading-5 text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700 transition ease-in-out duration-150"
        >
          <ArrowLongLeftIcon class="h-6 w-6 mr-2" />
          {{ __('Return to Custom Field List') }}
        </Link>
        <CustomFieldForm
          v-model="form.customField"
          :title="__('Update Custom Field')"
          :action="__('Update Custom Field')"
          :custom-field="customField"
          :can-submit="false"
          :errors="form.errors"
          @saved="updateCustomField"
        />
        <DeleteCustomFieldForm
          :custom-field="customField"
          class="mt-8"
        />
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import MaintenanceAside from '../Partials/MaintenanceAside.vue';
import { ArrowLongLeftIcon } from '@heroicons/vue/24/outline';
import CustomFieldForm from './Partials/CustomFieldForm.vue';
import DeleteCustomFieldForm from './Partials/DeleteCustomFieldForm.vue';
</script>

<script>
  export default {
    props: {
      customField: {
        type: Object,
        required: true
      }
    },
    data() {
      return {
        form: this.$inertia.form({
          customField: {
            options: Array.from(this.customField.options ?? '').join("\r\n")
          }
        })
      }
    },
    methods: {
      updateCustomField() {
        this.form.transform(data => ({
          ...data.customField
        })).put(this.route('maintenance.custom_fields.update', this.customField), {
          preserveState: true
        });
      },
    }
  };
</script>
