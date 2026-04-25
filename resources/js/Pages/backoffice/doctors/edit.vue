<template>
    <Head>
        <title>Modifier Médecin | Dr. {{ doctor.user.lastname }}</title>
    </Head>

    <div class="content-wrapper">
        <div class="container">
            
            <div class="navigation-links">
                <Link :href="route('doctors.index')" class="back-link">
                    <i class="fa fa-arrow-left"></i>
                    <span>Retour à l'Équipe</span>
                </Link>
            </div>

            <div class="card form-card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa fa-user-edit icon-circle"></i>
                        <span>Modifier le Médecin</span>
                    </div>
                    <p class="card-subtitle">Mettez à jour les informations professionnelles du Dr. {{ doctor.user.lastname }}</p>
                </div>

                <div class="card-body">
                    <form @submit.prevent="update" class="user-form">
                        <div class="form-section">
                            <div class="section-title">Informations Personnelles</div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="firstname">Prénom <span class="required">*</span></label>
                                        <div class="input-wrapper">
                                            <i class="fa fa-user input-icon"></i>
                                            <input id="firstname" type="text" class="form-control"
                                                v-model="form.firstname" placeholder="Prénom"
                                                :class="{ 'is-invalid': form.errors.firstname }" />
                                        </div>
                                        <div v-if="form.errors.firstname" class="error-message">{{ form.errors.firstname }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="lastname">Nom <span class="required">*</span></label>
                                        <div class="input-wrapper">
                                            <i class="fa fa-user input-icon"></i>
                                            <input id="lastname" type="text" class="form-control"
                                                v-model="form.lastname" placeholder="Nom"
                                                :class="{ 'is-invalid': form.errors.lastname }" />
                                        </div>
                                        <div v-if="form.errors.lastname" class="error-message">{{ form.errors.lastname }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email <span class="required">*</span></label>
                                        <div class="input-wrapper">
                                            <i class="fa fa-envelope input-icon"></i>
                                            <input id="email" type="email" class="form-control"
                                                v-model="form.email" placeholder="email@exemple.com"
                                                :class="{ 'is-invalid': form.errors.email }" />
                                        </div>
                                        <div v-if="form.errors.email" class="error-message">{{ form.errors.email }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">Téléphone</label>
                                        <div :class="['phone-input-wrapper', { 'phone-input-wrapper--invalid': form.errors.phone }]">
                                            <VueTelInput
                                                v-model="form.phone"
                                                mode="international"
                                                default-country="TG"
                                                :auto-default-country="true"
                                                :dropdown-options="phoneDropdownOptions"
                                                :input-options="getPhoneInputOptions(form.errors.phone)"
                                                @validate="(state) => isPhoneValid = state.valid"
                                                style-classes="phone-input"
                                            />
                                        </div>
                                        <div v-if="form.errors.phone" class="error-message">{{ form.errors.phone }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="birthday">Date de naissance</label>
                                        <div class="input-wrapper">
                                            <i class="fa fa-calendar input-icon"></i>
                                            <input id="birthday" type="date" class="form-control"
                                                v-model="form.birthday"
                                                :class="{ 'is-invalid': form.errors.birthday }" />
                                        </div>
                                        <div v-if="form.errors.birthday" class="error-message">{{ form.errors.birthday }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Genre <span class="required">*</span></label>
                                        <div class="gender-selection d-flex gap-4 mt-2">
                                            <label class="radio-label">
                                                <input type="radio" v-model="form.gender" value="male" />
                                                <span>Masculin</span>
                                            </label>
                                            <label class="radio-label">
                                                <input type="radio" v-model="form.gender" value="female" />
                                                <span>Féminin</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="section-title">Spécialisations & Services</div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Service Médical <span class="required">*</span></label>
                                        <div class="input-wrapper">
                                            <i class="fa fa-hospital input-icon"></i>
                                            <v-select
                                                v-model="form.service_id"
                                                :options="services"
                                                :reduce="service => service.id"
                                                label="name"
                                                placeholder="Choisir le service..."
                                                class="custom-v-select"
                                                :class="{ 'is-invalid': form.errors.service_id }"
                                            />
                                        </div>
                                        <div v-if="form.errors.service_id" class="error-message">{{ form.errors.service_id }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Spécialités <span class="required">*</span></label>
                                        <div class="input-wrapper">
                                            <i class="fa fa-award input-icon"></i>
                                            <v-select
                                                v-model="form.specialty_ids"
                                                :options="specialties"
                                                :reduce="specialty => specialty.id"
                                                label="name"
                                                placeholder="Choisir les spécialités..."
                                                class="custom-v-select"
                                                :class="{ 'is-invalid': form.errors.specialty_ids }"
                                                multiple
                                            />
                                        </div>
                                        <div v-if="form.errors.specialty_ids" class="error-message">{{ form.errors.specialty_ids }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="bio">Biographie</label>
                                        <textarea id="bio" class="form-control" rows="4" v-model="form.bio"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="premium-switch-container">
                                        <div class="switch-info">
                                            <span class="switch-label">Disponibilité du Médecin</span>
                                            <span class="switch-desc">Activer ou désactiver la prise de rendez-vous</span>
                                        </div>
                                        <label class="premium-switch">
                                            <input type="checkbox" v-model="form.is_available">
                                            <span class="slider"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions mt-5">
                            <div class="action-buttons">
                                <Link :href="route('doctors.index')" class="btn btn-outline-secondary">
                                    <i class="fa fa-times"></i>
                                    <span>Annuler</span>
                                </Link>

                                <button type="submit" class="btn btn-primary" :disabled="form.processing">
                                    <i class="fa fa-save"></i>
                                    <span>{{ form.processing ? 'Mise à jour...' : 'Mettre à jour' }}</span>
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
import { ref } from 'vue';
import { useToast } from "vue-toastification";
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import { VueTelInput } from 'vue-tel-input';
import 'vue-tel-input/vue-tel-input.css';

const props = defineProps({
    doctor: Object,
    services: Array,
    specialties: Array
});

const toast = useToast();
const isPhoneValid = ref(false);

const phoneDropdownOptions = {
    disabled: false,
    showFlags: true,
    showDialCodeInSelection: true,
    showDialCodeInList: true,
    showSearchBox: true,
};

const getPhoneInputOptions = (hasError) => ({
    id: 'phone',
    name: 'phone',
    placeholder: 'Ex: 90 00 00 00',
    styleClasses: hasError ? 'form-control phone-input-control is-invalid' : 'form-control phone-input-control',
});

const form = useForm({
    firstname: props.doctor.user.firstname,
    lastname:  props.doctor.user.lastname,
    email:     props.doctor.user.email,
    phone:     props.doctor.user.phone,
    service_id:    props.doctor.medical_service_id,
    specialty_ids: props.doctor.specialties.map(s => s.id),
    bio:          props.doctor.bio,
    is_available: props.doctor.is_available,
    gender:       props.doctor.user.gender || 'male',
    birthday:     props.doctor.user.birthday || '',
});

function update() {
    form.put(route("doctors.update", props.doctor.uuid), {
        onSuccess: () => {
            toast.success('Médecin mis à jour avec succès');
        },
        onError: () => {
            toast.error('Veuillez vérifier les erreurs du formulaire');
        }
    });
}
</script>

<style lang="scss" scoped>
$primary-color: #4361ee;
$secondary-color: #3f4254;
$danger-color: #f64e60;
$border-color: #e4e6ef;
$body-color: #f9fafb;
$border-radius: 0.475rem;

.content-wrapper { background-color: $body-color; min-height: calc(100vh - 65px); padding: 2rem 0; }
.navigation-links { margin-bottom: 1.5rem; .back-link { display: inline-flex; align-items: center; text-decoration: none; color: $secondary-color; font-weight: 500; transition: all 0.3s ease; i { margin-right: 0.5rem; } &:hover { color: $primary-color; transform: translateX(-3px); } } }

.form-card { background-color: white; border-radius: $border-radius; box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.05); border: none; .card-header { padding: 1.5rem; border-bottom: 1px solid $border-color; .card-title { font-weight: 600; font-size: 1.25rem; display: flex; align-items: center; .icon-circle { width: 35px; height: 35px; border-radius: 50%; background: rgba($primary-color, 0.1); color: $primary-color; display: flex; align-items: center; justify-content: center; margin-right: 0.75rem; } } .card-subtitle { margin: 0; color: #7e8299; font-size: 0.9rem; padding-left: 3rem; } } .card-body { padding: 1.5rem; } }

.form-section { margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px dashed $border-color; &:last-child { border-bottom: none; } .section-title { font-weight: 600; color: $secondary-color; margin-bottom: 1.25rem; padding-left: 0.75rem; position: relative; &::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: $primary-color; border-radius: 4px; } } }

.form-group { label { font-weight: 600; margin-bottom: 0.5rem; display: block; .required { color: $danger-color; } } .input-wrapper { position: relative; .input-icon { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #a1a5b7; z-index: 10; pointer-events: none; } .form-control { padding: 0.6rem 1rem 0.6rem 2.5rem; border-radius: 8px; border: 1px solid $border-color; &:focus { border-color: $primary-color; box-shadow: 0 0 0 0.25rem rgba($primary-color, 0.1); } } } .phone-input-wrapper { :deep(.phone-input) { border: 1px solid $border-color; border-radius: $border-radius; &:focus-within { border-color: $primary-color; box-shadow: 0 0 0 0.25rem rgba($primary-color, 0.25); } } :deep(.vti__dropdown) { border-right: 1px solid $border-color; background: #f8f9fa; } &--invalid :deep(.phone-input) { border-color: $danger-color; } } .error-message { color: $danger-color; font-size: 0.8rem; margin-top: 0.4rem; } }

.custom-v-select {
    :deep(.vs__dropdown-toggle) {
        padding: 0.3rem 0.5rem 0.3rem 2.5rem;
        border-radius: 8px;
        border: 1px solid $border-color;
        font-size: 0.95rem;
        min-height: 42px;
    }
    :deep(.vs__selected-options) { padding: 0; }
    :deep(.vs__search) { margin: 0; padding-left: 0; }
    
    &.is-invalid :deep(.vs__dropdown-toggle) {
        border-color: $danger-color;
    }
}

.premium-switch-container {
    display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.25rem; background-color: #f8f9fa; border-radius: 0.75rem; border: 1px solid $border-color;
    .switch-label { font-weight: 600; color: $secondary-color; font-size: 0.95rem; display: block; }
    .switch-desc { font-size: 0.8rem; color: #7e8299; }
}

.premium-switch {
    position: relative; display: inline-block; width: 46px; height: 24px;
    input { opacity: 0; width: 0; height: 0; &:checked + .slider { background-color: $primary-color; } &:checked + .slider:before { transform: translateX(22px); } }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #e4e6ef; transition: .4s; border-radius: 24px; &:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; } }
}

.form-actions { display: flex; justify-content: flex-end; .action-buttons { display: flex; gap: 1rem; .btn { display: flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.5rem; border-radius: 8px; font-weight: 600; transition: all 0.3s ease; &.btn-primary { background: $primary-color; border: none; color: white; &:hover { background: darken($primary-color, 10%); } } } } }
</style>
