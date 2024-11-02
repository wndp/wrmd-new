<script setup>
import {ref} from 'vue';
import {ReceiptPercentIcon, TrashIcon} from '@heroicons/vue/24/outline';
import Badge from '@/Components/Badge.vue';
import DeleteLabReportModal from './DeleteLabReportModal.vue';
import FecalResult from './FecalResult.vue';
import CytologyResult from './CytologyResult.vue';
import CbcResult from './CbcResult.vue';
import ChemistryResult from './ChemistryResult.vue';
import UrinalysisResult from './UrinalysisResult.vue';
import ToxicologyResult from './ToxicologyResult.vue';
import {__} from '@/Composables/Translate';

defineProps({
  patientId: {
    type: String,
    required: true
  },
  labReport: {
    type: Object,
    required: true
  }
});

const showConfirmDelete = ref(false);
const showLabResult = ref(null);
</script>

<template>
  <tr>
    <td class="whitespace-nowrap pl-4 pr-3 py-4 sm:pl-6 text-sm font-medium text-gray-900">
      <div class="flex">
        <button>
          <TrashIcon
            class="w-5 h-5 mr-2 text-red-500"
            @click="showConfirmDelete = true"
          />
        </button>
        <button
          class="flex items-center text-blue-600"
          @click="showLabResult = labReport.analysis_type"
        >
          <ReceiptPercentIcon class="w-5 h-5 mr-2" />
          {{ __('View Results') }}
        </button>
      </div>
    </td>
    <td class="whitespace-nowrap pl-4 pr-3 py-4 sm:pl-6 text-sm font-medium text-gray-900">
      {{ labReport.analysis_date_at_for_humans }}
    </td>
    <td class="whitespace-nowrap px-3 py-3.5 text-sm text-gray-500">
      <Badge :color="labReport.badge_color">
        {{ labReport.badge_text }}
      </Badge>
    </td>
    <td class="px-3 py-3.5 text-sm text-gray-500">
      {{ labReport.technician }}
    </td>
    <td class="px-3 py-3.5 text-sm text-gray-500">
      {{ labReport.comments }}
    </td>
    <DeleteLabReportModal
      v-if="showConfirmDelete"
      :patientId="patientId"
      :labReport="labReport"
      :show="true"
      @close="showConfirmDelete = false"
    />
    <FecalResult
      v-if="showLabResult === 'fecal'"
      :patientId="patientId"
      :labReport="labReport"
      :show="true"
      @close="showLabResult = null"
    />
    <CytologyResult
      v-if="showLabResult === 'cytology'"
      :patientId="patientId"
      :labReport="labReport"
      :show="true"
      @close="showLabResult = null"
    />
    <CbcResult
      v-if="showLabResult === 'cbc'"
      :patientId="patientId"
      :labReport="labReport"
      :show="true"
      @close="showLabResult = null"
    />
    <ChemistryResult
      v-if="showLabResult === 'chemistry'"
      :patientId="patientId"
      :labReport="labReport"
      :show="true"
      @close="showLabResult = null"
    />
    <UrinalysisResult
      v-if="showLabResult === 'urinalysis'"
      :patientId="patientId"
      :labReport="labReport"
      :show="true"
      @close="showLabResult = null"
    />
    <ToxicologyResult
      v-if="showLabResult === 'toxicology'"
      :patientId="patientId"
      :labReport="labReport"
      :show="true"
      @close="showLabResult = null"
    />
  </tr>
</template>
