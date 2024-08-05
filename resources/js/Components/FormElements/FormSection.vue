<template>
  <section class="shadow sm:rounded-md">
    <form @submit.prevent="$emit('submitted')">
      <div class="bg-white py-6 px-4 sm:p-6">
        <div v-if="hasHeading">
          <h3
            v-if="$slots.title"
            class="text-lg leading-6 font-medium text-gray-900"
          >
            <slot name="title" />
          </h3>
          <div
            v-if="$slots.description"
            class="mt-1 text-sm text-gray-500"
          >
            <slot name="description" />
          </div>
        </div>
        <div
          class="grid grid-cols-4 gap-6"
          :class="{'mt-6': hasHeading}"
        >
          <slot />
        </div>
      </div>
      <div
        v-if="$slots.actions"
        class="flex items-center justify-end px-4 py-3 bg-gray-50 text-right sm:px-6"
      >
        <slot name="actions" />
      </div>
    </form>
  </section>
</template>

<script>
export default {
    emits: ['submitted'],
    computed: {
        hasHeading() {
            return !! this.$slots.title || !! this.$slots.description;
        }
    }
};
</script>
