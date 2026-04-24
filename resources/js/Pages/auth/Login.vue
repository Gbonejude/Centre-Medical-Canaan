<template>
    <Head>
        <title>Login</title>
    </Head>
    <section class="login-section">
        <div class="login-box">
            <h1>Centre Médical Canaan</h1>
            <div v-if="errors.message" class="mt-2 mb-2 error-message-container">
                <p class="text-center text-danger">{{ errors.message[0] }}</p>
            </div>
            <form @submit.prevent="login">
                <div class="mb-3">
                    <input type="email" class="form-control" id="email" autofocus="" placeholder="Email Address"
                        v-model="loginForm.email" :disabled="loginForm.processing">
                    <div v-if="errors.email"><span class="text-danger">{{ errors.email[0] }}</span>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="password-wrapper">
                        <input :type="showPassword ? 'text' : 'password'" class="form-control" id="password"
                            placeholder="Password" v-model="loginForm.password" :disabled="loginForm.processing">
                        <button type="button" class="password-toggle" @click="togglePassword" :disabled="loginForm.processing">
                            <i :class="showPassword ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
                        </button>
                    </div>
                    <div v-if="errors.password">
                        <span class="text-danger">{{ errors.password[0] }}</span>
                    </div>
                    <div class="text-end mt-2">
                        <Link :href="route('auth.password.forgot.form')" class="forgot-password">
                            Forgot Password?
                        </Link>
                    </div>
                </div>
                <button type="submit" class="btn btn-green w-100" :disabled="loginForm.processing">
                    <span v-if="loginForm.processing" class="loader-content">
                        <div class="spinner-border spinner-border-sm me-2" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        Connexion ...
                    </span>
                    <span v-else class="normal-content">
                        <i class="fa fa-sign-in-alt me-2"></i>
                        Connexion
                    </span>
                </button>
            </form>
        </div>
    </section>
</template>

<script setup>
import '../../../../public/assets/css/style.css'

import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    errors: Object
})

const loginForm = useForm({
    email: '',
    password: ''
})

const showPassword = ref(false)

function togglePassword() {
    showPassword.value = !showPassword.value
}

function login() {
    loginForm.post(route('auth.login'), {
        onSuccess: (page) => {
            if (page.props?.flash?.success) {
                toast.success(page.props.flash.success, {
                    position: "top-right",
                    timeout: 5000,
                    closeOnClick: true,
                    pauseOnHover: true,
                });
            }
        },
        onError: (errors) => {
            if (Object.keys(props.errors).length > 0) {
                loginForm.password = ''
            }
        },
    })
}
</script>

<style>
.login-box::before {
    content: '';
    position: absolute;
    background-color: #0056b3;
    width: 300px;
    height: 300px;
    border-radius: 50%;
    top: -100px;
    left: -100px;
    z-index: -1;
}

.login-box::after {
    content: '';
    position: absolute;
    background-color: #28a745;
    width: 300px;
    height: 300px;
    border-radius: 50%;
    top: 274px;
    right: -150px;
    z-index: -1;
}

.login-section {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    width: 100%;
}

.login-box {
    background-color: #fff;
    padding: 50px;
    border-radius: 15px;
    box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.1);
    width: 400px;
    max-width: 90vw;
    position: relative;
}

.error-message-container {
    max-width: 100%;
}

.error-message-container p {
    word-wrap: break-word;
    font-size: 0.875rem;
    line-height: 1.4;
    margin: 0;
}

.login-box h1 {
    margin: 0px 0px 30px 0px;
    font-size: 1.5rem;
    font-weight: 600;
    color: #28a745;
}

.form-control {
    border-radius: 10px;
}

.password-wrapper {
    position: relative;
}

.password-toggle {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    border: none;
    background: none;
    cursor: pointer;
    color: #6c757d;
    font-size: 1rem;
    padding: 0;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.password-toggle:hover {
    color: #28a745;
}

.password-toggle:focus {
    outline: none;
}

.btn-green {
    background-color: #28a745;
    border-color: #28a745;
    border-radius: 10px;
    font-weight: 600;
    color: #fff;
    font-size: 1rem;
}

.btn-green:hover {
    background-color: #218838;
    color: #fff;
}

.btn-blue {
    background-color: #0056b3;
    border-color: #0056b3;
    border-radius: 10px;
    font-weight: 600;
    color: #fff;
    font-size: 1rem;
}

.btn-blue:hover {
    background-color: #004095;
    color: #fff;
}

.forgot-password {
    font-size: 0.9rem;
    color: #6c757d;
    text-decoration: none;
}

.forgot-password:hover {
    text-decoration: underline;
}

.divider {
    font-size: 1rem;
    font-weight: bold;
    margin: 10px 0;
    color: #6c757d;
    text-align: center;
}

.btn {
    position: relative;
    transition: all 0.3s ease;
    overflow: hidden;
}

.btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.loader-content {
    display: flex;
    align-items: center;
    justify-content: center;
}

.normal-content {
    display: flex;
    align-items: center;
    justify-content: center;
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
    border-width: 0.15em;
}



.btn-green:hover:not(:disabled) {
    background: linear-gradient(135deg, #218838, #1ea085);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
}

.btn-green:active:not(:disabled) {
    transform: translateY(0);
}

.btn-green:disabled {
    background-color: #28a745;
    border-color: #28a745;
    border-radius: 10px;
    font-weight: 600;
    color: #fff;
    transform: none;
    box-shadow: none;
}

.form-control:disabled {
    background-color: #f8f9fa;
    opacity: 0.8;
}

.password-toggle:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
    }
}

.btn-green:disabled .loader-content {
    animation: pulse 2s infinite;
}

.login-box {
    transition: all 0.3s ease;
}

.login-box.loading {
    pointer-events: none;
    opacity: 0.9;
}
.spinner-border {
    border: 0.15em solid transparent;
    border-top: 0.15em solid currentColor;
    border-right: 0.15em solid currentColor;
    animation: spinner-border 0.75s linear infinite;
}

@keyframes spinner-border {
    to {
        transform: rotate(360deg);
    }
}

.alert {
    border-radius: 10px;
    padding: 12px 15px;
}

.alert-success {
    background-color: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
}
</style>
