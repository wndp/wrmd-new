<script setup>
import {computed} from 'vue';
import {useForm, usePage} from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import {ChevronRightIcon} from '@heroicons/vue/24/solid';
import {CheckIcon, ArrowRightOnRectangleIcon, HomeIcon} from '@heroicons/vue/24/outline';
import Checkbox from '@/Components/FormElements/Checkbox.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import DonateHeader from '@/Layouts/Partials/DonateHeader.vue';
import AnalyticNumber from '@/Components/Analytics/AnalyticNumber.vue';
import SparkLine from '@/Components/Analytics/SparkLine.vue';
import {__} from '@/Composables/Translate';

defineProps({
  whatsNew: Object,
  organizations: Number,
  countries: Number,
  usStates: Number,
  testimonials: Array,
  avatars: Array,
  analyticFiltersForThisYear: Object
});

const features = [
  { name: 'Annual Reports', description: 'Produce your annual reports at the push of a button.' },
  { name: 'Attachments', description: 'Upload images and pdfs to your patient.' },
  { name: 'Classification Tagging', description: 'Tag your patients with standardized terminology.' },
  { name: 'Prescriptions', description: 'Write detailed prescriptions for your patients.' },
  { name: 'Hotline', description: 'Log your communications with the public.' },
  { name: 'Batch Update', description: 'Update multiple patients all at the same time.' },
];

const loginForm = useForm({
  email: '',
  password: '',
  remember: false
});

const showDonateHeader = computed(() => usePage().props.showDonateHeader || false);

const loginUser = () => {
    loginForm.post(route('login'), {
        onFinish: () => loginForm.reset('password'),
    }, false);
};
</script>

