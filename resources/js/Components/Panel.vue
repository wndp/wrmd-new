<template>
  <div class="bg-white shadow rounded-lg">
    <div
      v-if="$slots.title || $slots.description"
      class="px-4 pt-5 sm:px-6"
    >
      <h3
        v-if="$slots.title"
        class="text-xl leading-6 font-bold text-gray-700"
      >
        <slot name="title" />
      </h3>
      <p
        v-if="$slots.description"
        class="mt-1 text-sm text-gray-500"
      >
        <slot name="description" />
      </p>
    </div>
    <div class="px-4 py-4 sm:px-6">
      <div class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-6">
        <slot name="content" />
      </div>
    </div>
    <div
      v-if="$slots.actions"
      class="px-4 py-2 sm:px-6 bg-white rounded-b-lg"
    >
      <slot name="actions" />
    </div>
  </div>
</template>
