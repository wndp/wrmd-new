<template>
  <PublicLayout title="About Wildlife Rehabilitation MD">
    <div class="relative py-8 px-4 sm:px-6 lg:px-8 bg-white">
      <div class="prose prose-lg mx-auto">
        <h1>
          <span class="mt-2 block text-3xl text-center leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">About Wildlife Rehabilitation MD</span>
        </h1>
        <p class="mt-8 text-xl text-gray-500 leading-8">
          Wildlife Rehabilitation MD is an out-of-the-box, simple record-keeping system designed specifically for wildlife rehabilitators in North America to collect, manage and analyze data for their patients. Wildlife Rehabilitation MD was born from decades of experience in wildlife rehabilitation combined with website application design and development. When Wildlife Rehabilitation MD was conceived of, it was clear that the database needed to easily store and manage large volumes of data without being overwhelming to the average wildlife rehabilitator. We wanted a database that allowed for quick data entry and excellent data mining.
        </p>
        <p class="mt-8 text-xl text-gray-500 leading-8">
          <strong>And now finally we have it!</strong> Wildlife Rehabilitation MD is designed specifically for wildlife rehabilitators by wildlife rehabilitators.
        </p>
      </div>
      <div class="mt-6 prose prose-xl prose-blue text-gray-500 mx-auto">
        <p>This database had been through many name changes until we finally settled on Wildlife Rehabilitation MD. Traditionally "MD" stands for Medical Doctor, which fits this database fine, but we use the "MD" to stand for Medical Database. For short we call the database WRMD, pronounced [wurm - ed], hence the worm as the logo.</p>
        <h4>Guiding Principles Behind Wildlife Rehabilitation MD's Development</h4>
        <ol>
          <li>Wildlife Rehabilitation MD should be as simple as possible so that any wildlife rehabilitator can use it.</li>
          <li>Wildlife Rehabilitation MD should should also scale easily to meet the unique needs of all wildlife rehabilitators regardless of the wildlife they care for.</li>
          <li>The integrity and safety of the data is paramount. Nothing should get out that doesn't have permission to be out.</li>
        </ol>
        <h4>Not What You're Looking For?</h4>
        <p>Consider one of these known alternatives.</p>
        <ul>
          <li><a href="http://wild-one.org/">WILD-ONe</a></li>
          <li><a href="http://www.raptormed.com/">RaptorMed</a></li>
        </ul>
        <h4>Want to Get in Contact With Us?</h4>
        <p>
          <Link :href="route('contact.create')">
            Send us a message and we'll be glad to hear from you!
          </Link>
        </p>
        <h4>From Devin and Rachel</h4>
        <p>We would like to recognize and give thanks to all the institutions that we have served and learned from. They gratefully offered their expertise and experiences to us and allowed us to learn about their record management systems. Because of their willingness to share, we were able to congregate those experiences and share them with you. We are opening this service to our colleagues and friends so that together, we can care for our wild neighbors and advance the wildlife rehabilitation profession.</p>
        <ul>
          <li>
            <a
              href="http://wildlife-museum.org/"
              target="_blank"
            >Lindsay Wildlife Museum</a>
          </li>
          <li>
            <a
              href="http://bird-rescue.org/"
              target="_blank"
            >International Bird Rescue</a>
          </li>
          <li>
            <a
              href="http://www.owcn.org/"
              target="_blank"
            >Oiled Wildlife Care Network</a>
          </li>
          <li>
            <a
              href="http://www.crowclinic.org/"
              target="_blank"
            >Clinic for the Rehabilitation of Wildlife</a>
          </li>
          <li>
            <a
              href="https://www.belizebirdrescue.com/"
              target="_blank"
            >Belize Bird Rescue</a>
          </li>
        </ul>
        <h4>Thank You!</h4>
        <img
          loading="lazy"
          width="838"
          height="420"
          style="max-width: 100%;"
          src="../../../images/devin-and-rachel.png"
          alt="Devin and Rachel"
        >
        <p class="text-foot-note">
          &mdash; Devin Dombrowski and Rachel Avilla
        </p>
      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
</script>
