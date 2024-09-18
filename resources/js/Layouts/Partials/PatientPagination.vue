<script setup>
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import {__} from '@/Composables/Translate';

const caseNumber = ref(null);
const admissionsPaginator = computed(() => usePage().props.admissionsPaginator);
const searchPaginator = computed(() => usePage().props.searchPaginator);
const lastCaseId = computed(() => usePage().props.lastCaseId);

const redirectToCaseYear = () => {
  var match, caseYear, caseID;
  match = caseNumber.value.match(/^(((20)?\d{2})-)?(\d+)$/);

  if (match) {
    // If no case year was found in the regex then assume current viewing year
    // else use the found year, making sure that it is a 4 digit year
    caseYear = (! match[2]) ? lastCaseId.value.year : (match[2].length === 4 ? match[2] : '20' + match[2]);
    caseID = match[4];

    router.get(window.location.href, {
      y: caseYear,
      c: caseID,
      queryCache: 'flush'
    });
  }
}
</script>

<template>
  <div
    v-if="searchPaginator !== null"
    class="flex justify-center items-center space-x-2 mb-2"
  >
    <PrimaryButton
      :disabled="searchPaginator.current_page === 1"
      @click="$inertia.get(searchPaginator.prev_page_url)"
    >
      {{ __('Prev') }}
    </PrimaryButton>
    <span class="text-gray-700 text-md">{{ __(':current of :total', {current: searchPaginator.current_page, total: searchPaginator.total}) }}</span>
    <PrimaryButton
      :disabled="searchPaginator.current_page === searchPaginator.total"
      @click="$inertia.get(searchPaginator.next_page_url)"
    >
      {{ __('Next') }}
    </PrimaryButton>
  </div>
  <div class="flex justify-center items-center space-x-2">
    <PrimaryButton
      :disabled="admissionsPaginator.current_page === 1"
      @click="$inertia.get(admissionsPaginator.first_page_url)"
    >
      {{ __('First') }}
    </PrimaryButton>
    <PrimaryButton
      :disabled="admissionsPaginator.current_page === 1"
      @click="$inertia.get(admissionsPaginator.prev_page_url)"
    >
      {{ __('Back') }}
    </PrimaryButton>
    <div class="w-24">
      <TextInput
        v-model="caseNumber"
        name="case_number"
        class="text-center"
        @change="redirectToCaseYear"
      />
    </div>
    <PrimaryButton
      :disabled="admissionsPaginator.current_page === lastCaseId.id"
      @click="$inertia.get(admissionsPaginator.next_page_url)"
    >
      {{ __('Next') }}
    </PrimaryButton>
    <PrimaryButton
      :disabled="admissionsPaginator.current_page === lastCaseId.id"
      @click="$inertia.get(admissionsPaginator.last_page_url)"
    >
      {{ __('Last') }}
    </PrimaryButton>
  </div>
</template>
