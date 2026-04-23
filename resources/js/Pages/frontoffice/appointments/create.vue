<template>
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
                                        <v-select
                                            v-model="form.medical_service_id"
                                            :options="services"
                                            :reduce="service => service.id"
                                            label="name"
                                            placeholder="Rechercher une spécialité..."
                                            class="custom-v-select"
                                        >
                                            <template #option="option">
                                                <span>{{ option.name }}</span>
                                            </template>
                                            <template #selected-option="option">
                                                {{ option.name }}
                                            </template>
                                        </v-select>
                                    </div>

                                <div class="row mb-4">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label class="form-label fw-bold">Date souhaitée</label>
                                        <DatePickerComponent v-model="form.appointment_date" minDate="today" placeholder="Choisir une date" />
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
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import DatePickerComponent from '../../components/DateComponent.vue';
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";

const props = defineProps({
    services: Array,
    selectedServiceId: [String, Number],
    selectedDate: String,
    selectedTime: String
});

const form = useForm({
    medical_service_id: props.selectedServiceId || "",
    appointment_date: props.selectedDate || "",
    appointment_time: props.selectedTime || "",
    reason: "",
});

const formatPrice = (price) => {
    if (!price && price !== 0) return '0';
    return new Intl.NumberFormat('fr-FR').format(price);
};

const submit = () => {
    form.post(route('front.appointments.store'));
};
</script>

<style scoped>
.reservation-page { background-color: #f0f2f5; min-height: 90vh; }
.card { border-radius: 1.5rem; }
.form-label { color: #344767; }
.btn-primary { background: linear-gradient(310deg, #2152ff, #21d4fd); border: none; }
.btn-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(33, 82, 255, 0.4); }

/* Custom V-Select Styling */
.custom-v-select :deep(.vs__dropdown-toggle) {
    padding: 0.5rem;
    border-radius: 12px;
    border: 1px solid #dee2e6;
    background-color: #fff;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}
.custom-v-select :deep(.vs__selected) {
    font-size: 1.1rem;
    color: #344767;
}
.custom-v-select :deep(.vs__search::placeholder) {
    color: #6c757d;
}
.custom-v-select :deep(.vs__dropdown-menu) {
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border: none;
    margin-top: 5px;
}
.custom-v-select :deep(.vs__dropdown-option) {
    padding: 10px 15px;
    display: flex;
    align-items: center;
}
.custom-v-select :deep(.vs__dropdown-option--highlight) {
    background: #2152ff;
}
</style>
