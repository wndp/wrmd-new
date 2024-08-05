<template>
    <PublicLayout title="Confirm Password">
        <div class="flex justify-center">
            <div class="w-full sm:max-w-lg mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                <div class="mb-4 text-sm text-gray-600">
                    {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
                </div>
                <form @submit.prevent="submit">
                    <div>
                        <Label for="password">{{ __('Password') }}</Label>
                        <Input id="password" type="password" class="mt-1 block w-full" v-model="form.password" required autocomplete="current-password" autofocus />
                    </div>
                    <div class="flex justify-end mt-4">
                        <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            {{ __('Confirm') }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </PublicLayout>
</template>

<script>
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Input from '@/Components/FormElements/Input.vue';
import Label from '@/Components/FormElements/Label.vue';
import InputError from '@/Components/FormElements/InputError.vue';

export default {
    components: {
        PublicLayout,
        PrimaryButton,
        Input,
        Label,
        InputError
    },
    data() {
        return {
            form: this.$inertia.form({
                password: '',
            })
        };
    },
    methods: {
        submit() {
            this.form.post(this.route('password.confirm'), {
                onFinish: () => this.form.reset(),
            });
        }
    }
};
</script>
