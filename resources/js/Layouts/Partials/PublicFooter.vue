<script setup>
import { defineComponent, h } from 'vue';
import format from 'date-fns/format';

const social = [
  {
    name: 'Facebook',
    href: 'https://www.facebook.com/WildlifeRehabilitationMD',
    icon: defineComponent({
      render: () =>
        h('svg', { fill: 'currentColor', viewBox: '0 0 24 24' }, [
          h('path', {
            'fill-rule': 'evenodd',
            d:
              'M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z',
            'clip-rule': 'evenodd',
          }),
        ]),
    }),
  },
  {
    name: 'Twitter',
    href: 'https://www.twitter.com/WildlifeRehabMD',
    icon: defineComponent({
      render: () =>
        h('svg', { fill: 'currentColor', viewBox: '0 0 24 24' }, [
          h('path', {
            d:
              'M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84',
          }),
        ]),
    }),
  },
  {
    name: 'Youtube',
    href: 'https://www.youtube.com/user/WildlifeRehabMD',
    icon: defineComponent({
      render: () =>
        h('svg', { fill: 'currentColor', viewBox: '0 0 24 24' }, [
          h('path', {
            'fill-rule': 'evenodd',
            d:
              'M12 2.76c-11.794 0-12 1.049-12 9.24s0.206 9.24 12 9.24 12-1.049 12-9.24-0.206-9.24-12-9.24zM15.846 12.401l-5.388 2.515c-0.472 0.218-0.858-0.026-0.858-0.547v-4.738c0-0.52 0.386-0.766 0.858-0.547l5.388 2.515c0.472 0.221 0.472 0.581 0 0.802z',
            'clip-rule': 'evenodd',
          }),
        ]),
    }),
  },
];

const year = format(new Date(), 'yyyy');
</script>

