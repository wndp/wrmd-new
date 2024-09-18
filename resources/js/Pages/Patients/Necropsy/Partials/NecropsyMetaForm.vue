<template>
  <Panel>
    <template #heading>
      {{ __('Necropsy') }}
    </template>
    <div class="space-y-4 sm:space-y-2">
      <div class="sm:grid sm:grid-cols-6 sm:gap-2 md:items-center space-y-2">
        <div class="col-span-3">
          <div class="space-y-2">
            <div class="md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
              <Label
                for="necropsied_at"
                class="md:text-right"
              >
                {{ __('Date') }}
                <Required v-if="enforceRequired" />
              </Label>
              <div class="col-span-2 mt-1 md:mt-0">
                <DatePicker
                  id="necropsied_at"
                  v-model="form.necropsied_at"
                  time
                  :required="enforceRequired"
                />
                <InputError :message="form.errors.necropsied_at" />
              </div>
            </div>
            <div class="md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
              <Label
                for="prosector"
                class="md:text-right"
              >
                {{ __('Prosector') }}
                <Required v-if="enforceRequired" />
              </Label>
              <div class="col-span-2 mt-1 md:mt-0">
                <Input
                  v-model="form.prosector"
                  name="prosector"
                  :required="enforceRequired"
                />
                <InputError :message="form.errors.prosector" />
              </div>
            </div>
          </div>
        </div>
        <div class="col-span-3">
          <div class="space-y-2">
            <div class="md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
              <div class="col-start-2 col-span-2 mt-1 md:mt-0">
                <Toggle
                  v-model="form.is_photos_collected"
                  dusk="is_photos_collected"
                >
                  {{ __('Photos Collected?') }}
                </Toggle>
              </div>
            </div>
            <div class="md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
              <div class="col-start-2 col-span-2 mt-1 md:mt-0">
                <Toggle
                  v-model="form.is_carcass_radiographed"
                  dusk="is_carcass_radiographed"
                >
                  {{ __('Radiographed?') }}
                </Toggle>
              </div>
            </div>
          </div>
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
          {{ __('Update Necropsy') }}
        </PrimaryButton>
      </div>
    </template>
  </Panel>
</template>

<script setup>
import Panel from '@/Components/Panel.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import Toggle from '@/Components/FormElements/Toggle.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import Required from '@/Components/FormElements/Required.vue';
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
          necropsied_at: this.necropsy?.necropsied_at,
          prosector: this.necropsy?.prosector,
          is_photos_collected: this.necropsy?.is_photos_collected,
          is_carcass_radiographed: this.necropsy?.is_carcass_radiographed
        })
      }
    },
    methods: {
      save() {
        if (this.canSubmit) {
          this.form.put(this.route('patients.necropsy.update', {
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
