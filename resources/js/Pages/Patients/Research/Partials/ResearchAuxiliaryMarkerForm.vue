<script setup>
import Panel from '@/Components/Panel.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import RequiredInput from '@/Components/FormElements/RequiredInput.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
</script>

<script>
  export default {
    mixins: [autoSave, hoistForm],
    props: {
      banding: {
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
          auxiliary_marker: this.banding?.auxiliary_marker,
          auxiliary_marker_color: this.banding?.auxiliary_marker_color,
          auxiliary_side_of_bird: this.banding?.auxiliary_side_of_bird,
          auxiliary_marker_type: this.banding?.auxiliary_marker_type,
          auxiliary_marker_code_color: this.banding?.auxiliary_marker_code_color,
          auxiliary_placement_on_leg: this.banding?.auxiliary_placement_on_leg,
        })
      }
    },
    methods: {
      save() {
        if (this.canSubmit) {
          this.form.put(this.route('patients.research.auxiliary_marker.update', {
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

<template>
  <Panel>
    <template #heading>
      {{ __('Auxiliary Marker') }}
    </template>
    <div class="space-y-4 sm:space-y-2">
      <div class="sm:grid sm:grid-cols-6 sm:gap-2 md:items-center">
        <div class="col-span-3 space-y-2">
          <div class="md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
            <Label
              for="auxiliary_marker"
              class="md:text-right"
            >
              {{ __('Marker Code') }}
            </Label>
            <div class="col-span-2 mt-1 md:mt-0">
              <Input
                v-model="form.auxiliary_marker"
                name="auxiliary_marker"
              />
              <InputError :message="form.errors.auxiliary_marker" />
            </div>
          </div>
          <div class="md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
            <Label
              for="auxiliary_marker_color"
              class="md:text-right"
            >
              {{ __('Marker Color') }}
            </Label>
            <div class="col-span-2 mt-1 md:mt-0">
              <Select
                v-model="form.auxiliary_marker_color"
                name="auxiliary_marker_color"
                :options="$page.props.options.auxillaryColorCodes"
              />
            </div>
          </div>
          <div class="md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
            <Label
              for="auxiliary_side_of_bird"
              class="md:text-right"
            >
              {{ __('Side of Bird') }}
            </Label>
            <div class="col-span-2 mt-1 md:mt-0">
              <Select
                v-model="form.auxiliary_side_of_bird"
                name="auxiliary_side_of_bird"
                :options="$page.props.options.auxiliarySideOfBird"
              />
            </div>
          </div>
        </div>
        <div class="col-span-3 space-y-2">
          <div class="md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
            <Label
              for="auxiliary_marker_type"
              class="md:text-right"
            >
              {{ __('Marker Type') }}
            </Label>
            <div class="col-span-2 mt-1 md:mt-0">
              <Select
                v-model="form.auxiliary_marker_type"
                name="auxiliary_marker_type"
                :options="$page.props.options.auxillaryMarkerTypeCodes"
              />
            </div>
          </div>
          <div class="md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
            <Label
              for="auxiliary_marker_code_color"
              class="md:text-right"
            >
              {{ __('Marker Code Color') }}
            </Label>
            <div class="col-span-2 mt-1 md:mt-0">
              <Select
                v-model="form.auxiliary_marker_code_color"
                name="auxiliary_marker_code_color"
                :options="$page.props.options.auxillaryCodeColors"
              />
            </div>
          </div>
          <div class="md:grid md:grid-cols-3 md:gap-x-2 md:items-center">
            <Label
              for="auxiliary_placement_on_leg"
              class="md:text-right"
            >
              {{ __('Placement on Leg') }}
            </Label>
            <div class="col-span-2 mt-1 md:mt-0">
              <Select
                v-model="form.auxiliary_placement_on_leg"
                name="auxiliary_placement_on_leg"
                :options="$page.props.options.auxiliaryPlacementOnLeg"
              />
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
          {{ __('Update Auxiliary Marker Data') }}
        </PrimaryButton>
      </div>
    </template>
  </Panel>
</template>
