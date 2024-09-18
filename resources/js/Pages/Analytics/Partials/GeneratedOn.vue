<script setup>
import {onMounted, inject, ref, computed} from 'vue';
import { format } from 'date-fns';
import {__} from '@/Composables/Translate';

const emitter = inject('emitter');

const currentDate = ref('');

onMounted(() => {
    emitter.on('appliedAnalyticsFilters', () => {
        currentDate.value = formatCurrentDate.value;
    });

    currentDate.value = formatCurrentDate.value;
});

// Thursday, September 16th 2021 at 1:19pm
const formatCurrentDate = computed(() => format(new Date(), "EEEE, MMM do yyyy 'at' h:mbbb"));

const refresh = () => window.history.go(0);
</script>

<template>
  <p class="text-sm text-gray-400">
    {{ __('Generated on') }} {{ currentDate }} - <button
      type="button"
      class="text-blue-600"
      @click="refresh"
    >
      {{ __('Refresh Report') }}
    </button>
  </p>
</template>
