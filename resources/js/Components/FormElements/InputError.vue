<template>
  <div
    v-show="message"
    class="flex"
  >
    <div class="pr-3 flex items-center pointer-events-none">
      <ExclamationCircleIcon
        class="h-5 w-5 text-red-500"
        aria-hidden="true"
      />
    </div>
    <p class="text-sm text-red-600">
      {{ message }}
    </p>
  </div>
</template>

<script>
    import { ExclamationCircleIcon } from '@heroicons/vue/24/solid'

    export default {
        components: {
            ExclamationCircleIcon,
        },

        props: ['message']
    }
</script>
