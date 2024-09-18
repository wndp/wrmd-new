<template>
    <tr>
        <td v-if="! onlyOneAccount" class="px-2 py-2 text-sm text-gray-600">{{ row.account.organization }}</td>
        <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-600">{{ row.case_number }}</td>
        <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-600">{{ row.patient_id }}</td>
        <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-600">
            <Link :href="route('taxa.edit', row.patient.taxon_id)">{{ row.patient.taxon_id }}</Link>
        </td>
        <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-600">{{ row.patient.common_name }}</td>
        <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-600">
            <CommonName v-model="newCommonName"/>
            <div v-if="message" :class="[success ? 'text-green-600' : 'text-red-600']" class="py-2">{{ message }}</div>
        </td>
        <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-600">
            <div class="flex">
                <PrimaryButton class="mr-4" @click="update()">Update</PrimaryButton>
                <SecondaryButton v-if="onlyOneAccount" @click="updateAll(row.account)">Update All</SecondaryButton>
            </div>
        </td>
    </tr>
</template>

<script>
import UnrecognizedRowMixin from './UnrecognizedRowMixin.js';

export default {
    mixins: [UnrecognizedRowMixin]
};
</script>
