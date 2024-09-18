<script setup>
import {ref} from 'vue';
import CommonName from '@/Components/FormElements/CommonName.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import includes from 'lodash/includes';

const props = defineProps({
  row: {
    type: Object,
    required: true
  },
  index: {
    type: Number,
    required: true
  }
})

const emit = defineEmits(['updated', 'updatedAll']);

const updating = ref(false);
const success = ref(null);
const message = ref('');
const newCommonName = ref('');

const update = () => {
  updating.value = true;

  window.axios.put(`/internal-api/unrecognized-patients/patient/${props.row.patient_id}`, {
      newCommonName: newCommonName.value
  })
      .then(response => {
          success.value = true;
          message.value = response.data.message;
          updating.value = false;

          setTimeout(() => {
              emit('updated', props.index);
          }, 1500);
      })
      .catch(error => {
          if (error.response.status === 422) {
              success.value = false;
              message.value = error.response.data.message;
              updating.value = false;
          }
      });
};

const updateAll = (account) => {
  window.axios.put(`/internal-api/unrecognized-patients/account/${account.id}`, {
          taxon_id: props.row.patient.taxon_id,
          oldCommonName: props.row.patient.common_name,
          newCommonName: newCommonName.value
      })
      .then(response => {
          success.value = true;
          message.value = response.data.message;
          updating.value = false;

          setTimeout(() => {
              emit('updatedAll', props.index);
          }, 1500);
      })
      .catch(error => {
          if (error.response.status === 422) {
              success.value = false;
              message.value = error.response.data.message;
              updating.value = false;
          }
  });
};

// import UnrecognizedRowMixin from './UnrecognizedRowMixin.js';

// export default {
//     mixins: [UnrecognizedRowMixin],
// };
</script>

<template>
  <tr>
    <!-- <td
      v-if="! onlyOneAccount"
      class="px-2 py-2 text-sm text-gray-600"
    >
      <Link :href="route('accounts.show', row.team.id)">
        {{ row.team.organization }}
      </Link>
    </td>
    <td
      v-if="! onlyOneAccount"
      class="px-2 py-2 text-sm text-gray-600"
    >
      {{ row.team.city }}, {{ row.team.subdivision }}, {{ row.team.country }}
    </td> -->
    <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-600">
      {{ row.case_number }}
    </td>
    <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-600">
      {{ row.patient.common_name }}
    </td>
    <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-600">
      <CommonName v-model="newCommonName" />
      <div
        v-if="message"
        :class="[success ? 'text-green-600' : 'text-red-600']"
        class="py-2"
      >
        {{ message }}
      </div>
    </td>
    <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-600 align-top">
      <div class="flex">
        <PrimaryButton
          class="mr-4"
          @click="update()"
        >
          Update
        </PrimaryButton>
        <!-- <SecondaryButton
          v-if="onlyOneAccount"
          @click="updateAll(row.team)"
        >
          Update All
        </SecondaryButton> -->
      </div>
    </td>
  </tr>
</template>
