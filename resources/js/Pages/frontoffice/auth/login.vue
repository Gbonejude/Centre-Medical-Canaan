<template>
     <Head>
        <title>Login</title>
        <meta name="description" content="Manage system users and their permissions" />
    </Head>
  <div class="login-page">
       
    <section class="login-form-section">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-6 col-xl-5">
            <div class="login-card">
              <div class="login-card-header">
                <div class="login-icon">
                  <i class="fas fa-sign-in-alt"></i>
                </div>
                <h2 class="login-title">Sign In</h2>
                <p class="login-subtitle">Enter your credentials to continue</p>
              </div>

              
              <div v-if="errors.message" class="alert alert-danger mx-4 mb-0">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ errors.message[0] }}
              </div>

              <form @submit.prevent="submit" class="login-form">
                
                <div class="form-group">
                  <label for="email" class="form-label">
                    <i class="fas fa-envelope me-2"></i>Email Address
                  </label>
                  <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="form-control"
                    :class="{ 'is-invalid': errors.email }"
                    placeholder="Enter your email address"
                  />
                    <div v-if="errors.email"><span class="text-danger">{{ errors.email[0] }}</span>
                            </div>
                </div>

                
                <div class="form-group">
                  <label for="password" class="form-label">
                    <i class="fas fa-lock me-2"></i>Password
                  </label>
                  <div class="password-input-group">
                    <input
                      id="password"
                      v-model="form.password"
                      :type="showPassword ? 'text' : 'password'"
                      class="form-control"
                      :class="{ 'is-invalid': errors.password }"
                      placeholder="Enter your password"
                    />
                    <button
                      type="button"
                      class="password-toggle"
                      @click="showPassword = !showPassword"
                    >
                      <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                    </button>
                  </div>
                  <div v-if="errors.password" class="invalid-feedback">
                    {{ Array.isArray(errors.password) ? errors.password[0] : errors.password }}
                  </div>
                </div>

                
                

                
                <div class="form-actions">
                  <button
                    type="submit"
                    :disabled="form.processing"
                    class="btn btn-primary btn-login"
                  >
                  <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                  <i v-else class="fas fa-sign-in-alt me-2"></i>
                  {{ form.processing ? 'Signing In...' : 'Sign In' }}
    
                  </button>
                </div>
              </form>

              
              <div class="login-card-footer">
                <p class="register-link">
                  Don't have an account?
                  <Link :href="route('auth.guest.register.form')" class="link-primary">
                    <i class="fas fa-user-plus me-1"></i>Create account
                  </Link>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'

defineProps({
  errors: Object,
})

const showPassword = ref(false)

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const submit = () => {
  form.post(route('auth.guest.login'), {
    onFinish: () => {
      form.reset('password')
    },
  })
}

const { processing } = form
</script>

<style scoped>
.login-page {
  background-color: #f8f9fa;
  min-height: 100vh;
}

.login-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 4rem 0 2rem;
  position: relative;
  overflow: hidden;
}

.login-header::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="white" opacity="0.1"><polygon points="1000,100 1000,0 0,100"/></svg>') no-repeat center bottom;
  background-size: cover;
}

.page-title {
  font-size: 3rem;
  font-weight: bold;
  margin-bottom: 1rem;
  position: relative;
  z-index: 2;
}

.page-subtitle {
  font-size: 1.2rem;
  opacity: 0.9;
  position: relative;
  z-index: 2;
}

.login-form-section {
  padding: 3rem 0;
  margin-top: -2rem;
  position: relative;
  z-index: 10;
}

.login-card {
  background: white;
  border-radius: 15px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.1);
  overflow: hidden;
}

.login-card-header {
  background: #3b5998;
  color: white;
  padding: 2rem;
  text-align: center;
}

