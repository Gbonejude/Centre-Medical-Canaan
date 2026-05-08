<template>
    <Head>
        <title>Horaires du Dr. {{ doctor.user.lastname }} | CMC</title>
    </Head>

    <div class="content-wrapper">
        <div class="container">
            <div class="navigation-links">
                <Link :href="route('schedules.index')" class="back-link">
                    <i class="fa fa-arrow-left"></i>
                    <span>Retour aux Disponibilités</span>
                </Link>
            </div>

            <div class="card schedule-edit-card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa fa-clock icon-circle"></i>
                        <span>Disponibilités Hebdomadaires : Dr. {{ doctor.user.lastname }}</span>
                    </div>
                </div>

                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle me-2"></i>
                        Définissez les plages horaires durant lesquelles le médecin peut recevoir des patients.
                    </div>

                    <form @submit.prevent="saveSchedule" class="mt-4">
                        <div class="weekly-grid">
                            <div v-for="(dayName, dayKey) in days" :key="dayKey" class="day-row">
                                <div class="day-info">
                                    <div class="day-label">{{ dayName }}</div>
                                    <div class="premium-switch">
                                        <input type="checkbox" v-model="form.availability[dayKey].enabled" :id="'switch-'+dayKey">
                                        <label :for="'switch-'+dayKey" class="slider"></label>
                                    </div>
                                </div>

                                <div class="time-slots" :class="{ 'disabled': !form.availability[dayKey].enabled }">
                                    <div v-if="form.availability[dayKey].enabled" class="slots-container">
                                        <div v-for="(slot, index) in form.availability[dayKey].slots" :key="index" class="slot-item">
                                            <div class="time-input-group">
                                                <input type="time" v-model="slot.start" class="form-control form-control-sm">
                                                <span class="separator">à</span>
                                                <input type="time" v-model="slot.end" class="form-control form-control-sm">
                                            </div>
                                            <button type="button" @click="removeSlot(dayKey, index)" class="btn-remove" v-if="form.availability[dayKey].slots.length > 1">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </div>
                                        <button type="button" @click="addSlot(dayKey)" class="btn-add-slot">
                                            <i class="fa fa-plus-circle me-1"></i> Ajouter une plage
                                        </button>
                                    </div>
                                    <div v-else class="closed-label text-muted italic">
                                        Indisponible (Fermé)
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions mt-5 pt-4 border-top">
                            <div class="action-buttons">
                                <Link :href="route('schedules.index')" class="btn btn-outline-secondary">
                                    Annuler
                                </Link>
                                <button type="submit" class="btn btn-primary" :disabled="form.processing">
                                    <i class="fa fa-save me-2"></i>
                                    {{ form.processing ? 'Enregistrement...' : 'Enregistrer les Disponibilités' }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useToast } from "vue-toastification";

const props = defineProps({
    doctor: Object,
    availability: Object,
});

const toast = useToast();

const days = {
    monday: 'Lundi',
    tuesday: 'Mardi',
    wednesday: 'Mercredi',
    thursday: 'Jeudi',
    friday: 'Vendredi',
    saturday: 'Samedi',
    sunday: 'Dimanche',
};

const form = useForm({
    availability: JSON.parse(JSON.stringify(props.availability)), // Deep copy
});

function addSlot(dayKey) {
    form.availability[dayKey].slots.push({ start: '07:00', end: '17:00' });
}

function removeSlot(dayKey, index) {
    form.availability[dayKey].slots.splice(index, 1);
}

function saveSchedule() {
    form.put(route('schedules.update', props.doctor.uuid), {
        onSuccess: () => {
            toast.success('Disponibilités mises à jour avec succès');
        }
    });
}
</script>

<style lang="scss" scoped>
$primary-color: #4361ee;
$secondary-color: #3f4254;
$border-color: #e4e6ef;
$body-color: #f9fafb;
$border-radius: 0.475rem;

.content-wrapper { background-color: $body-color; min-height: calc(100vh - 65px); padding: 2rem 0; }
.navigation-links { margin-bottom: 1.5rem; .back-link { display: inline-flex; align-items: center; text-decoration: none; color: $secondary-color; font-weight: 500; transition: all 0.3s ease; i { margin-right: 0.5rem; } &:hover { color: $primary-color; transform: translateX(-3px); } } }

.schedule-edit-card {
    border: none; border-radius: $border-radius; box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.05); background: white;
    .card-header { padding: 1.5rem; border-bottom: 1px solid $border-color; .card-title { font-weight: 700; font-size: 1.2rem; display: flex; align-items: center; .icon-circle { width: 35px; height: 35px; border-radius: 50%; background: rgba($primary-color, 0.1); color: $primary-color; display: flex; align-items: center; justify-content: center; margin-right: 0.75rem; } } }
}

.weekly-grid {
    .day-row {
        display: grid; grid-template-columns: 200px 1fr; align-items: start; padding: 1.5rem 0; border-bottom: 1px solid #f1f3f9;
        &:last-child { border-bottom: none; }
        .day-info {
            display: flex; align-items: center; justify-content: space-between; padding-right: 2rem;
            .day-label { font-weight: 700; color: #181c32; font-size: 1rem; }
        }
    }
}

.premium-switch {
    position: relative; width: 42px; height: 22px;
    input { opacity: 0; width: 0; height: 0; &:checked + .slider { background-color: $primary-color; } &:checked + .slider:before { transform: translateX(18px); } }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #e4e6ef; transition: .4s; border-radius: 24px; &:before { position: absolute; content: ""; height: 16px; width: 16px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; } }
}

.time-slots {
    &.disabled { opacity: 0.5; }
    .slots-container { display: flex; flex-wrap: wrap; gap: 1rem; align-items: center; }
    .slot-item {
        display: flex; align-items: center; gap: 0.5rem; background: #f8f9fa; padding: 0.4rem 0.75rem; border-radius: 8px; border: 1px solid #eee;
        .time-input-group { display: flex; align-items: center; gap: 0.5rem; .form-control { width: 90px; border: 1px solid #ddd; } .separator { color: #7e8299; font-size: 0.8rem; } }
        .btn-remove { border: none; background: transparent; color: #f64e60; padding: 0.2rem; &:hover { color: darken(#f64e60, 10%); } }
    }
    .btn-add-slot { border: 1px dashed #4361ee; background: transparent; color: #4361ee; font-size: 0.8rem; font-weight: 600; padding: 0.4rem 0.8rem; border-radius: 8px; &:hover { background: rgba(#4361ee, 0.05); } }
}

.form-actions { .action-buttons { display: flex; justify-content: flex-end; gap: 1rem; .btn { padding: 0.6rem 1.5rem; border-radius: 8px; font-weight: 600; } } }
</style>
