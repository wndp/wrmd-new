<script setup>
import { inject, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import TextareaAutosize from '@/Components/FormElements/TextareaAutosize.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import DangerButton from '@/Components/FormElements/DangerButton.vue';
import { BookmarkIcon, TrashIcon } from '@heroicons/vue/24/outline';

const route = inject('route');

const props = defineProps({
  autocomplete: {
    type: Object,
    required: true
  }
})

let form = useForm({
  field: props.autocomplete.field,
  values: props.autocomplete.values.join(', '),
});

let autoCompleteAble = computed(() => usePage().props.options.autoCompleteAble);

let store = () => {
  form.put(route('maintenance.autocomplete.update', props.autocomplete.field), {
    preserveState: false
  });
}

let destroy = () => {
  form.delete(route('maintenance.autocomplete.destroy', props.autocomplete.field), {
    preserveState: false
  });
}
</script>

<template>
  <tr>
    <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-800">
      <SelectInput
        v-model="form.field"
        name="field"
        :options="autoCompleteAble"
      />
      <div class="xl:hidden mt-2">
        <TextareaAutosize
          v-model="form.values"
          name="values"
        />
        <div class="flex mt-2 space-x-4">
          <PrimaryButton
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
            @click="store"
          >
            <BookmarkIcon class="h-5 w-5" />
          </PrimaryButton>
          <DangerButton
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
            @click="destroy"
          >
            <TrashIcon class="h-5 w-5" />
          </DangerButton>
        </div>
      </div>
    </td>
    <td class="hidden px-2 py-2 whitespace-nowrap text-sm text-gray-500 xl:table-cell">
      <TextareaAutosize
        v-model="form.values"
        name="values"
      />
    </td>
    <td class="hidden px-2 py-2 whitespace-nowrap text-sm text-gray-500 xl:table-cell">
      <PrimaryButton
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
        @click="store"
      >
        <BookmarkIcon class="h-5 w-5" />
      </PrimaryButton>
    </td>
    <td class="hidden px-2 py-2 whitespace-nowrap text-sm text-gray-500 xl:table-cell">
      <DangerButton
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
        @click="destroy"
      >
        <TrashIcon class="h-5 w-5" />
      </DangerButton>
    </td>
  </tr>
</template>
