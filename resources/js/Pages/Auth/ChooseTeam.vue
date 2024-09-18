<script setup>
import {useForm, usePage} from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import { RadioGroup, RadioGroupDescription, RadioGroupLabel, RadioGroupOption } from '@headlessui/vue';
import {__} from '@/Composables/Translate';

defineProps({
  usersAccounts: {
    type: Array,
    required: true
  }
});

const form = useForm({
    account: usePage().props.auth.account?.id
});

const submit = () => {
    form.post(route('choose_account.store'));
};
</script>

<template>
  <PublicLayout title="ChangeAcount">
    <div class="flex justify-center">
      <form @submit.prevent="submit">
        <div class="w-full">
          <RadioGroup v-model="form.account">
            <RadioGroupLabel class="sr-only">
              {{ __('Choose Account') }}
            </RadioGroupLabel>
            <div class="w-full bg-white shadow-md -space-y-px rounded-md">
              <RadioGroupOption
                v-for="(account, i) in usersAccounts"
                :key="account.id"
                v-slot="{ checked, active }"
                as="template"
                :value="account.id"
                :dusk="`account-radio-${account.id}`"
              >
                <div :class="[i === 0 ? 'rounded-tl-md rounded-tr-md' : '', i === usersAccounts.length - 1 ? 'rounded-bl-md rounded-br-md' : '', checked ? 'bg-green-50 border-green-200 z-10' : 'border-gray-200', 'relative border p-4 flex cursor-pointer focus:outline-none']">
                  <span
                    :class="[checked ? 'bg-green-600 border-transparent' : 'bg-white border-gray-300', active ? 'ring-2 ring-offset-2 ring-green-500' : '', 'h-4 w-4 mt-0.5 cursor-pointer rounded-full border flex items-center justify-center']"
                    aria-hidden="true"
                  >
                    <span class="rounded-full bg-white w-1.5 h-1.5" />
                  </span>
                  <div class="mx-6 flex-shrink-0">
                    <img
                      class="h-16 w-16 rounded-md"
                      :src="account.profile_photo_url"
                      :alt="account.organization"
                    >
                  </div>
                  <div class="flex flex-col">
                    <RadioGroupLabel
                      as="span"
                      :class="[checked ? 'text-green-900' : 'text-gray-900', 'block text-sm font-medium']"
                    >
                      {{ account.organization }}
                    </RadioGroupLabel>
                    <RadioGroupDescription
                      as="span"
                      :class="[checked ? 'text-green-700' : 'text-gray-500', 'block text-sm']"
                    >
                      {{ account.locale }}
                    </RadioGroupDescription>
                  </div>
                </div>
              </RadioGroupOption>
            </div>
          </RadioGroup>
        </div>
        <div class="flex items-center justify-end mt-4">
          <Link
            :href="route('dashboard')"
            class="underline text-sm text-gray-600 hover:text-gray-900"
          >
            {{ __('Nevermind') }}
          </Link>
          <PrimaryButton
            class="ml-4"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
            @click="submit"
          >
            {{ __('Choose Account') }}
          </PrimaryButton>
        </div>
      </form>
    </div>
  </PublicLayout>
</template>
