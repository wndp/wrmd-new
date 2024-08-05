<template>
  <div class="mt-4">
    <div class="-mx-2 -my-1.5">
      <button
        type="button"
        :class="classes"
        class="px-2 py-1.5 rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 uppercase"
        @click="$emit('click')"
      >
        <slot />
      </button>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    color: {
      type: String,
      default: 'yellow'
    },
  },
  emits:['click'],
  computed: {
    classes() {
      switch (this.color) {
          case 'red':
              return 'bg-red-100 text-red-700 hover:bg-red-50 focus:ring-offset-red-100 focus:ring-red-600';
          case 'green':
              return 'bg-green-100 text-green-800 hover:bg-green-200 focus:ring-offset-green-100 focus:ring-green-600';
          case 'blue':
              return 'bg-blue-100 text-blue-700 hover:bg-blue-50 focus:ring-offset-blue-100 focus:ring-blue-600';
          default:
              return 'bg-yellow-100 text-yellow-800 hover:bg-yellow-300 focus:ring-offset-yellow-100 focus:ring-yellow-600';
      }
    },
  }
}
</script>
