<template>

    <Head>
        <title>Ajouter un Utilisateur</title>
        <meta name="description" content="Ajouter un nouvel utilisateur au système" />
    </Head>

    <div class="content-wrapper">
        <div class="container">
            
            <div class="navigation-links">
                <Link :href="route('users.index')" class="back-link">
                <i class="fa fa-arrow-left"></i>
                <span>Retour aux Utilisateurs</span>
                </Link>
            </div>

            <div class="card form-card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa fa-user-plus icon-circle"></i>
                        <span>Ajouter un Nouvel Utilisateur</span>
                    </div>
                    <p class="card-subtitle">Créer un nouveau compte utilisateur avec un rôle spécifique</p>
                </div>

                <div class="card-body">
                    <form @submit.prevent="save" class="user-form">
                        <div class="form-section">
                            <div class="section-title">Photo de Profil</div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Image de Profil</label>
                                        <div class="image-upload-container">
                                            <div class="image-preview-area">
                                                <div v-if="imagePreview" class="image-preview" @click="triggerFileInput">
                                                    <img :src="imagePreview" alt="Aperçu" />
                                                </div>
                                                <div v-else class="image-placeholder" @click="triggerFileInput">
                                                    <div class="placeholder-content">
                                                        <i class="fa fa-camera"></i>
                                                        <p>Cliquez pour télécharger</p>
                                                        <small>JPG, PNG ou GIF (Max: 2Mo)</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <input
                                                ref="fileInput"
                                                type="file"
                                                @change="handleFileUpload"
                                                accept="image/*"
                                                style="display: none;"
                                            />
                                            <div class="upload-actions" v-if="imagePreview">
                                                <button type="button" @click="triggerFileInput" class="btn btn-outline-primary btn-sm">
                                                    <i class="fa fa-sync"></i> Changer
                                                </button>
                                                <button type="button" @click="removeImage" class="btn btn-outline-danger btn-sm">
                                                    <i class="fa fa-trash"></i> Supprimer
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="section-title">Informations Personnelles</div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="firstname">
                                            Prénom
                                            <span class="required">*</span>
                                        </label>
                                        <div class="input-wrapper">
                                            <i class="fa fa-user input-icon"></i>
                                            <input id="firstname" type="text" class="form-control"
                                                v-model="form.firstname" placeholder="Entrez le prénom"
                                                :class="{ 'is-invalid': errors.firstname }" />
                                        </div>
                                        <transition name="fade">
                                            <div v-if="errors.firstname" class="error-message">
                                                <i class="fa fa-exclamation-circle"></i>
                                                {{ errors.firstname[0] }}
                                            </div>
                                        </transition>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="lastname">
                                            Nom
                                             <span class="required">*</span>
                                        </label>
                                        <div class="input-wrapper">
                                            <i class="fa fa-user input-icon"></i>
                                            <input id="lastname" type="text" class="form-control"
                                                v-model="form.lastname" placeholder="Entrez le nom"
                                                :class="{ 'is-invalid': errors.lastname }" />
                                        </div>
                                        <transition name="fade">
                                            <div v-if="errors.lastname" class="error-message">
                                                <i class="fa fa-exclamation-circle"></i>
                                                {{ errors.lastname[0] }}
                                            </div>
                                        </transition>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">
                                            Numéro de Téléphone
                                             <span class="required">*</span>
                                        </label>
                                        <div :class="['phone-input-wrapper', { 'phone-input-wrapper--invalid': errors.phone }]">
                                            <VueTelInput
                                                v-model="form.phone"
                                                mode="international"
                                                default-country="TG"
                                                :auto-default-country="true"
                                                :dropdown-options="phoneDropdownOptions"
                                                :input-options="getPhoneInputOptions(errors.phone)"
                                                @validate="(state) => isPhoneValid = state.valid"
                                                style-classes="phone-input"
                                            />
                                        </div>
                                        <transition name="fade">
                                            <div v-if="errors.phone" class="error-message">
                                                <i class="fa fa-exclamation-circle"></i>
                                                {{ errors.phone[0] }}
                                            </div>
                                        </transition>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>
                                            Genre
                                        </label>
                                        <div class="gender-options">
                                            <div class="gender-option">
                                                <input type="radio" id="gender-male" value="male" v-model="form.gender"
                                                    name="gender" />
                                                <label for="gender-male">
                                                    <i class="fa fa-mars"></i>
                                                    <span>Masculin</span>
                                                </label>
                                            </div>

                                            <div class="gender-option">
                                                <input type="radio" id="gender-female" value="female"
                                                    v-model="form.gender" name="gender" />
                                                <label for="gender-female">
                                                    <i class="fa fa-venus"></i>
                                                    <span>Féminin</span>
                                                </label>
                                            </div>
                                        </div>
                                        <transition name="fade">
                                            <div v-if="errors.gender" class="error-message">
                                                <i class="fa fa-exclamation-circle"></i>
                                                {{ errors.gender[0] }}
                                            </div>
                                        </transition>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="section-title">Informations du Compte</div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">
                                            Adresse Email
                                            <span class="required">*</span>
                                        </label>
                                        <div class="input-wrapper">
                                            <i class="fa fa-envelope input-icon"></i>
                                            <input id="email" type="email" class="form-control" v-model="form.email"
                                                placeholder="Entrez l l'adresse email"
                                                :class="{ 'is-invalid': errors.email }" />
                                        </div>
                                        <transition name="fade">
                                            <div v-if="errors.email" class="error-message">
                                                <i class="fa fa-exclamation-circle"></i>
                                                {{ errors.email[0] }}
                                            </div>
                                        </transition>
                                        <div class="form-text">L'email sera utilisé pour la connexion et les notifications.</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="birthday" class="form-label fw-medium">Date de Naissance</label>
                                    <DatePickerComponent v-model="form.birthday" maxDate="today" />
                                    <transition name="fade">
                                        <div v-if="errors.birthday" class="error-message">
                                            <i class="fa fa-exclamation-circle"></i>
                                            {{ errors.birthday[0] }}
                                        </div>
                                    </transition>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="section-title">Rôle de l'Utilisateur</div>

                            <div class="permissions-container">
                                <div class="permissions-title">
                                    <i class="fa fa-shield-alt me-2"></i>
                                    <span>Attribuer un rôle unique à cet utilisateur</span>
                                </div>

                                <div class="permissions-grid">
                                    <div v-for="permission in permissions" :key="permission.id" class="permission-item">
                                        <div class="custom-radio">
                                            <input type="radio" :id="'permission-' + permission.id"
                                                :value="permission.id" v-model="form.selectedPermission" name="user_role" />
                                            <label :for="'permission-' + permission.id">
                                                <span class="radio-icon">
                                                    <i class="fa fa-circle"></i>
                                                </span>
                                                <span class="radio-label">{{ translateRole(permission.name) }}</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <transition name="fade">
                                    <div v-if="errors.permissions" class="error-message mt-2">
                                        <i class="fa fa-exclamation-circle"></i>
                                        {{ errors.permissions[0] }}
                                    </div>
                                </transition>
                            </div>
                        </div>

                        <div class="form-actions">
                            <div class="action-buttons">
                                <Link :href="route('users.index')" class="btn btn-outline-secondary">
                                    <i class="fa fa-times"></i>
                                    <span>Annuler</span>
                                </Link>

                                <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                                    <i class="fa fa-save"></i>
                                    <span>{{ isSubmitting ? 'Enregistrement...' : 'Créer l\'Utilisateur' }}</span>
                                    <div v-if="isSubmitting" class="spinner-border spinner-border-sm" role="status">
                                        <span class="visually-hidden">Chargement...</span>
                                    </div>
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
import { ref } from 'vue';
import { useForm, Link, Head } from '@inertiajs/vue3';
import { useToast } from "vue-toastification";
import DatePickerComponent from '../../components/DateComponent.vue';
import { VueTelInput } from 'vue-tel-input';
import 'vue-tel-input/vue-tel-input.css';

