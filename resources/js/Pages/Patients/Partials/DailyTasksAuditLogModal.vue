<script setup>
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import Badge from '@/Components/Badge.vue';
import Alert from '@/Components/Alert.vue';
import {__} from '@/Composables/Translate';

defineProps({
  schedulable: {
    type: Object,
    required: true
  },
  show: Boolean
});

const emit = defineEmits(['close']);

const close = () => emit('close');
</script>

<template>
  <DialogModal
    :show="show"
    @close="close"
  >
    <template #title>
      <div class="flex justify-between">
        <span>{{ __('Task Audit Log') }}</span>
        <Badge
          :color="schedulable.badge_color"
          class="mt-1"
        >
          {{ schedulable.badge_text }}
        </Badge>
      </div>
    </template>
    <template #content>
      <Alert>
        {{ schedulable.summary_body }}
      </Alert>
      <div class="mt-8">
        <Alert
          v-if="schedulable.recorded_tasks.length === 0"
          color="red"
          class="mt-4"
        >
          {{ __('There is no user activity recorded for this task.') }}
        </Alert>
        <ul
          role="list"
          class="divide-y divide-gray-200"
        >
          <li
            v-for="activityItem in schedulable.recorded_tasks"
            :key="activityItem.id"
            class="py-4"
          >
            <div class="flex space-x-3">
              <div class="flex-1 space-y-1">
                <div class="flex items-center justify-between">
                  <h3 class="text-sm font-medium">
                    {{ activityItem.user?.name || __('Unknown User') }}
                  </h3>
                </div>
                <p class="text-sm text-gray-500">
                  <template v-if="activityItem.completed_at_for_humans">
                    {{ __('Checked task :occurrence of :occurrences scheduled for :occurrenceDate as completed on :completedDate.', {
                      occurrence: activityItem.occurrence,
                      occurrences: schedulable.occurrences,
                      occurrenceDate: activityItem.occurrence_at_for_humans,
                      completedDate: activityItem.completed_at_for_humans
                    }) }}
                  </template>
                  <template v-else>
                    {{ __('Checked task :occurrence of :occurrences scheduled for :occurrenceDate as not completed on :createdDate.', {
                      occurrence: activityItem.occurrence,
                      occurrences: schedulable.occurrences,
                      occurrenceDate: activityItem.occurrence_at_for_humans,
                      createdDate: activityItem.created_at_for_humans
                    }) }}
                  </template>
                </p>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </template>
    <template #footer>
      <SecondaryButton @click="close">
        {{ __('Close') }}
      </SecondaryButton>
    </template>
  </DialogModal>
</template>
