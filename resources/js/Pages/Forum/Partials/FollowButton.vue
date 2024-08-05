<script setup>
import {ref} from 'vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import DangerButton from '@/Components/FormElements/DangerButton.vue';
import axios from 'axios';
import {__} from '@/Composables/Translate';

const props = defineProps({
    isFollowing: Boolean
})

const following = ref(props.isFollowing);

const follow = () => {
    axios.post(window.location.pathname + '/subscriptions')
        .then(() => following.value = true);
};

const unfollow = () => {
    axios.delete(window.location.pathname + '/subscriptions')
        .then(() => following.value = false);
};
</script>

<template>
  <DangerButton
    v-if="following"
    @click="unfollow"
  >
    {{ __('Unfollow') }}
  </DangerButton>
  <PrimaryButton
    v-else
    @click="follow"
  >
    {{ __('Follow') }}
  </PrimaryButton>
</template>