.login-icon {
  width: 80px;
  height: 80px;
  background: rgba(255,255,255,0.2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1rem;
  font-size: 2rem;
}

.login-title {
  font-size: 2rem;
  font-weight: bold;
  margin-bottom: 0.5rem;
}

.login-subtitle {
  opacity: 0.9;
  margin-bottom: 0;
}

.login-form {
  padding: 2rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-label {
  font-weight: 600;
  color: #333;
  margin-bottom: 0.5rem;
  display: flex;
  align-items: center;
}

.form-label i {
  color: #3b5998;
  width: 16px;
}

.form-control {
  border-radius: 10px;
  border: 1px solid #dee2e6;
  padding: 0.75rem 1rem;
  transition: all 0.3s ease;
  font-size: 1rem;
}

.form-control:focus {
  border-color: #3b5998;
  box-shadow: 0 0 0 0.2rem rgba(40, 82, 167, 0.25);
}

.form-control.is-invalid {
  border-color: #dc3545;
}

.invalid-feedback {
  display: block;
  color: #dc3545;
  font-size: 0.875rem;
  margin-top: 0.25rem;
  font-weight: 500;
}

.password-input-group {
  position: relative;
}

.password-input-group .form-control {
  padding-right: 3rem;
}

.password-toggle {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: #6c757d;
  cursor: pointer;
  padding: 0;
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.password-toggle:hover {
  color: #3b5998;
}

.form-options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.form-check {
  display: flex;
  align-items: center;
}

.form-check-input {
  margin-right: 0.5rem;
  transform: scale(1.1);
}

.form-check-label {
  color: #666;
  margin-bottom: 0;
  cursor: pointer;
  font-size: 0.9rem;
}

.forgot-password-link {
  color: #3b5998;
  text-decoration: none;
  font-size: 0.9rem;
  font-weight: 500;
}

.forgot-password-link:hover {
  color: #20c997;
  text-decoration: underline;
}

.form-actions {
  margin-top: 2rem;
}

.btn-login {
  width: 100%;
  padding: 0.875rem 2rem;
  font-size: 1.1rem;
  font-weight: 600;
  border-radius: 10px;
  background: #3b5998;
  border: none;
  transition: all 0.3s ease;
}

.btn-login:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
}

.btn-login:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.login-card-footer {
  background: #f8f9fa;
  padding: 1.5rem 2rem;
  text-align: center;
  border-top: 1px solid #dee2e6;
}

.register-link {
  margin-bottom: 0;
  color: #666;
}

.link-primary {
  color: #3b5998 !important;
  text-decoration: none;
  font-weight: 600;
}

.link-primary:hover {
  color: #3b5998 !important;
  text-decoration: underline;
}

.alert {
  border-radius: 10px;
  border: none;
  margin-top: 1rem;
}

.alert-danger {
  background-color: #f8d7da;
  color: #721c24;
}

.quick-access-section {
  padding: 3rem 0;
  background: white;
}

.quick-access-card {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 2rem;
  border-radius: 15px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.quick-title {
  font-size: 1.5rem;
  font-weight: bold;
  margin-bottom: 0.5rem;
}

.quick-text {
  margin-bottom: 0;
  opacity: 0.9;
}

.btn-quick {
  background: white;
  color: #3b5998;
  padding: 0.75rem 2rem;
  border-radius: 25px;
  text-decoration: none;
  font-weight: bold;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
}

.btn-quick:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(255,255,255,0.3);
  color: #667eea;
}

@media (max-width: 768px) {
  .page-title {
    font-size: 2.5rem;
  }

  .page-subtitle {
    font-size: 1.1rem;
  }

  .login-card-header {
    padding: 1.5rem;
  }

  .login-form {
    padding: 1.5rem;
  }

  .login-title {
    font-size: 1.75rem;
  }

  .form-options {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }

  .quick-access-card .row {
    text-align: center;
  }

  .quick-access-card .col-lg-4 {
    margin-top: 1rem;
  }
}

@media (max-width: 576px) {
  .login-header {
    padding: 3rem 0 1.5rem;
  }

  .page-title {
    font-size: 2rem;
  }

  .page-subtitle {
    font-size: 1rem;
  }

  .login-card-header {
    padding: 1rem;
  }

  .login-form {
    padding: 1rem;
  }

  .login-title {
    font-size: 1.5rem;
  }

  .login-icon {
    width: 60px;
    height: 60px;
    font-size: 1.5rem;
  }

  .btn-quick {
    padding: 0.6rem 1.5rem;
    font-size: 0.9rem;
  }
}

@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.login-card {
  animation: slideInUp 0.6s ease forwards;
}

.quick-access-card {
  animation: slideInUp 0.6s ease forwards;
  animation-delay: 0.2s;
}
</style>
