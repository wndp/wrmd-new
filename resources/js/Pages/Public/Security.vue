<template>
  <PublicLayout title="Security">
    <div class="relative py-8 px-4 sm:px-6 lg:px-8 bg-white">
      <div class="prose prose-lg mx-auto">
        <h1>
          <span class="mt-2 block text-3xl text-center leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">Security and Data Integrity</span>
        </h1>
        <p class="mt-8 text-xl text-gray-500 leading-8">
          In today’s digital world, protecting online data is essential because personal, financial, and sensitive information is often stored and shared on the internet. Without strong security measures, this data can be at risk of being hacked, stolen, or accessed by unauthorized people. To keep your information safe, Wildlife Rehabilitation MD uses strong encryption, multi-factor authentication (MFA), and strict user permissions. We also regularly conduct security audits and back up data. We also make sure to follow privacy laws like GDPR and CCPA to protect your privacy.
        </p>
      </div>
      <div class="mt-6 prose prose-xl prose-blue text-gray-500 mx-auto">
        <h4>Strong Passwords</h4>
        <p>When creating an account or adding users to your account, Wildlife Rehabilitation MD requires you to use strong passwords. Also, automatic lockouts are enforced when incorrect passwords are repeatedly entered.</p>
        <h4>User Level Authorizations</h4>
        <p>Wildlife Rehabilitation MD allows you to set the permission levels of your users, giving you the ability to control what users can and cannot do within Wildlife Rehabilitation MD.</p>
        <h4>Multifactor Authorization</h4>
        <p>Multi-Factor Authentication (MFA) enhances security by requiring users to verify their identity through something they have (like a code sent to their phone), adding an extra layer of security against unauthorized access.</p>
        <h4>Restricting Remote Access</h4>
        <p>Because Wildlife Rehabilitation MD is on-line, it can be accessed from anywhere with an internet connection. This default feature can be restricted to only allow access to your account from within your physical address, hence restricting remote access.</p>
        <h4>Data Integrity</h4>
        <p>
          We are often asked about the integrity of the data within Wildlife Rehabilitation MD, which is an entirely fair question to ask. The data collected by Wildlife Rehabilitation MD is private and precious to each account and it is our responsibility to keep it that way. Over the course of the development of Wildlife Rehabilitation MD we have adopted many precautions to ensure that your data stays put. Below is a list of the features within Wildlife Rehabilitation MD that help to ensure just that.
        </p>
        <ol>
          <li>Your data is stored on private servers that only we and the server administrators have access to.</li>
          <li>The database is replicated across multiple servers which allows you faster access to your data.</li>
          <li>Every day the entire database is backed up remotely. In the event of a system-wide breakdown, we can quickly restore the entire database to the last back-up cycle.</li>
          <li>Every week the entire database server is backed up. If the master database server ever failed, it could be restored back to its last stable state.</li>
          <li>We keep a revision history of every change to every patient. If you create, update or delete a record we record that change, when it occurred and who made it.</li>
          <li>At any time, you may export any of your records from Wildlife Rehabilitation MD, keeping an external backup of your data.</li>
          <li>When you share a patient (<em>print, email or export</em>), a log may be created of that sharing and can be viewed at any time allowing you to see when and how data left your account.</li>
          <li>There are other security and integrity walls in place, but in the interest of security we won't disclose them. 😊</li>
        </ol>
      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
</script>
