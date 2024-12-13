<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import {CheckIcon} from '@heroicons/vue/20/solid'

const tiers = [
  {
    name: 'Standard',
    id: 'tier-standard',
    href: '#',
    priceMonthly: '$0',
    description: 'Yes actually free. One of our mottoes is "Keep it Free." Not only this service to our community but the animals we dedicate our lives to.',
    features: [
      'Unlimited users',
      'Daily tasks',
      'Agency annual reports',
      'Basic analytics',
      '48-hour support response time',
      'and so much more!'
    ],
  },
  {
    name: 'Pro',
    id: 'tier-pro',
    href: '#',
    priceMonthly: '$250',
    description: 'Unlock WRMD\'s full potential with Pro features, advanced tools and priority support to help you work smarter and achieve more.',
    features: [
      'All the standard features',
      'File Uploads',
      'Patient Batch Updating',
      'Custom Classification Tags',
      'Custom Fields',
      'Sub Accounts',
    ],
  },
];
</script>

<template>
  <PublicLayout title="Pricing">
    <div class="py-16 bg-gray-50 overflow-hidden lg:py-24">
      <div class="relative max-w-xl mx-auto px-4 sm:px-6 lg:px-8 lg:max-w-7xl">
        <div class="relative">
          <h1 class="text-center text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
            Simple no-tricks pricing
          </h1>
          <p class="mt-4 max-w-3xl mx-auto text-center text-xl text-gray-500">
            No matter how well established your organization is, we are sure that your funds are limited and as wildlife rehabilitators ourselves we know that paying for food is a priority. Our Pricing is simple. The free plan includes all the essential features you need, while the pro plan offers additional advanced tools and priority support, which require extra resources for us to maintain.
          </p>
        </div>
      </div>
      <div class="mt-12 mx-auto grid max-w-md grid-cols-1 gap-8 lg:max-w-4xl lg:grid-cols-2">
        <div
          v-for="tier in tiers"
          :key="tier.id"
          class="flex flex-col justify-between rounded-lg bg-white p-8 shadow-lg sm:p-10"
        >
          <div>
            <h3
              :id="tier.id"
              class="text-lg font-semibold text-blue-600"
            >
              {{ tier.name }}
            </h3>
            <div class="mt-4 flex items-baseline gap-x-2">
              <span class="text-5xl font-semibold tracking-tight text-gray-900">{{ tier.priceMonthly }}</span>
              <span class="text-base/7 font-semibold text-gray-600">/year</span>
            </div>
            <p class="mt-6 text-base/7 text-gray-600">
              {{ tier.description }}
            </p>
            <ul
              role="list"
              class="mt-10 space-y-4 text-sm/6 text-gray-600"
            >
              <li
                v-for="feature in tier.features"
                :key="feature"
                class="flex gap-x-3"
              >
                <CheckIcon
                  class="h-6 w-5 flex-none text-blue-600"
                  aria-hidden="true"
                />
                {{ feature }}
              </li>
            </ul>
          </div>
          <Link
            :href="route('register')"
            :aria-describedby="tier.id"
            class="mt-8 block rounded-md bg-green-600 px-3.5 py-2 text-center text-sm/6 font-semibold text-white uppercase shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600"
          >
            Get started today
          </Link>
        </div>
        <div class="bg-white flex flex-col items-start gap-x-8 gap-y-6 rounded-lg p-8 ring-1 ring-gray-900/10 sm:gap-y-10 sm:p-10 lg:col-span-2 lg:flex-row lg:items-center">
          <div class="lg:min-w-0 lg:flex-1">
            <h3 class="text-base/7 font-semibold text-blue-600">
              Wildlife Rehabilitation MD (WRMD) Community Scholarship
            </h3>
            <p class="mt-1 text-base/7 text-gray-600">
              We understand that some organizations may face budget constraints, which is why we offer a scholarship subscription for those who need access to our Pro features but cannot afford the full cost. This special program provides qualified organizations with free or discounted access to advanced tools, helping them maximize their impact without the financial burden. To apply, simply reach out and let us know how our platform can support your mission.
            </p>
          </div>
          <div class="w-full lg:w-auto">
            <img
              class="object-cover h-48 w-full"
              src="https://picsum.photos/id/237/200/300"
            >
            <Link
              :href="route('contact.create')"
              class="mt-4 flex items-center justify-center px-4 py-2 bg-green-300 border border-transparent rounded-md font-semibold text-xs text-green-800 uppercase tracking-widest hover:bg-green-500 active:bg-green-500 focus:outline-none focus:border-green-500 focus:ring focus:ring-green-300 transition"
            >
              Apply for the WRMD Scholarship
            </Link>
          </div>
        </div>
      </div>
      <div class="mt-12 prose prose-lg prose-blue text-gray-500 mx-auto max-w-4xl">
        <h4>In Exchange For Wildlife Rehabilitation MD (WRMD) We Have A Few Requests</h4>
        <ol>
          <li>
            Share Wildlife Rehabilitation MD (WRMD) with as many wildlife rehabilitation organizations as you can. The more broad our reach, the better we will grow.
            <div class="flex justify-between mt-2">
              <a
                class="not-prose no-underline text-center rounded-lg flex items-center justify-center w-36"
                style="background-color: #1877f2; color: #ffffff"
                target="_blank"
                href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fwww.wrmd.org%2Fdonate"
              >
                <svg
                  fill="currentColor"
                  viewBox="0 0 24 24"
                  class="h-6 w-6 mr-3"
                  aria-hidden="true"
                ><path
                  fill-rule="evenodd"
                  d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"
                  clip-rule="evenodd"
                /></svg>
                Facebook
              </a>
              <a
                class="not-prose no-underline text-center rounded-lg flex items-center justify-center text-white w-36 mx-2"
                style="background-color: #1c8eef"
                target="_blank"
                href="https://twitter.com/intent/tweet/?url=https%3A%2F%2Fwww.wrmd.org%2Fdonate&amp;text=I support Wildlife Rehabilitation MD (WRMD) because..."
              >
                <svg
                  fill="currentColor"
                  viewBox="0 0 24 24"
                  class="h-6 w-6 mr-3"
                  aria-hidden="true"
                ><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" /></svg>
                Twitter
              </a>
              <a
                class="not-prose no-underline text-center rounded-lg flex items-center justify-center text-white w-36"
                style="background-color: #046293"
                target="_blank"
                href="https://www.linkedin.com/shareArticle?mini=true&amp;url=https%3A%2F%2Fwww.wrmd.org%2Fdonate"
              >
                <svg
                  class="h-6 w-6 mr-3"
                  fill="currentColor"
                  viewBox="0 0 24 24"
                  aria-hidden="true"
                >
                  <path
                    fill-rule="evenodd"
                    d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z"
                    clip-rule="evenodd"
                  />
                </svg>
                LinkedIn
              </a>
            </div>
          </li>
          <li>
            <Link :href="route('contact.create')">
              Give us your feedback
            </Link>. We would love to hear from you and learn about how Wildlife Rehabilitation MD (WRMD) has benefited you or maybe you have a great feature request that we can add in.
          </li>
          <li>
            <Link :href="route('donate.index')">
              Make a donation
            </Link>. <strong>Yes this service is free to you but it's not free to us.</strong> This database is entirely community driven and each year we must collectively support its growth. Any donation you are able to make will help to ensure our longevity and support other rehabilitators across the world.
          </li>
        </ol>
      </div>
    </div>
  </PublicLayout>
</template>