<template>
  <footer
    class="bg-gray-800"
    aria-labelledby="footer-heading"
  >
    <h2
      id="footer-heading"
      class="sr-only"
    >
      Footer
    </h2>
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8">
      <div class="xl:grid xl:grid-cols-3 xl:gap-8">
        <div class="space-y-8 xl:col-span-1">
          <img
            class="h-10 w-10 ring-2 ring-gray-100 bg-gray-100 rounded"
            src="../../../images/logo-48x48.png"
            :alt="$page.props.appName"
          >
          <p class="text-gray-300 text-base">
            Wildlife patient data management; simplified.
          </p>
          <div class="flex space-x-6">
            <a
              v-for="item in social"
              :key="item.name"
              :href="item.href"
              class="text-gray-400 hover:text-gray-300"
            >
              <span class="sr-only">{{ item.name }}</span>
              <component
                :is="item.icon"
                class="h-6 w-6"
                aria-hidden="true"
              />
            </a>
          </div>
        </div>
        <div class="mt-12 grid grid-cols-2 gap-8 xl:mt-0 xl:col-span-2">
          <div class="md:grid md:grid-cols-2 md:gap-8">
            <div>
              <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">
                Solutions
              </h3>
              <ul
                role="list"
                class="mt-4 space-y-4"
              >
                <li>
                  <Link
                    :href="route('about.security')"
                    class="text-base text-gray-300 hover:text-gray-100"
                  >
                    Security and Data Integrity
                  </Link>
                </li>
                <li>
                  <Link
                    :href="route('about.agencies')"
                    class="text-base text-gray-300 hover:text-gray-100"
                  >
                    Regulatory Agencies
                  </Link>
                </li>
                <li>
                  <Link
                    :href="route('about.oil-spills')"
                    class="text-base text-gray-300 hover:text-gray-100"
                  >
                    Oil Spills
                  </Link>
                </li>
                <li>
                  <Link
                    :href="route('about.wildalert')"
                    class="text-base text-gray-300 hover:text-gray-100"
                  >
                    WildAlert
                  </Link>
                </li>
                <!-- <li>
                  <Link
                    :href="route('about.support-us')"
                    class="text-base text-gray-300 hover:text-gray-100"
                  >
                    Support Us
                  </Link>
                </li> -->
              </ul>
            </div>
            <div class="mt-12 md:mt-0">
              <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">
                Support
              </h3>
              <ul
                role="list"
                class="mt-4 space-y-4"
              >
                <li>
                  <Link
                    :href="route('about.importing')"
                    class="text-base text-gray-300 hover:text-gray-100"
                  >
                    Importing Patients
                  </Link>
                </li>
                <li>
                  <a
                    href="https://wrmd.helpscoutdocs.com/collection/11-wrmd"
                    class="text-base text-gray-300 hover:text-gray-100"
                    target="_blank"
                  >
                    Knowledge Base
                  </a>
                </li>
                <li>
                  <a
                    :href="route('apiv3.index')"
                    class="text-base text-gray-300 hover:text-gray-100"
                  >
                    Developer API
                  </a>
                </li>
                <li>
                  <Link
                    :href="route('contact.create')"
                    class="text-base text-gray-300 hover:text-gray-100"
                  >
                    Contact Us
                  </Link>
                </li>
              </ul>
            </div>
          </div>
          <div class="md:grid md:grid-cols-2 md:gap-8">
            <div>
              <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">
                Company
              </h3>
              <ul
                role="list"
                class="mt-4 space-y-4"
              >
                <li>
                  <Link
                    :href="route('about.wndp')"
                    class="text-base text-gray-300 hover:text-gray-100"
                  >
                    The Wild Neighbors Database Project
                  </Link>
                </li>
                <li>
                  <Link
                    :href="route('about')"
                    class="text-base text-gray-300 hover:text-gray-100"
                  >
                    About WRMD
                  </Link>
                </li>
                <li>
                  <Link
                    :href="route('about.new')"
                    class="text-base text-gray-300 hover:text-gray-100"
                  >
                    What's New
                  </Link>
                </li>
                <li>
                  <Link
                    :href="route('about.pricing')"
                    class="text-base text-gray-300 hover:text-gray-100"
                  >
                    Pricing
                  </Link>
                </li>
              </ul>
            </div>
            <div class="mt-12 md:mt-0">
              <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">
                Legal
              </h3>
              <ul
                role="list"
                class="mt-4 space-y-4"
              >
                <li>
                  <Link
                    :href="route('about.terms')"
                    class="text-base text-gray-300 hover:text-gray-100"
                  >
                    Terms and Conditions
                  </Link>
                </li>
                <li>
                  <Link
                    :href="route('about.privacy')"
                    class="text-base text-gray-300 hover:text-gray-100"
                  >
                    Privacy Policy
                  </Link>
                </li>
                <li>
                  <Link
                    :href="route('about.inactive-account')"
                    class="text-base text-gray-300 hover:text-gray-100"
                  >
                    Inactive Account Policy
                  </Link>
                </li>
                <li>
                  <Link
                    :href="route('about.sla')"
                    class="text-base text-gray-300 hover:text-gray-100"
                  >
                    Service Level Agreement
                  </Link>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="mt-12 border-t border-gray-500 pt-8 md:flex md:justify-between">
        <div class="flex">
          <div class="mr-4 flex-shrink-0">
            <img
              src="../../../images/WNDP-logo.png"
              alt="The Wild Neighbors Database Project"
              class="h-16 w-16"
            >
          </div>
          <div>
            <h4 class="text-lg font-bold">
              <a
                href="https://www.wildneighborsdp.org/"
                target="_blank"
                class="text-gray-400 hover:text-gray-300"
              >The Wild Neighbors Database Project</a>
            </h4>
            <p class="text-base text-gray-400 mt-1">
              &copy; {{ year }} All rights reserved.
            </p>
          </div>
        </div>
        <div class="mt-8 md:mt-0 text-sm">
          <h5 class="text-gray-400">
            Developed With the Support From
          </h5>
          <div class="flex justify-between md:justify-end space-x-6 mt-2">
            <a
              href="https://rollbar.com/"
              target="_blank"
            >
              <img
                src="../../../images/rollbar-logo.png"
                class="h-8 w-auto"
                alt="Rollbar"
              >
            </a>
            <a
              href="https://www.algolia.com/"
              target="_blank"
            >
              <img
                src="../../../images/algolia-logo.png"
                class="h-8 w-auto"
                alt="Algolia"
              >
            </a>
            <a
              href="https://www.helpscout.com/"
              target="_blank"
            >
              <img
                src="../../../images/help-scout-logo.png"
                class="h-8 w-auto"
                alt="Help Scout"
              >
            </a>
            <a
              href="https://usefathom.com/"
              target="_blank"
            >
              <img
                src="../../../images/fathom-logo.png"
                class="h-8 w-auto"
                alt="Fathom Analytics"
              >
            </a>
            <a
              href="https://github.com/wndp"
              target="_blank"
            >
              <img
                src="../../../images/github-logo.png"
                class="h-8 w-auto bg-white rounded-full"
                alt="Github"
              >
            </a>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>
