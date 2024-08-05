<script setup>
defineProps({
    on: Boolean,
});
</script>

<template>
  <div>
    <transition
      leaveActiveClass="transition ease-in duration-1000"
      leaveFromClass="opacity-100"
      leaveToClass="opacity-0"
    >
      <div
        v-show="on"
        class="text-sm text-gray-600"
      >
        <slot />
      </div>
    </transition>
  </div>
</template>
