<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

const form = useForm({
    name: '',
    username: '',
    email: '',
    phone: '',
    address: '',
    city: '',
    cp: '',
    country: '',
    age: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <AuthBase title="Create an account" description="Enter your details below to create your account">
        <Head title="Register" />

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="name">Full Name</Label>
                    <Input id="name" type="text" required autofocus autocomplete="name" v-model="form.name" placeholder="Full name" />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="username">Username</Label>
                    <Input id="username" type="text" required v-model="form.username" placeholder="Username" />
                    <InputError :message="form.errors.username" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input id="email" type="email" required autocomplete="email" v-model="form.email" placeholder="email@example.com" />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="phone">Phone</Label>
                    <Input id="phone" type="text" v-model="form.phone" placeholder="Phone number" />
                    <InputError :message="form.errors.phone" />
                </div>

                <div class="grid gap-2">
                    <Label for="address">Address</Label>
                    <Input id="address" type="text" v-model="form.address" placeholder="Address" />
                    <InputError :message="form.errors.address" />
                </div>

                <div class="grid gap-2">
                    <Label for="city">City</Label>
                    <Input id="city" type="text" v-model="form.city" placeholder="City" />
                    <InputError :message="form.errors.city" />
                </div>

                <div class="grid gap-2">
                    <Label for="cp">Postal Code</Label>
                    <Input id="cp" type="text" v-model="form.cp" placeholder="Postal Code" />
                    <InputError :message="form.errors.cp" />
                </div>

                <div class="grid gap-2">
                    <Label for="country">Country</Label>
                    <Input id="country" type="text" v-model="form.country" placeholder="Country" />
                    <InputError :message="form.errors.country" />
                </div>

                <div class="grid gap-2">
                    <Label for="age">Age</Label>
                    <Input id="age" type="number" required v-model="form.age" placeholder="Age" min="1" />
                    <InputError :message="form.errors.age" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Password</Label>
                    <Input id="password" type="password" required autocomplete="new-password" v-model="form.password" placeholder="Password" />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Confirm password</Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        required
                        autocomplete="new-password"
                        v-model="form.password_confirmation"
                        placeholder="Confirm password"
                    />
                    <InputError :message="form.errors.password_confirmation" />
                </div>

                <Button type="submit" class="mt-2 w-full" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Create account
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Already have an account?
                <TextLink :href="route('login')" class="underline underline-offset-4">Log in</TextLink>
            </div>
        </form>
    </AuthBase>
</template>
