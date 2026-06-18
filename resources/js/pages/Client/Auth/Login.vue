<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import StoreLayout from '@/Layouts/StoreLayout.vue'
import { ref } from 'vue'
import { Eye, EyeOff } from 'lucide-vue-next'

defineOptions({ layout: StoreLayout })

const props = defineProps<{
  status?: string
  userIp?: string
}>()

const showPassword = ref(false)

const form = useForm({
  username: 'cli1',
  password: 'Mudar@123',
  remember: false,
})

const submit = () => {
  form.post(route('client.login.post'), {
    onFinish: () => form.reset('password'),
  })
}
</script>

<template>
  <Head title="Login Cliente">
    <meta name="robots" content="follow" />
  </Head>

  <div class="min-h-[60vh] flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow-2xl rounded-3xl sm:px-10 border border-slate-100">
        <div class="text-center mb-8">
          <h1 class="text-3xl font-black text-slate-900 uppercase tracking-tighter">Bem-vindo!</h1>
          <p class="mt-2 text-sm font-bold text-slate-500 uppercase tracking-widest">
            Faça login para acessar sua conta
          </p>
        </div>

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
          {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-6">
          <div>
            <label for="username" class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">
              Nome de Usuário
            </label>
            <div class="mt-1">
              <input
                id="username"
                v-model="form.username"
                type="text"
                class="block w-full bg-slate-50 border-slate-200 rounded-2xl px-4 py-3 text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-primary transition-all outline-none shadow-sm"
                required
                autocomplete="username"
                placeholder="seu-usuario"
              />
            </div>
            <p v-if="form.errors.username" class="mt-2 text-xs font-bold text-red-600 uppercase">
              {{ form.errors.username }}
            </p>
          </div>

          <div>
            <label for="password" class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">
              Senha
            </label>
            <div class="mt-1 relative">
              <input
                id="password"
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                class="block w-full bg-slate-50 border-slate-200 rounded-2xl px-4 py-3 text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-primary transition-all outline-none shadow-sm pr-12"
                required
                autocomplete="current-password"
                placeholder="••••••••"
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-primary transition-colors"
              >
                <component :is="showPassword ? EyeOff : Eye" class="w-5 h-5" />
              </button>
            </div>
            <p v-if="form.errors.password" class="mt-2 text-xs font-bold text-red-600 uppercase">
              {{ form.errors.password }}
            </p>
          </div>

          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <input
                id="remember"
                v-model="form.remember"
                type="checkbox"
                class="h-4 w-4 rounded-lg border-slate-300 text-primary focus:ring-primary"
              />
              <label for="remember" class="ml-2 block text-xs font-bold text-slate-700 uppercase tracking-widest">
                Lembrar de mim
              </label>
            </div>

            <div class="text-xs">
              <Link
                :href="route('client.forgot.password')"
                class="font-black text-primary hover:text-primary-hover uppercase tracking-widest transition"
              >
                Esqueceu sua senha?
              </Link>
            </div>
          </div>

          <div>
            <button
              type="submit"
              class="btn-primary w-full py-4 rounded-2xl shadow-lg shadow-primary/20 text-sm uppercase tracking-widest font-black"
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
            >
              Entrar na conta
            </button>
          </div>
        </form>

        <div class="mt-8 pt-8 border-t border-slate-100 text-center">
          <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">
            Não tem uma conta?
            <Link
              :href="route('client.register')"
              class="ml-1 font-black text-primary hover:text-primary-hover transition"
            >
              Cadastre-se agora
            </Link>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>
