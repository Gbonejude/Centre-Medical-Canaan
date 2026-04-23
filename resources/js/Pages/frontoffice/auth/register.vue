<template>
     <Head>
        <title>Register</title>
        <meta name="description" content="Manage system users and their permissions" />
    </Head>
  <div class="register-page">

    
    <section class="register-form-section">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-8 col-xl-6">
            <div class="register-card">
              <div class="register-card-header">
                <div class="register-icon">
                  <i class="fas fa-user-plus"></i>
                </div>
                <h2 class="register-title">Create Account</h2>
                <p class="register-subtitle">Fill in your details to get started</p>
              </div>

              <form @submit.prevent="submit" class="register-form">
                
                <div class="row g-3">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="firstname" class="form-label">
                        <i class="fas fa-user me-2"></i>First Name
                      </label>
                      <input id="firstname" v-model="form.firstname" type="text" class="form-control"
                        :class="{ 'is-invalid': form.errors.firstname }" placeholder="Enter your first name" />
                     <div v-if="errors.firstname" class="invalid-feedback">
                                {{ errors.firstname[0] }}
                            </div>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="lastname" class="form-label">
                        <i class="fas fa-user me-2"></i>Last Name
                      </label>
                      <input id="lastname" v-model="form.lastname" type="text" class="form-control"
                        :class="{ 'is-invalid': form.errors.lastname }" placeholder="Enter your last name" />
                      <div v-if="errors.lastname" class="invalid-feedback">
                                {{ errors.lastname[0] }}
                            </div>
                    </div>
                  </div>
                </div>

                
                <div class="form-group">
                  <label for="email" class="form-label">
                    <i class="fas fa-envelope me-2"></i>Email Address
                  </label>
                  <input id="email" v-model="form.email" type="email" class="form-control"
                    :class="{ 'is-invalid': form.errors.email }" placeholder="Enter your email address" />
                   <div v-if="errors.email" class="invalid-feedback">
                                {{ errors.email[0] }}
                            </div>
                </div>

                
                <div class="form-group">
                  <label for="phone" class="form-label">
                    <i class="fas fa-phone me-2"></i>Phone Number
                  </label>
                  <VueTelInput
                    v-model="form.phone"
                    mode="international"
                    :dropdownOptions="{ showDialCodeInSelection: true, showFlags: true }"
                    :inputOptions="{ placeholder: 'Enter your phone number', class: 'form-control', id: 'phone' }"
                    defaultCountry="US"
                    @validate="(state) => isPhoneValid = state.valid"
                    style-classes="phone-input"
                  />
                   <div v-if="errors.phone" class="invalid-feedback">
                                {{ errors.phone[0] }}
                            </div>
                </div>

                
                <div class="row g-3">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="password" class="form-label">
                        <i class="fas fa-lock me-2"></i>Password
                      </label>
                      <input id="password" v-model="form.password" type="password" class="form-control"
                        :class="{ 'is-invalid': form.errors.password }" placeholder="Enter your password" />
                      <div v-if="errors.password" class="invalid-feedback">
                                {{ errors.password[0] }}
                            </div>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="password_confirmation" class="form-label">
                        <i class="fas fa-check-circle me-2"></i>Confirm Password
                      </label>
                      <input id="password_confirmation" v-model="form.password_confirmation" type="password"
                        class="form-control" :class="{ 'is-invalid': form.errors.password_confirmation }"
                        placeholder="Confirm your password" />

                       <div v-if="errors.password_confirmation" class="invalid-feedback">
                                {{ errors.password_confirmation[0] }}
                            </div>
                    </div>
                  </div>
                </div>

                
                <div class="form-actions">
                  <button type="submit" :disabled="form.processing" class="btn btn-primary btn-register">
                    <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                    <i v-else class="fas fa-user-plus me-2"></i>
                    {{ form.processing ? 'Creating Account...' : 'Create Account' }}
                  </button>
                </div>
              </form>

              
              <div class="register-card-footer">
                <p class="login-link">
                  Already have an account?
                  <Link :href="route('auth.guest.login.form')" class="link-primary">
                  <i class="fas fa-sign-in-alt me-1"></i>Sign in here
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
import { ref } from 'vue';
import { useForm, Link, usePage } from '@inertiajs/vue3'
import { useToast } from 'vue-toastification';
import { VueTelInput } from 'vue-tel-input';
import 'vue-tel-input/vue-tel-input.css';

const toast = useToast();
const page = usePage();
const isPhoneValid = ref(false);
const props = defineProps({
  errors: Object,
});

