<template>
  <SwitchGroup>
    <div class="flex items-center">
      <Switch
        v-model="bool"
        :class="[bool ? 'bg-green-600' : 'bg-gray-200', 'relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500']"
      >
        <span class="sr-only">Use setting</span>
        <span :class="[bool ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none relative inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200']">
          <span
            :class="[bool ? 'opacity-0 ease-out duration-100' : 'opacity-100 ease-in duration-200', 'absolute inset-0 h-full w-full flex items-center justify-center transition-opacity']"
            aria-hidden="true"
          >
            <XMarkIcon class="h-3 w-3 text-gray-400" />
          </span>
          <span
            :class="[bool ? 'opacity-100 ease-in duration-200' : 'opacity-0 ease-out duration-100', 'absolute inset-0 h-full w-full flex items-center justify-center transition-opacity']"
            aria-hidden="true"
          >
            <CheckIcon class="h-3 w-3 text-green-600" />
          </span>
        </span>
      </Switch>
      <SwitchLabel
        v-if="label || $slots.default"
        class="block font-medium text-sm text-gray-700 ml-2"
      >
        <template v-if="label">
          {{ label }}
        </template>
        <template v-else>
          <slot />
        </template>
      </SwitchLabel>
    </div>
  </SwitchGroup>
</template>

<script>
import { SwitchGroup, Switch, SwitchLabel } from '@headlessui/vue';
import { CheckIcon, XMarkIcon } from '@heroicons/vue/24/outline';

export default {
    components: {
        SwitchGroup,
        Switch,
        SwitchLabel,
        CheckIcon,
        XMarkIcon
    },
    props: ['modelValue', 'label'],
    emits: ['update:modelValue'],
    data() {
        return {
            bool: this.modelValue
        };
    },
    watch:{
        bool(value) {
            this.$emit('update:modelValue', value);
        }
    }
};
</script>
