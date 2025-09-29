<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

import DeleteUser from '@/components/DeleteUser.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { premiumBorderOptions, resolveBorderClass } from '@/lib/premium';
import { type BreadcrumbItem, type SharedData, type User } from '@/types';

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: '/settings/profile',
    },
];

const page = usePage<SharedData>();
const user = page.props.auth.user as User;

const borderOptions = premiumBorderOptions;

const form = useForm({
    name: user.name ?? '',
    username: user.username ?? '',
    email: user.email ?? '',
    phone: user.phone ?? '',
    address: user.address ?? '',
    city: user.city ?? '',
    cp: user.cp ?? '',
    country: user.country ?? '',
    age: user.age ?? '',
    display_name_color: user.display_name_color ?? '',
    display_alias: user.display_alias ?? '',
    profile_border_style: user.profile_border_style ?? 'none',
});

const previewColor = computed(() => form.display_name_color || '#1f2937');

const selectedBorderClass = computed(() => resolveBorderClass(form.profile_border_style));

const previewInitials = computed(() => {
    const source = user.name || user.username;
    return (source ?? 'U').slice(0, 2).toUpperCase();
});


const submit = () => {
    form.patch(route('profile.update'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Profile settings" />

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall title="Profile information" description="Update your name and email address" />

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid gap-2">
                        <Label for="name">Name</Label>
                        <Input id="name" class="mt-1 block w-full" v-model="form.name" required autocomplete="name" placeholder="Full name" />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="username">Username</Label>
                        <Input id="username" class="mt-1 block w-full" v-model="form.username" required placeholder="Username" />
                        <InputError class="mt-2" :message="form.errors.username" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="phone">Phone</Label>
                        <Input id="phone" class="mt-1 block w-full" v-model="form.phone" placeholder="Phone number" />
                        <InputError class="mt-2" :message="form.errors.phone" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="address">Address</Label>
                        <Input id="address" class="mt-1 block w-full" v-model="form.address" placeholder="Address" />
                        <InputError class="mt-2" :message="form.errors.address" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="city">City</Label>
                        <Input id="city" class="mt-1 block w-full" v-model="form.city" placeholder="City" />
                        <InputError class="mt-2" :message="form.errors.city" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="cp">Postal Code</Label>
                        <Input id="cp" class="mt-1 block w-full" v-model="form.cp" placeholder="Postal Code" />
                        <InputError class="mt-2" :message="form.errors.cp" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="country">Country</Label>
                        <Input id="country" class="mt-1 block w-full" v-model="form.country" placeholder="Country" />
                        <InputError class="mt-2" :message="form.errors.country" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="age">Age</Label>
                        <Input id="age" class="mt-1 block w-full" v-model="form.age" type="number" placeholder="Age" />
                        <InputError class="mt-2" :message="form.errors.age" />
                    </div>


                    <div
                        v-if="user.is_subscribed"
                        class="space-y-4 rounded-lg border border-primary/20 bg-primary/5 p-4"
                    >
                        <div>
                            <h3 class="text-base font-semibold text-primary">Personnalisation premium</h3>
                            <p class="text-sm text-muted-foreground">
                                Ajoutez une touche unique à votre profil visible par toute la communauté.
                            </p>
                        </div>

                        <div class="grid gap-2">
                            <Label for="display_alias">Pseudo affiché à côté de votre nom</Label>
                            <Input
                                id="display_alias"
                                class="mt-1 block w-full"
                                v-model="form.display_alias"
                                placeholder="Ex. Le Stratège"
                            />
                            <InputError class="mt-2" :message="form.errors.display_alias" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="display_name_color">Couleur de votre nom</Label>
                            <div class="flex flex-wrap items-center gap-3">
                                <Input
                                    id="display_name_color"
                                    class="mt-1 w-36"
                                    v-model="form.display_name_color"
                                    placeholder="#6366f1"
                                />
                                <input
                                    type="color"
                                    class="h-10 w-12 cursor-pointer rounded border border-sidebar-border/80"
                                    :value="form.display_name_color || '#6366f1'"
                                    @input="form.display_name_color = ($event.target as HTMLInputElement).value"
                                />
                                <Button
                                    type="button"
                                    variant="outline"
                                    size="sm"
                                    @click="form.display_name_color = ''"
                                >
                                    Réinitialiser
                                </Button>
                            </div>
                            <p class="text-xs text-muted-foreground">
                                Aperçu :
                                <span class="font-semibold" :style="{ color: previewColor }">
                                    {{ user.name }}
                                </span>
                                <span v-if="form.display_alias" class="text-muted-foreground">
                                    ({{ form.display_alias }})
                                </span>
                            </p>
                            <InputError class="mt-2" :message="form.errors.display_name_color" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="profile_border_style">Bordure de votre avatar</Label>
                            <div class="flex flex-wrap items-center gap-3">
                                <select
                                    id="profile_border_style"
                                    v-model="form.profile_border_style"
                                    class="mt-1 rounded-lg border border-sidebar-border/70 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary dark:bg-neutral-900"
                                >
                                    <option
                                        v-for="option in borderOptions"
                                        :key="option.value"
                                        :value="option.value"
                                    >
                                        {{ option.label }}
                                    </option>
                                </select>
                                <div
                                    class="flex size-12 items-center justify-center rounded-full bg-white font-semibold uppercase text-neutral-500 shadow-sm dark:bg-neutral-900"
                                    :class="selectedBorderClass"
                                >
                                    {{ previewInitials }}
                                </div>
                            </div>
                            <InputError class="mt-2" :message="form.errors.profile_border_style" />
                        </div>
                    </div>


                    <div class="grid gap-2">
                        <Label for="email">Email address</Label>
                        <Input
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            v-model="form.email"
                            required
                            autocomplete="username"
                            placeholder="Email address"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div v-if="mustVerifyEmail && !user.email_verified_at">
                        <p class="-mt-4 text-sm text-muted-foreground">
                            Your email address is unverified.
                            <Link
                                :href="route('verification.send')"
                                method="post"
                                as="button"
                                class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                            >
                                Click here to resend the verification email.
                            </Link>
                        </p>

                        <div v-if="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
                            A new verification link has been sent to your email address.
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="form.processing">Save</Button>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Saved.</p>
                        </Transition>
                    </div>
                </form>
            </div>

            <DeleteUser />
        </SettingsLayout>
    </AppLayout>
</template>
