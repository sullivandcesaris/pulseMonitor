<script setup lang="ts">
import type { SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

type Language = 'en' | 'pt';
const page = usePage<SharedData>();
const language = ref<Language>('en');

const copy = computed(() =>
    language.value === 'pt'
        ? {
              docs: 'Documentação',
              login: 'Entrar',
              register: 'Criar conta',
              badge: 'Monitoramento de disponibilidade, com engenharia de verdade',
              title: 'Saiba quando seus serviços estão fora do ar.',
              intro: 'PulseMonitor é uma plataforma de monitoramento HTTP construída para transformar falhas silenciosas em incidentes visíveis e acionáveis.',
              explore: 'Explorar documentação',
              dashboard: 'Acessar dashboard',
              problem: 'O problema',
              problemText:
                  'Equipes precisam de um sinal confiável quando um endpoint fica lento ou indisponível — sem depender de verificações manuais ou de um painel cheio de ruído.',
              solution: 'A solução',
              solutionText:
                  'Monitores executam verificações assíncronas, registram latência e status, detectam falhas consecutivas e acompanham a recuperação automaticamente.',
              stackTitle: 'Construído para ser entendido',
              stackText:
                  'Um monólito modular mantém o primeiro deploy simples sem esconder os limites do domínio. Laravel coordena o produto; Vue e Inertia entregam uma interface rápida; PostgreSQL preserva a verdade; Redis absorve o trabalho assíncrono.',
              ctaTitle: 'Veja as decisões por trás do produto.',
              ctaText: 'A documentação explica o fluxo de dados, as proteções SSRF, a estratégia de filas e o caminho de produção.',
              footer: 'PulseMonitor · um estudo de engenharia aplicado a monitoramento confiável.',
              cards: [
                  ['01', 'Organizações isoladas', 'Políticas e dados separados por organização.'],
                  ['02', 'Workers assíncronos', 'Filas dedicadas para checks e notificações.'],
                  ['03', 'Operação observável', 'Logs estruturados, métricas e health checks.'],
              ],
              stack: ['Laravel 12', 'Vue 3 + Inertia', 'PostgreSQL', 'Redis + Horizon', 'Docker + Caddy', 'Prometheus + Sentry'],
          }
        : {
              docs: 'Documentation',
              login: 'Log in',
              register: 'Create account',
              badge: 'Uptime monitoring, with real engineering underneath',
              title: 'Know when your services go down.',
              intro: 'PulseMonitor is an HTTP monitoring platform built to turn silent failures into visible, actionable incidents.',
              explore: 'Explore documentation',
              dashboard: 'Open dashboard',
              problem: 'The problem',
              problemText:
                  'Teams need a dependable signal when an endpoint becomes slow or unavailable — without manual checks or a noisy control panel.',
              solution: 'The solution',
              solutionText:
                  'Monitors run asynchronous checks, record latency and status, detect consecutive failures, and track recovery automatically.',
              stackTitle: 'Built to be understood',
              stackText:
                  'A modular monolith keeps the first deployment simple without hiding domain boundaries. Laravel coordinates the product; Vue and Inertia deliver a fast interface; PostgreSQL preserves truth; Redis absorbs asynchronous work.',
              ctaTitle: 'See the decisions behind the product.',
              ctaText: 'The documentation explains the data flow, SSRF protections, queue strategy, and path to production.',
              footer: 'PulseMonitor · an engineering case study in reliable monitoring.',
              cards: [
                  ['01', 'Isolated organizations', 'Policies and data stay scoped to each organization.'],
                  ['02', 'Asynchronous workers', 'Dedicated queues for checks and notifications.'],
                  ['03', 'Observable operations', 'Structured logs, metrics, and health checks.'],
              ],
              stack: ['Laravel 12', 'Vue 3 + Inertia', 'PostgreSQL', 'Redis + Horizon', 'Docker + Caddy', 'Prometheus + Sentry'],
          },
);

const setLanguage = (value: Language) => {
    language.value = value;
    window.localStorage.setItem('pulsemonitor-language', value);
};
onMounted(() => {
    const stored = window.localStorage.getItem('pulsemonitor-language');
    if (stored === 'en' || stored === 'pt') language.value = stored;
});
</script>

<template>
    <Head :title="language === 'pt' ? 'Monitoramento confiável' : 'Reliable monitoring'"
        ><meta name="description" :content="copy.intro" /><link rel="preconnect" href="https://rsms.me/" /><link
            rel="stylesheet"
            href="https://rsms.me/inter/inter.css"
    /></Head>
    <div class="min-h-screen overflow-hidden bg-[#f5f7f4] text-[#17221c] dark:bg-[#0b110e] dark:text-[#edf5ef]">
        <header class="relative z-10 mx-auto flex max-w-7xl items-center justify-between px-6 py-6 lg:px-10">
            <Link href="/" class="flex items-center gap-3 font-semibold tracking-tight"
                ><span
                    class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#173b2b] text-sm text-[#c9f36b] shadow-lg shadow-[#173b2b]/20 dark:bg-[#c9f36b] dark:text-[#173b2b]"
                    >P</span
                ><span>PulseMonitor</span></Link
            >
            <nav class="flex items-center gap-3 text-sm">
                <Link
                    href="/docs"
                    class="hidden rounded-full px-4 py-2 text-[#587064] transition hover:bg-white hover:text-[#173b2b] dark:text-[#a9bbb0] dark:hover:bg-[#17241b] dark:hover:text-white sm:inline-block"
                    >{{ copy.docs }}</Link
                >
                <div class="flex rounded-full border border-[#d4ded6] bg-white/70 p-1 dark:border-[#2a3a2f] dark:bg-[#111b14]">
                    <button
                        type="button"
                        class="rounded-full px-3 py-1 text-xs font-semibold"
                        :class="language === 'en' ? 'bg-[#173b2b] text-white dark:bg-[#c9f36b] dark:text-[#173b2b]' : 'text-[#587064]'"
                        @click="setLanguage('en')"
                    >
                        EN</button
                    ><button
                        type="button"
                        class="rounded-full px-3 py-1 text-xs font-semibold"
                        :class="language === 'pt' ? 'bg-[#173b2b] text-white dark:bg-[#c9f36b] dark:text-[#173b2b]' : 'text-[#587064]'"
                        @click="setLanguage('pt')"
                    >
                        PT
                    </button>
                </div>
                <Link
                    v-if="page.props.auth?.user"
                    :href="route('dashboard')"
                    class="hidden rounded-full border border-[#d4ded6] px-4 py-2 font-medium dark:border-[#2a3a2f] sm:inline-block"
                    >{{ copy.dashboard }}</Link
                ><template v-else
                    ><Link :href="route('login')" class="hidden px-3 py-2 text-[#587064] dark:text-[#a9bbb0] sm:inline-block">{{ copy.login }}</Link
                    ><Link
                        :href="route('register')"
                        class="rounded-full bg-[#173b2b] px-4 py-2 font-medium text-white shadow-lg shadow-[#173b2b]/20 transition hover:-translate-y-0.5 dark:bg-[#c9f36b] dark:text-[#173b2b]"
                        >{{ copy.register }}</Link
                    ></template
                >
            </nav>
        </header>
        <main>
            <section class="relative mx-auto max-w-7xl px-6 pb-24 pt-16 lg:px-10 lg:pb-36 lg:pt-24">
                <div class="pointer-events-none absolute -right-32 -top-32 h-96 w-96 rounded-full bg-[#c9f36b]/30 blur-3xl dark:bg-[#c9f36b]/10" />
                <div class="relative grid items-end gap-14 lg:grid-cols-[1.05fr_0.95fr] lg:gap-20">
                    <div>
                        <p
                            class="mb-7 inline-flex items-center gap-2 rounded-full border border-[#c9d8c8] bg-white/70 px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-[#49705a] dark:border-[#334c3a] dark:bg-[#152119] dark:text-[#c9f36b]"
                        >
                            <span class="h-2 w-2 rounded-full bg-[#8bc34a] shadow-[0_0_0_5px_rgba(139,195,74,0.15)]" />{{ copy.badge }}
                        </p>
                        <h1
                            class="max-w-4xl text-5xl font-semibold leading-[0.98] tracking-[-0.055em] text-[#173b2b] dark:text-[#eaf6ed] sm:text-7xl lg:text-[6.4rem]"
                        >
                            {{ copy.title }}
                        </h1>
                        <p class="mt-8 max-w-xl text-lg leading-8 text-[#587064] dark:text-[#a9bbb0]">{{ copy.intro }}</p>
                        <div class="mt-10 flex flex-wrap items-center gap-4">
                            <Link
                                href="/docs"
                                class="group inline-flex items-center gap-3 rounded-full bg-[#173b2b] px-6 py-3.5 font-semibold text-white shadow-xl shadow-[#173b2b]/20 transition hover:-translate-y-1 dark:bg-[#c9f36b] dark:text-[#173b2b]"
                                >{{ copy.explore }} <span class="transition group-hover:translate-x-1">→</span></Link
                            ><Link
                                v-if="page.props.auth?.user"
                                :href="route('dashboard')"
                                class="inline-flex items-center gap-2 px-3 py-3.5 font-semibold text-[#41604d] dark:text-[#c9f36b]"
                                >{{ copy.dashboard }} <span>↗</span></Link
                            >
                        </div>
                    </div>
                    <div class="relative mx-auto w-full max-w-lg lg:mb-3">
                        <div class="absolute -inset-5 rounded-[2.5rem] bg-[#c9f36b]/20 blur-2xl" />
                        <div
                            class="relative overflow-hidden rounded-[2rem] border border-[#31543e] bg-[#173b2b] p-5 text-white shadow-2xl shadow-[#173b2b]/25"
                        >
                            <div class="flex items-center justify-between border-b border-white/10 pb-5 text-xs text-[#a9c2af]">
                                <span>pulsemonitor / overview</span
                                ><span class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-[#c9f36b]" /> live</span>
                            </div>
                            <div class="grid grid-cols-2 gap-3 py-5">
                                <div class="rounded-2xl bg-white/10 p-4">
                                    <p class="text-xs text-[#a9c2af]">Uptime</p>
                                    <p class="mt-2 text-3xl font-semibold">99.98%</p>
                                    <p class="mt-1 text-xs text-[#c9f36b]">↑ 0.12% this month</p>
                                </div>
                                <div class="rounded-2xl bg-white/10 p-4">
                                    <p class="text-xs text-[#a9c2af]">Checks</p>
                                    <p class="mt-2 text-3xl font-semibold">12,480</p>
                                    <p class="mt-1 text-xs text-[#a9c2af]">last 24 hours</p>
                                </div>
                            </div>
                            <div class="rounded-2xl bg-[#0f2c20] p-4">
                                <div class="mb-4 flex items-center justify-between text-xs text-[#a9c2af]">
                                    <span>Response time</span><span>24 hours</span>
                                </div>
                                <div class="flex h-28 items-end gap-1.5">
                                    <span
                                        v-for="(height, index) in [38, 55, 48, 70, 44, 62, 76, 58, 83, 65, 91, 73, 86, 68, 96, 81, 89, 75, 94, 88]"
                                        :key="index"
                                        class="flex-1 rounded-t bg-[#c9f36b]/80"
                                        :style="{ height: `${height}%` }"
                                    />
                                </div>
                            </div>
                            <div class="mt-3 flex items-center gap-3 rounded-xl border border-[#d77c5d]/40 bg-[#d77c5d]/10 p-3 text-xs">
                                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-[#d77c5d] text-[#173b2b]">!</span
                                ><span
                                    ><strong class="block">api.acme.com</strong
                                    ><span class="text-[#e8b5a2]">Incident detected · 2 min ago</span></span
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section class="border-y border-[#dce5dc] bg-white/50 dark:border-[#203126] dark:bg-[#101a13]">
                <div class="mx-auto grid max-w-7xl gap-0 px-6 lg:grid-cols-3 lg:px-10">
                    <div
                        v-for="item in copy.cards"
                        :key="item[0]"
                        class="border-b border-[#dce5dc] py-10 dark:border-[#203126] lg:border-b-0 lg:border-r lg:px-10 lg:first:pl-0 lg:last:border-r-0"
                    >
                        <span class="text-sm font-semibold text-[#8bad91]">{{ item[0] }}</span>
                        <h2 class="mt-5 text-xl font-semibold text-[#173b2b] dark:text-[#eaf6ed]">{{ item[1] }}</h2>
                        <p class="mt-2 max-w-xs text-sm leading-6 text-[#668071] dark:text-[#a9bbb0]">{{ item[2] }}</p>
                    </div>
                </div>
            </section>
            <section class="mx-auto max-w-7xl px-6 py-24 lg:px-10 lg:py-32">
                <div class="grid gap-12 lg:grid-cols-[0.75fr_1.25fr] lg:gap-24">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#78917d]">{{ copy.problem }}</p>
                        <h2 class="mt-5 text-3xl font-semibold tracking-tight text-[#173b2b] dark:text-[#eaf6ed] lg:text-4xl">{{ copy.solution }}</h2>
                    </div>
                    <div class="grid gap-8 text-lg leading-8 text-[#587064] dark:text-[#a9bbb0]">
                        <p>{{ copy.problemText }}</p>
                        <p>{{ copy.solutionText }}</p>
                    </div>
                </div>
            </section>
            <section class="bg-[#173b2b] text-white">
                <div class="mx-auto max-w-7xl px-6 py-24 lg:px-10 lg:py-28">
                    <div class="max-w-2xl">
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#c9f36b]">PulseMonitor</p>
                        <h2 class="mt-5 text-4xl font-semibold tracking-tight lg:text-5xl">{{ copy.stackTitle }}</h2>
                        <p class="mt-6 text-lg leading-8 text-[#c0d5c5]">{{ copy.stackText }}</p>
                    </div>
                    <div class="mt-12 flex max-w-4xl flex-wrap gap-3">
                        <span v-for="item in copy.stack" :key="item" class="rounded-full border border-white/20 px-4 py-2 text-sm text-[#e0eee2]">{{
                            item
                        }}</span>
                    </div>
                </div>
            </section>
            <section class="mx-auto max-w-7xl px-6 py-24 lg:px-10 lg:py-32">
                <div class="rounded-[2rem] bg-[#c9f36b] px-7 py-12 text-[#173b2b] sm:px-12 lg:flex lg:items-end lg:justify-between">
                    <div class="max-w-xl">
                        <h2 class="text-4xl font-semibold tracking-tight lg:text-5xl">{{ copy.ctaTitle }}</h2>
                        <p class="mt-5 text-lg leading-8 text-[#41604d]">{{ copy.ctaText }}</p>
                    </div>
                    <Link
                        href="/docs"
                        class="mt-8 inline-flex shrink-0 items-center gap-3 rounded-full bg-[#173b2b] px-6 py-3.5 font-semibold text-white transition hover:bg-[#27583f] lg:mt-0"
                        >{{ copy.docs }} <span>→</span></Link
                    >
                </div>
            </section>
        </main>
        <footer
            class="mx-auto flex max-w-7xl flex-col gap-4 border-t border-[#dce5dc] px-6 py-8 text-sm text-[#78917d] dark:border-[#203126] sm:flex-row sm:items-center sm:justify-between lg:px-10"
        >
            <span>{{ copy.footer }}</span
            ><Link href="/docs" class="font-semibold text-[#41604d] hover:text-[#173b2b] dark:text-[#c9f36b]">{{ copy.docs }} ↗</Link>
        </footer>
    </div>
</template>
