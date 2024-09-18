<template>
  <Panel class="mt-4">
    <template #heading>
      {{ title }}
    </template>
    <div class="space-y-4 sm:space-y-2">
      <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center">
        <Label
          for="parent_category"
          class="sm:text-right"
        >{{ __('Parent Category') }}</Label>
        <div class="col-span-5 mt-1 sm:mt-0">
          <Select
            v-model="form.parent_category"
            name="parent_category"
            :options="parentCategories"
          />
          <InputError
            :message="form.errors.parent_category"
            class="mt-1"
          />
        </div>
      </div>
      <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center">
        <Label
          for="name"
          class="sm:text-right"
        >{{ __('Name') }}</Label>
        <div class="col-span-2 mt-1 sm:mt-0">
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
      <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center">
        <Label
          for="description"
          class="sm:text-right"
        >{{ __('Description') }}</Label>
        <div class="col-span-5 mt-1 sm:mt-0">
          <Input
            v-model="form.description"
            name="description"
          />
        </div>
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
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Panel from '@/Components/Panel.vue';
import Label from '@/Components/FormElements/Label.vue';
import Input from '@/Components/FormElements/Input.vue';
import Select from '@/Components/FormElements/Select.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import hoistForm from '@/Mixins/HoistForm';

let parentCategories = computed(() => usePage().props.options.parentCategories);
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
      category: {
        type: Object,
        default: () => ({})
      },
    },
    emits: ['saved'],
    data() {
      return {
        form: this.$inertia.form({
          parent_category: this.category.id ? this.category.parent.name : '',
          name: this.category.id ? this.category.name : '',
          description: this.category.id ? this.category.description : '',
        })
      };
    }
  };
</script>
