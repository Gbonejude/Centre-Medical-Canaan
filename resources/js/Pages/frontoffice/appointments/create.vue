<template>
    <DefaultLayout>
        <div class="reservation-page py-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-7">
                        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                            <div class="card-header bg-primary text-white p-4">
                                <h3 class="mb-1">Prendre un Rendez-vous</h3>
                                <p class="mb-0 opacity-75">Remplissez le formulaire pour demander une consultation.</p>
                            </div>
                            <div class="card-body p-4 p-lg-5">
                                <form @submit.prevent="submit">
                                    <div class="mb-4">
                                        <label class="form-label fw-bold">Service Médical</label>
                                        <select v-model="form.medical_service_id" class="form-select form-select-lg shadow-sm" required>
                                            <option value="">Choisissez une spécialité</option>
                                            <option v-for="service in services" :key="service.id" :value="service.id">
                                                {{ service.name }} ({{ formatPrice(service.consultation_fee) }} FCFA)
                                            </option>
                                        </select>
                                    </div>

                                    <div class="row mb-4">
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <label class="form-label fw-bold">Date souhaitée</label>
                                            <input type="date" v-model="form.appointment_date" class="form-control form-control-lg shadow-sm" required :min="minDate">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Heure</label>
                                            <input type="time" v-model="form.appointment_time" class="form-control form-control-lg shadow-sm" required>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label fw-bold">Motif / Symptômes</label>
                                        <textarea v-model="form.reason" class="form-control shadow-sm" rows="4" placeholder="Décrivez brièvement le sujet de votre visite..."></textarea>
                                    </div>

                                    <div class="d-grid mt-5">
                                        <button class="btn btn-primary btn-lg rounded-pill py-3 fw-bold" :disabled="form.processing">
                                            <i class="fa fa-paper-plane me-2"></i> Envoyer ma demande
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </DefaultLayout>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import DefaultLayout from "../../components/layouts/DefaultLayout.vue";

const props = defineProps({
    services: Array,
});

const form = useForm({
    medical_service_id: "",
    appointment_date: "",
    appointment_time: "",
    reason: "",
});

const minDate = new Date().toISOString().split('T')[0];

const formatPrice = (price) => {
    return new Intl.NumberFormat('fr-FR').format(price);
};

const submit = () => {
    form.post(route('appointments.store'));
};
</script>

<style scoped>
.reservation-page { background-color: #f0f2f5; min-height: 90vh; }
.card { border-radius: 1.5rem; }
.form-label { color: #344767; }
.btn-primary { background: linear-gradient(310deg, #2152ff, #21d4fd); border: none; }
.btn-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(33, 82, 255, 0.4); }
</style>
