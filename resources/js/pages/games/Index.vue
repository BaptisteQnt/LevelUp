<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Link as InertiaLink } from '@inertiajs/vue3';

const Link = InertiaLink;

defineProps<{
    games: {
        id: number;
        title: string;
        slug: string;
        cover_url: string | null;
        description: string | null;
    }[];
}>();
</script>

<template>
    <Head title="Games" />

    <div class="max-w-5xl mx-auto px-4 py-10">
        <h1 class="text-3xl font-bold mb-8">Liste des jeux</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="game in games" :key="game.id" class="bg-white shadow p-4 rounded-lg">
                <img
                    v-if="game.cover_url"
                    :src="`https:${game.cover_url.replace('t_thumb', 't_cover_big')}`"
                    alt=""
                    class="w-full h-auto rounded mb-4"
                />
                <inertia-link :href="route('games.show', game.slug)" class="text-xl font-semibold text-blue-600 hover:underline">
                    {{ game.title }}
                </inertia-link>

                <p class="text-sm text-gray-600" v-if="game.description">{{ game.description }}</p>
            </div>
        </div>
    </div>
</template>
