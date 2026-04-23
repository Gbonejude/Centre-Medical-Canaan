<template>
    <div class="profile-page py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                        <div class="card-header bg-primary text-white p-4">
                            <div class="d-flex align-items-center">
                                <div class="profile-avatar-circle bg-white text-primary me-3">
                                    <i class="fa fa-user fa-lg"></i>
                                </div>
                                <div>
                                    <h3 class="mb-0">Mon Profil</h3>
                                    <p class="mb-0 opacity-75">Gérez vos informations personnelles et de sécurité</p>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-4 p-lg-5">
                            <form @submit.prevent="submit">
                                <div class="row g-4 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Prénom</label>
                                        <input type="text" v-model="form.firstname" class="form-control form-control-lg" required>
                                        <div v-if="form.errors.firstname" class="text-danger small mt-1">{{ form.errors.firstname }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Nom</label>
                                        <input type="text" v-model="form.lastname" class="form-control form-control-lg" required>
                                        <div v-if="form.errors.lastname" class="text-danger small mt-1">{{ form.errors.lastname }}</div>
                                    </div>
                                </div>

                                <div class="row g-4 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Adresse Email</label>
                                        <input type="email" v-model="form.email" class="form-control form-control-lg" required>
                                        <div v-if="form.errors.email" class="text-danger small mt-1">{{ form.errors.email }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Téléphone</label>
                                        <input type="tel" v-model="form.phone" class="form-control form-control-lg" required>
                                        <div v-if="form.errors.phone" class="text-danger small mt-1">{{ form.errors.phone }}</div>
                                    </div>
                                </div>

                                <hr class="my-5 opacity-10">

                                <h5 class="mb-4 text-primary fw-bold"><i class="fa fa-shield-alt me-2"></i>Changer le mot de passe</h5>
                                <p class="text-muted small mb-4">Laissez ces champs vides si vous ne souhaitez pas modifier votre mot de passe.</p>

                                <div class="row g-4 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Nouveau mot de passe</label>
                                        <input type="password" v-model="form.password" class="form-control form-control-lg">
                                        <div v-if="form.errors.password" class="text-danger small mt-1">{{ form.errors.password }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Confirmer le mot de passe</label>
                                        <input type="password" v-model="form.password_confirmation" class="form-control form-control-lg">
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-5">
                                    <button class="btn btn-primary btn-lg rounded-pill px-5 fw-bold" :disabled="form.processing">
                                        <i class="fa fa-save me-2"></i>Enregistrer les modifications
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";

const props = defineProps({
    user: Object
});

const toast = useToast();

const form = useForm({
    firstname: props.user.firstname,
    lastname: props.user.lastname,
    email: props.user.email,
    phone: props.user.phone,
    password: "",
    password_confirmation: ""
});

const submit = () => {
    form.put(route('front.profile.update'), {
        onSuccess: () => {
            toast.success("Profil mis à jour avec succès");
            form.reset('password', 'password_confirmation');
        }
    });
};
</script>

<style scoped>
.profile-page { background-color: #f8f9fa; min-height: 85vh; }
.profile-avatar-circle { width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
.form-control-lg { border-radius: 12px; border: 1px solid #e0e0e0; font-size: 1rem; }
.form-control-lg:focus { border-color: #2152ff; box-shadow: 0 0 0 0.25rem rgba(33, 82, 255, 0.1); }
.btn-primary { background: linear-gradient(310deg, #2152ff, #21d4fd); border: none; }
</style>
