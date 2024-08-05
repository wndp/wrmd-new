<template>
  <PublicLayout title="Importing">
    <div class="relative py-8 px-4 sm:px-6 lg:px-8 bg-white">
      <div class="prose prose-lg mx-auto">
        <h1>
          <span class="mt-2 block text-3xl text-center leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">Importing Patients into<br>Wildlife Rehabilitation MD</span>
        </h1>
      </div>
      <div class="mt-6 prose max-w-none prose-blue text-gray-500 mx-auto">
        <p>Using the Wildlife Rehabilitation MD Import tool, you can import patients and various related data into your database from other sources. We have created templates and a guide to help you organize your data so that it can easily be imported into WRMD.</p>

        <h2>Before You Start</h2>
        <p>Here are some things to know before you begin this process.</p>

        <ol>
          <li>The data that you want to import needs to be formatted into an <strong>MS Excel (.xlsx)</strong> file.</li>
          <li>At this time, we (the Wildlife Rehabilitation MD team) prefer to do the actual importing for you. This is because importing can be tricky and we want to make sure everything goes where it needs to. <strong>You send us the file(s) and we import them</strong>. <sup>*</sup><small><a href="#us">have us prepare your data for you</a></small></li>
        </ol>

        <h3>You Can Import...</h3>
        <ul>
          <li
            v-for="entity in importableEntities"
            :key="entity.name"
          >
            {{ entity.name }}
          </li>
          <li>Other: If want to import something that's not in this list then we can likely make it possible. <a href="mailto:support@wildneighborsdp.org?subject=Help with importing patients into WRMD">Just ask!</a></li>
        </ul>

        <h3>Required Data</h3>
        <p>Make sure your data has the required columns and each row has values for those columns. The required columns for importing patients are:</p>
        <ul>
          <li
            v-for="field in filteredRequiredFields"
            :key="field"
          >
            {{ field }}
          </li>
        </ul>
        <p>The required columns for importing anything other than patients depends on what you are importing. However a column that is always required is one with a unique identifier to correlate the data to the patient it belongs to. The data may correlate to a patients case number, reference number, microchip number or band.</p>

        <h3>Species</h3>
        <p>Entering the correct common names is incredibly helpful for all data analysis and reporting. The easiest way to choose a common name is to just look them up in the WRMD new patient screen. Anything that auto populates within WRMD is an appropriate common name. Typically the most common common-names are known to WRMD. After importing your patients, WRMD will verify the species common names and identify any that are unrecognized.</p>

        <h3>Prepare for Import</h3>
        <ol>
          <li>
            Download one of our template files to help you organize your data.
            <ul>
              <li
                v-for="entity in importableEntities"
                :key="entity.name"
              >
                <a :href="`../../../import-templates/wrmd-${entity.slug}-import-template.xlsx`">{{ entity.name }}</a>
              </li>
            </ul>
          </li>
          <li>Add your data to the template file.</li>
          <li>If there is a column who's values are always empty, then you can delete that column as long as it is not a required column.</li>
          <li>Standardize your date values for consistency. Ideally into <code>yyyy-mm-dd</code> format.</li>
          <li>Standardize your "translatable" values. For example, when filling in the Disposition column you should ideally use the same disposition terms used in WRMD. If however you use your own terms, we will need to know their meaning so that we can translate them into the correct WRMD equivalent.</li>
          <li>Once your data is cleaned and standardized, email us at <a href="mailto:support@wildneighborsdp.org?subject=Importing patients into WRMD">support@wildneighborsdp.org</a> with your name, account and Excel file(s). We will then coordinate the importing process with you.</li>
        </ol>

        <h3>Below are Some Examples of Fields In WRMD Who's Values are Chosen From Drop-down Menus.</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-4">
          <div
            v-for="field in translatableFields"
            :key="field.name"
          >
            <h4>{{ field.name }}</h4>
            <ul>
              <li
                v-for="option in field.options"
                :key="option"
              >
                {{ option }}
              </li>
            </ul>
          </div>
        </div>

        <a name="us" />
        <h2>Have us Prepare Your Data For You</h2>
        <p>Your data may be in a format or database that you are unfamiliar with or perhaps there is so much of it that you are overwhelmed. If that's the case then, for a negotiated fee, we can prepare and import your data for you.</p>

        <p>There are too many variables to try and describe how this process could work. However; in the past, we have had data emailed to us that we were able to access and prepare for import. We have also visited organizations and worked directly with their staff to export, prepare and import their data. Depending on time lines and budget we are willing to help however we can.</p>

        <p>Past fees for preparing other organizations data have ranged from $300(US) to $5,000(US). It all depends on how much data there is, what shape it's in and how soon you need it imported.</p>

        <p>If you would like to discuss this option further then please email us at <a href="mailto:support@wildneighborsdp.org?subject=Help with importing patients into WRMD">support@wildneighborsdp.org</a> with your name, account and needs.</p>
      </div>
    </div>
  </PublicLayout>
</template>

<script>
import PublicLayout from '@/Layouts/PublicLayout.vue';

export default {
    components: {
        PublicLayout
    },
    props: {
        importableEntities: Array,
        filteredRequiredFields: Array,
        translatableFields: Array,
    },
    data() {
        return {

        };
    }
};
</script>
