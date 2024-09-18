<script setup>
import {ref} from 'vue';
import {useForm} from '@inertiajs/vue3';
import VueComboBlocks from 'vue-combo-blocks';
import Draggable from 'vuedraggable';
import FormSection from '@/Components/FormElements/FormSection.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import Toggle from '@/Components/FormElements/Toggle.vue';
import { XMarkIcon } from '@heroicons/vue/24/outline';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
  generalSettings: Object,
  selectableFields: Array,
  listedFields: Array
})

const form = useForm({
    showLookupRescuer: props.generalSettings.showLookupRescuer,
    showGeolocationFields: props.generalSettings.showGeolocationFields,
    listFields: []
});
const filteredList = ref(props.selectableFields);
const selectedItems = ref(props.listedFields);

const updateProfile = () => {
    form.transform((data) => ({
        ...data,
        listFields: selectedItems.value.map(item => item.value)
    }))
    .put(route('general-settings.update'), {
        preserveScroll: true
    });
};

const itemToString = (item) => {
  return item ? item.label : '';
};

const stateReducer = (state, actionAndChanges) => {
  const { changes, type } = actionAndChanges;
  switch (type) {
    case VueComboBlocks.stateChangeTypes.InputKeyUpEnter:
    case VueComboBlocks.stateChangeTypes.ItemClick:
      return {
        ...changes,
        isOpen: true, // keep menu open after selection.
        hoveredIndex: state.hoveredIndex,
        inputValue: '', // don't add the item string as input value at selection.
      };
    case VueComboBlocks.stateChangeTypes.InputBlur:
      return {
        ...changes,
        inputValue: '', // don't add the item string as input value at selection.
      };
    default:
      return changes;
  }
};

const handleSelectedItemChange = (selectedItem) => {
  if (!selectedItem) return;
  const index = selectedItems.value.indexOf(selectedItem);
  if (index > 0) {
    selectedItems.value = [
      ...selectedItems.value.slice(0, index),
      ...selectedItems.value.slice(index + 1),
    ];
  } else if (index === 0) {
    selectedItems.value = [...selectedItems.value.slice(1)];
  } else {
    // Add item
    selectedItems.value.push(selectedItem);
  }
};

const updateList = (text) => {
  filteredList.value = props.selectableFields
    .filter((item) => item.label.toLowerCase()
    .includes(text.toLowerCase()));
};
</script>

<template>
  <FormSection>
    <template #title>
      {{ __('Generic Settings') }}
    </template>
    <template #description>
      {{ __('Update these settings to adjust some generic behavior on your account.') }}
    </template>
    <div class="col-span-4">
      <InputLabel for="first-name">
        {{ __('Columns Shown When Listing Patients.') }}
      </InputLabel>
      <p class="mt-1 text-sm text-gray-500">
        {{ __('Case #, Common Name and Date Admitted will always be included in any list of patients.') }}
      </p>
      <VueComboBlocks
        v-slot="{
          getInputProps,
          getInputEventListeners,
          hoveredIndex,
          isOpen,
          getMenuProps,
          getMenuEventListeners,
          getItemProps,
          getItemEventListeners,
          getComboboxProps,
          selectedItem,
          reset,
        }"
        :itemToString="itemToString"
        :items="filteredList"
        :stateReducer="stateReducer"
        @input-value-change="updateList"
        @select="handleSelectedItemChange"
      >
        <div
          v-bind="getComboboxProps()"
          class="flex items-center flex-wrap focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border border-gray-300 rounded-md py-1.5 px-2 mt-2"
        >
          <div class="">
            <Draggable
              v-model="selectedItems"
              element="ul"
              class="flex flex-wrap gap-2"
              itemKey="id"
            >
              <template #item="{element}">
                <li
                  class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 whitespace-nowrap"
                >
                  {{ element.label }}
                  <XMarkIcon
                    class="h-4 w-4 ml-2 cursor-pointer"
                    @click="handleSelectedItemChange(element)"
                  />
                </li>
              </template>
            </Draggable>
          </div>
          <div class="relative w-68 ml-2">
            <input
              v-bind="getInputProps()"
              placeholder="Search"
              class="outline-none w-full"
              name="listFields-search"
              v-on="getInputEventListeners()"
            >
            <Transition
              leaveActiveClass="transition duration-100 ease-in"
              leaveFromClass="opacity-100"
              leaveToClass="opacity-0"
            >
              <ul
                v-show="isOpen"
                v-bind="getMenuProps()"
                class="absolute z-20 top-full w-full py-1 mt-1 overflow-auto text-base bg-white rounded-md shadow-lg max-h-60 w-72 ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
                v-on="getMenuEventListeners()"
              >
                <li
                  v-for="(item, index) in filteredList"
                  :key="item.value"
                  v-bind="getItemProps({ item, index })"
                  :class="[
                    hoveredIndex === index ? 'text-blue-900 bg-blue-100' : 'text-gray-900',
                    selectedItems.includes(item) ? 'bold' : 'normal',
                    'cursor-default select-none relative py-2 px-4 whitespace-nowrap',
                  ]"
                  v-on="getItemEventListeners({ item, index })"
                >
                  <button type="button">
                    {{ item.label }}
                  </button>
                </li>
              </ul>
            </Transition>
          </div>
        </div>
      </VueComboBlocks>
    </div>
    <div class="col-span-4 sm:col-span-2">
      <InputLabel for="first-name">{{ __('Show Lookup Rescuer Tab First When Creating a New Patient?') }}</InputLabel>
      <div class="mt-2">
        <Toggle
          v-model="form.showLookupRescuer"
          dusk="showLookupRescuer"
        />
      </div>
    </div>
    <div class="col-span-4 sm:col-span-2">
      <InputLabel for="first-name">{{ __('Show County, Latitude and Longitude fields?') }}</InputLabel>
      <div class="mt-2">
        <Toggle
          v-model="form.showGeolocationFields"
          dusk="showGeolocationFields"
        />
      </div>
    </div>
    <template #actions>
      <ActionMessage
        :on="form.recentlySuccessful"
        class="mr-3"
      >
        {{ __('Saved.') }}
      </ActionMessage>
      <PrimaryButton
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
        @click="updateProfile"
      >
        {{ __('Update Generic Settings') }}
      </PrimaryButton>
    </template>
  </FormSection>
</template>
