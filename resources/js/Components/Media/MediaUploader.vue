<script setup>
import { ref, reactive } from 'vue';
import { PhotoIcon } from '@heroicons/vue/24/outline';
import MediaUpload from './MediaUpload.vue';
import {__} from '@/Composables/Translate';

defineProps({
  resource: {
    type: String,
    required: true
  },
  resourceId: {
    type: Number,
    required: true
  },
  postParams: {
    type: Object,
    default: () => ({})
  }
});

const emit = defineEmits(['uploaded']);

const fileInput = ref(null);
const isDragActive = ref(false);
const fileList = reactive([]);

const dragover = (e) => {
  e.preventDefault();
  isDragActive.value = true;
};

const dragleave = () => isDragActive.value = false;

const drop = (e) => {
  e.preventDefault();
  fileInput.value.files = e.dataTransfer.files;
  onChange();
  isDragActive.value = false;
};

const onChange = () => fileList.push(...fileInput.value.files);
const removeFile = (i) => fileList.splice(i, 1);

const onUploaded = (i) => {
  removeFile(i);
  emit('uploaded');
}
</script>

<template>
  <div
    class="justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md"
    :class="[isDragActive ? 'bg-green-50' : 'bg-white']"
    @dragover="dragover"
    @dragleave="dragleave"
    @drop="drop"
  >
    <div class="space-y-1 text-center">
      <PhotoIcon class="mx-auto h-12 w-12 text-gray-400" />
      <div
        class="flex justify-center text-sm text-gray-600"
      >
        <label
          for="file-upload"
          class="relative cursor-pointer font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500"
        >
          <span>{{ __('Upload a file') }}</span>
          <input
            id="file-upload"
            ref="fileInput"
            name="file-upload"
            type="file"
            class="sr-only"
            multiple
            accept=".png,.jpg,.jpeg,.pdf,.doc,.docx,.xls,.xlsx"
            @change="onChange"
          >
        </label>
        <p class="pl-1">
          {{ __('or drag and drop') }}
        </p>
      </div>
      <p class="text-xs text-gray-500">
        PNG, JPG, PDF, DOC, XLS {{ __('up to') }} 10MB
      </p>
    </div>
    <div
      v-if="fileList.length"
      class="mt-4"
    >
      <ul
        role="list"
        class="grid grid-cols-2 gap-x-4 gap-y-8 sm:grid-cols-3 sm:gap-x-6 md:grid-cols-4 lg:grid-cols-5 xl:gap-x-8 mx-2 sm:mx-0"
      >
        <li
          v-for="(file, i) in fileList"
          :key="file.name"
          class="relative"
        >
          <MediaUpload
            :resource="resource"
            :resourceId="resourceId"
            :file="file"
            :postParams="postParams"
            collection="GENERIC"
            @cancel="removeFile(i)"
            @uploaded="onUploaded(i)"
          />
        </li>
      </ul>
    </div>
  </div>
</template>
