<template>
    <div class="reservation-page py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-9 col-lg-8">
                    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                        
                        <!-- Stepper Header -->
                        <div class="card-header bg-primary text-white p-4">
                            <h3 class="mb-3 text-center">Prendre un Rendez-vous</h3>
                            
                            <div class="d-flex justify-content-between position-relative stepper-container">
                                <div class="progress position-absolute" style="top: 15px; left: 10%; width: 80%; height: 2px; z-index: 1;">
                                    <div class="progress-bar bg-white" role="progressbar" :style="{width: progressWidth}"></div>
                                </div>
                                
                                <div class="step text-center" :class="{ 'active': currentStep >= 1 }">
                                    <div class="step-icon">1</div>
                                    <div class="step-label mt-2 small">Service</div>
                                </div>
                                <div class="step text-center" :class="{ 'active': currentStep >= 2 }">
                                    <div class="step-icon">2</div>
                                    <div class="step-label mt-2 small">Date & Heure</div>
                                </div>
                                <div class="step text-center" :class="{ 'active': currentStep >= 3 }">
                                    <div class="step-icon">3</div>
                                    <div class="step-label mt-2 small">Résumé</div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-4 p-lg-5">
                            <form @submit.prevent="submit">
                                
                                <!-- STEP 1: SERVICE -->
                                <div v-if="currentStep === 1" class="step-content animate__animated animate__fadeIn">
                                    <h4 class="mb-4 text-center">Sélectionnez un service médical</h4>
                                    
                                    <div class="row g-3">
                                        <div class="col-md-4 col-sm-6" v-for="service in services" :key="service.id">
                                            <div 
                                                class="service-card p-3 border rounded-3 cursor-pointer text-center h-100 d-flex flex-column justify-content-center"
                                                :class="{ 'selected-service': form.medical_service_id === service.id }"
                                                @click="form.medical_service_id = service.id"
                                            >
                                                <div class="service-icon mb-2">
                                                    <i class="fa fa-stethoscope fa-2x" :class="form.medical_service_id === service.id ? 'text-primary' : 'text-muted'"></i>
                                                </div>
                                                <h6 class="mb-0 text-truncate" :title="service.name" style="font-size: 0.95rem;">{{ service.name }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-end mt-5">
                                        <button 
                                            type="button" 
                                            class="btn btn-primary px-4 rounded-pill" 
                                            @click="nextStep"
                                            :disabled="!form.medical_service_id"
                                        >
                                            Suivant <i class="fa fa-arrow-right ms-2"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- STEP 2: DATE & HEURE -->
                                <div v-if="currentStep === 2" class="step-content animate__animated animate__fadeIn">
                                    <h4 class="mb-4 text-center">Choisissez la date et l'heure</h4>
                                    
                                    <div class="row mb-4">
                                        <div class="col-md-6 mb-4 mb-md-0">
                                            <label class="form-label fw-bold">Date souhaitée</label>
                                            <DatePickerComponent v-model="form.appointment_date" minDate="today" placeholder="Choisir une date" />
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Heure</label>
                                            
                                            <div v-if="!form.appointment_date" class="text-muted small p-3 bg-light rounded text-center">
                                                Veuillez d'abord sélectionner une date.
                                            </div>
                                            <div v-else class="time-slots-container">
                                                <div class="row g-2">
                                                    <div class="col-4" v-for="slot in timeSlots" :key="slot.value">
                                                        <button
                                                            type="button"
                                                            class="btn btn-outline-primary w-100 time-slot-btn p-2"
                                                            :class="{ 'active': form.appointment_time === slot.value }"
                                                            :disabled="slot.disabled"
                                                            @click="form.appointment_time = slot.value"
                                                        >
                                                            <span class="small">{{ slot.label }}</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div v-if="isPastDateTime" class="text-danger small mt-2 text-center">
                                                <i class="fa fa-exclamation-circle me-1"></i>
                                                Cette heure est déjà passée.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between mt-5">
                                        <button type="button" class="btn btn-outline-secondary px-4 rounded-pill" @click="prevStep">
                                            <i class="fa fa-arrow-left me-2"></i> Retour
                                        </button>
                                        <button 
                                            type="button" 
                                            class="btn btn-primary px-4 rounded-pill" 
                                            @click="nextStep"
                                            :disabled="!form.appointment_date || !form.appointment_time || isPastDateTime"
                                        >
                                            Suivant <i class="fa fa-arrow-right ms-2"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- STEP 3: SUMMARY & MOTIF -->
                                <div v-if="currentStep === 3" class="step-content animate__animated animate__fadeIn">
                                    <h4 class="mb-4 text-center">Résumé de votre demande</h4>
                                    
                                    <div class="card bg-light border-0 mb-4 shadow-sm">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-sm-4 fw-bold text-muted mb-1 mb-sm-0">Service :</div>
                                                <div class="col-sm-8 fw-semibold text-primary">{{ selectedServiceName }}</div>
                                            </div>
                                            <hr class="my-2">
                                            <div class="row">
                                                <div class="col-sm-4 fw-bold text-muted mb-1 mb-sm-0">Date :</div>
                                                <div class="col-sm-8 fw-semibold">{{ formattedDate }}</div>
                                            </div>
                                            <hr class="my-2">
                                            <div class="row">
                                                <div class="col-sm-4 fw-bold text-muted mb-1 mb-sm-0">Heure :</div>
                                                <div class="col-sm-8 fw-semibold">{{ form.appointment_time }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label fw-bold">Motif / Symptômes (Facultatif)</label>
                                        <textarea v-model="form.reason" class="form-control shadow-sm" rows="3" placeholder="Décrivez brièvement le sujet de votre visite..."></textarea>
                                    </div>

                                    <div class="d-flex justify-content-between mt-5">
                                        <button type="button" class="btn btn-outline-secondary px-4 rounded-pill" @click="prevStep">
                                            <i class="fa fa-arrow-left me-2"></i> Retour
                                        </button>
                                        <button
                                            type="submit"
                                            class="btn btn-primary px-4 rounded-pill fw-bold"
                                            :disabled="form.processing || isPastDateTime"
                                        >
                                            <span v-if="form.processing" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                            <i v-else class="fa fa-check-circle me-2"></i>
                                            Confirmer le rendez-vous
                                        </button>
                                    </div>
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
import { ref, computed, watch } from "vue";
import DatePickerComponent from '../../components/DateComponent.vue';

const props = defineProps({
    services: Array,
    selectedServiceId: [String, Number],
    selectedDate: String,
    selectedTime: String,
    bookedSlots: {
        type: Array,
        default: () => []
    }
});

const form = useForm({
    medical_service_id: props.selectedServiceId || "",
    appointment_date: props.selectedDate || "",
    appointment_time: props.selectedTime || "",
    reason: "",
});

const currentStep = ref(1);

const progressWidth = computed(() => {
    if (currentStep.value === 1) return '0%';
    if (currentStep.value === 2) return '50%';
    return '100%';
});

const nextStep = () => {
    if (currentStep.value < 3) currentStep.value++;
};

const prevStep = () => {
    if (currentStep.value > 1) currentStep.value--;
};

const selectedServiceName = computed(() => {
    if (!form.medical_service_id) return '';
    const service = props.services.find(s => s.id === form.medical_service_id);
    return service ? service.name : '';
});

const formattedDate = computed(() => {
    if (!form.appointment_date) return '';
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const date = new Date(form.appointment_date);
    return date.toLocaleDateString('fr-FR', options);
});

// ─── Helpers ────────────────────────────────────────────────────────────────

const isToday = computed(() => {
    if (!form.appointment_date) return false;
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm   = String(today.getMonth() + 1).padStart(2, '0');
    const dd   = String(today.getDate()).padStart(2, '0');
    return form.appointment_date === `${yyyy}-${mm}-${dd}`;
});

const nowMinutes = computed(() => {
    const now = new Date();
    return now.getHours() * 60 + now.getMinutes();
});

const timeSlots = computed(() => {
    const slots = [];
    const start = 8 * 60;   // 08:00
    const end   = 18 * 60;  // 18:00

    for (let m = start; m <= end; m += 30) {
        const h   = Math.floor(m / 60);
        const min = m % 60;
        const hh  = String(h).padStart(2, '0');
        const mm  = String(min).padStart(2, '0');
        const value = `${hh}:${mm}`;
        const label = `${hh}:${mm}`;

        let disabled = isToday.value && m <= nowMinutes.value + 5;

        let isBooked = false;
        if (form.appointment_date && form.medical_service_id) {
            isBooked = props.bookedSlots.some(slot => {
                const dateStr = slot.appointment_date ? String(slot.appointment_date).substring(0, 10) : '';
                const formDateStr = form.appointment_date ? String(form.appointment_date).substring(0, 10) : '';
                const isSameDate = dateStr === formDateStr;
                
                const isSameService = String(slot.medical_service_id) === String(form.medical_service_id);
                
                const timeStr = slot.appointment_time ? String(slot.appointment_time).substring(0, 5) : '';
                const isSameTime = timeStr === value;
                
                return isSameDate && isSameService && isSameTime;
            });
        }
        
        if (isBooked) {
            continue; // Skip the slot entirely so it doesn't appear
        }

        slots.push({ value, label, disabled });
    }
    return slots;
});

const isPastDateTime = computed(() => {
    if (!form.appointment_date || !form.appointment_time) return false;
    if (!isToday.value) return false;

    const [h, m] = form.appointment_time.split(':').map(Number);
    const selectedMinutes = h * 60 + m;
    return selectedMinutes <= nowMinutes.value + 5;
});

watch(() => form.appointment_date, () => {
    if (isToday.value && form.appointment_time) {
        const [h, m] = form.appointment_time.split(':').map(Number);
        const selectedMinutes = h * 60 + m;
        if (selectedMinutes <= nowMinutes.value + 5) {
            form.appointment_time = "";
        }
    }
});

const submit = () => {
    if (isPastDateTime.value) return;
    form.post(route('front.appointments.store'));
};
</script>

<style scoped>
.reservation-page { background-color: #f8f9fa; min-height: 90vh; }
.card { border-radius: 1.5rem; }
.form-label { color: #344767; }
.btn-primary { background: linear-gradient(310deg, #2152ff, #21d4fd); border: none; }
.btn-primary:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(33, 82, 255, 0.4); }
.btn-outline-primary { border-color: #2152ff; color: #2152ff; }
.btn-outline-primary:hover:not(:disabled) { background-color: #2152ff; color: #fff; }

/* Stepper */
.stepper-container { z-index: 2; padding: 0 10%; }
.step { position: relative; z-index: 2; }
.step-icon {
    width: 35px; height: 35px;
    border-radius: 50%;
    background-color: rgba(255,255,255,0.3);
    color: white;
    display: flex; align-items: center; justify-content: center;
    font-weight: bold; margin: 0 auto;
    transition: all 0.3s;
    border: 2px solid transparent;
}
.step.active .step-icon {
    background-color: white;
    color: #2152ff;
    box-shadow: 0 0 0 3px rgba(255,255,255,0.5);
}

/* Service Cards */
.cursor-pointer { cursor: pointer; }
.service-card { transition: all 0.2s ease; background: #fff; border-color: #e9ecef !important; }
.service-card:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.05); border-color: #2152ff !important; }
.selected-service { border-color: #2152ff !important; background-color: rgba(33, 82, 255, 0.05); box-shadow: 0 5px 15px rgba(33, 82, 255, 0.1); }

/* Time Slots */
.time-slots-container {
    max-height: 250px;
    overflow-y: auto;
    padding-right: 5px;
}
/* custom scrollbar */
.time-slots-container::-webkit-scrollbar { width: 5px; }
.time-slots-container::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 5px; }
.time-slots-container::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 5px; }
.time-slots-container::-webkit-scrollbar-thumb:hover { background: #a8a8a8; }

.time-slot-btn.active {
    background: linear-gradient(310deg, #2152ff, #21d4fd);
    color: white; border: none;
}
</style>
