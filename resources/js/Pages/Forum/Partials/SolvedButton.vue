<script setup>
import {ref} from 'vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import DangerButton from '@/Components/FormElements/DangerButton.vue';
import axios from 'axios';
import {__} from '@/Composables/Translate';

const props = defineProps({
    isSolved: Boolean
});

const solved = ref(props.isSolved);

const solve = () => {
    axios.put(window.location.pathname + '/status', {status: 'Solved'})
        .then(() => this.solved = true);
};

const unsolve = () => {
    axios.put(window.location.pathname + '/status', {status: 'Unsolved'})
        .then(() => this.solved = false);
};
</script>

<template>
  <DangerButton
    v-if="solved"
    @click="unsolve"
  >
    {{ __('Mark as Unsolved') }}
  </DangerButton>
  <PrimaryButton
    v-else
    @click="solve"
  >
    {{ __('Mark as Solved') }}
  </PrimaryButton>
</template>
