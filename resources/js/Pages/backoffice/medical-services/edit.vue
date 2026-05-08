<template>
    <Head>
        <title>Modifier le Service | CMC</title>
        <meta name="description" content="Modifier un service médical existant" />
    </Head>

    <div class="content-wrapper">
        <div class="container">
            
            <div class="navigation-links">
                <Link :href="route('medical-services.index')" class="back-link">
                    <i class="fa fa-arrow-left"></i>
                    <span>Retour aux Services</span>
                </Link>
            </div>

            <div class="card form-card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa fa-edit icon-circle"></i>
                        <span>Modifier le Service Médical</span>
                    </div>
                    <p class="card-subtitle">Mettre à jour les détails du service : {{ service.name }}</p>
                </div>

                <div class="card-body">
                    <form @submit.prevent="update" class="user-form">
                        <div class="form-section">
                            <div class="section-title">Informations Générales</div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="name">
                                            Nom du Service
                                            <span class="required">*</span>
                                        </label>
                                        <div class="input-wrapper">
                                            <i class="fa fa-stethoscope input-icon"></i>
                                            <input id="name" type="text" class="form-control"
                                                v-model="form.name" placeholder="Ex: Consultation Médecine Générale"
                                                :class="{ 'is-invalid': form.errors.name }" />
                                        </div>
                                        <transition name="fade">
                                            <div v-if="form.errors.name" class="error-message">
                                                <i class="fa fa-exclamation-circle"></i>
                                                {{ form.errors.name }}
                                            </div>
                                        </transition>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea id="description" class="form-control" rows="4"
                                            v-model="form.description" placeholder="Détails sur le service..."
                                            :class="{ 'is-invalid': form.errors.description }"></textarea>
                                        <transition name="fade">
                                            <div v-if="form.errors.description" class="error-message">
                                                <i class="fa fa-exclamation-circle"></i>
                                                {{ form.errors.description }}
                                            </div>
                                        </transition>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="section-title">Paramètres</div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="custom-control custom-switch">
                                        <div class="premium-switch-container">
                                            <div class="switch-info">
                                                <span class="switch-label">Service Actif</span>
                                                <span class="switch-desc">Rendre ce service disponible pour les rendez-vous</span>
                                            </div>
                                            <label class="premium-switch">
                                                <input type="checkbox" v-model="form.is_active">
                                                <span class="slider"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <div class="action-buttons">
                                <Link :href="route('medical-services.index')" class="btn btn-outline-secondary">
                                    <i class="fa fa-times"></i>
                                    <span>Annuler</span>
                                </Link>

                                <button type="submit" class="btn btn-primary" :disabled="form.processing">
                                    <i class="fa fa-save"></i>
                                    <span>{{ form.processing ? 'Mise à jour...' : 'Mettre à jour le Service' }}</span>
                                    <div v-if="form.processing" class="spinner-border spinner-border-sm ms-2" role="status"></div>
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

const toast = useToast();

const props = defineProps({
    service: Object
});

const form = useForm({
    name: props.service.name,
    description: props.service.description || '',
    is_active: Boolean(props.service.is_active),
});

function update() {
    form.put(route("medical-services.update", props.service.uuid), {
        onSuccess: () => {
            toast.success('Service médical mis à jour avec succès');
        },
        onError: () => {
            toast.error('Veuillez corriger les erreurs dans le formulaire');
        },
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
.form-card { background-color: white; border-radius: $border-radius; box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.075); border: none; .card-header { padding: 1.5rem; border-bottom: 1px solid $border-color; position: relative; &::after { content: ''; position: absolute; bottom: -1px; left: 1.5rem; width: 80px; height: 3px; background: linear-gradient(to right, $primary-color, lighten($primary-color, 20%)); border-radius: 3px 3px 0 0; } .card-title { font-size: 1.25rem; font-weight: 600; display: flex; align-items: center; .icon-circle { width: 35px; height: 35px; border-radius: 50%; background: rgba($primary-color, 0.1); color: $primary-color; display: flex; align-items: center; justify-content: center; margin-right: 0.75rem; } } .card-subtitle { margin: 0; color: #6c757d; font-size: 0.95rem; padding-left: 3rem; } } .card-body { padding: 1.5rem; } }
.form-section { margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px dashed $border-color; &:last-child { border-bottom: none; } .section-title { font-weight: 600; color: $secondary-color; margin-bottom: 1.25rem; position: relative; padding-left: 0.75rem; font-size: 1.1rem; &::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background-color: $primary-color; border-radius: 4px; } } }
.form-group { margin-bottom: 1.5rem; label { display: block; margin-bottom: 0.5rem; font-weight: 500; .required { color: $danger-color; } } .input-wrapper { position: relative; .input-icon { position: absolute; left: 0.875rem; top: 50%; transform: translateY(-50%); color: #a1a5b7; } .form-control { padding: 0.75rem 0.75rem 0.75rem 2.5rem; border-radius: $border-radius; border: 1px solid $border-color; font-size: 0.95rem; &:focus { border-color: $primary-color; box-shadow: 0 0 0 0.25rem rgba($primary-color, 0.25); } } } .error-message { color: $danger-color; font-size: 0.85rem; margin-top: 0.5rem; } }
.form-actions { display: flex; justify-content: flex-end; padding-top: 1.5rem; .action-buttons { display: flex; gap: 1rem; .btn { display: flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.25rem; border-radius: $border-radius; font-weight: 500; transition: all 0.3s ease; &.btn-outline-secondary { color: $secondary-color; border: 1px solid #d4d6dd; &:hover { background-color: #f1f1f3; } } &.btn-primary { background: linear-gradient(to right, $primary-color, lighten($primary-color, 10%)); border: none; color: white; &:hover { transform: translateY(-2px); box-shadow: 0 0.25rem 0.5rem rgba($primary-color, 0.4); } } } } }

/* Premium Switch Styles */
.premium-switch-container {
    display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.25rem; background-color: #f8f9fa; border-radius: 0.75rem; border: 1px solid $border-color; transition: all 0.3s ease;
    &:hover { border-color: $primary-color; background-color: rgba($primary-color, 0.03); }
    .switch-info { display: flex; flex-direction: column; gap: 0.25rem; .switch-label { font-weight: 600; color: $secondary-color; font-size: 0.95rem; } .switch-desc { font-size: 0.8rem; color: #7e8299; } }
}
.premium-switch {
    position: relative; display: inline-block; width: 46px; height: 24px; margin-left: 1rem;
    input { opacity: 0; width: 0; height: 0; &:checked + .slider { background-color: $primary-color; } &:checked + .slider:before { transform: translateX(22px); } }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #e4e6ef; transition: .4s; border-radius: 24px; &:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; } }
}

.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
