<template>
    <Head>
        <title>Reset Password</title>
    </Head>
    <section class="login-section">
        <div class="login-box">
            <h1>Centre Médical Canaan</h1>
            <h5 class="mb-3 text-center text-muted">Create New Password</h5>
            <p class="text-center text-muted mb-4">
                Please enter your new password below.
            </p>

            <div v-if="$page.props.flash?.success" class="alert alert-success mb-3" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ $page.props.flash.success }}
            </div>

            <div v-if="errors.email" class="alert alert-danger mb-3" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ errors.email[0] }}
            </div>

            <form @submit.prevent="resetPassword">
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input
                        type="email"
                        class="form-control"
                        id="email"
                        v-model="resetForm.email"
                        readonly
                        disabled
                    >
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">New Password</label>
                    <div class="password-wrapper">
                        <input
                            :type="showPassword ? 'text' : 'password'"
                            class="form-control"
                            id="password"
                            placeholder="Enter new password"
                            v-model="resetForm.password"
                            :disabled="resetForm.processing"
                        >
                        <button type="button" class="password-toggle" @click="togglePassword" :disabled="resetForm.processing">
                            <i :class="showPassword ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
                        </button>
                    </div>
                    <div v-if="errors.password">
                        <span class="text-danger">{{ errors.password[0] }}</span>
                    </div>
                    <small class="text-muted">
                        Password must be at least 8 characters and include uppercase, lowercase, numbers, and symbols.
                    </small>
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <div class="password-wrapper">
                        <input
                            :type="showPasswordConfirm ? 'text' : 'password'"
                            class="form-control"
                            id="password_confirmation"
                            placeholder="Confirm new password"
                            v-model="resetForm.password_confirmation"
                            :disabled="resetForm.processing"
                        >
                        <button type="button" class="password-toggle" @click="togglePasswordConfirm" :disabled="resetForm.processing">
                            <i :class="showPasswordConfirm ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
                        </button>
                    </div>
                    <div v-if="errors.password_confirmation">
                        <span class="text-danger">{{ errors.password_confirmation[0] }}</span>
                    </div>
                </div>

                <button type="submit" class="btn btn-green w-100 mb-3" :disabled="resetForm.processing">
                    <span v-if="resetForm.processing" class="loader-content">
                        <div class="spinner-border spinner-border-sm me-2" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        Resetting...
                    </span>
                    <span v-else class="normal-content">
                        <i class="fas fa-check me-2"></i>
                        Reset Password
                    </span>
                </button>

                <div class="text-center">
                    <Link :href="route('auth.loginForm')" class="back-to-login">
                        <i class="fas fa-arrow-left me-2"></i>Back to Login
                    </Link>
                </div>
            </form>
        </div>
    </section>
</template>

<script setup>
import '../../../../public/assets/css/style.css'
import { ref } from 'vue'
import { useForm, Link,usePage } from '@inertiajs/vue3'
import { useToast } from "vue-toastification";

const toast = useToast();
const props = defineProps({
    token: String,
    email: String,
    errors: Object
})

const page = usePage();

const resetForm = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: ''
})

const showPassword = ref(false)
const showPasswordConfirm = ref(false)

function togglePassword() {
    showPassword.value = !showPassword.value
}

function togglePasswordConfirm() {
    showPasswordConfirm.value = !showPasswordConfirm.value
}

function resetPassword() {
    resetForm.post(route('auth.password.reset'), {
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
        onFinish: () => {
            if (Object.keys(props.errors).length > 0) {
                resetForm.password = ''
                resetForm.password_confirmation = ''
            }
        }
    })
}
</script>

<style scoped>
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
    bottom: -100px;
    right: -150px;
    z-index: -1;
}

.login-section {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    width: 100%;
    padding: 20px;
}

.login-box {
    background-color: #fff;
    padding: 50px;
    border-radius: 15px;
    box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 550px;
    position: relative;
}

.login-box h1 {
    margin: 0px 0px 10px 0px;
    font-size: 1.5rem;
    font-weight: 600;
    color: #28a745;
    text-align: center;
}

.login-box h5 {
    font-size: 1.1rem;
    font-weight: 600;
}

.form-control {
    border-radius: 10px;
}

.form-label {
    font-weight: 500;
    color: #333;
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

.password-toggle:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-green {
    background-color: #28a745;
    border-color: #28a745;
    border-radius: 10px;
    font-weight: 600;
    color: #fff;
    font-size: 1rem;
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
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.back-to-login {
    font-size: 0.95rem;
    color: #6c757d;
    text-decoration: none;
    transition: color 0.2s;
}

.back-to-login:hover {
    color: #28a745;
    text-decoration: underline;
}

.alert {
    border-radius: 10px;
    padding: 12px 15px;
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

.form-control:disabled {
    background-color: #f8f9fa;
    opacity: 0.8;
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
</style>
