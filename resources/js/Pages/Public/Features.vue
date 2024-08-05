<template>
  <PublicLayout title="Features">
    <div class="py-16 bg-gray-50 overflow-hidden lg:py-24">
      <div class="relative max-w-xl mx-auto px-4 sm:px-6 lg:px-8 lg:max-w-7xl">
        <div class="relative">
          <h2 class="text-center text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
            Out-of-the-box simple;
          </h2>
          <p class="mt-4 max-w-3xl mx-auto text-center text-xl text-gray-500">
            WRMD is designed to be used by any wildlife rehabilitator. Whether you rehab from home or at a large organization or university, WRMD can meet your needs. It works  by customizing the features that work for you.
          </p>
        </div>
        <div class="relative mt-12 sm:mt-16 lg:mt-24 lg:grid lg:grid-flow-row-dense lg:grid-cols-2 lg:gap-8 lg:items-center">
          <div class="lg:col-start-2 prose prose-blue">
            <h2>Customized Annual Reports</h2>
            <p>
              Wildlife Rehabilitation MD has the benefit of being able to adapt to many circumstances including the reporting needs of different agencies. We are able to create customized annual reports at the push of a button. The customization is the real trick. Each state, province or territory has their own idea of what should be report on at the end of the year and how it should be organized.
            </p>
            <p>Below is a list of all the annual reports that Wildlife Rehabilitation MD is currently able to create. If your state, province or territory is not listed then don't fret. Send us the actual report as created by your government and we'll recreate it as soon as possible. Send missing or updated annual reports to <a href="mailto:support@wildneighborsdp.org">support@wildneighborsdp.org</a> and please indicate in the subject that you are including an annual report.</p>
          </div>
          <div class="mt-10 -mx-4 relative lg:mt-0 lg:col-start-1">
            <img
              class="relative mx-auto shadow-lg rounded-lg"
              width="490"
              src="../../../images/usfws-wildlife-rehabilitation-annual-report.png"
              alt="USFWS Wildlife Rehabilitation Annual Report"
            >
          </div>
        </div>
        <div class="relative mt-12 lg:mt-24 lg:grid lg:grid-cols-2 lg:gap-8 lg:items-center">
          <div class="prose">
            <h2>Batch Update Your Patients</h2>
            <p>
              Batch Updating is perfect for those times when you have a handful of records which need to have the same information updated on each. Instead of going to each record individually, why not just update them at the same time? All you need to do is choose the field(s) that you want to batch update, record their new values and decide whether or not you want to overwrite any existing value or just append your new value. Done.
            </p>
          </div>
          <div
            class="mt-10 -mx-4 relative lg:mt-0"
            aria-hidden="true"
          >
            <img
              class="relative mx-auto shadow-lg rounded-lg"
              width="490"
              src="https://tailwindui.com/img/features/feature-example-1.png"
              alt=""
            >
          </div>
        </div>
        <div class="relative mt-12 sm:mt-16 lg:mt-24">
          <div class="lg:grid lg:grid-flow-row-dense lg:grid-cols-2 lg:gap-8 lg:items-center">
            <div class="lg:col-start-2 prose prose-blue">
              <h2>Analytics for the Non-analytical</h2>
              <p>
                Once you have all that data collected, what should you do with it? Analyze it and learn from it of course. The data we collect can teach us just how effective our techniques are, show us hidden trends and even highlight early warning signs that may predict epidemics. Bottom line, we can save more lives by learning from our records.
              </p>
              <p>Wildlife Rehabilitation MD offers unmatched analytical abilities to drill down into your data and discover rich insights in your daily wildlife rehabilitation routines. All you need to do is visit the analytics section and let Wildlife Rehabilitation MD do the rest.</p>
            </div>
            <div class="mt-10 -mx-4 relative lg:mt-0 lg:col-start-1">
              <img
                class="relative mx-auto shadow-lg rounded-lg"
                width="490"
                src="../../../images/most-prevalent-patients.png"
                alt="Most prevalent patients"
              >
            </div>
          </div>
        </div>
        <div class="relative mt-12 lg:mt-24 lg:grid lg:grid-cols-2 lg:gap-8 lg:items-center">
          <div class="prose">
            <h2>Standardized Terminology</h2>
            <p>
              By employing standardized terms, wildlife professionals and experts can convey ideas, data, and information with precision and accuracy, reducing the risk of misinterpretation and confusion. Wildlife Rehabilitation MD uses lists of standardized terminology that encompass the most common situations encountered in wildlife rehabilitation. Some terms will be automatically tagged on your patients by WRMD using data already recorded on the patient record. Users can also manually choose terms that best fit the patient's circumstances.
            </p>
          </div>
          <div
            class="mt-10 -mx-4 relative lg:mt-0"
            aria-hidden="true"
          >
            <img
              class="relative mx-auto shadow-lg rounded-lg"
              width="490"
              src="https://tailwindui.com/img/features/feature-example-1.png"
              alt=""
            >
          </div>
        </div>
        <div class="relative mt-12 lg:mt-24 lg:grid lg:grid-cols-2 lg:grid-flow-row-dense lg:gap-8 lg:items-center">
          <div class="lg:col-start-2 prose prose-blue">
            <h2>Import Patients into<br>Wildlife Rehabilitation MD</h2>
            <p>
              Using the Wildlife Rehabilitation MD Import tool, you can import patients and various related data into your database from other sources. We have created templates and a guide to help you organize your data so that it can easily be imported into WRMD.
            </p>
            <p>
              <Link :href="route('about.importing')">
                Read more about how to import your past patients
              </Link>
            </p>
          </div>
          <div
            class="mt-10 -mx-4 relative lg:mt-0"
            aria-hidden="true"
          >
            <img
              class="relative mx-auto shadow-lg rounded-lg"
              width="490"
              src="https://tailwindui.com/img/features/feature-example-1.png"
              alt=""
            >
          </div>
        </div>
      </div>
    </div>
    <div class="relative bg-white py-16 sm:py-24 lg:py-32">
      <div class="mx-auto max-w-md px-4 text-center sm:max-w-3xl sm:px-6 lg:px-8 lg:max-w-7xl">
        <h2 class="text-3xl font-semibold tracking-wider text-green-600 uppercase">
          Extend Your WRMD
        </h2>
        <!-- <p class="mt-2 text-3xl font-extrabold text-gray-900 tracking-tight sm:text-4xl">
                    All the tools needed to be as detailed as you want
                </p> -->
        <p class="mt-5 max-w-prose mx-auto text-xl text-gray-500">
          Wildlife Rehabilitation MD has many built in features that you can choose to activate. Prescriptions, Attachments, Lab results and Hotline just to name a few.
        </p>
        <div class="mt-12">
          <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
            <div
              v-for="extension in extensions"
              :key="extension.namespace"
              class="pt-6"
            >
              <div class="flow-root bg-gray-50 rounded-lg px-6 pb-8 h-full">
                <div class="-mt-6">
                  <div>
                    <span class="inline-flex items-center justify-center p-3 bg-green-500 rounded-md shadow-lg">
                      <component
                        :is="heroIcons[extension.icon]"
                        class="h-6 w-6 text-white"
                        aria-hidden="true"
                      />
                    </span>
                  </div>
                  <h3 class="mt-8 text-lg font-medium text-gray-900 tracking-tight">
                    {{ extension.name }}
                  </h3>
                  <p class="mt-5 text-base text-gray-500">
                    {{ extension.tagline }}
                  </p>
                </div>
              </div>
            </div>
            <div class="pt-6">
              <div class="flow-root bg-gray-50 rounded-lg px-6 pb-8 h-full">
                <div class="-mt-6">
                  <div>
                    <span class="inline-flex items-center justify-center p-3 bg-orange-500 rounded-md shadow-lg">
                      <component
                        :is="heroIcons['MegaphoneIcon']"
                        class="h-6 w-6 text-white"
                        aria-hidden="true"
                      />
                    </span>
                  </div>
                  <h3 class="mt-8 text-lg font-medium text-gray-900 tracking-tight">
                    tell us
                  </h3>
                  <p class="mt-5 text-base text-gray-500">
                    Let us know what amazing feature you want added!
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </PublicLayout>
</template>

<script>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import * as heroIcons from "@heroicons/vue/24/outline";

export default {
    components: {
        PublicLayout
    },
    props: {
        extensions: Array
    },
    data() {
        return {
            heroIcons: heroIcons
        };
    }
};
</script>
