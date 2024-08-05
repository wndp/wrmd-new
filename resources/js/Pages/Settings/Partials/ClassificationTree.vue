<template>
  <ul class="space-y-2 -ml-6">
    <TreeLimb
      v-for="limb in treeData"
      :key="limb.id"
      :limb="limb"
      :category="category"
      @make-folder="makeFolder"
      @add-item="addItem"
    />
  </ul>
</template>

<script>
import TreeLimb from './TreeLimb.vue';

export default {
    components: {
        TreeLimb
    },
    props: {
        category: String
    },
    data() {
        return {
            treeData: []
        };
    },
    created() {
        window.axios.get(this.route('custom-classification-terminology.index', this.category)).then(response => {
            this.treeData = response.data;
        });
    },
    methods: {
        makeFolder(item) {
            item.children = [];
            this.addItem(item);
        },
        addItem(item) {
            item.children.push({
                name: "new stuff"
            });
        }
    }
};
</script>
