<template>
    <Head>
        <title>Login</title>
        <meta name="description" content="Ceci est la page d'accueil de Kimoasoft">
    </Head>
   <div class="main-wrapper account-wrapper">
        <div class="account-page">
			<div class="account-center">
				<div class="account-box">
                     <div v-if="errors.message">
                        <p class="text-center text-danger">{{ errors.message[0] }}</p>
                    </div>
                    <form  @submit.prevent="login" class="form-signin">
						<div class="account-logo ms-auto">
                            <a href="index-2.html"><img src="assets/img/logo-dark.png" alt=""></a>
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" class="form-control" autofocus=""  placeholder="Email Address"
                                        v-model="form.email">
                            <div v-if="errors.email"><span class="text-danger">{{ errors.email[0] }}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" placeholder="Password" v-model="form.password">
                             <div v-if="errors.password"> <span class="text-danger">{{ errors.password[0]}}</span>
                            </div>
                        </div>
                        <div class="form-group text-right">
                            <a href="forgot-password.html">Forgot your password?</a>
                        </div>
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary account-btn">Login</button>
                        </div>

                    </form>
                </div>
			</div>
        </div>
    </div>
</template>

<script setup>
import '../../../../public/assets/css/style.css'

import { reactive } from 'vue'
import { useForm } from '@inertiajs/vue3'
const props = defineProps({
    errors: Object
})
    const form = reactive({
        email: '',
        password: ''
    })
function login() {
    useForm(form).post(route('auth.login'));
}
</script>

<style lang="scss" scoped>

</style>
