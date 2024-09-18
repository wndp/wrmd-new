<template>
  <Panel>
    <template #heading>
      {{ __('Body Systems') }}
    </template>
    <div class="space-y-4 md:space-y-2">
      <div
        v-for="bodyPart in $page.props.options.necopsyBodyParts"
        :key="bodyPart.value"
        class="xl:grid xl:grid-cols-6 xl:gap-x-2 xl:items-center"
      >
        <Label
          :for="bodyPart.value"
          class="xl:text-right"
        >{{ bodyPart.label }}</Label>
        <div class="col-span-5 mt-1 xl:mt-0 lg:flex">
          <Select
            v-model="form[`${bodyPart.value}_finding`]"
            :name="`${bodyPart.value}_finding`"
            :options="$page.props.options.findings"
            class="mr-2 lg:w-36"
          />
          <Input
            v-model="form[bodyPart.value]"
            :name="bodyPart.value"
            class="mt-2 lg:mt-0"
          />
        </div>
      </div>
    </div>
    <template
      v-if="canSubmit"
      #footing
    >
      <div class="flex items-center justify-end text-right">
        <ActionMessage
          :on="form.isDirty"
          class="mr-3"
        >
          <span class="text-red-600">{{ __('There are unsaved changes') }}</span>
        </ActionMessage>
        <ActionMessage
          :on="form.recentlySuccessful"
          class="mr-3"
        >
          {{ __('Saved') }}
        </ActionMessage>
        <PrimaryButton
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
          @click="save"
        >
          {{ __('Update Body Systems Details') }}
        </PrimaryButton>
      </div>
    </template>
  </Panel>
</template>

<script setup>
import Panel from '@/Components/Panel.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import autoSave from '@/Mixins/AutoSave';
import hoistForm from '@/Mixins/HoistForm';
</script>

<script>
  export default {
    mixins: [autoSave, hoistForm],
    props: {
      necropsy: {
        type: Object,
        default: () => ({})
      },
      enforceRequired: {
        type: Boolean,
        default: true
      }
    },
    data() {
      return {
        form: this.$inertia.form({
          integument: this.necropsy?.integument,
          cavities: this.necropsy?.cavities,
          cardiovascular: this.necropsy?.cardiovascular,
          respiratory: this.necropsy?.respiratory,
          gastrointestinal: this.necropsy?.gastrointestinal,
          endocrine_reproductive: this.necropsy?.endocrine_reproductive,
          liver_gallbladder: this.necropsy?.liver_gallbladder,
          hematopoietic: this.necropsy?.hematopoietic,
          renal: this.necropsy?.renal,
          nervous: this.necropsy?.nervous,
          musculoskeletal: this.necropsy?.musculoskeletal,
          head: this.necropsy?.head,
          integument_finding: this.necropsy?.integument_finding || 'Not examined',
          cavities_finding: this.necropsy?.cavities_finding || 'Not examined',
          cardiovascular_finding: this.necropsy?.cardiovascular_finding || 'Not examined',
          respiratory_finding: this.necropsy?.respiratory_finding || 'Not examined',
          gastrointestinal_finding: this.necropsy?.gastrointestinal_finding || 'Not examined',
          endocrine_reproductive_finding: this.necropsy?.endocrine_reproductive_finding || 'Not examined',
          liver_gallbladder_finding: this.necropsy?.liver_gallbladder_finding || 'Not examined',
          hematopoietic_finding: this.necropsy?.hematopoietic_finding || 'Not examined',
          renal_finding: this.necropsy?.renal_finding || 'Not examined',
          nervous_finding: this.necropsy?.nervous_finding || 'Not examined',
          musculoskeletal_finding: this.necropsy?.musculoskeletal_finding || 'Not examined',
          head_finding: this.necropsy?.head_finding || 'Not examined',
        })
      }
    },
    methods: {
      save() {
        if (this.canSubmit) {
          this.form.put(this.route('patients.necropsy.systems.update', {
            patient: this.$page.props.admission.patient
          }), {
            preserveScroll: true,
            onError: () => this.stopAutoSave()
          });
        }
      }
    }
  }
</script>
