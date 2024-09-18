<template>
    <div>
        <div class="bg-white overflow-hidden shadow rounded-lg divide-y divide-gray-200" v-if="admissions.length === 0">
            <div class="px-4 py-5 sm:px-6" v-if="heading">
                {{ heading }}
            </div>
            <div class="px-4 py-5 sm:p-6 prose max-w-none">
                <h6>There are no misidentified patients.</h6>
            </div>
        </div>
        <div v-else class="flex flex-col">
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-blue-100">
                <tr>
                    <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider" v-if="! hasAccount">Account</th>
                    <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Case #</th>
                    <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Patient ID</th>
                    <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Taxon ID</th>
                    <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Current Common Name</th>
                    <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">New Common Name</th>
                    <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr
                    is="vue:MisidentifiedPatientsRow"
                    v-for="(admission, index) in admissions"
                    :row="admission"
                    :index="index"
                    :key="admission.patient_id"
                    @updated="deleteRow"
                    @updatedAll="deleteRows"
                >
                </tr>
            </tbody>
        </table>
        </div></div></div></div>
    </div>
</template>

<script>
import UnrecognizedMixin from './UnrecognizedMixin.js';
import MisidentifiedPatientsRow from './MisidentifiedPatientsRow.vue';

export default {
    mixins: [UnrecognizedMixin],
    components: {
        MisidentifiedPatientsRow
    },
};
</script>
