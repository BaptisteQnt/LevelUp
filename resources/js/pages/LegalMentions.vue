<script setup lang="ts">
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

type DataRequest = {
    id: number;
    request_type: 'account_deletion' | 'data_deletion';
    details: string | null;
    status: 'pending' | 'in_progress' | 'resolved';
    admin_notes: string | null;
    created_at: string | null;
    resolved_at: string | null;
};

type FlashMessage = {
    success?: string | null;
    error?: string | null;
};

const props = defineProps<{
    requests: DataRequest[];
    flash?: FlashMessage;
}>();

const page = usePage<{ auth: { user: unknown }; flash?: FlashMessage }>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Accueil',
        href: '/',
    },
    {
        title: 'Mentions légales',
        href: route('legal.mentions'),
    },
];

const form = useForm({
    details: '',
});

const isAuthenticated = computed(() => Boolean(page.props.auth?.user));

const flash = computed<FlashMessage | undefined>(() => props.flash ?? page.props.flash);

const submit = () => {
    form.post(route('legal.requests.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('details');
        },
    });
};

const requestTypeLabels: Record<DataRequest['request_type'], string> = {
    account_deletion: 'Suppression du compte et des données personnelles',
    data_deletion: 'Suppression du compte et des données personnelles',
};

const statusLabels: Record<DataRequest['status'], string> = {
    pending: 'En attente',
    in_progress: 'En cours de traitement',
    resolved: 'Résolue',
};

const formatDate = (value: string | null) =>
    value
        ? new Intl.DateTimeFormat('fr-FR', {
              dateStyle: 'medium',
              timeStyle: 'short',
          }).format(new Date(value))
        : '—';
</script>

