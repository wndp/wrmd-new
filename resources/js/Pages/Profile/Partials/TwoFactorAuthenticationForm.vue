<script setup>
import { ref, computed, watch } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3'
import ConfirmsPassword from '@/Components/ConfirmsPassword.vue';
import FormSection from '@/Components/FormElements/FormSection.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import DangerButton from '@/Components/FormElements/DangerButton.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import Alert from '@/Components/Alert.vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
    requiresConfirmation: Boolean,
});
const enabling = ref(false);
const confirming = ref(false);
const disabling = ref(false);
const qrCode = ref(null);
const setupKey = ref(null);
const recoveryCodes = ref([]);
const confirmationForm = useForm({
    code: '',
});
const twoFactorEnabled = computed(
    () => ! enabling.value && usePage().props.auth.user?.two_factor_enabled,
);
watch(twoFactorEnabled, () => {
    if (! twoFactorEnabled.value) {
        confirmationForm.reset();
        confirmationForm.clearErrors();
    }
});
const enableTwoFactorAuthentication = () => {
    enabling.value = true;
    router.post('/user/two-factor-authentication', {}, {
        preserveScroll: true,
        onSuccess: () => Promise.all([
            showQrCode(),
            showSetupKey(),
            showRecoveryCodes(),
        ]),
        onFinish: () => {
            enabling.value = false;
            confirming.value = props.requiresConfirmation;
        },
    });
};
const showQrCode = () => {
    return window.axios.get('/user/two-factor-qr-code').then(response => {
        qrCode.value = response.data.svg;
    });
};
const showSetupKey = () => {
    return window.axios.get('/user/two-factor-secret-key').then(response => {
        setupKey.value = response.data.secretKey;
    });
}
const showRecoveryCodes = () => {
    return window.axios.get('/user/two-factor-recovery-codes').then(response => {
        recoveryCodes.value = response.data;
    });
};
const confirmTwoFactorAuthentication = () => {
    confirmationForm.post('/user/confirmed-two-factor-authentication', {
        errorBag: "confirmTwoFactorAuthentication",
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            confirming.value = false;
            qrCode.value = null;
            setupKey.value = null;
        },
    });
};
const regenerateRecoveryCodes = () => {
    window.axios
        .post('/user/two-factor-recovery-codes')
        .then(() => showRecoveryCodes());
};
const disableTwoFactorAuthentication = () => {
    disabling.value = true;
    router.delete('/user/two-factor-authentication', {
        preserveScroll: true,
        onSuccess: () => {
            disabling.value = false;
            confirming.value = false;
        },
    });
};
</script>

<template>
  <FormSection>
    <template #title>
      <template
        v-if="twoFactorEnabled && ! confirming"
      >
        {{ __('You Have Enabled Two Factor Authentication.') }}
      </template>
      <template
        v-else-if="twoFactorEnabled && confirming"
      >
        {{ __('Finish Enabling Two Factor Authentication.') }}
      </template>
      <template
        v-else
      >
        {{ __('You Have Not Enabled Two Factor Authentication.') }}
      </template>
    </template>
    <template #description>
      {{ __("When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your password manager or phone's authenticator application.") }}
    </template>
    <template v-if="twoFactorEnabled">
      <template v-if="qrCode">
        <Alert class="col-span-4">
          <template
            v-if="confirming"
          >
            {{ __("To finish enabling two factor authentication, scan the following QR code using a password manage or your phone's authenticator application or enter the setup key and provide the generated OTP code.") }}
          </template>
          <template v-else>
            {{ __("Two factor authentication is now enabled. Scan the following QR code using your phone's authenticator application or enter the setup key.") }}
          </template>
        </Alert>
        <div
          class="col-span-4 sm:col-span-3"
          v-html="qrCode"
        />
        <div
          v-if="setupKey"
          class="col-span-4 sm:col-span-3"
        >
          <p class="font-semibold">
            {{ __('Setup Key') }}: <span v-html="setupKey" />
          </p>
        </div>
        <div
          v-if="confirming"
          class="col-span-4 sm:col-span-2"
        >
          <InputLabel for="code">
            {{ __('Code') }}
          </InputLabel>
          <TextInput
            id="code"
            v-model="confirmationForm.code"
            type="text"
            name="code"
            class="mt-1"
            inputmode="numeric"
            autofocus
            autocomplete="one-time-code"
            @keyup.enter="confirmTwoFactorAuthentication"
          />
          <InputError
            :message="confirmationForm.errors.code"
            class="mt-2"
          />
        </div>
      </template>
      <template v-if="recoveryCodes.length > 0 && ! confirming">
        <Alert class="col-span-4">
          {{ __('Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.') }}
        </Alert>
        <div class="col-span-4 md:col-span-2 text-sm text-gray-500">
          <div class="grid gap-1 max-w-xl px-4 py-4 font-mono text-sm bg-gray-100 rounded-lg">
            <div
              v-for="code in recoveryCodes"
              :key="code"
            >
              {{ code }}
            </div>
          </div>
        </div>
      </template>
    </template>
    <div class="col-span-4">
      <div v-if="! twoFactorEnabled">
        <ConfirmsPassword @confirmed="enableTwoFactorAuthentication">
          <PrimaryButton
            type="button"
            :class="{ 'opacity-25': enabling }"
            :disabled="enabling"
          >
            {{ __('Enable') }}
          </PrimaryButton>
        </ConfirmsPassword>
      </div>
      <div v-else>
        <ConfirmsPassword @confirmed="confirmTwoFactorAuthentication">
          <PrimaryButton
            v-if="confirming"
            type="button"
            class="mr-3"
            :class="{ 'opacity-25': enabling }"
            :disabled="enabling"
          >
            {{ __('Confirm') }}
          </PrimaryButton>
        </ConfirmsPassword>
        <ConfirmsPassword @confirmed="regenerateRecoveryCodes">
          <SecondaryButton
            v-if="recoveryCodes.length > 0 && ! confirming"
            class="mr-3"
          >
            {{ __('Regenerate Recovery Codes') }}
          </SecondaryButton>
        </ConfirmsPassword>
        <ConfirmsPassword @confirmed="showRecoveryCodes">
          <SecondaryButton
            v-if="recoveryCodes.length === 0 && ! confirming"
            class="mr-3"
          >
            {{ __('Show Recovery Codes') }}
          </SecondaryButton>
        </ConfirmsPassword>
        <ConfirmsPassword @confirmed="disableTwoFactorAuthentication">
          <SecondaryButton
            v-if="confirming"
            :class="{ 'opacity-25': disabling }"
            :disabled="disabling"
          >
            {{ __('Cancel') }}
          </SecondaryButton>
        </ConfirmsPassword>
        <ConfirmsPassword @confirmed="disableTwoFactorAuthentication">
          <DangerButton
            v-if="! confirming"
            :class="{ 'opacity-25': disabling }"
            :disabled="disabling"
          >
            {{ __('Disable') }}
          </DangerButton>
        </ConfirmsPassword>
      </div>
    </div>
  </FormSection>
</template>
