<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

defineProps<{
    user: {
        name: string;
        username: string;
        city: string | null;
        country: string | null;
        age: number;
        created_at: string;
    };
}>();
</script>

<template>
    <Head :title="`@${user.username}`" />

    <section class="max-w-2xl mx-auto px-4 py-10">
        <h1 class="text-3xl font-bold">@{{ user.username }}</h1>
        <!-- <p class="text-neutral-600">Nom : {{ user.name }}</p> -->
        <!-- <p class="text-neutral-600">Âge : {{ user.age }} ans</p> -->
        <p class="text-neutral-600">Localisation : {{ user.country }}</p>
        <p class="text-neutral-400 mt-4 text-sm">Membre depuis {{ user.created_at }}</p>
    </section>
</template>
