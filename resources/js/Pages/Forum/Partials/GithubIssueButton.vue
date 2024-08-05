<template>
  <DangerButton
    v-if="isPostedToGithub"
    @click="closeIssue"
  >
    Close GitHub Issue
  </DangerButton>
  <PrimaryButton
    v-else
    @click="postIssue"
  >
    Post to GitHub
  </PrimaryButton>
</template>

<script>
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import DangerButton from '@/Components/FormElements/DangerButton.vue';

export default {
    components: {
        PrimaryButton,
        DangerButton
    },
    data() {
        return {
            isPostedToGithub: false
        };
    },
    created() {
        axios.get(window.location.pathname + '/issues')
            .then(response => this.isPostedToGithub = response.data.exists);
    },
    methods: {
        postIssue() {
            axios.post(window.location.pathname + '/issues')
                .then(response => this.isPostedToGithub = response.data.posted);
        },
        closeIssue() {
            axios.delete(window.location.pathname + '/issues')
                .then(response => this.isPostedToGithub = response.data.closed);
        }
    }
};
</script>
