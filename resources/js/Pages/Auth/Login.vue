<script setup>
import BreezeButton from '@/Components/Button.vue';
import BreezeCheckbox from '@/Components/Checkbox.vue';
import BreezeGuestLayout from '@/Layouts/Guest.vue';
import BreezeInput from '@/Components/Input.vue';
import BreezeLabel from '@/Components/Label.vue';
import BreezeValidationErrors from '@/Components/ValidationErrors.vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <BreezeGuestLayout class="items-center">
        <br>
        <Head title="Login" />

        <BreezeValidationErrors class="mb-4" />

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <BreezeInput id="email" type="email" placeholder="Email" class="mt-1 block w-full font-calibri" style="width: 400px;" v-model="form.email" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <BreezeInput id="password" type="password" placeholder="Password" class="mt-1 block w-full font-calibri" style="width: 400px;" v-model="form.password" required autocomplete="current-password" />
            </div>

            <div class="block mt-4">
                <label class="flex items-center text-white-600">
                    <BreezeCheckbox name="remember" v-model:checked="form.remember" />
                    <span class="ml-2 font-calibri" style="color:white;">Remember me</span>
                </label>
            </div>
            <br>

            <div class="flex justify-center items-center">

                <BreezeButton class="bg-white font-calibri text-green bold-text" style="font-size: 25px; font-weight: bold; height: 35px; width: 100px; text-align: center;" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Login
                </BreezeButton>
            </div>
        </form> 
    </BreezeGuestLayout>
    
</template>
