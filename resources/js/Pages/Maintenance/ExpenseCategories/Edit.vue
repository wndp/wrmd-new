<script setup>
import {useForm} from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import MaintenanceAside from '../Partials/MaintenanceAside.vue';
import { ArrowLongLeftIcon } from '@heroicons/vue/24/outline';
import CategoryForm from './Partials/CategoryForm.vue';
import DeleteCategoryForm from './Partials/DeleteCategoryForm.vue';
import Alert from '@/Components/Alert.vue';

const props = defineProps({
  category: {
    type: Object,
    required: true
  }
});

const form = useForm({
  category: {}
});

const updateCategory = () => {
  form.transform(data => ({
    ...data.category
  })).put(route('maintenance.expense_categories.update', props.category), {
    preserveScroll: true
  });
};
</script>

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
          :title="__('Update Expense Category')"
          :action="__('Update Expense Category')"
          :category="category"
          :canSubmit="false"
          @saved="updateCategory"
        />
        <DeleteCategoryForm
          v-if="category.transactions_count === 0"
          :category="category"
          class="mt-8"
        />
        <Alert
          v-else
          class="mt-8"
        >
          {{ __('Categories with related transactions can not be deleted.') }}
        </Alert>
      </div>
    </div>
  </AppLayout>
</template>
