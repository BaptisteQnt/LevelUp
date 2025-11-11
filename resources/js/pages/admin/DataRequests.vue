<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { computed, reactive } from 'vue';
import { Trash2 } from 'lucide-vue-next';

type DataRequest = {
    id: number;
    request_type: 'account_deletion' | 'data_deletion';
    details: string | null;
    status: 'pending' | 'in_progress' | 'resolved';
    admin_notes: string | null;
    created_at: string | null;
    resolved_at: string | null;
    user: {
        id: number;
        name: string | null;
        username: string;
        email: string;
    };
};

type FlashMessage = {
    success?: string | null;
    error?: string | null;
};

const props = defineProps<{
    requests: DataRequest[];
    flash?: FlashMessage;
}>();

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    {
        title: 'Dashboard',
        href: route('dashboard'),
    },
    {
        title: 'Demandes RGPD',
        href: route('admin.privacy.requests.index'),
    },
]);

const statusOptions: { value: DataRequest['status']; label: string }[] = [
    { value: 'pending', label: 'En attente' },
    { value: 'in_progress', label: 'En cours' },
    { value: 'resolved', label: 'Résolue' },
];

const typeLabels: Record<DataRequest['request_type'], string> = {
    account_deletion: 'Suppression du compte et des données personnelles',
    data_deletion: 'Suppression du compte et des données personnelles',
};

const initialForms = Object.fromEntries(
    props.requests.map((request) => [
        request.id,
        {
            status: request.status,
            admin_notes: request.admin_notes ?? '',
        },
    ]),
) as Record<number, { status: DataRequest['status']; admin_notes: string }>;

const forms = reactive(initialForms);

const flash = computed(() => props.flash);

const formatDate = (value: string | null) =>
    value
        ? new Intl.DateTimeFormat('fr-FR', {
              dateStyle: 'medium',
              timeStyle: 'short',
          }).format(new Date(value))
        : '—';

const updateRequest = (id: number) => {
    const payload = forms[id];

    router.patch(route('admin.privacy.requests.update', id), payload, {
        preserveScroll: true,
    });
};

const deleteAccount = (id: number) => {
    const confirmation = confirm(
        'Confirmez-vous la suppression définitive du compte, de toutes ses données personnelles et de ses interactions (commentaires, likes, notes) ? Cette action est irréversible.',
    );

    if (!confirmation) {
        return;
    }

    router.delete(route('admin.privacy.requests.destroy_user', id), {
        preserveScroll: true,
        preserveState: false,
    });
};
</script>

<template>
    <Head title="Demandes RGPD" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <div v-if="flash?.success" class="rounded-lg border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200">
                {{ flash.success }}
            </div>
            <div v-if="flash?.error" class="rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-700 dark:bg-red-900/40 dark:text-red-200">
                {{ flash.error }}
            </div>

            <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                <header class="border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-neutral-800 dark:bg-neutral-950">
                    <h1 class="text-lg font-semibold">Demandes de suppression de données</h1>
                    <p class="text-sm text-gray-600 dark:text-neutral-400">
                        Consultez et traitez les demandes envoyées par les membres pour exercer leurs droits RGPD.
                    </p>
                </header>

                <div v-if="props.requests.length === 0" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-neutral-400">
                    Aucune demande à afficher pour le moment.
                </div>

                <ul v-else class="divide-y divide-gray-200 dark:divide-neutral-800">
                    <li
                        v-for="request in props.requests"
                        :key="request.id"
                        class="flex flex-col gap-6 px-6 py-5 lg:flex-row lg:items-start lg:justify-between"
                    >
                        <div class="space-y-4 lg:max-w-2xl">
                            <div class="space-y-1">
                                <h2 class="text-base font-semibold text-gray-900 dark:text-neutral-100">
                                    {{ typeLabels[request.request_type] }}
                                </h2>
                                <p class="text-sm text-gray-600 dark:text-neutral-300">
                                    <span class="font-medium text-gray-900 dark:text-neutral-100">{{ request.user.name ?? request.user.username }}</span>
                                    • @{{ request.user.username }} • {{ request.user.email }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-neutral-400">
                                    Créée le {{ formatDate(request.created_at) }}
                                    <span v-if="request.resolved_at"> • Résolue le {{ formatDate(request.resolved_at) }}</span>
                                </p>
                            </div>
                            <p v-if="request.details" class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700 dark:border-neutral-800 dark:bg-neutral-900 dark:text-neutral-200">
                                {{ request.details }}
                            </p>
                        </div>

                        <form class="flex-1 space-y-5" @submit.prevent="updateRequest(request.id)">
                            <section
                                class="space-y-4 rounded-xl border border-red-200 bg-red-50 p-5 text-red-900 shadow-sm dark:border-red-800/80 dark:bg-red-950/40 dark:text-red-100"
                            >
                                <header class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="flex items-start gap-3">
                                        <span
                                            class="flex size-10 items-center justify-center rounded-full bg-red-100 text-red-700 dark:bg-red-900/60 dark:text-red-200"
                                        >
                                            <Trash2 class="size-5" aria-hidden="true" />
                                        </span>
                                        <div class="space-y-1">
                                            <p class="text-xs font-semibold uppercase tracking-wide">Suppression définitive</p>
                                            <p class="text-sm text-red-800 dark:text-red-100/80">
                                                Supprime immédiatement le compte, toutes les données personnelles et l'ensemble
                                                des interactions associées.
                                            </p>
                                        </div>
                                    </div>

                                    <button
                                        type="button"
                                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow transition hover:bg-red-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600"
                                        @click="deleteAccount(request.id)"
                                    >
                                        <Trash2 class="size-4" aria-hidden="true" />
                                        Supprimer le compte et les données personnelles
                                    </button>
                                </header>
                                <p class="text-xs text-red-700/80 dark:text-red-200/70">
                                    Action irréversible : aucune restauration ne sera possible après la confirmation.
                                </p>
                            </section>

                            <div class="space-y-2">
                                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-neutral-300">
                                    Statut de la demande
                                </label>
                                <select
                                    v-model="forms[request.id].status"
                                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30 dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100"
                                >
                                    <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                                        {{ option.label }}
                                    </option>
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-neutral-300">
                                    Notes internes
                                </label>
                                <textarea
                                    v-model="forms[request.id].admin_notes"
                                    rows="4"
                                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30 dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100"
                                    placeholder="Ajoutez des précisions sur le traitement de la demande."
                                ></textarea>
                            </div>

                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600"
                            >
                                Enregistrer les modifications
                            </button>
                        </form>
                    </li>
                </ul>
            </section>
        </div>
    </AppLayout>
</template>
