<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { useLanguage } from '@/composables/useLanguage';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { Activity, AlertTriangle, BookOpen, Folder, LayoutGrid, Settings } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';

const { isPortuguese } = useLanguage();
const mainNavItems = computed<NavItem[]>(() => [
    { title: isPortuguese.value ? 'Dashboard' : 'Dashboard', href: '/dashboard', icon: LayoutGrid },
    { title: isPortuguese.value ? 'Monitores' : 'Monitors', href: '/monitors', icon: Activity },
    { title: isPortuguese.value ? 'Incidentes' : 'Incidents', href: '/incidents', icon: AlertTriangle },
    { title: isPortuguese.value ? 'Configurações' : 'Settings', href: '/settings/profile', icon: Settings },
]);
const footerNavItems = computed<NavItem[]>(() => [
    { title: 'Github Repo', href: 'https://github.com/sullivandcesaris/pulseMonitor', icon: Folder },
    { title: isPortuguese.value ? 'Documentação' : 'Documentation', href: '/docs', icon: BookOpen },
]);
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
