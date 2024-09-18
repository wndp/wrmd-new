<template>
  <div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
      <div class="sm:flex-auto">
        <h1 class="text-xl font-semibold text-gray-900">
          {{ assay }}
        </h1>
      </div>
      <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
        <button
          type="button"
          class="inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:w-auto"
        >
          Export
        </button>
      </div>
    </div>
    <div class="flex flex-col">
      <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
          <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
            <table class="min-w-full divide-y divide-gray-300">
              <thead class="bg-blue-100">
                <tr>
                  <th
                    scope="col"
                    class="whitespace-nowrap py-3.5 pl-4 pr-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider sm:pl-6"
                  >
                    Date
                  </th>
                  <th
                    scope="col"
                    class="whitespace-nowrap px-2 py-3.5 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                  >
                    data...
                  </th>
                  <th
                    scope="col"
                    class="relative whitespace-nowrap py-3.5 pl-3 pr-4 sm:pr-6"
                  >
                    <span class="sr-only">Edit</span>
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 bg-white">
                <tr
                  v-for="transaction in transactions"
                  :key="transaction.id"
                >
                  <td class="whitespace-nowrap py-2 pl-4 pr-3 text-sm text-gray-500 sm:pl-6">
                    {{ transaction.id }}
                  </td>
                  <td class="whitespace-nowrap px-2 py-2 text-sm text-gray-500">
                    ...
                  </td>
                  <td class="relative whitespace-nowrap py-2 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                    <a
                      href="#"
                      class="text-blue-600 hover:text-blue-900"
                    >Edit<span class="sr-only">, {{ transaction.id }}</span></a>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  assay: {
    type: String,
    required: true
  }
});
const transactions = [
  {
    id: 'AAPS0L',
    company: 'Chase & Co.',
    share: 'CAC',
    commission: '+$4.37',
    price: '$3,509.00',
    quantity: '12.00',
    netAmount: '$4,397.00',
  },
  // More transactions...
]
</script>
