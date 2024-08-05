<template>
  <PublicLayout title="Security">
    <div class="relative py-8 px-4 sm:px-6 lg:px-8 bg-white">
      <div class="prose prose-lg mx-auto">
        <h1>
          <span class="mt-2 block text-3xl text-center leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">Security</span>
        </h1>
        <p class="mt-8 text-xl text-gray-500 leading-8">
          Aliquet nec orci mattis amet quisque ullamcorper neque, nibh sem. At arcu, sit dui mi, nibh dui, diam eget aliquam. Quisque id at vitae feugiat egestas ac. Diam nulla orci at in viverra scelerisque eget. Eleifend egestas fringilla sapien.
        </p>
      </div>
      <div class="mt-6 prose prose-xl prose-blue text-gray-500 mx-auto">
        <h4>User Level Access</h4>
        <p>Wildlife Rehabilitation MD allows you to set the permission levels of your users, giving you the ability to control what users can and cannot do within Wildlife Rehabilitation MD.</p>
        <h4>Strong Passwords</h4>
        <p>When creating an account or adding users to your account, Wildlife Rehabilitation MD requires you to use strong passwords. Also, automatic lockouts are enforced when incorrect passwords are repeatedly entered.</p>
        <h4>Restricting Remote Access</h4>
        <p>Because Wildlife Rehabilitation MD is on-line, it can be accessed from anywhere with an internet connection. This default feature can be restricted to only allow access to your account from within your physical address, hence restricting remote access.</p>
        <h4>SSL</h4>
        <p>SSL (Secure Sockets Layer) is the standard security technology for establishing an encrypted link between a web server and a browser. This link ensures that all data passed between the web server and browsers remain private and integral. SSL is an industry standard and is used by millions of websites in the protection of their online transactions with their customers.</p>
      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
</script>
