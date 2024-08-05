<script setup>
import { ref, reactive, nextTick, inject } from 'vue';
import DialogModal from './DialogModal.vue';
import InputError from './FormElements/InputError.vue';
import PrimaryButton from './FormElements/PrimaryButton.vue';
import SecondaryButton from './FormElements/SecondaryButton.vue';
import Input from './FormElements/Input.vue';
import Translate from '@/Mixins/Translate';

const emit = defineEmits(['confirmed']);
const route = inject('route');

defineProps({
    title: {
        type: String,
        default: Translate.methods.__('Confirm Password'),
    },
    content: {
        type: String,
        default: Translate.methods.__('For your security, please confirm your password to continue.'),
    },
    button: {
        type: String,
        default: Translate.methods.__('Confirm'),
    },
});

const confirmingPassword = ref(false);

const form = reactive({
    password: '',
    error: '',
    processing: false,
});

const passwordInput = ref(null);

const startConfirmingPassword = () => {
    window.axios.get(route('password.confirmation')).then(response => {
        if (response.data.confirmed) {
            emit('confirmed');
        } else {
            confirmingPassword.value = true;

            setTimeout(() => passwordInput.value.focus(), 250);
        }
    });
};

const confirmPassword = () => {
    form.processing = true;

    window.axios.post(route('password.confirm'), {
        password: form.password,
    }).then(() => {
        form.processing = false;

        closeModal();
        nextTick().then(() => emit('confirmed'));

    }).catch(error => {
        form.processing = false;
        form.error = error.response.data.errors.password[0];
        passwordInput.value.focus();
    });
};

const closeModal = () => {
    confirmingPassword.value = false;
    form.password = '';
    form.error = '';
};
</script>

<template>
  <span>
    <span @click="startConfirmingPassword">
      <slot />
    </span>

    <DialogModal
      :show="confirmingPassword"
      @close="closeModal"
    >
      <template #title>
        {{ title }}
      </template>

      <template #content>
        {{ content }}

        <div class="mt-4">
          <Input
            ref="passwordInput"
            v-model="form.password"
            type="password"
            name="confirm_password"
            class="mt-1 block w-full md:w-1/2"
            placeholder="Password"
            @keyup.enter="confirmPassword"
          />

          <InputError
            :message="form.error"
            class="mt-2"
          />
        </div>
      </template>

      <template #footer>
        <SecondaryButton @click="closeModal">
          {{ __('Cancel') }}
        </SecondaryButton>

        <PrimaryButton
          class="ml-3"
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
          @click="confirmPassword"
        >
          {{ button }}
        </PrimaryButton>
      </template>
    </DialogModal>
  </span>
</template>