const toast = useToast();
const isSubmitting = ref(false);
const isPhoneValid = ref(false);
const imagePreview = ref(null);
const fileInput = ref(null);

const props = defineProps({
    permissions: {
        type: Array,
        default: () => [],
    },
    errors: Object,
});

const form = useForm({
    firstname: '',
    lastname: '',
    gender: '',
    phone: '',
    email: '',
    permissions: [],
    selectedPermission: null,
    birthday: '',
    image: null,
});

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
    required: true,
    styleClasses: hasError ? 'form-control phone-input-control is-invalid' : 'form-control phone-input-control',
});

function triggerFileInput() {
    fileInput.value.click();
}

function handleFileUpload(event) {
    const file = event.target.files[0];
    if (file) {
        if (file.size > 2 * 1024 * 1024) {
            toast.error('La taille de l\'image doit être inférieure à 2Mo');
            return;
        }
        if (!file.type.startsWith('image/')) {
            toast.error('Veuillez sélectionner un fichier image valide');
            return;
        }
        form.image = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}

function removeImage() {
    form.image = null;
    imagePreview.value = null;
    if (fileInput.value) {
        fileInput.value.value = '';
    }
}

function translateRole(roleName) {
    if (!roleName) return '';
    const roles = {
        'ADMIN': 'Administrateur',
        'RECEPTIONIST': 'Réceptionniste',
        'DOCTOR': 'Médecin',
        'PATIENT': 'Patient',
        'SUPER ADMIN': 'Super Admin',
        'OFFICE': 'Bureau',
        'CAREGIVER': 'Soignant',
        'PROGRAM DIRECTOR': 'Directeur de Programme'
    };
    if (roleName.includes('.')) {
        const [category, action] = roleName.split('.');
        return `${translateRole(category)} : ${action}`;
    }
    return roles[roleName.toUpperCase()] || roleName;
}

function save() {
    isSubmitting.value = true;
    
    // Convert single selection to array for backend compatibility
    form.permissions = form.selectedPermission ? [form.selectedPermission] : [];
    
    form.post(route("users.store"), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            isSubmitting.value = false;
            toast.success('Utilisateur créé avec succès');
        },
        onError: () => {
            isSubmitting.value = false;
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
.form-group { margin-bottom: 1.5rem; label { display: block; margin-bottom: 0.5rem; font-weight: 500; .required { color: $danger-color; } } .input-wrapper { position: relative; .input-icon { position: absolute; left: 0.875rem; top: 50%; transform: translateY(-50%); color: #a1a5b7; } .form-control { padding: 0.75rem 0.75rem 0.75rem 2.5rem; border-radius: $border-radius; border: 1px solid $border-color; font-size: 0.95rem; &:focus { border-color: $primary-color; box-shadow: 0 0 0 0.25rem rgba($primary-color, 0.25); } } } .phone-input-wrapper { :deep(.phone-input) { border: 1px solid $border-color; border-radius: $border-radius; &:focus-within { border-color: $primary-color; box-shadow: 0 0 0 0.25rem rgba($primary-color, 0.25); } } :deep(.vti__dropdown) { border-right: 1px solid $border-color; background: #f8f9fa; } } .gender-options { display: flex; gap: 1.5rem; .gender-option { input[type="radio"] { opacity: 0; position: absolute; &:checked + label { color: $primary-color; background-color: rgba($primary-color, 0.1); border-color: rgba($primary-color, 0.5); i { color: $primary-color; } } } label { display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.25rem; border: 1px solid $border-color; border-radius: $border-radius; cursor: pointer; i { color: #a1a5b7; } } } } .error-message { color: $danger-color; font-size: 0.85rem; margin-top: 0.5rem; } .form-text { margin-top: 0.5rem; font-size: 0.825rem; color: #6c757d; } }
.permissions-container { background-color: #f3f6f9; border-radius: $border-radius; padding: 1.25rem; .permissions-title { display: flex; align-items: center; font-weight: 500; margin-bottom: 1.25rem; i { color: $primary-color; } } .permissions-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; .permission-item { .custom-radio { input[type="radio"] { opacity: 0; position: absolute; &:checked + label { .radio-icon { background-color: $primary-color; border-color: $primary-color; i { opacity: 1; transform: scale(1); } } } } label { display: flex; align-items: center; gap: 0.75rem; cursor: pointer; .radio-icon { width: 20px; height: 20px; border-radius: 50%; border: 1px solid $border-color; background-color: white; display: flex; align-items: center; justify-content: center; i { color: white; font-size: 0.6rem; opacity: 0; transform: scale(0.5); transition: all 0.2s ease; } } } } } } }
.form-actions { display: flex; justify-content: flex-end; padding-top: 1.5rem; .action-buttons { display: flex; gap: 1rem; .btn { display: flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.25rem; border-radius: $border-radius; font-weight: 500; transition: all 0.3s ease; &.btn-outline-secondary { color: $secondary-color; border: 1px solid #d4d6dd; &:hover { background-color: #f1f1f3; } } &.btn-primary { background: linear-gradient(to right, $primary-color, lighten($primary-color, 10%)); border: none; color: white; &:hover { transform: translateY(-2px); box-shadow: 0 0.25rem 0.5rem rgba($primary-color, 0.4); } } } } }
.image-upload-container { .image-preview-area { text-align: center; .image-preview, .image-placeholder { width: 120px; height: 120px; border-radius: 50%; border: 2px dashed $border-color; margin: 0 auto; overflow: hidden; display: flex; align-items: center; justify-content: center; cursor: pointer; img { width: 100%; height: 100%; object-fit: cover; } } } .upload-actions { display: flex; justify-content: center; gap: 0.75rem; margin-top: 1rem; } }
</style>
