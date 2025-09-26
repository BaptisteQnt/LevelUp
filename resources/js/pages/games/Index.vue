<script setup lang="ts">
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link as InertiaLink } from '@inertiajs/vue3';
import { computed, onMounted, ref, watch } from 'vue';

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

type LanguageCode = 'fr' | 'en';

interface LanguageOption {
    value: LanguageCode;
    label: string;
    flag: string;
}

const languages: LanguageOption[] = [
    { value: 'fr', label: 'Français', flag: '🇫🇷' },
    { value: 'en', label: 'English', flag: '🇬🇧' },
];

const selectedLanguage = ref<LanguageCode>('fr');

onMounted(() => {
    if (typeof window === 'undefined') {
        return;
    }

    const storedLanguage = window.localStorage.getItem('levelup_language');

    if (storedLanguage === 'fr' || storedLanguage === 'en') {
        selectedLanguage.value = storedLanguage;
    }
});

watch(selectedLanguage, (language) => {
    if (typeof window === 'undefined') {
        return;
    }

    window.localStorage.setItem('levelup_language', language);
});

const selectedLanguageConfig = computed(() => {
    return languages.find((language) => language.value === selectedLanguage.value) ?? languages[0];
});

const pageText = computed(() => {
    if (selectedLanguage.value === 'fr') {
        return {
            headTitle: 'Jeux',
            title: 'Liste des jeux',
            languageLabel: 'Langue',
            languageNotice: 'Choisissez la langue de navigation pour personnaliser votre expérience.',
        } as const;
    }

    return {
        headTitle: 'Games',
        title: 'Games catalog',
        languageLabel: 'Language',
        languageNotice: 'Pick your preferred browsing language to personalise the experience.',
    } as const;
});
</script>

<template>
    <Head :title="pageText.headTitle" />

    <AppHeaderLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-5xl px-4 py-10">
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <h1 class="text-3xl font-bold">{{ pageText.title }}</h1>
                <div class="flex flex-col items-start gap-2 sm:flex-row sm:items-center sm:gap-3">
                    <span class="text-sm font-medium text-gray-600 dark:text-neutral-300">{{ pageText.languageLabel }}</span>
                    <div class="flex items-center gap-2">
                        <span class="text-2xl leading-none" aria-hidden="true">{{ selectedLanguageConfig.flag }}</span>
                        <select
                            v-model="selectedLanguage"
                            class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/60 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100"
                            :aria-label="pageText.languageLabel"
                        >
                            <option v-for="language in languages" :key="language.value" :value="language.value">
                                {{ language.flag }} {{ language.label }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>
            <p class="mb-8 text-sm text-gray-500 dark:text-neutral-400">{{ pageText.languageNotice }}</p>

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
