<script setup lang="ts">
import { TransitionRoot } from '@headlessui/vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

import DeleteUser from '@/components/DeleteUser.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useLanguage } from '@/composables/useLanguage';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem, type SharedData, type User } from '@/types';
import { computed } from 'vue';

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
    className?: string;
}

defineProps<Props>();

const page = usePage<SharedData>();
const user = page.props.auth.user as User;
const { isPortuguese } = useLanguage();
const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: isPortuguese.value ? 'Configurações do perfil' : 'Profile settings', href: '/settings/profile' },
]);

const form = useForm({
    name: user.name,
    email: user.email,
    alert_notifications_enabled: user.alert_notifications_enabled,
});

const submit = () => {
    form.patch(route('profile.update'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="isPortuguese ? 'Configurações do perfil' : 'Profile settings'" />

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall
                    :title="isPortuguese ? 'Informações do perfil' : 'Profile information'"
                    :description="isPortuguese ? 'Atualize seu nome e endereço de e-mail' : 'Update your name and email address'"
                />

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid gap-2">
                        <Label for="name">{{ isPortuguese ? 'Nome' : 'Name' }}</Label>
                        <Input
                            id="name"
                            class="mt-1 block w-full"
                            v-model="form.name"
                            required
                            autocomplete="name"
                            :placeholder="isPortuguese ? 'Nome completo' : 'Full name'"
                        />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">{{ isPortuguese ? 'Endereço de e-mail' : 'Email address' }}</Label>
                        <Input
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            v-model="form.email"
                            required
                            autocomplete="username"
                            :placeholder="isPortuguese ? 'Endereço de e-mail' : 'Email address'"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div v-if="mustVerifyEmail && !user.email_verified_at">
                        <p class="mt-2 text-sm text-neutral-800">
                            {{ isPortuguese ? 'Seu endereço de e-mail não foi verificado.' : 'Your email address is unverified.' }}
                            <Link
                                :href="route('verification.send')"
                                method="post"
                                as="button"
                                class="focus:outline-hidden rounded-md text-sm text-neutral-600 underline hover:text-neutral-900 focus:ring-2 focus:ring-offset-2"
                            >
                                {{
                                    isPortuguese
                                        ? 'Clique aqui para reenviar o e-mail de verificação.'
                                        : 'Click here to re-send the verification email.'
                                }}
                            </Link>
                        </p>

                        <div v-if="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
                            {{
                                isPortuguese
                                    ? 'Um novo link de verificação foi enviado para seu e-mail.'
                                    : 'A new verification link has been sent to your email address.'
                            }}
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <input
                            id="alert_notifications_enabled"
                            v-model="form.alert_notifications_enabled"
                            type="checkbox"
                            class="mt-1 size-4 rounded border-input"
                        />
                        <div>
                            <Label for="alert_notifications_enabled">{{
                                isPortuguese ? 'Notificações de incidentes por e-mail' : 'Incident email notifications'
                            }}</Label>
                            <p class="text-sm text-muted-foreground">
                                {{
                                    isPortuguese
                                        ? 'Receba um e-mail quando um serviço ficar indisponível ou se recuperar.'
                                        : 'Receive an email when a service goes down or recovers.'
                                }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="form.processing">{{ isPortuguese ? 'Salvar' : 'Save' }}</Button>

                        <TransitionRoot
                            :show="form.recentlySuccessful"
                            enter="transition ease-in-out"
                            enter-from="opacity-0"
                            leave="transition ease-in-out"
                            leave-to="opacity-0"
                        >
                            <p class="text-sm text-neutral-600">{{ isPortuguese ? 'Salvo.' : 'Saved.' }}</p>
                        </TransitionRoot>
                    </div>
                </form>
            </div>

            <DeleteUser />
        </SettingsLayout>
    </AppLayout>
</template>
