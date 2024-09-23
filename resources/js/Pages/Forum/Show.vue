<script setup>
import {computed} from 'vue';
import {useForm, usePage} from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ChatBubbleOvalLeftEllipsisIcon } from '@heroicons/vue/24/outline';
import Badge from '@/Components/Badge.vue';
import FollowButton from './Partials/FollowButton.vue';
import SolvedButton from './Partials/SolvedButton.vue';
import FormSection from '@/Components/FormElements/FormSection.vue';
import TextareaInput from '@/Components/FormElements/TextareaInput.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import ForumAside from './Partials/ForumAside.vue';
import {__} from '@/Composables/Translate';
import {can} from '@/Composables/Can';
import {Abilities} from '@/Enums/Abilities';

const props = defineProps({
  thread: Object,
  channels: Array,
  teams: Array,
  groups: Array,
});

const form = useForm({
    reply: '',
});

const canResolve = computed(() => props.thread.team_id === usePage().props.auth.team.id || can('anything'));

const postReply = () => {
  form.post(route('forum.replies.store', props.thread), {
      preserveScroll: true,
      onSuccess: () => form.reset('reply')
  });
};
</script>

<template>
  <AppLayout title="Forum">
    <div class="lg:grid grid-cols-8 gap-8">
      <ForumAside
        :channels="channels"
        :groups="groups"
        :teams="teams"
        class="mb-4 lg:mb-0 col-span-2"
      />
      <div class="col-span-6">
        <div class="bg-white py-6 px-4 sm:p-6 shadow sm:rounded-md sm:overflow-hidden">
          <div>
            <div class="flex justify-between">
              <h3 class="text-lg leading-6 font-medium text-gray-900 mr-8">
                {{ thread.title }}
              </h3>
              <div class="space-x-3">
                <Link
                  v-if="thread.group"
                  :href="route('forum.index', {group: thread.group.slug})"
                >
                  <Badge
                    color="blue"
                    large
                  >
                    {{ thread.group.name }}
                  </Badge>
                </Link>
                <Link :href="route('forum.index', {channel: thread.channel.slug})">
                  <Badge
                    color="yellow"
                    large
                  >
                    {{ thread.channel.name }}
                  </Badge>
                </Link>
              </div>
            </div>
            <p class="mt-1 text-sm text-gray-500">
              {{ __('This discussion was started :timeDifference by :organization.', {
                timeDifference: thread.created_at_for_humans,
                organization: thread.team.organization
              }) }}
            </p>
          </div>
        </div>
        <div class="flow-root mt-8">
          <ul
            role="list"
            class="-mb-8"
          >
            <li>
              <div class="relative pb-8">
                <span
                  class="absolute top-5 left-8 -ml-px h-full w-0.5 bg-gray-200"
                  aria-hidden="true"
                />
                <div class="relative flex items-start space-x-3">
                  <div class="relative">
                    <img
                      class="h-16 w-16 rounded-full bg-gray-400 flex items-center justify-center ring-8 ring-white"
                      :src="thread.team.profile_photo_url"
                      :alt="thread.team.name"
                    >
                    <span class="absolute -bottom-0.5 -right-1 bg-white rounded-tl px-0.5 py-px">
                      <ChatBubbleOvalLeftEllipsisIcon class="h-5 w-5 text-gray-400" />
                    </span>
                  </div>
                  <div class="min-w-0 flex-1 bg-white py-2 px-3 shadow-sm sm:rounded-md">
                    <div>
                      <div class="text-sm">
                        <Link
                          v-if="can(Abilities.COMPUTED_MANAGE_ACCOUNTS)"
                          :href="route('accounts.show', thread.account)"
                          class="font-medium text-gray-900"
                        >
                          {{ thread.team.name }}
                        </Link>
                        <span
                          v-else
                          class="font-medium text-gray-900"
                        >
                          {{ thread.team.name }}
                        </span>
                      </div>
                      <p class="mt-0.5 text-sm text-gray-500">
                        {{ thread.user?.name }} posted <time class="font-medium">{{ thread.created_at_for_humans }}</time>
                      </p>
                    </div>
                    <div
                      class="mt-2 text-sm prose lg:prose-lg max-w-none"
                      v-html="thread.body_for_humans"
                    />
                  </div>
                </div>
              </div>
            </li>
            <li
              v-for="reply in thread.replies"
              :key="reply.id"
            >
              <div class="relative pb-8">
                <span
                  class="absolute top-5 left-8 -ml-px h-full w-0.5 bg-gray-200"
                  aria-hidden="true"
                />
                <div class="relative flex items-start space-x-3">
                  <div class="relative">
                    <img
                      class="h-16 w-16 rounded-full bg-gray-400 flex items-center justify-center ring-8 ring-white"
                      :src="reply.team.profile_photo_url"
                      :alt="reply.team.name"
                    >
                    <span class="absolute -bottom-0.5 -right-1 bg-white rounded-tl px-0.5 py-px">
                      <ChatBubbleOvalLeftEllipsisIcon class="h-5 w-5 text-gray-400" />
                    </span>
                  </div>
                  <div class="min-w-0 flex-1 bg-white py-2 px-3 shadow-sm sm:rounded-md">
                    <div>
                      <div class="text-sm">
                        <Link
                          v-if="can(Abilities.COMPUTED_MANAGE_ACCOUNTS)"
                          :href="route('accounts.show', reply.account)"
                          class="font-medium text-gray-900"
                        >
                          {{ reply.team.name }}
                        </Link>
                        <span
                          v-else
                          class="font-medium text-gray-900"
                        >
                          {{ reply.team.name }}
                        </span>
                      </div>
                      <p class="mt-0.5 text-sm text-gray-500">
                        {{ reply.user?.name }} replied <time class="font-medium">{{ reply.created_at_for_humans }}</time>
                      </p>
                    </div>
                    <div
                      class="mt-2 text-sm text-gray-700 prose lg:prose-lg max-w-none"
                      v-html="reply.body_for_humans"
                    />
                  </div>
                </div>
              </div>
            </li>
          </ul>
        </div>
        <FormSection class="mt-8 relative">
          <template #title>
            {{ __('Reply to This Discussion') }}
          </template>
          <div class="col-span-4">
            <div class="flex">
              <img
                class="h-16 w-16 rounded-full bg-gray-400 mr-8"
                :src="$page.props.auth.team.profile_photo_url"
                :alt="$page.props.auth.team.name"
              >
              <div class="flex-1">
                <TextareaInput
                  v-model="form.reply"
                  name="reply"
                  class="mt-1"
                />
                <InputError
                  :message="form.errors.reply"
                  class="mt-2"
                />
              </div>
            </div>
          </div>
          <template #actions>
            <ActionMessage
              :on="form.recentlySuccessful"
              class="mr-3"
            >
              {{ __('Saved') }}
            </ActionMessage>
            <PrimaryButton
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
              @click="postReply"
            >
              {{ __('Send Reply') }}
            </PrimaryButton>
          </template>
        </FormSection>
      </div>
    </div>
  </AppLayout>
</template>
