<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps<{
    comments: {
        data: Array<{
            id: number;
            content: string;
            created_at: string;
            user: { id: number; username: string };
            game: { id: number; title: string; slug: string };
        }>;
        links: Array<{
            url: string | null;
            label: string;
            active: boolean;
        }>;
        meta: {
            current_page: number;
            last_page: number;
            per_page: number;
            total: number;
        };
    };
    flash?: {
        success?: string | null;
        error?: string | null;
    };
}>();

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    {
        title: 'Dashboard',
        href: route('dashboard'),
    },
    {
        title: 'Modération des commentaires',
        href: route('admin.comments.index'),
    },
]);

const formatDate = (value: string) =>
    new Intl.DateTimeFormat('fr-FR', {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(new Date(value));

const deleteComment = (id: number) => {
    if (confirm('Supprimer ce commentaire ?')) {
        router.delete(route('comments.destroy', id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Modération des commentaires" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <div v-if="flash?.success" class="rounded-lg border border-green-300 bg-green-100 px-4 py-3 text-sm text-green-700">
                {{ flash.success }}
            </div>
            <div v-if="flash?.error" class="rounded-lg border border-red-300 bg-red-100 px-4 py-3 text-sm text-red-700">
                {{ flash.error }}
            </div>

            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-neutral-800 dark:bg-neutral-950">
                    <h1 class="text-lg font-semibold">Tous les commentaires ({{ comments.meta.total }})</h1>
                    <p class="text-sm text-gray-600 dark:text-neutral-400">
                        Consultez et modérez les derniers avis laissés sur les jeux de la plateforme.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-sm dark:divide-neutral-800">
                        <thead class="bg-gray-50 text-xs font-medium uppercase tracking-wide text-gray-500 dark:bg-neutral-950 dark:text-neutral-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">Jeu</th>
                                <th scope="col" class="px-6 py-3">Auteur</th>
                                <th scope="col" class="px-6 py-3">Commentaire</th>
                                <th scope="col" class="px-6 py-3">Publié le</th>
                                <th scope="col" class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-neutral-800">
                            <tr v-for="comment in comments.data" :key="comment.id" class="bg-white dark:bg-neutral-900">
                                <td class="px-6 py-4">
                                    <Link :href="route('games.show', { slug: comment.game.slug })" class="font-medium text-blue-600 hover:underline">
                                        {{ comment.game.title }}
                                    </Link>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-700 dark:text-neutral-200">
                                    @{{ comment.user.username }}
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-neutral-300">
                                    {{ comment.content }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-neutral-400">
                                    {{ formatDate(comment.created_at) }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button
                                        type="button"
                                        class="rounded border border-red-200 px-3 py-1 text-sm font-medium text-red-600 transition hover:bg-red-50 dark:border-red-800 dark:text-red-400 dark:hover:bg-red-900/20"
                                        @click="deleteComment(comment.id)"
                                    >
                                        Supprimer
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="comments.data.length === 0">
                                <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-neutral-400">
                                    Aucun commentaire n'a encore été publié sur la plateforme.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <nav v-if="comments.meta.last_page > 1" class="flex justify-center">
                <ul class="flex items-center gap-2">
                    <li v-for="link in comments.links" :key="link.label">
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
    </AppLayout>
</template>
