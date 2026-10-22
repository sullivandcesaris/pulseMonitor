<script setup lang="ts">
import { useAppearance } from '@/composables/useAppearance';
import { useLanguage } from '@/composables/useLanguage';
import { Monitor, Moon, Sun } from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
    class?: string;
}

const { class: containerClass = '' } = defineProps<Props>();

const { appearance, updateAppearance } = useAppearance();
const { isPortuguese } = useLanguage();

const tabs = computed(
    () =>
        [
            { value: 'light', Icon: Sun, label: isPortuguese.value ? 'Claro' : 'Light' },
            { value: 'dark', Icon: Moon, label: isPortuguese.value ? 'Escuro' : 'Dark' },
            { value: 'system', Icon: Monitor, label: isPortuguese.value ? 'Sistema' : 'System' },
        ] as const,
);
</script>

<template>
    <div :class="['inline-flex gap-1 rounded-lg bg-neutral-100 p-1 dark:bg-neutral-800', containerClass]">
        <button
            v-for="{ value, Icon, label } in tabs"
            :key="value"
            @click="updateAppearance(value)"
            :class="[
                'flex items-center rounded-md px-3.5 py-1.5 transition-colors',
                appearance === value
                    ? 'bg-white shadow-sm dark:bg-neutral-700 dark:text-neutral-100'
                    : 'text-neutral-500 hover:bg-neutral-200/60 hover:text-black dark:text-neutral-400 dark:hover:bg-neutral-700/60',
            ]"
        >
            <component :is="Icon" class="-ml-1 h-4 w-4" />
            <span class="ml-1.5 text-sm">{{ label }}</span>
        </button>
    </div>
</template>
