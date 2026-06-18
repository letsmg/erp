<script setup>
import { Head } from '@inertiajs/vue3';
import { useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Eye, EyeOff, LogIn, ShieldCheck, Globe, Monitor, ArrowLeft, Mail } from 'lucide-vue-next';
import GuestLayout from '../../Layouts/GuestLayout.vue';

const props = defineProps({ 
    errors: Object,
    userIp: String,
    status: String,
});

const form = useForm({
    email: 'admin@teste.com',
    password: 'Mudar@123',
    remember: false,
});

const showPassword = ref(false);

const submit = () => {
    form.post(route('login.post'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Login - ERP Vue Laravel" />
        
        <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-8">
            <div class="mb-6 flex justify-center">
                <Link href="/" class="inline-flex items-center text-xs font-bold text-gray-400 hover:text-blue-600 transition group">
                    <ArrowLeft class="w-4 h-4 mr-1 group-hover:-translate-x-1 transition-transform" />
                    VOLTAR PARA A VITRINE / BACK TO STORE
                </Link>
            </div>
            
            <div class="text-center mb-8">
                <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">ERP Vue Laravel</h2>
                <p class="text-sm text-gray-500 mt-2">Identificação de acesso protegida</p>
            </div>

            <div v-if="status" class="mb-4 text-sm font-medium text-green-600 text-center">
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 italic">E-mail</label>
                    <div class="relative mt-1">
                        <Mail class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                        <input v-model="form.email" type="email" required autocomplete="email"
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition"
                            :class="{ 'border-red-500 ring-1 ring-red-500': errors.email }">
                    </div>
                    <div v-if="errors.email" class="text-red-500 text-xs mt-1 font-medium">{{ errors.email }}</div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 italic">Senha / Password</label>
                    <div class="relative mt-1">
                        <input :type="showPassword ? 'text' : 'password'" v-model="form.password" required autocomplete="current-password"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition"
                            :class="{ 'border-red-500 ring-1 ring-red-500': errors.password }">
                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition">
                            <component :is="showPassword ? EyeOff : Eye" class="h-5 w-5" />
                        </button>
                    </div>
                    <div v-if="errors.password" class="text-red-500 text-xs mt-1 font-medium">{{ errors.password }}</div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" v-model="form.remember" type="checkbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded cursor-pointer focus:ring-blue-500">
                        <label for="remember" class="ml-2 block text-sm text-gray-700 cursor-pointer">Lembrar / Remember</label>
                    </div>
                    <div>
                        <Link :href="route('password.request')" class="text-xs font-medium text-blue-600 hover:text-blue-500 transition">Esqueceu a senha? / Forgot password?</Link>
                    </div>
                </div>

                <button type="submit" :disabled="form.processing" class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 transition transform active:scale-95">
                    <LogIn v-if="!form.processing" class="w-5 h-5 mr-2" />
                    {{ form.processing ? 'Verificando...' : 'ENTRAR / LOGIN' }}
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-gray-100">
                <div class="flex items-center justify-center space-x-2 mb-6 py-1.5 px-3 bg-gray-50 rounded-full border border-gray-100 shadow-sm">
                    <Monitor class="w-3.5 h-3.5 text-gray-400" />
                    <span class="text-[10px] text-gray-500 font-mono tracking-wider uppercase">
                        IP: <span class="font-bold text-blue-700">{{ userIp || 'Detecting...' }}</span>
                    </span>
                </div>

                <div class="space-y-4 bg-blue-50 p-4 rounded-xl border border-blue-100">
                    <div class="flex items-start space-x-3">
                        <ShieldCheck class="w-6 h-6 text-blue-600 mt-0.5 flex-shrink-0" />
                        <div class="space-y-3">
                            <p class="text-[11px] text-blue-900 leading-snug">
                                <strong class="uppercase">Identificação Ativa:</strong><br>
                                Seu IP foi registrado. O acesso é restrito a conexões brasileiras para auditoria e segurança.
                            </p>
                            <p class="text-[10px] text-blue-700 leading-snug italic border-t border-blue-200 pt-2">
                                <strong class="uppercase">Active Identification:</strong><br>
                                Your IP address has been logged. Access is restricted to Brazilian connections for security auditing purposes.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-center items-center mt-5 text-gray-400 space-x-1">
                    <Globe class="w-3 h-3" />
                    <span class="text-[9px] uppercase tracking-widest font-bold">Authorized: Brazil (BR) only</span>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
