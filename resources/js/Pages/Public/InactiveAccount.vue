<template>
  <PublicLayout title="Inactive Account Policy">
    <div class="relative py-8 px-4 sm:px-6 lg:px-8 bg-white">
      <div class="prose prose-lg mx-auto">
        <h1>
          <span class="mt-2 block text-3xl text-center leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">Inactive Account Policy</span>
        </h1>
      </div>
      <div class="mt-6 prose max-w-none prose-blue text-gray-500 mx-auto">
        <h4>Last updated: March 15, 2025</h4>
        <p>This Inactive Account Policy describes Our policies and procedures related to the management of Accounts that have been Inactive for a specified period of time.</p>
        <h2>Interpretation and Definitions</h2>
        <h3>Interpretation</h3>
        <p>The words of which the initial letter is capitalized have meanings defined under the following conditions. The following definitions shall have the same meaning regardless of whether they appear in singular or in plural.</p>
        <h3>Definitions</h3>
        <p>For the purposes of this Privacy Policy:</p>
        <ul>
          <li>
            <p><strong>Account</strong> means a unique account created for You to access our Service or parts of our Service.</p>
          </li>
          <li>
            <p><strong>Inactive</strong> means a organization account that has not engaged with or accessed the service within a 2-year period.</p>
          </li>
          <li>
            <p><strong>Service</strong> refers to the Website.</p>
          </li>
          <li>
            <p><strong>You</strong> means the individual accessing or using the Service, or the company, or other legal entity on behalf of which such individual is accessing or using the Service, as applicable.</p>
          </li>
        </ul>
        <h2>What Happens When Your Account is Inactive?</h2>
        <p>When you don't use your Wildlife Rehabilitation MD (WRMM) Account within a 2-year period, it's then deemed inactive, and all of its content and data can be deleted. Before this happens, you have the opportunity to take action in your account when We send you notifications to your Account email.</p>
        <p>Prior to deleting your Account, We may mark your Account as suspended. In a suspended condition, the Account data will still exists however You and your users will not be able to authenticate into the Account.</p>
        <h3>Recover a Suspended Account</h3>
        <p>If your Account has become suspended, then you may request reactivation by contacting us by email at <a href="mailto:support@wildneighborsdp.org?subject=Reactivate%20suspended%20WRMD%20account">support@wildneighborsdp.org</a>. The request must come from the name and email address matching the Account contact information. If those details are no longer available, please mail us a letter on your organization letterhead to The Wild Neighbors Database Project, PO Box 421 Middletown, CA 95461. Additionally, we may make a reasonable assumption based on available evidence that reactivating your Account is warranted.</p>
        <h2>Changes to this Inactive Account Policy</h2>
        <p>We may update Our Inactive Account Policy from time to time. We will notify You of any changes by posting the new Inactive Account Policy on this page.</p>
        <p>We will let You know via email and/or a prominent notice on Our Service, prior to the change becoming effective and update the &quot;Last updated&quot; date at the top of this Inactive Account Policy.</p>
        <p>You are advised to review this Inactive Account Policy periodically for any changes. Changes to this Inactive Account Policy are effective when they are posted on this page.</p>
        <h2>Contact Us</h2>
        <p>If you have any questions about this Inactive Account Policy, You can contact us:</p>
        <ul>
          <li>
            By email: <a href="mailto:support@wildneighborsdp.org?subject=WRMD%20Inactive%20Account%20Policy%20">support@wildneighborsdp.org</a>
          </li>
          <li>
            By visiting this page on our website: <Link :href="route('contact.create')">
              https://www.wrmd.org/contact
            </Link>
          </li>
        </ul>
      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
</script>
