<script setup>
import {inject, ref} from 'vue';
import {StarIcon as StarIconSolid} from '@heroicons/vue/24/solid';
import {StarIcon as StarIconOutline} from '@heroicons/vue/24/outline';
import axios from 'axios';

const emitter = inject('emitter');

const props = defineProps({
    report: {
        type: Object,
        required: true
    }
});

const mutableReport = ref(props.report);

const toggle = () => {
    let requestType = mutableReport.value.isFavorited ? 'delete' : 'post';
    axios[requestType](`/reports/favorite/${props.report.key}`).then(() => {
        mutableReport.value.isFavorited = !mutableReport.value.isFavorited;
        emitter.emit('report.toggle-favorite', mutableReport.value);
    });
}
</script>

<template>
  <button
    type="button"
    @click="toggle"
  >
    <StarIconSolid
      v-if="mutableReport.isFavorited"
      class="text-green-500 h-5 w-5"
    />
    <StarIconOutline
      v-else
      class="text-gray-500 h-5 w-5"
    />
  </button>
</template>
