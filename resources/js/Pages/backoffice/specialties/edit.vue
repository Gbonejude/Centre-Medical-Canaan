<template>
    <Head>
        <title>Modifier Spécialité | {{ specialty.name }}</title>
    </Head>

    <div class="content-wrapper">
        <div class="container">
            <div class="navigation-links">
                <Link :href="route('specialties.index')" class="back-link">
                    <i class="fa fa-arrow-left"></i>
                    <span>Retour aux Spécialités</span>
                </Link>
            </div>

            <div class="card form-card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa fa-edit icon-circle"></i>
                        <span>Modifier la Spécialité</span>
                    </div>
                </div>

                <div class="card-body">
                    <form @submit.prevent="update" class="user-form">
                        <div class="form-section">
                            <div class="form-group">
                                <label for="name">Nom de la Spécialité <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <i class="fa fa-award input-icon"></i>
                                    <input id="name" type="text" class="form-control"
                                        v-model="form.name" placeholder="Ex: Cardiologie..."
                                        :class="{ 'is-invalid': form.errors.name }" />
                                </div>
                                <div v-if="form.errors.name" class="error-message">{{ form.errors.name }}</div>
                            </div>

                            <div class="form-group mt-4">
                                <label for="description">Description</label>
                                <textarea id="description" class="form-control" rows="4"
                                    v-model="form.description" placeholder="Description..."></textarea>
                            </div>

                            <div class="form-group mt-4">
                                <div class="premium-switch-container">
                                    <div class="switch-info">
                                        <span class="switch-label">Spécialité Active</span>
                                        <span class="switch-desc">Rendre cette spécialité sélectionnable pour les médecins</span>
                                    </div>
                                    <label class="premium-switch">
                                        <input type="checkbox" v-model="form.is_active">
                                        <span class="slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions mt-5">
                            <Link :href="route('specialties.index')" class="btn btn-outline-secondary me-2">
                                Annuler
                            </Link>
                            <button type="submit" class="btn btn-primary" :disabled="form.processing">
                                {{ form.processing ? 'Mise à jour...' : 'Mettre à jour' }}
                            </button>
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
    specialty: Object
});

const toast = useToast();
const form = useForm({
    name: props.specialty.name,
    description: props.specialty.description || '',
    is_active: props.specialty.is_active,
});

function update() {
    form.put(route("specialties.update", props.specialty.id), {
        onSuccess: () => toast.success('Spécialité mise à jour avec succès'),
    });
}
</script>

<style lang="scss" scoped>
/* Même styles que create.vue */
$primary-color: #4361ee;
$secondary-color: #3f4254;
$border-color: #e4e6ef;
$body-color: #f9fafb;
$border-radius: 8px;

.content-wrapper { background-color: $body-color; min-height: calc(100vh - 65px); padding: 2rem 0; }
.navigation-links { margin-bottom: 1.5rem; .back-link { display: inline-flex; align-items: center; text-decoration: none; color: $secondary-color; font-weight: 500; transition: all 0.3s ease; i { margin-right: 0.5rem; } &:hover { color: $primary-color; transform: translateX(-3px); } } }

.form-card { background-color: white; border-radius: $border-radius; box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.05); border: none; .card-header { padding: 1.5rem; border-bottom: 1px solid $border-color; .card-title { font-weight: 600; font-size: 1.25rem; display: flex; align-items: center; .icon-circle { width: 35px; height: 35px; border-radius: 50%; background: rgba($primary-color, 0.1); color: $primary-color; display: flex; align-items: center; justify-content: center; margin-right: 0.75rem; } } } .card-body { padding: 2rem; max-width: 800px; margin: 0 auto; } }

.form-group { label { font-weight: 500; margin-bottom: 0.5rem; display: block; .required { color: #f64e60; } } .input-wrapper { position: relative; .input-icon { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #a1a5b7; } .form-control { padding: 0.6rem 1rem 0.6rem 2.5rem; border-radius: 8px; border: 1px solid $border-color; &:focus { border-color: $primary-color; box-shadow: 0 0 0 0.25rem rgba($primary-color, 0.1); } } } .error-message { color: #f64e60; font-size: 0.8rem; margin-top: 0.4rem; } }

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

.btn-primary { background: $primary-color; border: none; padding: 0.75rem 1.5rem; font-weight: 600; border-radius: 8px; }
.btn-outline-secondary { padding: 0.75rem 1.5rem; border-radius: 8px; }
</style>
