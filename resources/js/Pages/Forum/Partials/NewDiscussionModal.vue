<script setup>
import DialogModal from '@/Components/DialogModal.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import TextareaInput from '@/Components/FormElements/TextareaInput.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import URI from 'urijs';
import {__} from '@/Composables/Translate';
</script>

<script>
export default {
  props: {
    channels: {
      type: Array,
      required: true
    },
    groups: {
      type: Array,
      required: true
    },
    show: Boolean
  },
  emits: ['close'],
  data() {
    return {
      form: this.$inertia.form({
        channel: 'general',
        group: new URI().query(true).group ?? '',
        title: '',
        body: ''
      }),
    };
  },
  computed: {
    computedGroups() {
      return [{
        value: '',
        label: ''
      }].concat(this.groups.map(group => {
        return {
          value: group.slug,
          label: group.name
        };
      }));
    }
  },
  methods: {
    close() {
      this.$emit('close');
    },
    postThread() {
      this.form.post(this.route('forum.store'), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
          if (Object.keys(page.props.errors).length === 0) {
            this.form.reset();
            this.close();
          }
        },
      });
    },
  }
};
</script>

<template>
  <DialogModal
    :show="show"
    @close="close"
  >
    <template #title>
      {{ __('Start A New Discussion') }}
    </template>
    <template #content>
      <div class="mt-6 grid grid-cols-4 gap-6">
        <div class="col-span-4">
          <InputLabel for="title">{{ __('Add a Title') }}</InputLabel>
          <TextInput
            id="title"
            v-model="form.title"
            name="title"
            class="mt-1"
          />
          <InputError
            :message="form.errors.title"
            class="mt-2"
          />
        </div>
        <div class="col-span-4">
          <InputLabel for="body">{{ __("What's on Your Mind?") }}</InputLabel>
          <TextareaInput
            id="body"
            v-model="form.body"
            name="body"
            class="mt-1"
          />
          <InputError
            :message="form.errors.body"
            class="mt-2"
          />
        </div>
        <div class="col-span-2">
          <InputLabel for="channel">{{ __('Select a Channel') }}</InputLabel>
          <SelectInput
            id="channel"
            v-model="form.channel"
            name="channel"
            :options="channels"
            class="mt-1"
          />
          <InputError
            :message="form.errors.channel"
            class="mt-2"
          />
        </div>
        <div
          v-if="groups.length > 0"
          class="col-span-2"
        >
          <InputLabel for="group">{{ __('Select a Group') }}</InputLabel>
          <SelectInput
            id="group"
            v-model="form.group"
            name="group"
            :options="computedGroups"
            class="mt-1"
          />
          <InputError
            :message="form.errors.group"
            class="mt-2"
          />
        </div>
      </div>
    </template>
    <template #footer>
      <SecondaryButton
        class="mr-3"
        @click="close"
      >
        {{ __('Nevermind') }}
      </SecondaryButton>
      <PrimaryButton
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
        @click="postThread"
      >
        {{ __('Start Discussion') }}
      </PrimaryButton>
    </template>
  </DialogModal>
</template>
