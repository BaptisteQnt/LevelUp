<script setup lang="ts">
import { useForm, usePage, Head, router } from '@inertiajs/vue3';

// Props du jeu et des flash messages
const props = defineProps<{
    game: {
        id: number;
        title: string;
        cover_url: string | null;
        description: string | null;
        comments: {
            id: number;
            content: string;
            user: {
                username: string;
            };
        }[];
    };
    flash?: string | null;
}>();

const page = usePage();
const auth = page.props.auth;

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
</script>

<template>
    <Head :title="game.title" />

    <div class="max-w-4xl mx-auto px-4 py-10">
        <!-- Flash message -->
        <div
            v-if="flash"
            class="mb-6 px-4 py-2 rounded bg-green-100 text-green-800 border border-green-300"
        >
            {{ flash }}
        </div>

        <!-- Titre -->
        <h1 class="text-3xl font-bold mb-4">{{ game.title }}</h1>

        <!-- Image -->
        <img
            v-if="game.cover_url"
            :src="`https:${game.cover_url.replace('t_thumb', 't_cover_big')}`"
            alt="Couverture du jeu"
            class="mb-6 rounded-lg shadow-md"
        />

        <!-- Description -->
        <p class="text-lg text-gray-700 mb-10">
            {{ game.description ?? 'Aucune description disponible.' }}
        </p>

        <!-- Zone de commentaires -->
        <div class="mt-8">
            <h2 class="text-xl font-bold mb-4">Commentaires</h2>

            <!-- Formulaire -->
            <form v-if="auth.user" @submit.prevent="submit" class="mb-6">
                <textarea
                    v-model="form.content"
                    rows="4"
                    class="w-full p-3 border border-gray-300 rounded resize-none focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Écris un commentaire..."
                ></textarea>
                <div class="mt-2 flex justify-between items-center">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50"
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
                    <div class="flex justify-between items-center">
                        <p class="font-semibold text-blue-700">@{{ comment.user.username }}</p>
                        <button
                            v-if="auth.user && auth.user.username === comment.user.username"
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
</template>
