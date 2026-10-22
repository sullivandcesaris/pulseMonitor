<script setup lang="ts">
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { useLanguage } from '@/composables/useLanguage';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();

const form = useForm({});
const { isPortuguese } = useLanguage();

const submit = () => {
    form.post(route('verification.send'));
};
</script>

<template>
    <AuthLayout
        :title="isPortuguese ? 'Verifique seu e-mail' : 'Verify email'"
        :description="
            isPortuguese
                ? 'Verifique seu endereço de e-mail clicando no link que enviamos.'
                : 'Please verify your email address by clicking on the link we just emailed to you.'
        "
    >
        <Head :title="isPortuguese ? 'Verificação de e-mail' : 'Email verification'" />

        <div v-if="status === 'verification-link-sent'" class="mb-4 text-center text-sm font-medium text-green-600">
            {{
                isPortuguese
                    ? 'Um novo link de verificação foi enviado para o endereço informado no cadastro.'
                    : 'A new verification link has been sent to the email address you provided during registration.'
            }}
        </div>

        <form @submit.prevent="submit" class="space-y-6 text-center">
            <Button :disabled="form.processing" variant="secondary">
                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                {{ isPortuguese ? 'Reenviar e-mail de verificação' : 'Resend verification email' }}
            </Button>

            <TextLink :href="route('logout')" method="post" as="button" class="mx-auto block text-sm">{{
                isPortuguese ? 'Sair' : 'Log out'
            }}</TextLink>
        </form>
    </AuthLayout>
</template>
