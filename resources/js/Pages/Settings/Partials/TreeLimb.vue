<script setup>
import { ChevronRightIcon, ChevronDownIcon } from '@heroicons/vue/24/outline';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import DangerButton from '@/Components/FormElements/DangerButton.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import {__} from '@/Composables/Translate';
</script>

<script>
export default {
    props: {
        limb: Object,
        category: String
    },
    emits: ['delete-limb'],
    data() {
        return {
            isOpen: false,
            editing: false,
            text: this.limb.text
        };
    },
    computed: {
        hasChildren() {
            return this.limb.children && this.limb.children.length;
        }
    },
    methods: {
        toggle() {
            if (this.hasChildren) {
                this.isOpen = !this.isOpen;
            }
        },
        addChild(limb) {
            window.axios.post(this.route('custom-classification-terminology.store', this.category), {
                parentId: limb.id,
                name: 'New Term',
            }).then(response => {
                if (! ("children" in limb)) {
                    limb.children = [];
                }

                limb.children.push({
                    text: 'New Term',
                    id: response.data.id,
                    data: {
                        canHaveCustom: false,
                        isCustom: true
                    }
                });

                this.$nextTick(() => {
                    this.isOpen = true;
                    this.$refs['node'+response.data.id].editing = true;
                    this.$refs['node'+response.data.id].focus();
                });
            });
            // .catch(error => {
            //     alert(error.response.data.message);
            // });

            //this.$emit('add-item', item);
        },
        editChild(limb) {
            this.editing = true;
        },
        updateChild(limb) {
            window.axios.put(this.route('custom-classification-terminology.update', [this.category, limb.id]), {
                name: this.text,
            }).then(() => {
                this.editing = false;
            });
        },
        deleteChild(limb) {
            if (confirm('Are you sure? !! ALL SAVED CUSTOM TERMS WILL ALSO BE DELETED !!')) {
                window.axios.delete(this.route('custom-classification-terminology.destroy', [this.category, limb.id]))
                    .then(() => this.$emit('delete-limb', limb));
            }
        },
        spliceLimb(limb) {
            let index = this.limb.children.findIndex(o => o.id === limb.id);
            this.limb.children.splice(index, 1);
        },
        focus() {
            this.$nextTick(() => {
                this.$refs.input.focus();
            });
        }
    }
};
</script>

<template>
  <li class="ml-6">
    <div
      class="flex items-center font-medium mb-2"
      @click="toggle"
    >
      <template v-if="hasChildren">
        <ChevronDownIcon
          v-if="isOpen"
          class="h-5 w-5"
        />
        <ChevronRightIcon
          v-else
          class="h-5 w-5"
        />
      </template>
      <TextInput
        v-if="editing"
        ref="input"
        v-model="text"
        name="child-term-editing"
        class="w-52"
      />
      <span
        v-else-if="limb.data.isCustom"
        class="ml-7"
      >
        {{ text }}
      </span>
      <button
        v-else
        :class="[hasChildren ? 'ml-2' : 'ml-7']"
      >
        {{ text }}
      </button>
      <PrimaryButton
        v-if="limb.data.canHaveCustom"
        class="ml-3 py-1"
        @click="addChild(limb)"
      >
        {{ __('Add Child Term') }}
      </PrimaryButton>

      <PrimaryButton
        v-if="editing"
        class="ml-3 py-1"
        @click="updateChild(limb)"
      >
        {{ __('Save') }}
      </PrimaryButton>
      <div v-else>
        <SecondaryButton
          v-if="limb.data.isCustom"
          class="ml-3 py-1"
          @click="editChild(limb)"
        >
          {{ __('Edit') }}
        </SecondaryButton>
        <DangerButton
          v-if="limb.data.isCustom"
          class="ml-3 py-1"
          @click="deleteChild(limb)"
        >
          {{ __('Remove') }}
        </DangerButton>
      </div>
    </div>
    <ul
      v-show="isOpen"
      v-if="hasChildren"
      class="space-y-2"
    >
      <TreeLimb
        v-for="child in limb.children"
        :key="child.id"
        :ref="'node'+child.id"
        :limb="child"
        :category="category"
        @delete-limb="spliceLimb"
      />
    </ul>
  </li>
</template>
