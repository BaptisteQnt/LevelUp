<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import type { BreadcrumbItem, NavItem, SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { Menu, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Props {
    breadcrumbs?: BreadcrumbItem[];
}

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage<SharedData>();
const user = computed(() => page.props.auth.user);

const mainNavItems: NavItem[] = [
    {
        title: 'Accueil',
        href: '/',
    },
    {
        title: 'Jeux',
        href: '/games',
    },
    {
        title: 'Information',
        href: '/information',
    },
    {
        title: 'Présentation',
        href: '/presentation',
    },
];

const isCurrentRoute = (href: string) => page.url === href;

const mobileMenuOpen = ref(false);

const toggleMobileMenu = () => {
    mobileMenuOpen.value = !mobileMenuOpen.value;
};

const closeMobileMenu = () => {
    mobileMenuOpen.value = false;
};
</script>

<template>
    <div class="border-b border-sidebar-border/80 bg-white/80 backdrop-blur dark:bg-neutral-950/70">
        <div class="mx-auto flex h-16 items-center justify-between px-4 md:max-w-7xl">
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    class="inline-flex items-center justify-center rounded-md p-2 text-neutral-700 transition hover:bg-neutral-100 focus:outline-none focus:ring-2 focus:ring-primary lg:hidden dark:text-neutral-200 dark:hover:bg-neutral-800"
                    :aria-expanded="mobileMenuOpen"
                    aria-controls="primary-navigation"
                    @click="toggleMobileMenu"
                >
                    <span class="sr-only">Basculer la navigation</span>
                    <Menu v-if="!mobileMenuOpen" class="h-6 w-6" />
                    <X v-else class="h-6 w-6" />
                </button>
                <Link :href="route('home')" class="flex items-center gap-2" @click="closeMobileMenu">
                    <AppLogo />
                </Link>
            </div>

            <nav class="hidden items-center gap-6 text-sm font-medium lg:flex" aria-label="Navigation principale">
                <Link
                    v-for="item in mainNavItems"
                    :key="item.title"
                    :href="item.href"
                    class="transition hover:text-primary"
                    :class="isCurrentRoute(item.href) ? 'text-primary' : 'text-neutral-600 dark:text-neutral-300'"
                >
                    {{ item.title }}
                </Link>
                <Link
                    v-if="!user"
                    :href="route('login')"
                    class="rounded-lg bg-primary px-4 py-2 font-semibold text-white shadow hover:bg-primary/90"
                >
                    Connexion
                </Link>
                <Link
                    v-else
                    method="post"
                    as="button"
                    type="button"
                    :href="route('logout')"
                    class="rounded-lg bg-red-500 px-4 py-2 font-semibold text-white shadow hover:bg-red-500/90"
                >
                    Déconnexion
                </Link>
            </nav>
        </div>

        <div
            v-if="mobileMenuOpen"
            id="primary-navigation"
            class="border-t border-sidebar-border/80 bg-white/95 px-4 py-4 shadow-lg lg:hidden dark:bg-neutral-950/95"
        >
            <div class="flex flex-col gap-3 text-base font-medium">
                <Link
                    v-for="item in mainNavItems"
                    :key="item.title"
                    :href="item.href"
                    class="rounded-lg px-3 py-2 hover:bg-neutral-100 dark:hover:bg-neutral-800"
                    :class="isCurrentRoute(item.href) ? 'bg-neutral-100 font-semibold dark:bg-neutral-800' : 'text-neutral-700 dark:text-neutral-200'"
                    @click="closeMobileMenu"
                >
                    {{ item.title }}
                </Link>
                <Link
                    v-if="!user"
                    :href="route('login')"
                    class="rounded-lg bg-primary px-3 py-2 text-center font-semibold text-white hover:bg-primary/90"
                    @click="closeMobileMenu"
                >
                    Connexion
                </Link>
                <Link
                    v-else
                    method="post"
                    as="button"
                    type="button"
                    :href="route('logout')"
                    class="rounded-lg bg-red-500 px-3 py-2 text-center font-semibold text-white hover:bg-red-500/90"
                    @click="closeMobileMenu"
                >
                    Déconnexion
                </Link>
            </div>
        </div>

        <div v-if="props.breadcrumbs.length > 1" class="flex w-full border-t border-sidebar-border/70 bg-white/60 dark:bg-neutral-950/60">
            <div class="mx-auto flex h-12 w-full items-center justify-start px-4 text-neutral-500 md:max-w-7xl">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </div>
        </div>
    </div>
</template>
