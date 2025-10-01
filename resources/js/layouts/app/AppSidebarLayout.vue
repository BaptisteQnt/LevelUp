<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppFooter from '@/components/AppFooter.vue';
import AppHeader from '@/components/AppHeader.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppShell variant="sidebar">
        <div class="flex min-h-screen w-full flex-col">
            <AppHeader />
            <div class="flex flex-1">
                <AppSidebar />
                <AppContent variant="sidebar">
                    <AppSidebarHeader :breadcrumbs="breadcrumbs" />
                    <slot />
                </AppContent>
            </div>
            <AppFooter />
        </div>
    </AppShell>
</template>
