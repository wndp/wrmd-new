<template>
  <AppLayout title="Testimonials">
    <div class="mt-8 sm:flex sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-semibold text-gray-900">
          Testimonials
        </h1>
        <h4 class="mt-1 text-sm text-gray-500">
          Total: {{ testimonials.total }}
        </h4>
      </div>
      <div class="mt-3 sm:mt-0 sm:ml-4">
        <label
          for="mobile-search-candidate"
          class="sr-only"
        >Search</label>
        <label
          for="desktop-search-candidate"
          class="sr-only"
        >Search</label>
        <div class="flex rounded-md shadow-sm">
          <div class="relative flex-grow focus-within:z-10">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <MagnifyingGlassIcon
                class="h-5 w-5 text-gray-400"
                aria-hidden="true"
              />
            </div>
            <input
              id="mobile-search-candidate"
              v-model="form.search"
              type="text"
              name="mobile-testimonial-search"
              class="focus:ring-blue-500 focus:border-blue-500 block w-full rounded-none rounded-l-md pl-10 sm:hidden border-gray-300"
              placeholder="Search"
              @keyup="search"
            >
            <input
              id="desktop-search-candidate"
              v-model="form.search"
              type="text"
              name="desktop-testimonial-search"
              class="hidden focus:ring-blue-500 focus:border-blue-500 w-full rounded-none rounded-l-md pl-10 sm:block sm:text-sm border-gray-300"
              placeholder="Search testimonials..."
              @keyup="search"
            >
          </div>
          <PrimaryButton
            class="rounded-l-none"
            @click="$inertia.get(route('admin.testimonials.create'))"
          >
            <PlusIcon
              class="h-5 w-5"
              aria-hidden="true"
            />
            <span class="ml-2 whitespace-nowrap">New Testimonial</span>
          </PrimaryButton>
        </div>
      </div>
    </div>
    <div class="flex flex-col mt-8">
      <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
          <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-blue-100">
                <tr>
                  <th
                    scope="col"
                    class="px-5 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                  />
                  <th
                    scope="col"
                    class="px-2 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                  >
                    Name
                  </th>
                  <th
                    scope="col"
                    class="px-2 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                  >
                    Text
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr
                  v-for="(testimonial, i) in testimonials.data"
                  :key="testimonial.id"
                  :class="i % 2 === 0 ? 'bg-white' : 'bg-gray-50'"
                >
                  <td class="px-5 py-4 whitespace-nowrap">
                    <Link
                      :href="route('admin.testimonials.edit', testimonial)"
                      class="text-base leading-5 text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700 transition ease-in-out duration-150 truncate"
                    >
                      Edit Testimonial
                    </Link>
                  </td>
                  <td class="px-2 py-4 text-sm text-gray-900 align-top whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">
                      {{ testimonial.name }}
                    </div>
                    <div class="text-sm text-gray-500">
                      {{ testimonial.account.organization }}
                    </div>
                  </td>
                  <td class="px-2 py-4 text-sm text-gray-900">
                    <p class="text-sm text-gray-600 line-clamp-2">
                      {{ testimonial.text }}
                    </p>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <Paginator :properties="testimonials" />
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Paginator from '@/Components/Paginator.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import { MagnifyingGlassIcon, PlusIcon } from '@heroicons/vue/24/outline';
import debounce from 'lodash/debounce';
import URI from 'urijs';

defineProps({
    testimonials: {
        type: Object,
        required: true
    }
})
</script>

<script>
export default {
    data() {
        return {
            form: this.$inertia.form({
                search: new URI().query(true).search
            })
        };
    },
    methods: {
      search() {
        this.debounceSearch();
      },
      debounceSearch: debounce(function() {
          if (this.form.search === '') {
              this.$inertia.get(this.route(this.route().current()));
          } else {
              this.form.get(this.route('admin.testimonials.index'));
          }
      }, 500)
    }
};
</script>
