<script setup>
import {ref} from 'vue';
import {PlusIcon, TrashIcon} from '@heroicons/vue/24/outline';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import DeleteIngredientModal from './DeleteIngredientModal.vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
    form: {
        type: Object,
        required: true
    },
});


const showDeleteDialog = ref(false);
const indexToDelete = ref(null);

const addIngredient = () => {
    props.form.ingredients.push({
        id: null,
        quantity: '',
        unit_id: '',
        ingredient: ''
    });
};

const onShowDeleteDialog = (index) => {
    indexToDelete.value = index;
    showDeleteDialog.value = true;
};

const onDeleteIngredient = () => {
    props.form.ingredients.splice(indexToDelete.value, 1);
    indexToDelete.value = null;
    showDeleteDialog.value = false;
};
</script>

<template>
  <div class="grid grid-cols-6 gap-x-2 gap-y-4">
    <template v-if="form.ingredients.length > 0">
      <InputLabel class="col-span-1">
        {{ __('Quantity') }}
      </InputLabel>
      <InputLabel class="col-span-1">
        {{ __('Unit') }}
      </InputLabel>
      <InputLabel class="col-span-4">
        {{ __('Ingredient / Instruction') }}
      </InputLabel>
    </template>
    <template
      v-for="(ingredient, index) in form.ingredients"
      :key="ingredient.id"
    >
      <div class="col-span-1 col-start-1">
        <TextInput
          :id="`ingredients${index}`"
          v-model="form.ingredients[index].quantity"
          :name="`ingredients${index}`"
          type="number"
          step="any"
          min="0"
        />
        <InputError
          :message="form.errors[`ingredients.${index}.quantity`]"
          class="mt-2"
        />
      </div>
      <div class="col-span-1">
        <SelectInput
          :id="`ingredients${index}`"
          v-model="form.ingredients[index].unit_id"
          :name="`ingredients${index}`"
          :options="$page.props.options.dailyTaskNutritionIngredientUnitsOptions"
          hasBlankOption
        />
        <InputError
          :message="form.errors[`ingredients.${index}.unit_id`]"
          class="mt-2"
        />
      </div>
      <div class="col-span-4">
        <div class="flex items-center">
          <TextInput
            :id="`ingredients${index}`"
            v-model="form.ingredients[index].ingredient"
            :name="`ingredients${index}`"
          />
          <TrashIcon
            class="w-6 mx-auto cursor-pointer text-red-500 ml-2"
            @click="onShowDeleteDialog(index)"
          />
        </div>
        <InputError
          :message="form.errors[`ingredients.${index}.ingredient`]"
          class="mt-2"
        />
      </div>
    </template>
    <div class="col-span-6">
      <button
        type="button"
        class="flex items-center py-1 text-base leading-5 text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700 transition ease-in-out duration-150 truncate mt-2"
        @click="addIngredient()"
      >
        <PlusIcon class="w-5 h-5 mr-2" />
        {{ __('Add Ingredient') }}
      </button>
    </div>
  </div>
  <DeleteIngredientModal
    v-if="showDeleteDialog"
    :show="true"
    @confirm-delete="onDeleteIngredient"
    @close="showDeleteDialog = false"
  />
</template>
