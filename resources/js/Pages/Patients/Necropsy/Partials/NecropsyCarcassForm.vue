<template>
  <Panel>
    <template #heading>
      {{ __('Carcass') }}
    </template>
    <div class="space-y-4 sm:space-y-2">
      <div class="sm:grid sm:grid-cols-6 sm:gap-2 md:items-center space-y-2">
        <div class="col-span-3">
          <div class="space-y-2">
            <div class="md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
              <Label
                for="carcass_condition"
                class="md:text-right"
              >
                {{ __('Condition') }}
              </Label>
              <div class="col-span-2 mt-1 md:mt-0">
                <Select
                  v-model="form.carcass_condition"
                  name="carcass_condition"
                  :options="$page.props.options.carcassConditions"
                />
              </div>
            </div>
            <div class="md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
              <div class="col-start-2 col-span-2 mt-1 md:mt-0">
                <Toggle
                  v-model="form.is_previously_frozen"
                  dusk="is_previously_frozen"
                >
                  {{ __('Frozen?') }}
                </Toggle>
              </div>
            </div>
          </div>
        </div>
        <div class="col-span-3">
          <div class="space-y-2">
            <div class="md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
              <div class="col-start-2 col-span-2 mt-1 md:mt-0">
                <Toggle
                  v-model="form.is_scavenged"
                  dusk="is_scavenged"
                >
                  {{ __('Scavenged?') }}
                </Toggle>
              </div>
            </div>
            <div class="md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
              <div class="col-start-2 col-span-2 mt-1 md:mt-0">
                <Toggle
                  v-model="form.is_discarded"
                  dusk="is_discarded"
                >
                  {{ __('Discarded After Necropsy?') }}
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
          {{ __('Update Carcass Details') }}
        </PrimaryButton>
      </div>
    </template>
  </Panel>
</template>

<script setup>
import Panel from '@/Components/Panel.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import Toggle from '@/Components/FormElements/Toggle.vue';
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
          carcass_condition: this.necropsy?.carcass_condition,
          is_previously_frozen: this.necropsy?.is_previously_frozen,
          is_scavenged: this.necropsy?.is_scavenged,
          is_discarded: this.necropsy?.is_discarded
        })
      }
    },
    methods: {
      save() {
        if (this.canSubmit) {
          this.form.put(this.route('patients.necropsy.carcass.update', {
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
