<script setup lang="ts">
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue';
import { type BreadcrumbItem } from '@/types';

import { Head, Link as InertiaLink, router, usePage } from '@inertiajs/vue3';

import { computed, onMounted, ref, watch } from 'vue';

const Link = InertiaLink;

interface GameItem {
    id: number;
    title: string;
    slug: string;
    cover_url: string | null;
    summary: string | null;
    storyline: string | null;
    description: string | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedGames {
    data: GameItem[];
    links: PaginationLink[];
    meta: {
        current_page: number;
        last_page: number;
    };
}

const props = defineProps<{
    games: PaginatedGames;
    searchQuery?: string | null;
    searchMessage?: string | null;
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


const page = usePage<{ activeLanguage?: LanguageCode; searchQuery?: string | null }>();

const selectedLanguage = ref<LanguageCode>(page.props.activeLanguage ?? 'en');
const searchTerm = ref(props.searchQuery ?? '');
const isSearching = ref(false);


onMounted(() => {
    if (typeof window === 'undefined') {
        return;
    }

    const storedLanguage = window.localStorage.getItem('levelup_language');

    if (storedLanguage === 'fr' || storedLanguage === 'en') {

        if (storedLanguage !== selectedLanguage.value) {
            selectedLanguage.value = storedLanguage;
        }
    } else {
        window.localStorage.setItem('levelup_language', selectedLanguage.value);
    }
});

watch(selectedLanguage, (language, previousLanguage) => {
    if (language === previousLanguage) {
        return;
    }


    if (typeof window === 'undefined') {
        return;
    }

    window.localStorage.setItem('levelup_language', language);


    if (page.props.activeLanguage === language) {
        return;
    }

    router.visit(route('games.index', {
        lang: language,
        search: searchTerm.value.trim() === '' ? undefined : searchTerm.value.trim(),
    }), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        only: ['games', 'activeLanguage', 'searchQuery', 'searchMessage'],
    });
});

watch(
    () => page.props.activeLanguage,
    (language) => {
        if (!language || language === selectedLanguage.value) {
            return;
        }

        selectedLanguage.value = language;
    }
);


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

const games = computed(() => props.games);
const searchMessage = computed(() => props.searchMessage ?? null);
const hasActiveSearch = computed(() => searchTerm.value.trim().length > 0);

watch(
    () => props.searchQuery,
    (value) => {
        if ((value ?? '') === searchTerm.value) {
            return;
        }

        searchTerm.value = value ?? '';
    }
);

const submitSearch = () => {
    const query = searchTerm.value.trim();
    const normalized = query === '' ? null : query;
    const currentQuery = props.searchQuery ?? null;

    if (normalized === currentQuery) {
        return;
    }

    isSearching.value = true;

    router.visit(
        route('games.index', {
            lang: selectedLanguage.value,
            search: normalized ?? undefined,
        }),
        {
            preserveScroll: true,
            replace: true,
            only: ['games', 'activeLanguage', 'searchQuery', 'searchMessage'],
            onFinish: () => {
                isSearching.value = false;
            },
        }
    );
};

const clearSearch = () => {
    if (searchTerm.value === '') {
        return;
    }

    searchTerm.value = '';
    submitSearch();
};
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

            <form class="mb-8 flex flex-col gap-3 sm:flex-row sm:items-center" @submit.prevent="submitSearch">
                <label class="sr-only" for="game-search">Rechercher un jeu</label>
                <input
                    id="game-search"
                    v-model="searchTerm"
                    type="search"
                    name="search"
                    :placeholder="selectedLanguage.value === 'fr' ? 'Rechercher un jeu…' : 'Search a game…'"
                    class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/60 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100"
                    :disabled="isSearching"
                />
                <div class="flex items-center gap-2">
                    <button
                        type="submit"
                        class="inline-flex items-center rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white shadow transition hover:bg-primary/90 disabled:cursor-not-allowed disabled:opacity-70"
                        :disabled="isSearching"
                    >
                        {{ selectedLanguage.value === 'fr' ? 'Rechercher' : 'Search' }}
                    </button>
                    <button
                        v-if="hasActiveSearch"
                        type="button"
                        class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-100 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-800"
                        @click="clearSearch"
                    >
                        {{ selectedLanguage.value === 'fr' ? 'Réinitialiser' : 'Reset' }}
                    </button>
                </div>
            </form>

            <p v-if="searchMessage" class="mb-6 rounded-lg border border-primary/30 bg-primary/10 px-4 py-3 text-sm text-primary dark:border-primary/40 dark:bg-primary/15">
                {{ searchMessage }}
            </p>
            <p
                v-else-if="hasActiveSearch && games.data.length === 0"
                class="mb-6 rounded-lg border border-dashed border-gray-300 px-4 py-3 text-sm text-gray-600 dark:border-neutral-700 dark:text-neutral-300"
            >
                {{ selectedLanguage.value === 'fr' ? 'Aucun jeu ne correspond à votre recherche pour le moment.' : 'No game matches your search yet.' }}
            </p>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div v-for="game in games.data" :key="game.id" class="rounded-lg bg-white p-4 shadow">
                    <img
                        v-if="game.cover_url"
                        :src="`https:${game.cover_url.replace('t_thumb', 't_cover_big')}`"
                        alt=""
                        class="mb-4 h-auto w-full rounded"
                    />
                    <Link
                        :href="route('games.show', { slug: game.slug, lang: selectedLanguage })"
                        class="text-xl font-semibold text-blue-600 hover:underline"
                    >
                        {{ game.title }}
                    </Link>

                    <p class="text-sm text-gray-600" v-if="game.storyline || game.summary || game.description">
                        {{ game.storyline ?? game.summary ?? game.description }}
                    </p>
                </div>
            </div>

            <nav v-if="games.meta?.last_page > 1" class="mt-10 flex justify-center">
                <ul class="flex items-center gap-2">
                    <li v-for="link in games.links" :key="link.label">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            preserve-state
                            preserve-scroll
                            class="inline-flex items-center rounded-md border px-3 py-1 text-sm font-medium transition"
                            :class="[
                                link.active
                                    ? 'border-primary bg-primary text-white'
                                    : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-100 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-200 dark:hover:bg-neutral-800',
                            ]"
                            v-html="link.label"
                        />
                        <span
                            v-else
                            class="inline-flex items-center rounded-md border border-gray-200 px-3 py-1 text-sm font-medium text-gray-400 dark:border-neutral-800 dark:text-neutral-600"
                            v-html="link.label"
                        />
                    </li>
                </ul>
            </nav>
        </div>
    </AppHeaderLayout>
</template>
