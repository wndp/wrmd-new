<script setup>
import {watch} from 'vue';
import {useForm, usePage} from '@inertiajs/vue3';
import FormSection from '@/Components/FormElements/FormSection.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
  timezones: {
    type: Array,
    required: true
  }
});

const form = useForm({
  timezone: usePage().props.settings.timezone,
  language: usePage().props.settings.language,
});

const update = () => {
  form.put(route('account.profile.update.localization'), {
    preserveScroll: true,
    onFinish: () => window.location.reload()
  });
}

watch(() => props.timezones, (newVal) => form.timezone = newVal[0].value)
</script>

<template>
  <FormSection>
    <template #title>
      {{ __('Localization') }}
    </template>
    <div class="col-span-4 sm:col-span-2">
      <InputLabel for="timezone">
        {{ __('Timezone') }}
      </InputLabel>
      <SelectInput
        v-model="form.timezone"
        name="timezone"
        :options="timezones"
        class="mt-1"
      />
    </div>
    <div class="col-span-4 sm:col-span-2">
      <InputLabel for="language">
        {{ __('Language') }}
      </InputLabel>
      <SelectInput
        v-model="form.language"
        name="language"
        :options="$page.props.options.languages"
        class="mt-1"
      />
    </div>
    <template #actions>
      <ActionMessage
        :on="form.recentlySuccessful"
        class="mr-3"
      >
        {{ __('Saved.') }}
      </ActionMessage>
      <PrimaryButton
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
        @click="update"
      >
        {{ __('Update Localization') }}
      </PrimaryButton>
    </template>
  </FormSection>
</template>