const form = useForm({
  firstname: '',
  lastname: '',
  email: '',
  phone: '',
  password: '',
  password_confirmation: '',
  active: true,
})

const submit = () => {
  if (!isPhoneValid.value) {
    toast.error("Please enter a valid phone number");
    return;
  }
  form.post(route('auth.guest.register'), {
    onFinish: () => {
      form.reset('password', 'password_confirmation')
    },
    onSuccess: () => {
      toast.success('Your account has been created successfully. You can now log in.', {
        position: "top-right",
        timeout: 5000,
        closeOnClick: true,
        pauseOnHover: true,
      });
      
    },
    onError: (errors) => {
      console.error('Form validation errors:', errors);

      
      if (page.props.flash && page.props.flash.error) {
        toast.error(page.props.flash.error, {
          position: 'top-right',
          timeout: 5000,
          closeOnClick: true,
          pauseOnHover: true,
        });
      } else {
        toast.error('An error occurred while creating your account. Please try again.', {
          position: 'top-right',
          timeout: 5000,
          closeOnClick: true,
          pauseOnHover: true,
        });
      }
    }
  })
}
</script>

<style scoped>
.register-page {
  background-color: #f8f9fa;
  min-height: 100vh;
}

.register-header {
  background: #3b5998;
  color: white;
  padding: 4rem 0 2rem;
  position: relative;
  overflow: hidden;
}

.register-header::before {
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

.register-form-section {
  padding: 3rem 0;
  margin-top: -2rem;
  position: relative;
  z-index: 10;
}

.register-card {
  background: white;
  border-radius: 15px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.register-card-header {
  background: #3b5998;
  color: white;
  padding: 2rem;
  text-align: center;
}

.register-icon {
  width: 80px;
  height: 80px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1rem;
  font-size: 2rem;
}

.register-title {
  font-size: 2rem;
  font-weight: bold;
  margin-bottom: 0.5rem;
}

.register-subtitle {
  opacity: 0.9;
  margin-bottom: 0;
}

.register-form {
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
  box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.form-control.is-invalid {
  border-color: #dc3545;
}

.invalid-feedback {
  display: block;
  color: #dc3545;
  font-size: 0.875rem;
  margin-top: 0.25rem;
}

.form-check {
  display: flex;
  align-items: center;
  padding: 0.5rem 0;
}

.form-check-input {
  margin-right: 0.5rem;
  transform: scale(1.2);
}

.form-check-label {
  color: #666;
  margin-bottom: 0;
  cursor: pointer;
  display: flex;
  align-items: center;
}

.form-check-label i {
  color: #3b5998;
  width: 16px;
}

.form-actions {
  margin-top: 2rem;
}

.btn-register {
  width: 100%;
  padding: 0.875rem 2rem;
  font-size: 1.1rem;
  font-weight: 600;
  border-radius: 10px;
  background: #3b5998;
  border: none;
  transition: all 0.3s ease;
}

.btn-register:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
}

.btn-register:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.register-card-footer {
  background: #f8f9fa;
  padding: 1.5rem 2rem;
  text-align: center;
  border-top: 1px solid #dee2e6;
}

.login-link {
  margin-bottom: 0;
  color: #666;
}

.link-primary {
  color: #3b5998 !important;
  text-decoration: none;
  font-weight: 600;
}

.link-primary:hover {
  color: #31539d !important;
  text-decoration: underline;
}

@media (max-width: 768px) {
  .page-title {
    font-size: 2.5rem;
  }

  .page-subtitle {
    font-size: 1.1rem;
  }

  .register-card-header {
    padding: 1.5rem;
  }

  .register-form {
    padding: 1.5rem;
  }

  .register-title {
    font-size: 1.75rem;
  }
}

@media (max-width: 576px) {
  .register-header {
    padding: 3rem 0 1.5rem;
  }

  .page-title {
    font-size: 2rem;
  }

  .page-subtitle {
    font-size: 1rem;
  }

  .register-card-header {
    padding: 1rem;
  }

  .register-form {
    padding: 1rem;
  }

  .register-title {
    font-size: 1.5rem;
  }

  .register-icon {
    width: 60px;
    height: 60px;
    font-size: 1.5rem;
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

.register-card {
  animation: slideInUp 0.6s ease forwards;
}

.feature-card {
  animation: slideInUp 0.6s ease forwards;
}

.feature-card:nth-child(2) {
  animation-delay: 0.2s;
}

.feature-card:nth-child(3) {
  animation-delay: 0.4s;
}
</style>