<template>
  <PublicLayout title="Welcome">
    <div class="lg:grid lg:grid-cols-12 lg:gap-8">
      <DonateHeader
        v-if="showDonateHeader"
        class="col-start-2 col-span-10 mb-8"
      />
      <div class="sm:px-6 sm:text-center md:max-w-2xl md:mx-auto lg:col-span-7 lg:text-left lg:flex lg:items-center">
        <div>
          <h1 class="text-3xl md:text-4xl tracking-tight text-green-500 sm:mt-5 sm:leading-none lg:mt-6 lg:text-5xl">
            <span class="sr-only">Wildlife Rehabilitation MD</span>
            <div class="whitespace-nowrap">
              Wildlife Rehabilitation <span class="text-2xl uppercase">MD</span>
            </div>
            <div class="uppercase text-3xl">
              WRMD
            </div>
          </h1>
          <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-xl lg:text-lg xl:text-xl">
            A <strong class="font-bold">free</strong> on-line medical database designed specifically <strong class="font-bold">for wildlife rehabilitators</strong> to collect, manage and analyze data for our patients.
          </p>
          <template v-if="whatsNew">
            <a
              :href="whatsNew.url"
              target="_blank"
              class="mt-8 inline-flex items-center text-gray-500 bg-gray-200 rounded-full p-1 pr-2 sm:text-base lg:text-sm xl:text-base hover:text-gray-600"
            >
              <span class="px-3 py-0.5 text-white text-xs font-semibold leading-5 uppercase tracking-wide bg-blue-500 rounded-full">What's new</span>
              <span class="ml-4 text-xs">{{ whatsNew.title }}</span>
              <ChevronRightIcon
                class="ml-2 w-5 h-5 text-gray-500"
                aria-hidden="true"
              />
            </a>
          </template>
        </div>
      </div>
      <div class="mt-16 sm:mt-24 lg:mt-0 lg:col-span-5">
        <div
          v-if="$page.props.auth.user"
          class="grid grid-cols-2 gap-4"
        >
          <AnalyticNumber
            id="patients-in-care"
            title="Patients in Care"
          />
          <AnalyticNumber
            id="species-admitted"
            title="Species This Year"
            :urlParams="analyticFiltersForThisYear"
          />
          <SparkLine
            id="patients-admitted"
            title="Patients This Year"
            :urlParams="analyticFiltersForThisYear"
            class="col-span-2"
          />
          <div class="col-span-2 bg-white px-4 py-3 sm:rounded-lg sm:overflow-hidden shadow">
            <p class="py-3 truncate">
              <span class="block mb-0.5 text-xs text-gray-500">{{ __('Signed in as') }}</span>
              <span class="font-semibold">{{ $page.props.auth.user.email }}</span>
            </p>
            <div class="flex justify-between items-center">
              <Link
                :href="route('logout')"
                method="post"
                as="button"
                class="inline-flex items-center text-gray-400"
              >
                <ArrowRightOnRectangleIcon
                  class="w-5 h-5 mr-2"
                  aria-hidden="true"
                />
                {{ __('Sign Out') }}
              </Link>
              <Link
                :href="route('dashboard')"
                as="button"
                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring focus:ring-green-300 disabled:bg-green-400 transition"
              >
                <HomeIcon
                  class="w-5 h-5 mr-2"
                  aria-hidden="true"
                />
                {{ __('Go To the Dashboard') }}
              </Link>
            </div>
          </div>
        </div>
        <div
          v-else
          class="bg-white sm:max-w-md sm:w-full sm:mx-auto sm:rounded-lg sm:overflow-hidden"
        >
          <div>
            <div class="px-4 py-8 sm:px-10">
              <form
                action="#"
                method="POST"
                class="space-y-6"
                @submit.prevent="loginUser"
              >
                <div>
                  <label
                    for="email"
                    class="sr-only"
                  >{{ __('Email address') }}</label>
                  <input
                    id="email"
                    v-model="loginForm.email"
                    type="email"
                    name="email"
                    autocomplete="email"
                    :placeholder="__('Email address')"
                    required=""
                    class="block w-full shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm border-gray-300 rounded-md"
                  >
                  <InputError :message="loginForm.errors.email" class="mt-1" />
                </div>
                <div>
                  <label
                    for="password"
                    class="sr-only"
                  >{{ __('Password') }}</label>
                  <input
                    id="password"
                    v-model="loginForm.password"
                    name="password"
                    type="password"
                    :placeholder="__('Password')"
                    autocomplete="current-password"
                    required=""
                    class="block w-full shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm border-gray-300 rounded-md"
                  >
                  <InputError :message="loginForm.errors.email" class="mt-1" />
                </div>
                <div class="flex justify-between">
                  <div class="flex items-start mr-4">
                    <div class="flex items-center h-5">
                      <Checkbox
                        id="remember"
                        v-model="loginForm.remember"
                      />
                    </div>
                    <div class="ml-3 text-sm">
                      <label
                        for="remember"
                        class="text-sm text-gray-600"
                      >{{ __('Remember me') }}</label>
                    </div>
                  </div>
                  <Link
                    :href="route('password.request')"
                    class="underline text-sm text-gray-600 hover:text-gray-900"
                  >
                    {{ __('Forgot your password?') }}
                  </Link>
                </div>
                <div>
                  <button
                    type="submit"
                    class="w-full inline-flex justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring focus:ring-green-300 disabled:bg-green-400 transition"
                  >
                    {{ __('Sign In') }}
                  </button>
                </div>
              </form>
            </div>
            <div class="px-4 py-6 bg-gray-50 border-t-2 border-gray-200 sm:px-10">
              <h4 class="text-sm font-bold text-gray-500 tracking-wider uppercase">
                {{ __('Need An Account?') }}
              </h4>
              <p class="text-sm font-normal text-gray-500 mt-2">
                {{ __('Before signing into Wildlife Rehabilitation MD, your organization first needs to create an account.') }}
              </p>
              <Link
                :href="route('register')"
                class="block text-sm font-medium text-blue-600 hover:text-blue-700 mt-4"
              >
                {{ __('Register Your Organization') }}
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
    <section class="bg-white mt-12 lg:mt-20">
      <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8 lg:grid lg:grid-cols-3 lg:gap-x-8">
        <div>
          <h2 class="text-base font-semibold text-green-600 uppercase tracking-wide">
            Everything you need
          </h2>
          <p class="mt-2 text-3xl font-extrabold text-gray-900">
            All-in-one platform
          </p>
          <p class="mt-4 text-base text-gray-500">
            WRMD works for all wildlife rehabilitators. You decide which features you want to make available.
            <Link
              :href="route('about.features')"
              class="text-blue-600 hover:text-blue-700"
            >
              Learn about WRMD's features.
            </Link>
          </p>
        </div>
        <div class="mt-8 lg:mt-0 lg:col-span-2">
          <dl class="space-y-10 sm:space-y-0 sm:grid sm:grid-cols-3 sm:gap-4">
            <div
              v-for="feature in features"
              :key="feature.name"
            >
              <dt>
                <CheckIcon
                  class="absolute h-6 w-6 text-green-500"
                  aria-hidden="true"
                />
                <p class="ml-9 text-lg leading-6 font-medium text-gray-900">
                  {{ feature.name }}
                </p>
              </dt>
              <dd class="mt-2 ml-9 text-base text-gray-500">
                {{ feature.description }}
              </dd>
            </div>
          </dl>
        </div>
      </div>
    </section>
    <section class="bg-green-600 mt-12 lg:mt-20">
      <div class="max-w-7xl mx-auto py-12 px-4 sm:py-16 sm:px-6 lg:px-8 lg:py-20">
        <div class="max-w-4xl mx-auto text-center">
          <h2 class="text-3xl font-extrabold text-white sm:text-4xl">
            Trusted by Wildlife Rehabilitators Around the Planet
          </h2>
          <p class="mt-3 text-xl text-green-200 sm:mt-4">
            We are honored that so many trust WRMD to help manage the care of their patients!
          </p>
        </div>
        <dl class="mt-10 text-center sm:max-w-3xl sm:mx-auto sm:grid sm:grid-cols-3 sm:gap-8">
          <div class="flex flex-col">
            <dt class="order-2 mt-2 text-lg leading-6 font-medium text-green-200">
              Organizations
            </dt>
            <dd class="order-1 text-5xl font-extrabold text-white">
              {{ organizations }}
            </dd>
          </div>
          <div class="flex flex-col mt-10 sm:mt-0">
            <dt class="order-2 mt-2 text-lg leading-6 font-medium text-green-200">
              US States
            </dt>
            <dd class="order-1 text-5xl font-extrabold text-white">
              {{ usStates }}
            </dd>
          </div>
          <div class="flex flex-col mt-10 sm:mt-0">
            <dt class="order-2 mt-2 text-lg leading-6 font-medium text-green-200">
              Countries
            </dt>
            <dd class="order-1 text-5xl font-extrabold text-white">
              {{ countries }}
            </dd>
          </div>
        </dl>
      </div>
    </section>
    <section class="mt-12">
      <div class="max-w-7xl mx-auto md:grid md:grid-cols-2 md:px-6 lg:px-8">
        <div class="py-12 px-4 sm:px-6 md:flex md:flex-col md:py-16 md:pl-0 md:pr-10 md:border-r md:border-gray-300 lg:pr-16">
          <blockquote class="mt-6 md:flex-grow md:flex md:flex-col">
            <div class="relative text-lg font-normal leading-8 text-gray-500 md:flex-grow">
              <svg
                class="absolute top-0 left-0 transform -translate-x-3 -translate-y-2 h-8 w-8 text-green-300"
                fill="currentColor"
                viewBox="0 0 32 32"
                aria-hidden="true"
              >
                <path d="M9.352 4C4.456 7.456 1 13.12 1 19.36c0 5.088 3.072 8.064 6.624 8.064 3.36 0 5.856-2.688 5.856-5.856 0-3.168-2.208-5.472-5.088-5.472-.576 0-1.344.096-1.536.192.48-3.264 3.552-7.104 6.624-9.024L9.352 4zm16.512 0c-4.8 3.456-8.256 9.12-8.256 15.36 0 5.088 3.072 8.064 6.624 8.064 3.264 0 5.856-2.688 5.856-5.856 0-3.168-2.304-5.472-5.184-5.472-.576 0-1.248.096-1.44.192.48-3.264 3.456-7.104 6.528-9.024L25.864 4z" />
              </svg>
              <p class="relative top-4 whitespace-pre-wrap">
                {{ testimonials[0].text }}
              </p>
            </div>
            <footer class="mt-8">
              <div class="flex items-start">
                <div class="flex-shrink-0 inline-flex rounded-full border-2 border-white">
                  <img
                    class="h-12 w-12 rounded-full"
                    :src="testimonials[0].team.profile_photo_url"
                    alt="Logo"
                  >
                </div>
                <div class="ml-4">
                  <div class="text-base font-medium text-gray-400">
                    {{ testimonials[0].name }}
                  </div>
                  <div class="text-base font-medium text-gray-600">
                    {{ testimonials[0].team.name }}
                    <br>{{ testimonials[0].team.locale }}
                  </div>
                </div>
              </div>
            </footer>
          </blockquote>
        </div>
        <div class="py-12 px-4 border-t-2 border-gray-300 sm:px-6 md:py-16 md:pr-0 md:pl-10 md:border-t-0 md:border-l lg:pl-16">
          <blockquote class="mt-6 md:flex-grow md:flex md:flex-col">
            <div class="relative text-lg font-normal leading-8 text-gray-500 md:flex-grow">
              <svg
                class="absolute top-0 left-0 transform -translate-x-3 -translate-y-2 h-8 w-8 text-green-300"
                fill="currentColor"
                viewBox="0 0 32 32"
              >
                <path d="M9.352 4C4.456 7.456 1 13.12 1 19.36c0 5.088 3.072 8.064 6.624 8.064 3.36 0 5.856-2.688 5.856-5.856 0-3.168-2.208-5.472-5.088-5.472-.576 0-1.344.096-1.536.192.48-3.264 3.552-7.104 6.624-9.024L9.352 4zm16.512 0c-4.8 3.456-8.256 9.12-8.256 15.36 0 5.088 3.072 8.064 6.624 8.064 3.264 0 5.856-2.688 5.856-5.856 0-3.168-2.304-5.472-5.184-5.472-.576 0-1.248.096-1.44.192.48-3.264 3.456-7.104 6.528-9.024L25.864 4z" />
              </svg>
              <p class="relative top-4 whitespace-pre-wrap">
                {{ testimonials[1].text }}
              </p>
            </div>
            <footer class="mt-8">
              <div class="flex items-start">
                <div class="flex-shrink-0 inline-flex rounded-full border-2 border-white">
                  <img
                    class="h-12 w-12 rounded-full"
                    :src="testimonials[1].team.profile_photo_url"
                    alt="Logo"
                  >
                </div>
                <div class="ml-4">
                  <div class="text-base font-medium text-gray-400">
                    {{ testimonials[1].name }}
                  </div>
                  <div class="text-base font-medium text-gray-600">
                    {{ testimonials[1].team.name }}
                    <br>{{ testimonials[1].team.locale }}
                  </div>
                </div>
              </div>
            </footer>
          </blockquote>
        </div>
      </div>
      <Link
        :href="route('about.testimonials')"
        class="text-blue-600 hover:text-blue-700"
      >
        Read more testimonials from our users.
      </Link>
    </section>
    <!-- Account Logo Cloud -->
    <div class="bg-white mt-12">
      <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8">
        <div class="lg:grid lg:grid-cols-2 lg:gap-8 lg:items-center">
          <div>
            <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
              Used Internationally by All Wildlife Professionals
            </h2>
            <p class="mt-3 max-w-3xl text-lg text-gray-500">
              The WRMD community includes, wildlife rehabilitators, oiled wildlife responders, universities, government agencies and a wide range of NGOs.
            </p>
            <div class="mt-8 sm:flex">
              <div class="rounded-md shadow">
                <Link
                  :href="route('register')"
                  class="flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring focus:ring-green-300 transition"
                >
                  {{ __('Create Account') }}
                </Link>
              </div>
              <div class="mt-3 sm:mt-0 sm:ml-3">
                <Link
                  :href="route('contact.create')"
                  class="flex items-center justify-center px-4 py-2 bg-green-300 border border-transparent rounded-md font-semibold text-xs text-green-800 uppercase tracking-widest hover:bg-green-500 active:bg-green-500 focus:outline-none focus:border-green-500 focus:ring focus:ring-green-300 transition"
                >
                  {{ __('Contact Us') }}
                </Link>
              </div>
            </div>
          </div>
          <div class="mt-8 grid grid-cols-2 gap-0.5 md:grid-cols-3 lg:mt-0 lg:grid-cols-2">
            <div
              v-for="account in avatars"
              :key="account.id"
              class="col-span-1 flex justify-center py-8 px-8 bg-gray-50"
            >
              <a
                :href="account.website"
                target="_blank"
              >
                <img
                  class="max-h-16"
                  :src="account.profile_photo_url"
                  :alt="account.organization"
                  :title="account.organization"
                >
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Agency Logo Cloud -->
    <section class="mt-12">
      <div class="w-full md:w-2/4 lg:w-1/3">
        <p class="mb-8 text-sm text-gray-500 uppercase tracking-wide font-semibold sm:mb-6">
          WRMD proudly supports the standards of
        </p>
        <div class="flex flex-wra justify-center sm:justify-between gap-12">
          <div class="flex justify-center">
            <a
              href="https://www.fws.gov/"
              target="_blank"
            >
              <img
                class="h-12"
                src="../../images/usfws.png"
                alt="US Fish and Wildlife Service"
              >
            </a>
          </div>
          <div class="flex justify-center">
            <a
              href="https://wildlife.ca.gov/Conservation/Laboratories/Wildlife-Health/Rehab"
              target="_blank"
            >
              <img
                class="h-12"
                src="../../images/cadfw.png"
                alt="California Department of Fish and Wildlife"
              >
            </a>
          </div>
          <div class="flex justify-center">
            <a
              href="https://www.nwrawildlife.org/"
              target="_blank"
            >
              <img
                class="h-12"
                src="../../images/nwra.jpg"
                alt="National Wildlife Rehabilitators Association"
              >
            </a>
          </div>
          <div class="flex justify-center">
            <a
              href="https://theiwrc.org/"
              target="_blank"
            >
              <img
                class="h-12"
                src="../../images/iwrc.jpg"
                alt="International Wildlife Rehabilitation Council"
              >
            </a>
          </div>
        </div>
      </div>
    </section>
  </PublicLayout>
</template>