<template>
    <Head title="Mentions légales" />

    <AppHeaderLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto flex max-w-5xl flex-col gap-8 px-4 py-10">
            <header class="space-y-3">
                <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">Mentions légales</h1>
                <p class="text-base text-neutral-600 dark:text-neutral-300">
                    Retrouvez ici les informations juridiques relatives à LevelUp ainsi que le formulaire pour exercer vos droits
                    sur vos données personnelles.
                </p>
            </header>

            <section class="space-y-4 rounded-xl border border-sidebar-border/70 bg-white/80 p-6 shadow-sm backdrop-blur dark:border-sidebar-border dark:bg-neutral-900/80">
                <h2 class="text-2xl font-semibold text-neutral-900 dark:text-neutral-100">Éditeur du site</h2>
                <p class="text-neutral-600 dark:text-neutral-300">
                    LevelUp est édité par LevelUp SAS, société immatriculée au RCS de Paris sous le numéro 123&nbsp;456&nbsp;789, dont le
                    siège social est situé au 42 avenue du Gaming, 75000 Paris. Directeur de la publication&nbsp;: Alex Martin.
                </p>
                <div class="grid gap-2 text-neutral-600 dark:text-neutral-300 sm:grid-cols-2">
                    <p><span class="font-semibold">Email&nbsp;:</span> contact@levelup.gg</p>
                    <p><span class="font-semibold">Téléphone&nbsp;:</span> +33&nbsp;1&nbsp;23&nbsp;45&nbsp;67&nbsp;89</p>
                    <p><span class="font-semibold">Hébergeur&nbsp;:</span> OVHcloud, 2 rue Kellermann, 59100 Roubaix</p>
                    <p><span class="font-semibold">Capital social&nbsp;:</span> 50&nbsp;000&nbsp;€</p>
                </div>
            </section>

            <section class="space-y-4 rounded-xl border border-sidebar-border/70 bg-white/80 p-6 shadow-sm backdrop-blur dark:border-sidebar-border dark:bg-neutral-900/80">
                <h2 class="text-2xl font-semibold text-neutral-900 dark:text-neutral-100">Protection des données</h2>
                <p class="text-neutral-600 dark:text-neutral-300">
                    Conformément au Règlement Général sur la Protection des Données (RGPD) et à la loi Informatique et Libertés, vous disposez
                    d'un droit d'accès, de rectification, de suppression et de portabilité de vos données. Vous pouvez également vous opposer
                    ou limiter leur traitement.
                </p>
                <p class="text-neutral-600 dark:text-neutral-300">
                    LevelUp met en œuvre toutes les mesures nécessaires pour protéger vos données et ne les conserve que pour la durée strictement
                    nécessaire à la fourniture de ses services. Pour toute demande complémentaire, contactez notre délégué à la protection des données
                    à l'adresse dpo@levelup.gg.
                </p>
            </section>

            <section class="space-y-6 rounded-xl border border-blue-200/70 bg-blue-50/80 p-6 shadow-sm backdrop-blur dark:border-blue-900/70 dark:bg-blue-950/50">
                <div class="space-y-2">
                    <h2 class="text-2xl font-semibold text-blue-900 dark:text-blue-100">Exercer vos droits</h2>
                    <p class="text-neutral-700 dark:text-neutral-200">
                        Utilisez le formulaire ci-dessous pour demander la suppression de votre compte et de toutes vos données personnelles. Notre équipe
                        vous répondra dans les meilleurs délais.
                    </p>
                </div>

                <div v-if="flash?.success" class="rounded-lg border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200">
                    {{ flash.success }}
                </div>
                <div v-if="flash?.error" class="rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-700 dark:bg-red-900/40 dark:text-red-200">
                    {{ flash.error }}
                </div>

                <div v-if="isAuthenticated" class="space-y-6">
                    <form class="space-y-5" @submit.prevent="submit">
                        <p class="rounded-lg border border-blue-200 bg-white/80 px-4 py-3 text-sm text-neutral-700 shadow-sm dark:border-blue-900/70 dark:bg-blue-950/40 dark:text-neutral-200">
                            Cette demande provoquera la suppression définitive de votre compte et de l'ensemble de vos données personnelles, y compris vos interactions (commentaires, likes, notes).
                        </p>

                        <div class="space-y-2">
                            <label for="details" class="block text-sm font-semibold text-neutral-800 dark:text-neutral-200">
                                Précisions complémentaires (facultatif)
                            </label>
                            <textarea
                                id="details"
                                v-model="form.details"
                                rows="4"
                                class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-800 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30 dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100"
                                placeholder="Expliquez votre demande pour nous aider à la traiter rapidement."
                            ></textarea>
                            <p v-if="form.errors.details" class="text-sm text-red-500">{{ form.errors.details }}</p>
                        </div>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-70 dark:bg-blue-500 dark:hover:bg-blue-400"
                        >
                            {{ form.processing ? 'Envoi en cours…' : 'Envoyer ma demande' }}
                        </button>
                    </form>

                    <section class="space-y-4">
                        <h3 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">Historique de vos demandes</h3>
                        <p v-if="props.requests.length === 0" class="text-sm text-neutral-600 dark:text-neutral-300">
                            Vous n'avez pas encore soumis de demande de suppression.
                        </p>
                        <div v-else class="space-y-4">
                            <article
                                v-for="request in props.requests"
                                :key="request.id"
                                class="rounded-lg border border-neutral-200 bg-white px-4 py-4 shadow-sm dark:border-neutral-800 dark:bg-neutral-900"
                            >
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                    <h4 class="text-base font-semibold text-neutral-900 dark:text-neutral-100">
                                        {{ requestTypeLabels[request.request_type] }}
                                    </h4>
                                    <span
                                        class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold"
                                        :class="{
                                            'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-200': request.status === 'pending',
                                            'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-200': request.status === 'in_progress',
                                            'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200': request.status === 'resolved',
                                        }"
                                    >
                                        {{ statusLabels[request.status] }}
                                    </span>
                                </div>
                                <p v-if="request.details" class="mt-2 text-sm text-neutral-700 dark:text-neutral-200">{{ request.details }}</p>
                                <dl class="mt-3 grid gap-2 text-xs text-neutral-500 dark:text-neutral-400 sm:grid-cols-2">
                                    <div>
                                        <dt class="font-semibold uppercase tracking-wide">Créée le</dt>
                                        <dd>{{ formatDate(request.created_at) }}</dd>
                                    </div>
                                    <div>
                                        <dt class="font-semibold uppercase tracking-wide">Résolue le</dt>
                                        <dd>{{ formatDate(request.resolved_at) }}</dd>
                                    </div>
                                    <div v-if="request.admin_notes" class="sm:col-span-2">
                                        <dt class="font-semibold uppercase tracking-wide">Message de l'équipe</dt>
                                        <dd class="text-neutral-700 dark:text-neutral-200">{{ request.admin_notes }}</dd>
                                    </div>
                                </dl>
                            </article>
                        </div>
                    </section>
                </div>
                <div v-else class="rounded-lg border border-neutral-200 bg-white/70 px-4 py-5 text-sm text-neutral-700 shadow-sm dark:border-neutral-800 dark:bg-neutral-900/70 dark:text-neutral-200">
                    Connectez-vous pour accéder au formulaire et suivre vos demandes de suppression.
                </div>
            </section>
        </div>
    </AppHeaderLayout>
</template>
