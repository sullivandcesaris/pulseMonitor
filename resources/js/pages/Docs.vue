<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

type Language = 'en' | 'pt';
const language = ref<Language>('en');
const copy = computed(() =>
    language.value === 'pt'
        ? {
              back: 'Voltar ao portfólio',
              title: 'Documentação do PulseMonitor',
              intro: 'Um guia conciso das decisões de produto, arquitetura e operação por trás da plataforma.',
              architecture: 'Arquitetura',
              architectureText:
                  'O PulseMonitor é um monólito modular. Laravel concentra autenticação, organizações, monitores, incidentes e notificações; Vue + Inertia entrega a interface; PostgreSQL é a fonte durável de verdade e Redis cuida de filas, cache e coordenação.',
              flow: 'Fluxo de monitoramento',
              flowItems: [
                  'Um scheduler encontra monitores habilitados cujo próximo check venceu.',
                  'O job valida novamente o destino, bloqueia endereços privados e faz uma requisição HTTP limitada.',
                  'O resultado é salvo com status, latência e classificação de erro.',
                  'Falhas consecutivas abrem um incidente; uma recuperação válida o encerra e pode enviar uma notificação.',
              ],
              security: 'Segurança por padrão',
              securityText:
                  'A entrada de monitoramento é tratada como uma fronteira SSRF: apenas HTTP/HTTPS, DNS revalidado, IPs privados e metadata bloqueados, redirects revalidados, portas permitidas e timeouts obrigatórios.',
              operations: 'Operação e produção',
              operationsText:
                  'A produção separa app, scheduler, workers de monitoramento e notificações. Caddy termina HTTPS; PostgreSQL mantém dados duráveis; Redis transporta filas. Logs estruturados, correlation IDs, health checks, Sentry e métricas Prometheus apoiam diagnóstico.',
              local: 'Executar localmente',
              localText: 'Use Laravel Sail para iniciar o ambiente de desenvolvimento:',
              docs: 'Documentos relacionados',
              related: ['Arquitetura', 'Segurança', 'Escalabilidade', 'Deploy de produção'],
              cta: 'Acessar o dashboard',
              footer: 'PulseMonitor · documentação técnica',
          }
        : {
              back: 'Back to portfolio',
              title: 'PulseMonitor documentation',
              intro: 'A concise guide to the product, architecture, and operational decisions behind the platform.',
              architecture: 'Architecture',
              architectureText:
                  'PulseMonitor is a modular monolith. Laravel owns authentication, organizations, monitors, incidents, and notifications; Vue + Inertia delivers the interface; PostgreSQL is the durable source of truth and Redis handles queues, cache, and coordination.',
              flow: 'Monitoring flow',
              flowItems: [
                  'The scheduler finds enabled monitors whose next check is due.',
                  'The job validates the destination again, blocks private addresses, and makes a bounded HTTP request.',
                  'The result is stored with status, latency, and error classification.',
                  'Consecutive failures open an incident; a valid recovery closes it and can send a notification.',
              ],
              security: 'Security by default',
              securityText:
                  'Monitoring input is treated as an SSRF boundary: HTTP/HTTPS only, DNS revalidation, private and metadata IP blocking, redirect revalidation, allowed ports, and mandatory timeouts.',
              operations: 'Operations and production',
              operationsText:
                  'Production separates the app, scheduler, monitoring workers, and notification workers. Caddy terminates HTTPS; PostgreSQL keeps durable data; Redis transports queues. Structured logs, correlation IDs, health checks, Sentry, and Prometheus metrics support diagnosis.',
              local: 'Run locally',
              localText: 'Use Laravel Sail to start the development environment:',
              docs: 'Related documents',
              related: ['Architecture', 'Security', 'Scalability', 'Production deployment'],
              cta: 'Open dashboard',
              footer: 'PulseMonitor · technical documentation',
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
    <Head :title="copy.title"><meta name="description" :content="copy.intro" /></Head>
    <div class="min-h-screen bg-[#f5f7f4] text-[#17221c] dark:bg-[#0b110e] dark:text-[#edf5ef]">
        <header class="mx-auto flex max-w-5xl items-center justify-between px-6 py-6 lg:px-10">
            <Link href="/" class="flex items-center gap-3 font-semibold tracking-tight"
                ><span
                    class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#173b2b] text-sm text-[#c9f36b] dark:bg-[#c9f36b] dark:text-[#173b2b]"
                    >P</span
                ><span>PulseMonitor</span></Link
            >
            <div class="flex items-center gap-4">
                <div class="flex rounded-full border border-[#d4ded6] bg-white/70 p-1 text-xs font-semibold dark:border-[#2a3a2f] dark:bg-[#111b14]">
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
                <Link href="/" class="text-sm font-medium text-[#41604d] dark:text-[#c9f36b]">{{ copy.back }} ↗</Link>
            </div>
        </header>
        <main class="mx-auto max-w-5xl px-6 pb-24 pt-14 lg:px-10 lg:pt-20">
            <div class="max-w-3xl">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#78917d]">PulseMonitor / docs</p>
                <h1 class="mt-5 text-5xl font-semibold tracking-[-0.04em] text-[#173b2b] dark:text-[#eaf6ed] lg:text-7xl">{{ copy.title }}</h1>
                <p class="mt-7 text-xl leading-9 text-[#587064] dark:text-[#a9bbb0]">{{ copy.intro }}</p>
            </div>
            <div class="mt-20 grid gap-12 lg:grid-cols-[0.75fr_1.25fr] lg:gap-24">
                <aside class="hidden lg:block">
                    <div class="sticky top-8 border-l-2 border-[#c9f36b] pl-5 text-sm text-[#78917d]">
                        <p>01 · {{ copy.architecture }}</p>
                        <p class="mt-3">02 · {{ copy.flow }}</p>
                        <p class="mt-3">03 · {{ copy.security }}</p>
                        <p class="mt-3">04 · {{ copy.operations }}</p>
                    </div>
                </aside>
                <div class="space-y-16">
                    <section>
                        <p class="text-sm font-semibold text-[#8bad91]">01</p>
                        <h2 class="mt-3 text-3xl font-semibold text-[#173b2b] dark:text-[#eaf6ed]">{{ copy.architecture }}</h2>
                        <p class="mt-5 text-lg leading-8 text-[#587064] dark:text-[#a9bbb0]">{{ copy.architectureText }}</p>
                    </section>
                    <section>
                        <p class="text-sm font-semibold text-[#8bad91]">02</p>
                        <h2 class="mt-3 text-3xl font-semibold text-[#173b2b] dark:text-[#eaf6ed]">{{ copy.flow }}</h2>
                        <ol class="mt-6 space-y-4">
                            <li
                                v-for="(item, index) in copy.flowItems"
                                :key="item"
                                class="flex gap-4 rounded-2xl border border-[#dce5dc] bg-white/60 p-5 text-[#587064] dark:border-[#203126] dark:bg-[#101a13] dark:text-[#a9bbb0]"
                            >
                                <span class="font-mono text-sm font-semibold text-[#8bad91]">0{{ index + 1 }}</span
                                ><span class="leading-7">{{ item }}</span>
                            </li>
                        </ol>
                    </section>
                    <section>
                        <p class="text-sm font-semibold text-[#8bad91]">03</p>
                        <h2 class="mt-3 text-3xl font-semibold text-[#173b2b] dark:text-[#eaf6ed]">{{ copy.security }}</h2>
                        <p class="mt-5 rounded-2xl bg-[#173b2b] p-7 text-lg leading-8 text-[#d8e9db]">{{ copy.securityText }}</p>
                    </section>
                    <section>
                        <p class="text-sm font-semibold text-[#8bad91]">04</p>
                        <h2 class="mt-3 text-3xl font-semibold text-[#173b2b] dark:text-[#eaf6ed]">{{ copy.operations }}</h2>
                        <p class="mt-5 text-lg leading-8 text-[#587064] dark:text-[#a9bbb0]">{{ copy.operationsText }}</p>
                    </section>
                    <section>
                        <h2 class="text-3xl font-semibold text-[#173b2b] dark:text-[#eaf6ed]">{{ copy.local }}</h2>
                        <p class="mt-4 text-lg leading-8 text-[#587064] dark:text-[#a9bbb0]">{{ copy.localText }}</p>
                        <pre
                            class="mt-6 overflow-x-auto rounded-2xl bg-[#17221c] p-6 text-sm leading-7 text-[#c9f36b]"
                        ><code>cp .env.example .env · php artisan key:generate · ./vendor/bin/sail up -d · ./vendor/bin/sail artisan migrate --seed</code></pre>
                    </section>
                    <section>
                        <h2 class="text-3xl font-semibold text-[#173b2b] dark:text-[#eaf6ed]">{{ copy.docs }}</h2>
                        <div class="mt-5 grid gap-3 sm:grid-cols-2">
                            <Link
                                v-for="item in copy.related"
                                :key="item"
                                href="/docs"
                                class="rounded-xl border border-[#dce5dc] bg-white/60 px-5 py-4 text-sm font-semibold text-[#41604d] transition hover:border-[#8bad91] dark:border-[#203126] dark:bg-[#101a13] dark:text-[#c9f36b]"
                                >{{ item }} ↗</Link
                            >
                        </div>
                    </section>
                    <Link href="/dashboard" class="inline-flex rounded-full bg-[#c9f36b] px-6 py-3.5 font-semibold text-[#173b2b]"
                        >{{ copy.cta }} →</Link
                    >
                </div>
            </div>
        </main>
        <footer class="mx-auto max-w-5xl border-t border-[#dce5dc] px-6 py-8 text-sm text-[#78917d] dark:border-[#203126] lg:px-10">
            {{ copy.footer }}
        </footer>
    </div>
</template>
