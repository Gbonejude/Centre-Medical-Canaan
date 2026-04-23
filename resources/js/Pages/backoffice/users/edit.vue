<template>
    <Head>
        <title>Modifier l'Utilisateur | {{ user.lastname }}, {{ user.firstname }}</title>
        <meta name="description" content="Modifier les informations de l'utilisateur" />
    </Head>

    <div class="content-wrapper">
        <div class="container">
            
            <div class="navigation-links">
                <Link :href="backToUsersUrl" class="back-link">
                <i class="fa fa-arrow-left"></i>
                <span>Retour aux Utilisateurs</span>
                </Link>
            </div>

            <div class="card form-card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa fa-user-edit icon-circle"></i>
                        <span>Modifier l'Utilisateur</span>
                    </div>
                    <p class="card-subtitle">Mettre à jour les informations et les permissions de l'utilisateur</p>
                </div>

                <div class="card-body">
                    <form @submit.prevent="update" class="user-form">
                        <div class="form-section">
                            <div class="section-title">Photo de Profil</div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Image de Profil</label>
                                        <div class="image-upload-container">
                                            <div class="image-preview-area">
                                                <div v-if="(imagePreview || props.userAvatar) && !form.remove_image" class="image-preview">
                                                    <img :src="imagePreview || props.userAvatar" alt="Aperçu du profil" />
                                                    <div class="image-overlay">
                                                        <button type="button" @click="removeImage" class="btn-remove-image">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div v-else class="image-placeholder" @click="triggerFileInput">
                                                    <div class="placeholder-content">
                                                        <i class="fa fa-camera"></i>
                                                        <p>Cliquez pour télécharger une photo</p>
                                                        <small>JPG, PNG ou GIF (Max: 2Mo)</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <input
                                                ref="fileInput"
                                                type="file"
                                                @change="handleFileUpload"
                                                accept="image/*"
                                                class="file-input"
                                                style="display: none;"
                                            />
                                            <div class="upload-actions">
                                                <button type="button" @click="triggerFileInput" class="btn btn-outline-primary btn-sm">
                                                    <i class="fa fa-upload"></i>
                                                    {{ (imagePreview || props.userAvatar) && !form.remove_image ? 'Changer l\'image' : 'Télécharger une image' }}
                                                </button>
                                                <button v-if="(imagePreview || props.userAvatar) && !form.remove_image" type="button" @click="removeImage" class="btn btn-outline-danger btn-sm">
                                                    <i class="fa fa-times"></i>
                                                    Supprimer
                                                </button>
                                            </div>
                                        </div>
                                        <transition name="fade">
                                            <div v-if="errors.image" class="error-message">
                                                <i class="fa fa-exclamation-circle"></i>
                                                {{ errors.image[0] }}
                                            </div>
                                        </transition>
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
                                                placeholder="Entrez l'adresse email"
                                                :class="{ 'is-invalid': errors.email }" />
                                        </div>
                                        <transition name="fade">
                                            <div v-if="errors.email" class="error-message">
                                                <i class="fa fa-exclamation-circle"></i>
                                                {{ errors.email[0] }}
                                            </div>
                                        </transition>
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

                            <div class="password-section-wrapper mt-4">
                                <div class="password-toggle-header" @click="showPasswordFields = !showPasswordFields">
                                    <div class="d-flex align-items-center">
                                        <i class="fa" :class="showPasswordFields ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                                        <span class="ms-2 fw-semibold">Changer le mot de passe</span>
                                    </div>
                                    <small class="text-muted">Laissez vide pour conserver le mot de passe actuel</small>
                                </div>

                                <transition name="fade">
                                    <div v-show="showPasswordFields" class="password-fields mt-3">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="password">Nouveau mot de passe</label>
                                                    <div class="input-wrapper">
                                                        <i class="fa fa-lock input-icon"></i>
                                                        <input id="password" type="password" class="form-control"
                                                            v-model="form.password" placeholder="Entrez le nouveau mot de passe"
                                                            :class="{ 'is-invalid': errors.password }" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="password_confirmation">Confirmer le mot de passe</label>
                                                    <div class="input-wrapper">
                                                        <i class="fa fa-lock input-icon"></i>
                                                        <input id="password_confirmation" type="password" class="form-control"
                                                            v-model="form.password_confirmation" placeholder="Confirmez le mot de passe" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </transition>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="section-title">Rôles & Permissions</div>

                            <div class="permissions-container">
                                <div class="permissions-title">
                                    <i class="fa fa-shield-alt me-2"></i>
                                    <span>Gérer les permissions de l'utilisateur</span>
                                </div>

                                <div class="permissions-grid">
                                    <div v-for="permission in permissions" :key="permission.id" class="permission-item">
                                        <div class="custom-checkbox">
                                            <input type="checkbox" :id="'permission-' + permission.id"
                                                :value="permission.id" v-model="form.permissions" />
                                            <label :for="'permission-' + permission.id">
                                                <span class="checkbox-icon">
                                                    <i class="fa fa-check"></i>
                                                </span>
                                                <span class="checkbox-label">{{ translateRole(permission.name) }}</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <div class="action-buttons">
                                <Link :href="backToUsersUrl" class="btn btn-outline-secondary">
                                    <i class="fa fa-times"></i>
                                    <span>Annuler</span>
                                </Link>

                                <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                                    <i class="fa fa-save"></i>
                                    <span>{{ isSubmitting ? 'Mise à jour...' : 'Mettre à jour' }}</span>
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
import { ref, computed } from 'vue';
import { useForm, Link, Head, usePage } from '@inertiajs/vue3';
import { useToast } from "vue-toastification";
import DatePickerComponent from '../../components/DateComponent.vue';
import { VueTelInput } from 'vue-tel-input';
import 'vue-tel-input/vue-tel-input.css';

