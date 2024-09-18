<template>
  <AppLayout title="Expense Categories">
    <div class="lg:grid grid-cols-8 gap-8 mt-4">
      <MaintenanceAside class="mb-4 lg:mb-0 col-span-2" />
      <div class="col-span-6">
        <Link
          :href="route('maintenance.expense_categories.index')"
          class="mt-1 inline-flex items-center text-base leading-5 text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700 transition ease-in-out duration-150"
        >
          <ArrowLongLeftIcon class="h-6 w-6 mr-2" />
          {{ __('Return to Category List') }}
        </Link>
        <CategoryForm
          v-model="form.category"
          :title="__('New Expense Category')"
          :action="__('Create New Expense Category')"
          :can-submit="false"
          @saved="storeFormula"
        />
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import MaintenanceAside from '../Partials/MaintenanceAside.vue';
import CategoryForm from './Partials/CategoryForm.vue';
import { ArrowLongLeftIcon } from '@heroicons/vue/24/outline';
</script>

<script>
  export default {
    data() {
      return {
        form: this.$inertia.form({
          category: {}
        })
      };
    },
    methods: {
      storeFormula() {
        this.form.transform(data => ({
          ...data.category
        })).post(this.route('maintenance.expense_categories.store'), {
          preserveScroll: true
        });
      }
    },
  };
</script>

