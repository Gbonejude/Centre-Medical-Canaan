<template>
    <Head>
        <title>Gérer le Rendez-vous | CMC</title>
    </Head>

    <div class="content-wrapper">
        <div class="container">
            <div class="navigation-links">
                <Link :href="route('appointments.index')" class="back-link">
                    <i class="fa fa-arrow-left"></i>
                    <span>Retour aux Rendez-vous</span>
                </Link>
            </div>

            <div class="row">
                <div class="col-lg-7">
                    <!-- Détails du Rendez-vous -->
                    <div class="card info-card mb-4">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="fa fa-info-circle me-2"></i>
                                <span>Détails de la Demande</span>
                            </div>
                            <span class="status-badge" :class="appointment.status.toLowerCase()">
                                {{ translateStatus(appointment.status) }}
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="detail-grid">
                                <div class="detail-item">
                                    <div class="label">Patient</div>
                                    <div class="value">{{ appointment.patient?.lastname }} {{ appointment.patient?.firstname }}</div>
                                </div>
                                <div class="detail-item">
                                    <div class="label">Service Demandé</div>
                                    <div class="value text-primary fw-bold">{{ appointment.medical_service?.name }}</div>
                                </div>
                                <div class="detail-item">
                                    <div class="label">Date Prévue</div>
                                    <div class="value">{{ formatDate(appointment.appointment_date) }}</div>
                                </div>
                                <div class="detail-item">
                                    <div class="label">Heure Prévue</div>
                                    <div class="value">{{ formatTime(appointment.appointment_time) }}</div>
                                </div>
                                <div class="detail-item full-width mt-2">
                                    <div class="label">Motif de consultation</div>
                                    <div class="value bio-text">{{ appointment.reason || 'Aucun motif renseigné.' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="card info-card mb-4" v-if="appointment.receptionist_notes || appointment.doctor_notes">
                        <div class="card-header bg-light">
                            <div class="card-title">
                                <i class="fa fa-sticky-note me-2"></i>
                                <span>Notes Médicales / Administration</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div v-if="appointment.receptionist_notes" class="note-item mb-3">
                                <div class="note-label">Note Réception :</div>
                                <div class="note-content">{{ appointment.receptionist_notes }}</div>
                            </div>
                            <div v-if="appointment.doctor_notes" class="note-item">
                                <div class="note-label">Note Docteur :</div>
                                <div class="note-content">{{ appointment.doctor_notes }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <!-- Actions d'Affectation -->
                    <div class="card action-card mb-4" v-if="['PENDING', 'POSTPONED'].includes(appointment.status)">
                        <div class="card-header bg-primary text-white">
                            <div class="card-title text-white">
                                <i class="fa fa-user-md me-2"></i>
                                <span>Affecter un Médecin</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <form @submit.prevent="confirmAppointment">
                                <div class="form-group mb-3">
                                    <label class="form-label">Choisir un médecin disponible <span class="required">*</span></label>
                                    <div class="input-wrapper">
                                        <select class="form-control" v-model="assignForm.doctor_id" required :disabled="filteredDoctors.length === 0">
                                            <option value="" disabled>{{ filteredDoctors.length === 0 ? 'Aucun médecin pour ce service' : 'Sélectionner un docteur' }}</option>
                                            <option v-for="doctor in filteredDoctors" :key="doctor.id" :value="doctor.id">
                                                Dr. {{ doctor.user.lastname }} {{ doctor.user.firstname }} ({{ (doctor.medical_service?.name || doctor.medicalService?.name) || 'Généraliste' }})
                                            </option>
                                        </select>
                                        <div v-if="filteredDoctors.length === 0" class="alert alert-warning mt-2 py-2 small">
                                            <i class="fa fa-exclamation-triangle me-1"></i>
                                            Aucun médecin n'est rattaché au service <strong>{{ appointment.medical_service?.name }}</strong>.
                                        </div>
                                    </div>
                                    <p class="text-muted small mt-2">
                                        <i class="fa fa-info-circle me-1"></i>
                                        Seuls les médecins rattachés au service <strong>{{ appointment.medical_service?.name }}</strong> sont affichés.
                                    </p>
                                </div>
                                <div class="form-group mb-4">
                                    <label class="form-label">Notes de réception</label>
                                    <textarea class="form-control" v-model="assignForm.receptionist_notes" rows="3" placeholder="Instructions pour le docteur ou le patient..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary w-100" :disabled="assignForm.processing">
                                    <i class="fa fa-check-circle me-1"></i>
                                    {{ assignForm.processing ? 'Chargement...' : (appointment.status === 'POSTPONED' ? 'Confirmer la nouvelle date' : 'Confirmer le Rendez-vous') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Changement de Statut (Visible pour tous les statuts sauf COMPLETED) -->
                    <div class="card action-card" v-if="appointment.status !== 'COMPLETED'">
                        <div class="card-header">
                            <div class="card-title">Actions Rapides</div>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <!-- Terminer (Seulement si confirmé) -->
                                <button v-if="appointment.status === 'CONFIRMED'" @click="updateStatus('COMPLETED')" class="btn btn-success">
                                    <i class="fa fa-check-double me-1"></i> Marquer comme Terminé
                                </button>
                                
                                <!-- Annuler/Reporter (Seulement pour réception/admin) -->
                                <template v-if="!isDoctor">
                                    <button v-if="['CONFIRMED', 'PENDING'].includes(appointment.status)" @click="updateStatus('CANCELLED')" class="btn btn-outline-danger">
                                        <i class="fa fa-times-circle me-1"></i> Annuler le Rendez-vous
                                    </button>
                                    <button v-if="['CONFIRMED', 'PENDING'].includes(appointment.status)" @click="updateStatus('POSTPONED')" class="btn btn-warning text-white">
                                        <i class="fa fa-calendar-alt me-1"></i> Reprogrammer la date
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Message si déjà terminé -->
                    <div class="card action-card mt-3" v-if="appointment.status === 'COMPLETED'">
                        <div class="card-body">
                            <div class="alert alert-success py-2 mb-0">
                                <i class="fa fa-check-circle me-1"></i> Ce rendez-vous est déjà terminé.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Reporter le Rendez-vous -->
    <div v-if="showPostponeModal" class="modal-backdrop-custom" @click.self="showPostponeModal = false">
        <div class="modal-dialog-custom">
            <div class="modal-header-custom">
                <h5 class="modal-title-custom">
                    <i class="fa fa-calendar-alt me-2 text-warning"></i>
                    Reprogrammer le rendez-vous
                </h5>
                <button type="button" class="btn-close" @click="showPostponeModal = false"></button>
            </div>
            <div class="modal-body-custom">
                <form @submit.prevent="submitPostpone">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Nouvelle Date <span class="required">*</span></label>
                        <DatePickerComponent v-model="postponeForm.appointment_date" minDate="today" placeholder="Choisir une date" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Nouvelle Heure <span class="required">*</span></label>
                        <input type="time" v-model="postponeForm.appointment_time" class="form-control" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Note / Motif du report</label>
                        <textarea v-model="postponeForm.notes" class="form-control" rows="2" placeholder="Ex: Patient indisponible..."></textarea>
                    </div>
                    <div class="d-flex gap-2 justify-content-end mt-4">
                        <button type="button" class="btn btn-outline-secondary" @click="showPostponeModal = false">Annuler</button>
                        <button type="submit" class="btn btn-warning text-white" :disabled="postponeForm.processing">
                            <i class="fa fa-check me-1"></i> Confirmer le report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useToast } from "vue-toastification";
import Swal from 'sweetalert2';
import DatePickerComponent from '../../components/DateComponent.vue';

const props = defineProps({
    appointment: Object,
    isDoctor: Boolean,
    doctors: Array,
});

const filteredDoctors = computed(() => {
    return props.doctors || [];
});

const toast = useToast();

const showPostponeModal = ref(false);
const postponeForm = useForm({
    status: 'POSTPONED',
    appointment_date: '',
    appointment_time: '',
    notes: '',
});

const assignForm = useForm({
    doctor_id: '',
    receptionist_notes: '',
});

function confirmAppointment() {
    assignForm.post(route('appointments.confirm', props.appointment.uuid), {
        onSuccess: () => {
            toast.success('Rendez-vous confirmé et médecin affecté');
        }
    });
}

function submitPostpone() {
    postponeForm.put(route('appointments.update-status', props.appointment.uuid), {
        onSuccess: () => {
            showPostponeModal.value = false;
            toast.success('Rendez-vous reporté avec succès');
        }
    });
}

function updateStatus(newStatus) {
    const actions = {
        'CONFIRMED': 'confirmer',
        'COMPLETED': 'terminer',
        'CANCELLED': 'annuler',
        'POSTPONED': 'reporter',
        'PENDING': 'remettre en attente'
    };
    
    const actionText = actions[newStatus] || 'modifier';

    if (newStatus === 'POSTPONED') {
        postponeForm.appointment_date = props.appointment.appointment_date?.substring(0, 10) ?? '';
        postponeForm.appointment_time = props.appointment.appointment_time?.substring(0, 5) ?? '';
        postponeForm.notes = '';
        showPostponeModal.value = true;
        return;
    } else {
        Swal.fire({
            title: 'Confirmer l\'action',
            text: `Voulez-vous vraiment ${actionText} ce rendez-vous ?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Oui, confirmer',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                useForm({ status: newStatus }).put(route('appointments.update-status', props.appointment.uuid), {
                    onSuccess: () => toast.success('Statut mis à jour')
                });
            }
        });
    }
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' });
}

function formatTime(time) {
    return time.substring(0, 5);
}

function translateStatus(status) {
    const statuses = {
        'PENDING': 'En attente',
        'CONFIRMED': 'Confirmé',
        'COMPLETED': 'Terminé',
        'CANCELLED': 'Annulé',
        'POSTPONED': 'Reporté'
    };
    return statuses[status] || status;
}
</script>

<style lang="scss" scoped>
$primary-color: #4361ee;
$secondary-color: #3f4254;
$success-color: #0abb87;
$warning-color: #ff9f43;
$danger-color: #f64e60;
$border-color: #e4e6ef;
$body-color: #f9fafb;
$border-radius: 0.475rem;

.content-wrapper { background-color: $body-color; min-height: calc(100vh - 65px); padding: 2rem 0; }
.navigation-links { margin-bottom: 1.5rem; .back-link { display: inline-flex; align-items: center; text-decoration: none; color: $secondary-color; font-weight: 500; transition: all 0.3s ease; i { margin-right: 0.5rem; } &:hover { color: $primary-color; transform: translateX(-3px); } } }

.card { border: none; border-radius: $border-radius; box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.05); overflow: hidden; }
.card-header { background: white; padding: 1.25rem 1.5rem; border-bottom: 1px solid $border-color; display: flex; justify-content: space-between; align-items: center; .card-title { font-weight: 600; font-size: 1.1rem; margin: 0; } }

.detail-grid {
    display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;
    .detail-item {
        &.full-width { grid-column: span 2; }
        .label { font-size: 0.8rem; color: #7e8299; text-transform: uppercase; margin-bottom: 0.25rem; }
        .value { font-weight: 600; color: #181c32; }
    }
}

.status-badge {
    padding: 0.35rem 1rem; border-radius: 20px; font-size: 0.85rem; font-weight: 600;
    &.pending { background-color: rgba($warning-color, 0.1); color: $warning-color; }
    &.confirmed { background-color: rgba($primary-color, 0.1); color: $primary-color; }
    &.completed { background-color: rgba($success-color, 0.1); color: $success-color; }
}

.note-item { .note-label { font-weight: 600; color: $secondary-color; margin-bottom: 0.25rem; } .note-content { color: #5e6278; font-style: italic; } }

.form-label { font-weight: 500; }
.form-control { border-radius: 8px; border: 1px solid $border-color; padding: 0.6rem 1rem; }
.required { color: $danger-color; }

.modal-backdrop-custom {
    position: fixed; inset: 0; background: rgba(0,0,0,0.45);
    display: flex; align-items: center; justify-content: center; z-index: 1050;
}
.modal-dialog-custom {
    background: #fff; border-radius: 12px; width: 100%; max-width: 480px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.18); overflow: hidden;
}
.modal-header-custom {
    display: flex; justify-content: space-between; align-items: center;
    padding: 1.25rem 1.5rem; border-bottom: 1px solid $border-color;
    .modal-title-custom { font-weight: 600; font-size: 1.05rem; margin: 0; }
}
.modal-body-custom { padding: 1.5rem; }
</style>
