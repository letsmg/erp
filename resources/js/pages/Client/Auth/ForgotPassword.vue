<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import StoreLayout from '@/Layouts/StoreLayout.vue';

defineOptions({ layout: StoreLayout });
defineProps<{ status?: string }>();

const form = useForm({
    username: '',
});

const submit = () => {
    form.post(route('client.forgot.password.post'));
};
</script>

<template>
    <Head title="Esqueci minha senha" />
    <div class="min-h-[60vh] flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow-2xl rounded-3xl sm:px-10 border border-slate-100">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-black text-slate-900 uppercase tracking-tighter">Recuperar Senha</h1>
                    <p class="mt-2 text-sm font-bold text-slate-500 uppercase tracking-widest">
                        Informe seu nome de usuário
                    </p>
                </div>

                <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">
                            Nome de Usuário
                        </label>
                        <input
                            v-model="form.username"
                            type="text"
                            class="block w-full bg-slate-50 border-slate-200 rounded-2xl px-4 py-3 text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-primary transition-all outline-none shadow-sm"
                            required
                            autofocus
                            autocomplete="username"
                        />
                        <p v-if="form.errors.username" class="mt-2 text-xs font-bold text-red-600 uppercase">
                            {{ form.errors.username }}
                        </p>
                    </div>

                    <div class="flex items-center justify-between">
                        <Link
                            :href="route('client.login')"
                            class="text-xs font-black text-primary hover:text-primary-hover uppercase tracking-widest transition"
                        >
                            Voltar ao Login
                        </Link>

                        <button
                            type="submit"
                            class="inline-flex items-center px-6 py-3 bg-slate-900 text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-slate-800 transition shadow-lg"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            {{ form.processing ? 'Enviando...' : 'Enviar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
