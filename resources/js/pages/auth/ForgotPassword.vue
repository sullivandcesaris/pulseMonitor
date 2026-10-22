<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useLanguage } from '@/composables/useLanguage';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
});
const { isPortuguese } = useLanguage();

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <AuthLayout
        :title="isPortuguese ? 'Esqueceu a senha' : 'Forgot password'"
        :description="isPortuguese ? 'Digite seu e-mail para receber um link de redefinição' : 'Enter your email to receive a password reset link'"
    >
        <Head :title="isPortuguese ? 'Esqueceu a senha' : 'Forgot password'" />

        <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <div class="space-y-6">
            <form @submit.prevent="submit">
                <div class="grid gap-2">
                    <Label for="email">{{ isPortuguese ? 'Endereço de e-mail' : 'Email address' }}</Label>
                    <Input id="email" type="email" name="email" autocomplete="off" v-model="form.email" autofocus placeholder="email@example.com" />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="my-6 flex items-center justify-start">
                    <Button class="w-full" :disabled="form.processing">
                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                        {{ isPortuguese ? 'Enviar link de redefinição' : 'Email password reset link' }}
                    </Button>
                </div>
            </form>

            <div class="space-x-1 text-center text-sm text-muted-foreground">
                <span>{{ isPortuguese ? 'Ou, volte para' : 'Or, return to' }}</span>
                <TextLink :href="route('login')">{{ isPortuguese ? 'entrar' : 'log in' }}</TextLink>
            </div>
        </div>
    </AuthLayout>
</template>
