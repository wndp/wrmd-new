<script setup>
import { computed } from 'vue';

const props = defineProps({
    color: {
        type: String,
        default: 'gray',
    },
    large: {
        type: Boolean,
        default: false
    }
});

const classes = computed(() => {
  let classes = props.large ? ['text-sm'] : ['text-xs'];

  switch (props.color) {
      case 'red':
           classes.push('bg-red-100 text-red-800');
           break;
      case 'orange':
            classes.push('bg-orange-200 text-orange-800');
            break;
      case 'yellow':
           classes.push('bg-yellow-100 text-yellow-800');
           break;
      case 'green':
           classes.push('bg-green-100 text-green-800');
           break;
      case 'blue':
           classes.push('bg-blue-100 text-blue-800');
           break;
      case 'gray':
      default:
           classes.push('bg-gray-100 text-gray-800');
           break;
  }

  return classes;
});
</script>

<template>
  <span
    class="inline-flex items-center px-2 py-1 rounded-full font-medium whitespace-nowrap"
    :class="classes"
  >
    <slot />
  </span>
</template>
