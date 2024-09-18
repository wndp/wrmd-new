<script setup>
import { inject, ref, onMounted } from 'vue';
import { XCircleIcon } from '@heroicons/vue/24/outline';
import Vapor from 'laravel-vapor'
import axios from 'axios';

const route = inject('route');

const props = defineProps({
  resource: {
    type: String,
    required: true
  },
  resourceId: {
    type: Number,
    required: true
  },
  file: {
    type: Object,
    required: true
  },
  collection: {
    type: String,
    required: true
  },
  postParams: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['uploaded', 'cancel']);

const uploadProgress = ref(0);
const uploadProgressError = ref(false);
const uploadErrorMessage = ref(null);

// We buffer the uploadProgress to account for the upload to AWS and to WRMD
const uploadProgressBuffer = 10;

// Using axios.CancelToken is deprecated but until Vapor
// supports the newer AbortController option this code can not be updated.
// https://axios-http.com/docs/cancellation
// https://github.com/laravel/vapor-js/blob/3494f40a869209e28b3d03e4b44696509c25fa8d/src/index.js#L60
const CancelToken = window.axios.CancelToken;
const cancelTokenSource = CancelToken.source();

const cancel = () => {
  cancelTokenSource.cancel()
  emit('cancel');
};

/**
 * @todo
 * Validate file type and return image src for that type
 */
const generateURL = () => {
  let fileSrc = URL.createObjectURL(props.file);

  setTimeout(() => {
      URL.revokeObjectURL(fileSrc);
  }, 1000);

  return fileSrc;
};

const upload = () => {
  Vapor.store(props.file, {
    progress: progress => {
      let percent = Math.round(progress * 100);
      uploadProgress.value = percent <= uploadProgressBuffer ? 0 : percent - uploadProgressBuffer
    },
    cancelToken: cancelTokenSource.token,
    visibility: 'public-read'
  })
  .then(response => {
    axios.post(route('media.store'), {
      resource: props.resource,
      resource_id: props.resourceId,
      uuid: response.uuid,
      key: response.key,
      bucket: response.bucket,
      name: props.file.name,
      content_type: props.file.type,
      extension: response.extension,
      collection: props.collection,
      custom_properties: props.postParams
    }).then(() => {
      uploadProgress.value === 100;
      emit('uploaded');
    })
    .catch((error) => {
        uploadProgressError.value = true;

        if (error.response.status === 422) {
            uploadErrorMessage.value = error.response.data.message;
        } else {
            uploadErrorMessage.value = trans('components.media_upload.upload_error');
        }
    });
  });
};

onMounted(() => upload());
</script>

<template>
  <div :class="[file.current ? 'ring-2 ring-offset-2 ring-green-500' : 'focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-offset-gray-100 focus-within:ring-green-500', 'group block w-full aspect-w-10 aspect-h-7 rounded-lg bg-gray-100 overflow-hidden']">
    <img
      :src="generateURL()"
      :alt="file.name"
      :class="[file.current ? '' : 'group-hover:opacity-75', 'object-cover pointer-events-none']"
    >
  </div>
  <p class="mt-2 block text-sm font-medium text-gray-900 truncate pointer-events-none">
    {{ file.name }}
  </p>
  <p class="block text-sm font-medium text-gray-500 pointer-events-none">
    {{ Math.round(file.size / 1000) + "kb" }}
  </p>
  <div class="flex justify-between items-center mt-2">
    <div
      class="bg-green-300 text-xs font-medium text-green-600 text-center p-0.5 leading-none rounded-full flex justify-center items-center h-5"
      :style="`width: ${uploadProgress}%`"
    >
      <span v-show="uploadProgress > 4">{{ uploadProgress }}%</span>
    </div>
    <button
      type="button"
      @click="cancel()"
    >
      <XCircleIcon class="h-5 w-5 ml-2 text-red-500" />
    </button>
  </div>
  <div>
    <span
      v-if="uploadErrorMessage"
      class="text-xs text-red-600 mt-1"
    >{{ uploadErrorMessage }}</span>
  </div>
</template>
