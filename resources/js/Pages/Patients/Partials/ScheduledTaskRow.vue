<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import DailyTasksAuditLogModal from './DailyTasksAuditLogModal.vue';
import SaveRecheckModal from '@/Pages/DailyTasks/Partials/SaveRecheckModal.vue';
import SavePrescriptionModal from '@/Pages/DailyTasks/Partials/SavePrescriptionModal.vue';
import SaveNutritionModal from '@/Pages/DailyTasks/Partials/SaveNutritionModal.vue';
import DeleteTaskModal from '@/Pages/DailyTasks/Partials/DeleteTaskModal.vue';
import { TrashIcon, PencilIcon, ClipboardDocumentCheckIcon } from '@heroicons/vue/24/outline';
import Badge from '@/Components/Badge.vue';
import { dateForHumans } from '@/Composables/DateFormats';
import {__} from '@/Composables/Translate';
import {can} from '@/Composables/Can';
import {Abilities} from '@/Enums/Abilities';

const props = defineProps({
  task: {
    type: Object,
    required: true
  }
});

let showAuditLog = ref(false);
let showEditTask = ref(false);
let showDeleteTask = ref(false);

let editModal = computed(() => {
  switch (props.task.type) {
    case 'recheck': return SaveRecheckModal;
    case 'prescription': return SavePrescriptionModal;
    case 'nutrition-plan': return SaveNutritionModal;
    default:
      return '';
  }
});

let editModalProps = computed(() => {
  switch (props.task.type) {
  case 'recheck':
    return {
      title: __('Update Recheck'),
      recheck: props.task,
      patientId: props.task.patient_id
    };
  case 'prescription':
    return {
      title: __('Update Prescription'),
      prescription: props.task,
      patientId: props.task.patient_id
    };
  case 'nutrition-plan':
    return {
      title: __('Update Nutrition Plan'),
      nutrition: props.task,
      patientId: props.task.patient_id
    };
  default:
    return {};
  }
});

const closeEditModal = () => {
  router.reload({ only: ['tasks'] });
  showEditTask.value = false;
};

const dateRange = () => {
    if (props.task.start_date === props.task.end_date ) {
        return dateForHumans(props.task.start_date);
    }

    let end = props.task.end_date ? dateForHumans(props.task.end_date) : '...';

    return `${dateForHumans(props.task.start_date)} &mdash; ${end}`;
};

const auditLog = () => {
  showAuditLog.value = true;
};

const editTask = () => {
  showEditTask.value = true;
};

const deleteTask = () => {
  showDeleteTask.value = true;
};
</script>

<template>
  <tr>
    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900">
      <div
        v-if="can(Abilities.MANAGE_DAILY_TASKS)"
        class="flex gap-2"
      >
        <button
          type="button"
          dusk="deleteTask"
          @click="deleteTask()"
        >
          <TrashIcon class="w-5 h-5 text-red-600" />
        </button>
        <button
          type="button"
          dusk="editTask"
          @click="editTask()"
        >
          <PencilIcon class="w-5 h-5 text-blue-600" />
        </button>
        <button
          type="button"
          dusk="auditLog"
          @click="auditLog()"
        >
          <ClipboardDocumentCheckIcon class="w-5 h-5 text-blue-600" />
        </button>
      </div>
    </td>
    <td class="whitespace-nowrap hidden px-3 py-4 md:table-cell">
      <p
        class="text-sm text-gray-500"
        v-html="dateRange(task)"
      />
      <Badge
        :color="task.badge_color"
        class="mt-1"
      >
        {{ task.badge_text }}
      </Badge>
    </td>
    <td class="px-3 py-4 text-sm">
      {{ task.summary_body }}
    </td>
  </tr>
  <DailyTasksAuditLogModal
    v-if="showAuditLog"
    :schedulable="task"
    :show="true"
    @close="showAuditLog = false"
  />
  <component
    :is="editModal"
    :show="showEditTask"
    v-bind="editModalProps"
    @close="closeEditModal"
  />
  <DeleteTaskModal
    v-if="showDeleteTask"
    :schedulable="task"
    :show="true"
    @close="showDeleteTask = false"
  />
</template>
