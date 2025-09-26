<script setup lang="ts">
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link as InertiaLink } from '@inertiajs/vue3';

const Link = InertiaLink;

defineProps<{
    games: {
        id: number;
        title: string;
        slug: string;
        cover_url: string | null;
        summary: string | null;
        storyline: string | null;
        description: string | null;
    }[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Accueil',
        href: '/',
    },
    {
        title: 'Jeux',
        href: '/games',
    },
];
</script>

<template>
    <Head title="Games" />

    <AppHeaderLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-5xl px-4 py-10">
            <h1 class="mb-8 text-3xl font-bold">Liste des jeux</h1>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div v-for="game in games" :key="game.id" class="rounded-lg bg-white p-4 shadow">
                    <img
                        v-if="game.cover_url"
                        :src="`https:${game.cover_url.replace('t_thumb', 't_cover_big')}`"
                        alt=""
                        class="mb-4 h-auto w-full rounded"
                    />
                    <Link :href="route('games.show', game.slug)" class="text-xl font-semibold text-blue-600 hover:underline">
                        {{ game.title }}
                    </Link>

                    <p class="text-sm text-gray-600" v-if="game.storyline || game.summary || game.description">
                        {{ game.storyline ?? game.summary ?? game.description }}
                    </p>
                </div>
            </div>
        </div>
    </AppHeaderLayout>
</template>
