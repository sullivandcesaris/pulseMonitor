<script setup lang="ts">
import { useLanguage } from '@/composables/useLanguage';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps<{ title?: string; description?: string }>();
const { language, isPortuguese, setLanguage } = useLanguage();
const brandCopy = computed(() =>
    isPortuguese.value
        ? {
              eyebrow: 'PulseMonitor',
              title: 'Observe. Entenda. Aja.',
              text: 'Monitoramento confiável para que sua equipe saiba o que está acontecendo antes dos seus clientes.',
              footer: 'Voltar ao portfólio',
          }
        : {
              eyebrow: 'PulseMonitor',
              title: 'Observe. Understand. Act.',
              text: 'Reliable monitoring so your team knows what is happening before your customers do.',
              footer: 'Back to portfolio',
          },
);
</script>

<template>
    <div class="min-h-svh bg-[#f5f7f4] text-[#17221c] dark:bg-[#0b110e] dark:text-[#edf5ef] lg:grid lg:grid-cols-[0.9fr_1.1fr]">
        <aside class="relative hidden overflow-hidden bg-[#173b2b] p-10 text-white lg:flex lg:flex-col lg:justify-between xl:p-16">
            <div class="pointer-events-none absolute -bottom-32 -right-20 h-96 w-96 rounded-full bg-[#c9f36b]/20 blur-3xl" />
            <div class="relative">
                <Link href="/" class="flex items-center gap-3 font-semibold tracking-tight"
                    ><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#c9f36b] text-[#173b2b]">P</span
                    ><span>{{ brandCopy.eyebrow }}</span></Link
                >
                <div class="mt-24 max-w-md">
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#c9f36b]">{{ brandCopy.eyebrow }}</p>
                    <h2 class="mt-5 text-5xl font-semibold leading-[1.02] tracking-[-0.05em]">{{ brandCopy.title }}</h2>
                    <p class="mt-7 text-lg leading-8 text-[#c0d5c5]">{{ brandCopy.text }}</p>
                </div>
            </div>
            <Link href="/" class="relative text-sm text-[#c0d5c5] transition hover:text-[#c9f36b]">← {{ brandCopy.footer }}</Link>
        </aside>
        <main class="flex min-h-svh items-center justify-center px-6 py-10 sm:px-10">
            <div class="w-full max-w-md">
                <div class="mb-8 flex items-center justify-between lg:justify-end">
                    <Link href="/" class="flex items-center gap-2 font-semibold lg:hidden"
                        ><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#173b2b] text-sm text-[#c9f36b]">P</span
                        >PulseMonitor</Link
                    >
                    <div
                        class="flex rounded-full border border-[#d4ded6] bg-white/70 p-1 text-xs font-semibold dark:border-[#2a3a2f] dark:bg-[#111b14]"
                    >
                        <button
                            type="button"
                            class="rounded-full px-3 py-1"
                            :class="language === 'en' ? 'bg-[#173b2b] text-white dark:bg-[#c9f36b] dark:text-[#173b2b]' : 'text-[#587064]'"
                            @click="setLanguage('en')"
                        >
                            EN</button
                        ><button
                            type="button"
                            class="rounded-full px-3 py-1"
                            :class="language === 'pt' ? 'bg-[#173b2b] text-white dark:bg-[#c9f36b] dark:text-[#173b2b]' : 'text-[#587064]'"
                            @click="setLanguage('pt')"
                        >
                            PT
                        </button>
                    </div>
                </div>
                <div class="mb-8">
                    <h1 class="text-3xl font-semibold tracking-tight text-[#173b2b] dark:text-[#eaf6ed]">{{ title }}</h1>
                    <p class="mt-3 text-sm leading-6 text-[#668071] dark:text-[#a9bbb0]">{{ description }}</p>
                </div>
                <div
                    class="rounded-[1.5rem] border border-[#dce5dc] bg-white/75 p-6 shadow-xl shadow-[#173b2b]/5 dark:border-[#203126] dark:bg-[#101a13] sm:p-8"
                >
                    <slot />
                </div>
            </div>
        </main>
    </div>
</template>
