<template>
  <div class="relative">
    <Loading
      v-show="loading"
      class="absolute inset-x-0 top-10"
    />
    <iframe
      ref="iframe"
      :src="`${url}&format=html`"
      class="border border-gray-200 h-full"
      frameborder="0"
      width="100%"
      height="100%"
      allowfullscreen
    />
  </div>
</template>

<script>
import Loading from '@/Components/Loading.vue';

export default {
    components: {
        Loading
    },
    props: {
        url: String,
    },
    data() {
        return {
            loading: true
        };
    },
    mounted() {
       this.$refs.iframe.addEventListener('load', this.contentFinishedLoading, true);
    },
    methods: {
        contentFinishedLoading() {
            this.loading = false;
        }
    },
};
</script>