const toast = useToast();
const page = usePage();
const isSubmitting = ref(false);
const isPhoneValid = ref(false);
const showPasswordFields = ref(false);
const imagePreview = ref(null);
const fileInput = ref(null);

const props = defineProps({
    permissions: {
        type: Array,
        default: () => [],
    },
    user: {
        type: Object,
        required: true,
    },
    userPermissions: {
        type: Array,
        default: () => [],
    },
    userAvatar: {
        type: String,
        default: null,
    },
    errors: Object,
});

const form = useForm({
    firstname: props.user.firstname || '',
    lastname: props.user.lastname || '',
    email: props.user.email || '',
    gender: props.user.gender || '',
    phone: props.user.phone || '',
    birthday: props.user.birthday || '',
    permissions: [...(props.userPermissions || [])],
    password: '',
    password_confirmation: '',
    image: null,
    remove_image: false,
    _method: 'PUT'
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

const getNavigationQueryString = () => {
    const origin = typeof window !== 'undefined' ? window.location.origin : 'http://localhost';
    const currentUrl = new URL(page.url, origin);
    const nextParams = new URLSearchParams();

    ['page', 'search', 'p'].forEach((key) => {
        const value = currentUrl.searchParams.get(key);
        if (value) {
            nextParams.set(key, value);
        }
    });

    return nextParams.toString();
};

const backToUsersUrl = computed(() => {
    const queryString = getNavigationQueryString();
    const baseUrl = route('users.index');
    return queryString ? `${baseUrl}?${queryString}` : baseUrl;
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
        form.remove_image = false;
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}

function removeImage() {
    form.image = null;
    form.remove_image = true;
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

function update() {
    isSubmitting.value = true;
    form.post(route('users.update', props.user.uuid), {
        preserveState: true,
        preserveScroll: true,
        forceFormData: true, 
        onSuccess: () => {
            isSubmitting.value = false;
            toast.success('Utilisateur mis à jour avec succès');
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
.permissions-container { background-color: #f3f6f9; border-radius: $border-radius; padding: 1.25rem; .permissions-title { display: flex; align-items: center; font-weight: 500; margin-bottom: 1.25rem; i { color: $primary-color; } } .permissions-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; .permission-item { .custom-checkbox { input[type="checkbox"] { opacity: 0; position: absolute; &:checked + label { .checkbox-icon { background-color: $primary-color; border-color: $primary-color; i { opacity: 1; transform: scale(1); } } } } label { display: flex; align-items: center; gap: 0.75rem; cursor: pointer; .checkbox-icon { width: 20px; height: 20px; border-radius: 4px; border: 1px solid $border-color; background-color: white; display: flex; align-items: center; justify-content: center; i { color: white; font-size: 0.75rem; opacity: 0; transform: scale(0.5); transition: all 0.2s ease; } } } } } } }
.form-actions { display: flex; justify-content: flex-end; padding-top: 1.5rem; .action-buttons { display: flex; gap: 1rem; .btn { display: flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.25rem; border-radius: $border-radius; font-weight: 500; transition: all 0.3s ease; &.btn-outline-secondary { color: $secondary-color; border: 1px solid #d4d6dd; &:hover { background-color: #f1f1f3; } } &.btn-primary { background: linear-gradient(to right, $primary-color, lighten($primary-color, 10%)); border: none; color: white; &:hover { transform: translateY(-2px); box-shadow: 0 0.25rem 0.5rem rgba($primary-color, 0.4); } } } } }
.image-upload-container { .image-preview-area { text-align: center; .image-preview, .image-placeholder { width: 120px; height: 120px; border-radius: 50%; border: 2px dashed $border-color; margin: 0 auto; overflow: hidden; display: flex; align-items: center; justify-content: center; cursor: pointer; img { width: 100%; height: 100%; object-fit: cover; } } } .upload-actions { display: flex; justify-content: center; gap: 0.75rem; margin-top: 1rem; } }
.password-toggle-header { cursor: pointer; display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: #f8f9fa; border-radius: $border-radius; border: 1px solid $border-color; transition: all 0.3s ease; &:hover { background: #f1f1f3; } }
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
