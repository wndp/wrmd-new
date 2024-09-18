<script setup>
import {ref} from 'vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import CommonName from '@/Components/FormElements/CommonName.vue';
import {__} from '@/Composables/Translate';

const emit = defineEmits(['use-segment']);

//const terms = ref([]);
const segment = ref('');

// Needed because vuejs does not support v-model.lazy on custom input components
// https://github.com/vuejs/vue/issues/6914
const commonName = ref('');

const use = (name) => {
    if (commonName.value.length) {
        segment.value = commonName;
    }

    emit('use-segment', `${name}: ${segment.value}`);

    setTimeout(() => {
        segment.value = '';
        commonName.value = '';
    }, 500);
};
</script>

<template>
  <div class="py-3">
    <button
      type="button"
      class="block text-sm font-bold text-gray-700"
      @click="use('All Patients')"
    >
      {{ __('All Patients') }}
    </button>
  </div>
  <div class="py-3">
    <label class="block text-sm font-bold text-gray-700">{{ __('Disposition') }}</label>
    <div class="mt-1 relative rounded-md">
      <SelectInput
        v-model.lazy="segment"
        :options="$page.props.options.patientDispositionsOptions"
        @change="use('Disposition')"
      />
    </div>
  </div>
  <div class="py-3">
    <label class="block text-sm font-bold text-gray-700">{{ __('Taxonomic Class') }}</label>
    <div class="mt-1 relative rounded-md">
      <SelectInput
        v-model.lazy="segment"
        :options="$page.props.options.taxaClassesOptions"
        @change="use('Taxonomic Class')"
      />
    </div>
  </div>
  <div class="py-3">
    <label class="block text-sm font-bold text-gray-700">{{ __('Biological Group') }}</label>
    <div class="mt-1 relative rounded-md">
      <SelectInput
        v-model.lazy="segment"
        :options="[]"
        @change="use('Biological Group')"
      />
    </div>
  </div>
  <div class="py-3">
    <label class="block text-sm font-bold text-gray-700">{{ __('Common Name') }}</label>
    <div class="mt-1 relative rounded-md">
      <CommonName
        v-model="commonName"
        @change="use('Common Name')"
      />
    </div>
  </div>
  <div class="py-3">
    <label class="block text-sm font-bold text-gray-700">{{ __('City Found') }}</label>
    <div class="mt-1 relative rounded-md">
      <TextInput
        v-model.lazy="segment"
        @change="use('City Found')"
      />
    </div>
  </div>
  <div class="py-3">
    <label class="block text-sm font-bold text-gray-700">{{ __('Circumstance of Admission') }}</label>
    <div class="mt-1 relative rounded-md">
      <SelectInput
        v-model.lazy="segment"
        :options="[]"
        @change="use('Circumstance of Admission')"
      />
    </div>
  </div>
  <div class="py-3">
    <label class="block text-sm font-bold text-gray-700">{{ __('Clinical Classification') }}</label>
    <div class="mt-1 relative rounded-md">
      <SelectInput
        v-model.lazy="segment"
        :options="[]"
        @change="use('Clinical Classification')"
      />
    </div>
  </div>
</template>
