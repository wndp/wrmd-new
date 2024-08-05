<script>
import { XMarkIcon, CheckCircleIcon, ExclamationCircleIcon } from '@heroicons/vue/20/solid';

export default {
    components: {
        CheckCircleIcon,
        ExclamationCircleIcon,
        XMarkIcon,
    },
    props: {
        notification: Object
    },
    watch: {
        notification(obj) {
            if (obj.heading) {
                this.notify(obj);
            }
        }
    },
    mounted() {
        if (this.notification.heading) {
            this.notify(this.notification);
        }
    },
    methods: {
        notify(obj) {
            this.$notify({
                title: obj.heading,
                text: obj.text,
                style: obj.style || 'success',
            }, 5000);
        }
    }
};
</script>

<template>
  <NotificationGroup>
    <div
      aria-live="assertive"
      class="fixed inset-0 z-40 flex items-end px-4 py-6 pointer-events-none sm:p-6 sm:items-start"
    >
      <div class="w-full flex flex-col items-cente space-y-4 sm:items-end">
        <Notification
          v-slot="{ notifications, close }"
          enter="transform ease-out duration-300 transition"
          enterFrom="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4"
          enterTo="translate-y-0 opacity-100 sm:translate-x-0"
          leave="transition ease-in duration-500"
          leaveFrom="opacity-100"
          leaveTo="opacity-0"
          move="transition duration-500"
          moveDelay="delay-300"
        >
          <div
            v-for="loopNotification in notifications"
            :key="loopNotification.id"
            class="max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-2 ring-black ring-opacity-5 overflow-hidden"
          >
            <div class="p-4">
              <div class="flex items-start">
                <div class="flex-shrink-0">
                  <ExclamationCircleIcon
                    v-if="loopNotification.style == 'danger'"
                    class="h-6 w-6 text-red-400"
                    aria-hidden="true"
                  />
                  <CheckCircleIcon
                    v-else
                    class="h-6 w-6 text-green-400"
                    aria-hidden="true"
                  />
                </div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                  <p
                    class="text-sm font-bold"
                    :class="[loopNotification.style == 'danger' ? 'text-red-600' : 'text-gray-900']"
                  >
                    {{ loopNotification.title }}
                  </p>
                  <p
                    class="mt-1 text-sm"
                    :class="[loopNotification.style == 'danger' ? 'text-red-500' : 'text-gray-500']"
                  >
                    {{ loopNotification.text }}
                  </p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                  <button
                    class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    @click="close(loopNotification.id)"
                  >
                    <span class="sr-only">Close</span>
                    <XMarkIcon
                      class="h-5 w-5"
                      aria-hidden="true"
                    />
                  </button>
                </div>
              </div>
            </div>
          </div>
        </Notification>
      </div>
    </div>
  </NotificationGroup>
</template>
