<script setup>
import {ref, computed} from 'vue';
import {router} from '@inertiajs/vue3';
import { formatISO9075 } from 'date-fns';
import Badge from '@/Components/Badge.vue';
import CheckboxThreeWay from '@/Components/FormElements/CheckboxThreeWay.vue';
import { PencilIcon } from '@heroicons/vue/24/outline';
import cloneDeep from 'lodash/cloneDeep';
import SaveRecheckModal from './SaveRecheckModal.vue';
import SavePrescriptionModal from './SavePrescriptionModal.vue';
import SaveNutritionModal from './SaveNutritionModal.vue';
import {__} from '@/Composables/Translate';
import {can} from '@/Composables/Can';
import {Abilities} from '@/Enums/Abilities';
import axios from 'axios';

const props = defineProps({
  task: {
    type: Object,
    required: true
  },
  date: {
    type: String,
    required: true
  },
  search: {
    type: String,
    default: ''
  }
});

// clone tasks in order to prevent changes in state propagating up the component chain
const clonedTask = ref(cloneDeep(props.task));
const occurrences = ref((() => {
  const occurrences = {};

  for (let i = 0; i < props.task.number_of_occurrences; i++) {
    occurrences[i+1] =
      props.task.completed_occurrences.includes(i+1)
        ? true
        : props.task.incomplete_occurrences.includes(i+1)
          ? false
          : null;
  }

  return occurrences;
})());

const showEditTask = ref(false);

const editModal = computed(() => {
  switch (props.task.type) {
    case 'recheck': return SaveRecheckModal;
    case 'prescription': return SavePrescriptionModal;
    case 'nutrition-plan': return SaveNutritionModal;
    default:
    return '';
  }
});

const editModalProps = computed(() => {
  switch (props.task.type) {
    case 'recheck':
      return {
        title: __('Update Recheck'),
        recheck: props.task.model,
        patientId: props.task.model.patient_id
      };
    case 'prescription':
      return {
        title: __('Update Prescription'),
        prescription: props.task.model,
        patientId: props.task.model.patient_id
      };
    case 'nutrition-plan':
      return {
        title: __('Update Nutrition Plan'),
        nutrition: props.task.model,
        patientId: props.task.model.patient_id
      };
    default:
      return {};
  }
});

const highlightSearch = () => {
  if (! props.search.length) {
    return clonedTask.value.body;
  }
  let pattern = new RegExp('([.]*)(' + props.search + ')([.]*)', 'gi');
  let replaceWith = '$1<span class="bg-yellow-200">$2</span>$3';
  return clonedTask.value.body.replace(pattern, replaceWith);
};

const occurrenceClicked = (occurrence) => {
  let occurrenceValue = occurrences.value[occurrence];

  if (occurrenceValue === null) {
    deleteOccurrence(occurrence);
  } else {
    postOccurrence(occurrence, occurrenceValue);
  }
};

const postOccurrence = (occurrence, occurrenceValue) => {
  axios.post(`/internal-api/daily-tasks/record/${clonedTask.value.type}/${clonedTask.value.type_id}`, {
    occurrence: occurrence,
    occurrence_at: props.date,
    completed_at: occurrenceValue ? formatISO9075(new Date()) : null,
  }).then(() => {
    router.reload({ only: ['taskGroups'] });
  });
};

const deleteOccurrence = (occurrence) => {
  axios.delete(`/internal-api/daily-tasks/record/${clonedTask.value.type}/${clonedTask.value.type_id}`, {
    data: {
      occurrence: occurrence,
      occurrence_at: props.date
    }
  })
  .then(() => {
    router.reload({ only: ['taskGroups'] });
  });
};

const closeEditModal = () => {
  router.reload({ only: ['taskGroups'] });
  showEditTask.value = false;
}
</script>

<template>
  <div>
    <div
      class="grid gap-2 mt-2 group"
      style="grid-template-columns: 1.25rem 90px 1fr"
      dusk="task"
    >
      <span class="flex">
        <button
          v-if="can(Abilities.MANAGE_DAILY_TASKS)"
          type="button"
          class="opacity-0 group-hover:opacity-100"
          dusk="editTask"
          @click="showEditTask = true"
        >
          <PencilIcon class="w-5 h-5 mr-4 text-blue-600" />
        </button>
      </span>
      <div class="flex items-center space-x-2">
        <template v-if="can(Abilities.MANAGE_DAILY_TASKS)">
          <CheckboxThreeWay
            v-for="(n, iiii) in clonedTask.number_of_occurrences"
            :id="'id' + clonedTask.type_id + n"
            :key="iiii"
            v-model="occurrences[n]"
            @change="occurrenceClicked(n)"
          />
        </template>
      </div>
      <div class="flex items-center text-sm">
        <div class="hidden sm:block">
          <Badge
            :color="clonedTask.badge_color"
            class="mr-4"
          >
            {{ clonedTask.badge_text }}
          </Badge>
        </div>
        <p v-html="highlightSearch()" />
      </div>
    </div>
    <component
      :is="editModal"
      :show="showEditTask"
      v-bind="editModalProps"
      @close="closeEditModal"
    />
  </div>
</template>
