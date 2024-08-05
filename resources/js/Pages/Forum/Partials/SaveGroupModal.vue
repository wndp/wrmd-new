<script setup>
import DialogModal from '@/Components/DialogModal.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextareaInput from '@/Components/FormElements/TextareaInput.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import CheckboxCombobox from '@/Components/FormElements/CheckboxCombobox.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import isInteger from 'lodash/isInteger';
import {__} from '@/Composables/Translate';
</script>

<script>
export default {
  props: {
    group: {
      type: Object,
      default: () => ({})
    },
    teams: {
      type: Array,
      required: true
    },
    heading: {
      type: String,
      default: ''
    },
    button: {
      type: String,
      default: ''
    },
    show: Boolean
  },
  emits: ['close'],
  data() {
    return {
      form: this.$inertia.form({
        name: '',
        description: '',
        members: [],
        settings: []
      }),
      mutatedTeams: this.teams.map(account => {
        return {
          value: account.id,
          label: account.organization
        }
      })
    };
  },
  computed: {
    hasGroup () {
      return isInteger(this.group.id);
    }
  },
  watch: {
    group: function () {
      if (this.hasGroup) {
        this.form.name = this.group.name,
        this.form.description = this.group.description,
        this.form.members = this.group.members.map(m => m.id),
        this.form.settings = this.group.settings
      }
    }
  },
  methods: {
    close() {
      this.$emit('close');
    },
    saveGroup() {
      this.form.submit(
        this.hasGroup ? 'put' : 'post',
        this.route(this.hasGroup ? 'forum.group.update' : 'forum.group.store', {
          group: this.group
        }), {
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
                        //if (! this.hasGroup) {
                          this.form.reset();
                        //}
                        this.close();
                      },
                    }
                    );
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
      {{ heading }}
    </template>
    <template #content>
      <div class="mt-6 grid grid-cols-4 gap-6">
        <div class="col-span-2">
          <InputLabel for="name">{{ __('Give Your Group a Name') }}</InputLabel>
          <TextInput
            v-model="form.name"
            name="name"
            class="mt-1"
          />
          <InputError
            :message="form.errors.name"
            class="mt-2"
          />
        </div>
        <div class="col-span-2">
          <InputLabel for="members">{{ __('Who is in the Group?') }}</InputLabel>
          <CheckboxCombobox
            v-model="form.members"
            name="members"
            :options="mutatedTeams"
            :can-filter="true"
            class="mt-1"
            dusk="group-members"
          />
          <InputError
            :message="form.errors.members"
            class="mt-2"
          />
        </div>
        <div class="col-span-4">
          <InputLabel for="description">{{ __('Describe Your Group') }}</InputLabel>
          <TextareaInput
            v-model="form.description"
            name="description"
            class="mt-1"
          />
          <InputError
            :message="form.errors.description"
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
        @click="saveGroup"
      >
        {{ button }}
      </PrimaryButton>
    </template>
  </DialogModal>
</template>
