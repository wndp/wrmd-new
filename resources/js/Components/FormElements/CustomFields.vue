<template>
  <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:gap-y-2 sm:items-center">
    <template
      v-for="customField in customFields"
      :key="customField.id"
    >
      <Label
        :for="`custom_field_${customField.account_field_id}`"
        class="sm:text-right mt-4 sm:mt-0"
        :class="[customField.type === 'textarea' ? 'col-start-1' : '']"
      >
        {{ customField.label }}
        <Required v-if="customField.is_required && enforceRequired" />
      </Label>
      <div
        :class="[customField.type === 'textarea' ? 'col-span-5' : 'col-span-2']"
        class="mt-1 sm:mt-0"
      >
        <Input
          v-if="['text', 'number'].includes(customField.type)"
          :id="`custom_field_${customField.account_field_id}`"
          v-model="proxyCustomValues[`custom_field_${customField.account_field_id}`]"
          :name="`custom_field_${customField.account_field_id}`"
          :required="customField.is_required && enforceRequired"
          :type="customField.type === 'number' ? 'number' : 'text'"
        />
        <Textarea
          v-else-if="customField.type === 'textarea'"
          :id="`custom_field_${customField.account_field_id}`"
          v-model="proxyCustomValues[`custom_field_${customField.account_field_id}`]"
          :name="`custom_field_${customField.account_field_id}`"
          :required="customField.is_required && enforceRequired"
        />
        <Select
          v-else-if="customField.type === 'select'"
          :id="`custom_field_${customField.account_field_id}`"
          v-model="proxyCustomValues[`custom_field_${customField.account_field_id}`]"
          :name="`custom_field_${customField.account_field_id}`"
          :required="customField.is_required && enforceRequired"
          :options="customField.options"
        />
        <DatePicker
          v-else-if="['date', 'datetime'].includes(customField.type)"
          :id="`custom_field_${customField.account_field_id}`"
          v-model="proxyCustomValues[`custom_field_${customField.account_field_id}`]"
          :name="`custom_field_${customField.account_field_id}`"
          :required="customField.is_required && enforceRequired"
          :time="customField.type === 'datetime'"
        />
        <Toggle
          v-else-if="customField.type === 'boolean'"
          :id="`custom_field_${customField.account_field_id}`"
          v-model="proxyCustomValues[`custom_field_${customField.account_field_id}`]"
          :name="`custom_field_${customField.account_field_id}`"
        />
        <div
          v-else-if="customField.type === 'checkbox-group'"
          class="col-span-5 mt-1 sm:mt-0 flex flex-wrap gap-x-4 gap-y-2"
        >
          <div
            v-for="(option, optionIdx) in customField.options"
            :key="option"
            class="relative flex items-start"
          >
            <div class="flex items-center h-5">
              <Checkbox
                :id="`custom_field_${customField.account_field_id}_value_${optionIdx}`"
                v-model="proxyCustomValues[`custom_field_${customField.account_field_id}`]"
                :name="`custom_field_${customField.account_field_id}`"
                :value="option"
              />
            </div>
            <div class="ml-2 text-sm">
              <label
                :for="`custom_field_${customField.account_field_id}_value_${optionIdx}`"
                class="font-medium text-gray-700"
              >{{ option }}</label>
            </div>
          </div>
        </div>
        <InputError
          class="mt-2"
          :message="errors[proxyCustomValues[`custom_field_${customField.account_field_id}`]]"
        />
      </div>
    </template>
  </div>
</template>

<script setup>
import { reactive, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import merge from 'lodash/merge';
import Label from '@/Components/FormElements/Label.vue';
import Input from '@/Components/FormElements/Input.vue';
import Select from '@/Components/FormElements/Select.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import Toggle from '@/Components/FormElements/Toggle.vue';
import Textarea from '@/Components/FormElements/Textarea.vue';
import Checkbox from '@/Components/FormElements/Checkbox.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import Required from '@/Components/FormElements/Required.vue';

const emit = defineEmits(['update:customValues']);

const props = defineProps({
    group: {
        type: String,
        required: true
    },
    panel: {
        type: String,
        default: null
    },
    location: {
        type: String,
        required: true
    },
    enforceRequired: {
        type: Boolean,
        default: false
    },
    customValues: {
        type: [Array, Object],
        required: true
    },
    errors: {
      type: Object,
      default: () => ({})
    }
});

const customFields = (usePage().props.extensions['patient.customFields'] ?? [])
  .flat()
  .filter(field => {
      return field.group === props.group
          && field.panel === props.panel
          && field.location === props.location
  });

let checkboxes = customFields
  .filter(field => field.type === "checkbox-group")
  .reduce((accumulator, field) => {
    accumulator[`custom_field_${field.account_field_id}`] = [];
    return accumulator;
  }, {})

const proxyCustomValues = reactive(merge(checkboxes, props.customValues));

watch(proxyCustomValues, () => {
  emit('update:customValues', proxyCustomValues);
}, { deep: true });

// const proxyCustomValues = computed({
//     get() {
//       //console.log('proxyCustomValues.get')
//       //return props.customValues;
//       let values = props.customValues;

//       let checkboxes = customFields
//         .filter(field => field.type === "checkbox-group")
//         .reduce((accumulator, field) => {
//           accumulator[`custom_field_${field.account_field_id}`] = [];
//           return accumulator;
//         }, {})

//       return merge(checkboxes, values);
//     },
//     set(val) {
//       console.log('proxyCustomValues.set')
//       emit('update:customValues', val);
//     },
// });

</script>
