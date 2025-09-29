<script setup lang="ts">
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

// Props du jeu et des flash messages
const props = defineProps<{
    game: {
        id: number;
        title: string;
        cover_url: string | null;
        summary: string | null;
        storyline: string | null;
        description: string | null;
        comments: {
            id: number;
            content: string;
            user: {
                username: string;
            };
        }[];
        ratings: {
            enabled: boolean;

            average: number | null;
            count: number;
            user: number | null;
        };
    };
    flash?: string | null;
}>();

const page = usePage();
const auth = page.props.auth;

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    {
        title: 'Accueil',
        href: '/',
    },
    {
        title: 'Jeux',
        href: '/games',
    },
    {
        title: props.game.title,
        href: page.url,
    },
]);

// Formulaire d'ajout de commentaire
const form = useForm({
    content: '',
    game_id: props.game.id,
});

// Envoi du commentaire
const submit = () => {
    form.post(route('comments.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset('content'),
    });
};

// Suppression d’un commentaire
const deleteComment = (id: number) => {
    if (confirm('Supprimer ce commentaire ?')) {
        router.delete(route('comments.destroy', id), {
            preserveScroll: true,
        });
    }
};

const displayText = computed(() => {
    const parts = [props.game.storyline, props.game.summary, props.game.description].filter(
        (value): value is string => Boolean(value && value.trim())
    );

    if (!parts.length) {
        return null;
    }

    return parts.join('\n\n');
});

const ratingForm = useForm({
    rating: props.game.ratings.enabled ? props.game.ratings.user ?? null : null,
});

const userRating = ref<number | null>(
    props.game.ratings.enabled ? props.game.ratings.user ?? null : null
);

watch(
    () => props.game.ratings,
    (value) => {
        if (!value.enabled) {
            ratingForm.rating = null;
            userRating.value = null;

            return;
        }

        ratingForm.rating = value.user ?? null;
        userRating.value = value.user ?? null;
    },
    { deep: true }

);

const stars = computed(() => Array.from({ length: 10 }, (_, index) => index + 1));

const ratingSummary = computed(() => {
    if (!props.game.ratings.enabled) {
        return 'Les notes ne sont pas disponibles pour le moment.';
    }


    const { average, count } = props.game.ratings;

    if (!count || average === null) {
        return 'Aucune note pour le moment.';
    }

    const suffix = count > 1 ? 'notes' : 'note';

    return `Moyenne : ${average}/10 (${count} ${suffix})`;
});

const setRating = (value: number) => {
    if (!props.game.ratings.enabled || !auth.user) {

        return;
    }

    ratingForm.rating = value;
    userRating.value = value;

    ratingForm.post(route('games.rating.store', props.game.id), {
        preserveScroll: true,
        onError: () => {
            userRating.value = props.game.ratings.user ?? null;
        },
    });
};
</script>

<template>
    <Head :title="game.title" />

    <AppHeaderLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-4xl px-4 py-10">
            <!-- Flash message -->
            <div
                v-if="flash"
                class="mb-6 rounded border border-green-300 bg-green-100 px-4 py-2 text-green-800"
            >
                {{ flash }}
            </div>

            <!-- Titre -->
            <h1 class="mb-4 text-3xl font-bold">{{ game.title }}</h1>

            <!-- Image -->
            <img
                v-if="game.cover_url"
                :src="`https:${game.cover_url.replace('t_thumb', 't_cover_big')}`"
                alt="Couverture du jeu"
                class="mb-6 rounded-lg shadow-md"
            />

            <!-- Description -->
            <p class="mb-10 whitespace-pre-line text-lg text-gray-700">
                {{ displayText ?? 'Aucune description disponible.' }}
            </p>

            <!-- Notes des utilisateurs -->
            <div class="mb-10 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-xl font-semibold">Note des joueurs</h2>
                        <p class="text-sm text-gray-600">{{ ratingSummary }}</p>
                    </div>
                    <div
                        class="flex items-center gap-1"
                        role="group"
                        aria-label="Noter ce jeu sur dix"
                    >
                        <button
                            v-for="star in stars"
                            :key="star"
                            type="button"

                            :disabled="
                                ratingForm.processing || !auth.user || !game.ratings.enabled
                            "

                            @click="setRating(star)"
                            class="text-2xl transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500"
                            :class="[
                                userRating !== null && star <= userRating
                                    ? 'text-yellow-400'
                                    : 'text-gray-300',

                                auth.user && game.ratings.enabled
                                    ? 'hover:text-yellow-500'
                                    : 'cursor-not-allowed opacity-70',

                            ]"
                        >
                            <span aria-hidden="true">★</span>
                            <span class="sr-only">Attribuer la note {{ star }}/10</span>
                        </button>
                    </div>
                </div>
                <p v-if="!game.ratings.enabled" class="mt-2 text-sm text-gray-500">
                    Les notes seront disponibles après la mise à jour de la base de données.
                </p>
                <p v-else-if="auth.user" class="mt-2 text-sm text-gray-600">

                    <span v-if="userRating !== null">Ta note : {{ userRating }}/10</span>
                    <span v-else>Clique sur une étoile pour noter ce jeu.</span>
                </p>
                <p v-else class="mt-2 text-sm text-gray-500">
                    Connecte-toi pour attribuer une note.
                </p>
                <p v-if="ratingForm.errors.rating" class="mt-2 text-sm text-red-500">
                    {{ ratingForm.errors.rating }}
                </p>
            </div>

            <!-- Zone de commentaires -->
            <div class="mt-8">
                <h2 class="mb-4 text-xl font-bold">Commentaires</h2>

                <!-- Formulaire -->
                <form v-if="auth.user" @submit.prevent="submit" class="mb-6">
                    <textarea
                        v-model="form.content"
                        rows="4"
                        class="w-full resize-none rounded border border-gray-300 p-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Écris un commentaire..."
                    ></textarea>
                    <div class="mt-2 flex items-center justify-between">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 disabled:opacity-50"
                        >
                            Publier
                        </button>
                        <p class="text-sm text-red-500" v-if="form.errors.content">
                            {{ form.errors.content }}
                        </p>
                    </div>
                </form>

                <!-- Liste des commentaires -->
                <div v-if="game.comments.length">
                    <div
                        v-for="comment in game.comments"
                        :key="comment.id"
                        class="mb-4 border-b pb-2"
                    >
                        <div class="flex items-center justify-between">
                            <p class="font-semibold text-blue-700">@{{ comment.user.username }}</p>
                            <button
                                v-if="
                                    auth.user &&
                                    (auth.user.username === comment.user.username || auth.user.is_admin)
                                "
                                @click="deleteComment(comment.id)"
                                class="text-sm text-red-500 hover:underline"
                            >
                                Supprimer
                            </button>
                        </div>
                        <p class="text-gray-800">{{ comment.content }}</p>
                    </div>
                </div>
                <p v-else class="text-gray-500">Aucun commentaire pour ce jeu.</p>
            </div>
        </div>
    </AppHeaderLayout>
</template>
