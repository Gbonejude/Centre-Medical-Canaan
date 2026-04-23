<template>
  <Head>
    <title>Edit Profile</title>
  </Head>
  <div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
      <div class="row align-items-center">
        <div class="col">
          <h3 class="page-title">Edit Profile</h3>
          <ul class="breadcrumb">
            <li class="breadcrumb-item">
              <Link :href="route('dashboard.index')">Dashboard</Link>
            </li>
            <li class="breadcrumb-item">
              <Link :href="route('profile.index')">Profile</Link>
            </li>
            <li class="breadcrumb-item active">Edit</li>
          </ul>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-10 offset-lg-1">
        <!-- Profile Information Form -->
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="card-title">Profile Information</h5>
            <p class="card-subtitle text-muted">Update your account's profile information and avatar.</p>
          </div>
          <div class="card-body">
            <form @submit.prevent="updateProfile">
              <!-- Profile Picture Section -->
              <div class="row g-3 mb-4">
                <div class="col-12">
                  <label class="form-label">Profile Picture</label>
                  <div class="profile-picture-section">
                    <div class="current-avatar">
                      <!-- Afficher l'image actuelle si disponible -->
                      <img
                          v-if="(imagePreview || user.avatar_url) && !profileForm.remove_image"
                          :src="imagePreview || user.avatar_url"
                          :alt="`${user.firstname} ${user.lastname}`"
                          class="avatar-preview"
                          @error="handleImageError"
                      />
                      <div
                          v-else
                          class="avatar-placeholder">
                          <span class="avatar-initials">{{ getInitials(user.firstname + ' ' + user.lastname) }}</span>
                      </div>
                    </div>

                    <div class="avatar-actions">
                      <input
                        ref="fileInput"
                        type="file"
                        @change="handleFileUpload"
                        accept="image/*"
                        style="display: none;"
                      />
                      <button type="button" @click="triggerFileInput" class="btn btn-outline-primary btn-sm">
                        <i class="fa fa-camera"></i>
                        {{ imagePreview || user.avatar_url ? 'Change Picture' : 'Upload Picture' }}
                      </button>
                      <button
                        v-if="(imagePreview || user.avatar_url) && !profileForm.remove_image"
                        type="button"
                        @click="removeImage"
                        class="btn btn-outline-danger btn-sm">
                        <i class="fa fa-trash"></i>
                        Remove
                      </button>
                    </div>
                  </div>
                  <div v-if="profileForm.errors.image" class="invalid-feedback d-block">
                    {{ profileForm.errors.image }}
                  </div>
                  <small class="text-muted">JPG, PNG or GIF. Max size 2MB.</small>
                </div>
              </div>

              <div class="row g-3">
                <!-- First Name -->
                <div class="col-md-6">
                  <label for="firstname" class="form-label">First Name</label>
                  <input
                    type="text"
                    id="firstname"
                    v-model="profileForm.firstname"
                    class="form-control"
                    :class="{ 'is-invalid': profileForm.errors.firstname }"
                  >
                  <div v-if="profileForm.errors.firstname" class="invalid-feedback">
                    {{ profileForm.errors.firstname }}
                  </div>
                </div>

                <!-- Last Name -->
                <div class="col-md-6">
                  <label for="lastname" class="form-label">Last Name</label>
                  <input
                    type="text"
                    id="lastname"
                    v-model="profileForm.lastname"
                    class="form-control"
                    :class="{ 'is-invalid': profileForm.errors.lastname }"
                  >
                  <div v-if="profileForm.errors.lastname" class="invalid-feedback">
                    {{ profileForm.errors.lastname }}
                  </div>
                </div>

                <!-- Username -->
                <div class="col-md-6">
                  <label for="username" class="form-label">Username</label>
                  <input
                    type="text"
                    id="username"
                    v-model="profileForm.username"
                    class="form-control"
                    :class="{ 'is-invalid': profileForm.errors.username }"
                  >
                  <div v-if="profileForm.errors.username" class="invalid-feedback">
                    {{ profileForm.errors.username }}
                  </div>
                </div>

                <!-- Email -->
                <div class="col-md-6">
                  <label for="email" class="form-label">Email</label>
                  <input
                    type="email"
                    id="email"
                    v-model="profileForm.email"
                    class="form-control"
                    :class="{ 'is-invalid': profileForm.errors.email }"
                  >
                  <div v-if="profileForm.errors.email" class="invalid-feedback">
                    {{ profileForm.errors.email }}
                  </div>
                </div>

                <!-- Phone -->
                <div class="col-md-6">
                  <label for="phone" class="form-label">Phone</label>
                  <input
                    type="text"
                    id="phone"
                    v-model="profileForm.phone"
                    class="form-control"
                    :class="{ 'is-invalid': profileForm.errors.phone }"
                  >
                  <div v-if="profileForm.errors.phone" class="invalid-feedback">
                    {{ profileForm.errors.phone }}
                  </div>
                </div>

                <!-- Gender -->
                <div class="col-md-6">
                  <label for="gender" class="form-label">Gender</label>
                  <select
                    id="gender"
                    v-model="profileForm.gender"
                    class="form-select"
                    :class="{ 'is-invalid': profileForm.errors.gender }"
                  >
                    <option value="">Select gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                  </select>
                  <div v-if="profileForm.errors.gender" class="invalid-feedback">
                    {{ profileForm.errors.gender }}
                  </div>
                </div>
              </div>

              <div class="mt-4 d-flex justify-content-end">
                <button
                  type="submit"
                  class="btn btn-primary"
                  :disabled="profileForm.processing"
                >
                  <span v-if="profileForm.processing">
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    Saving...
                  </span>
                  <span v-else>Save</span>
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Change Password Form -->
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="card-title">Change Password</h5>
            <p class="card-subtitle text-muted">Ensure your account is using a secure password.</p>
          </div>
          <div class="card-body">
            <form @submit.prevent="updatePassword">
              <div class="row g-3">
                <!-- Current Password -->
                <div class="col-md-12">
                  <label for="current_password" class="form-label">Current Password</label>
                  <input
                    type="password"
                    id="current_password"
                    v-model="passwordForm.current_password"
                    class="form-control"
                    :class="{ 'is-invalid': passwordForm.errors.current_password }"
                  >
                  <div v-if="passwordForm.errors.current_password" class="invalid-feedback">
                    {{ passwordForm.errors.current_password }}
                  </div>
                </div>

                <!-- New Password -->
                <div class="col-md-6">
                  <label for="password" class="form-label">New Password</label>
                  <input
                    type="password"
                    id="password"
                    v-model="passwordForm.password"
                    class="form-control"
                    :class="{ 'is-invalid': passwordForm.errors.password }"
                  >
                  <div v-if="passwordForm.errors.password" class="invalid-feedback">
                    {{ passwordForm.errors.password }}
                  </div>
                </div>

                <!-- Confirm Password -->
                <div class="col-md-6">
                  <label for="password_confirmation" class="form-label">Confirm Password</label>
                  <input
                    type="password"
                    id="password_confirmation"
                    v-model="passwordForm.password_confirmation"
                    class="form-control"
                  >
                </div>
              </div>

              <div class="mt-4 d-flex justify-content-end">
                <button
                  type="submit"
                  class="btn btn-primary"
                  :disabled="passwordForm.processing"
                >
                  <span v-if="passwordForm.processing">
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    Updating...
                  </span>
                  <span v-else>Update Password</span>
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Buttons -->
        <div class="d-flex justify-content-between">
          <Link
            :href="route('profile.index')"
            class="btn btn-light"
          >
            <i class="fa fa-arrow-left me-1"></i> Back to Profile
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link, useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import { ref } from 'vue';

const toast = useToast();
const fileInput = ref(null);
const imagePreview = ref(null);

const props = defineProps({
  user: Object,
});

// Profile update form avec gestion d'avatar
const profileForm = useForm({
  firstname: props.user.firstname,
  lastname: props.user.lastname,
  username: props.user.username,
  email: props.user.email,
  phone: props.user.phone || '',
  gender: props.user.gender,
  image: null,
  remove_image: false,
  _method: 'PUT'
});

// Password update form
const passwordForm = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
});

// Avatar functions
function triggerFileInput() {
  fileInput.value?.click();
}

function handleFileUpload(event) {
  const file = event.target.files[0];
  if (file) {
    // Validation
    if (file.size > 2 * 1024 * 1024) {
      toast.error('Image size must be less than 2MB', {
        position: "top-right",
        timeout: 5000,
      });
      event.target.value = '';
      return;
    }

    if (!file.type.startsWith('image/')) {
      toast.error('Please select a valid image file', {
        position: "top-right",
        timeout: 5000,
      });
      event.target.value = '';
      return;
    }

    profileForm.remove_image = false;
    profileForm.image = file;

    // Créer l'aperçu
    const reader = new FileReader();
    reader.onload = (e) => {
      imagePreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
  }
}

function removeImage() {
  profileForm.image = null;
  profileForm.remove_image = true;
  imagePreview.value = null;
  if (fileInput.value) {
    fileInput.value.value = '';
  }

  toast.info('Image will be removed when you save', {
    position: "top-right",
    timeout: 3000,
  });
}

function handleImageError() {
  imagePreview.value = null;
}

function getInitials(name) {
  if (!name) return "";
  return name
    .split(" ")
    .map((part) => part.charAt(0))
    .join("")
    .toUpperCase()
    .substring(0, 2);
}

// Update profile information
const updateProfile = () => {
  profileForm.post(route('profile.update'), {
    forceFormData: true,
    onSuccess: () => {
      toast.success('Profile updated successfully', {
        position: "top-right",
        timeout: 5000,
      });
    },
    onError: () => {
      toast.error('There was an error updating your profile', {
        position: "top-right",
        timeout: 5000,
      });
    },
  });
};

// Update password
const updatePassword = () => {
  passwordForm.put(route('profile.change-password'), {
    onSuccess: () => {
      toast.success('Password updated successfully', {
        position: "top-right",
        timeout: 5000,
      });

      passwordForm.reset();
    },
    onError: () => {
      toast.error('There was an error updating your password', {
        position: "top-right",
        timeout: 5000,
      });
    },
  });
};
</script>

<style scoped>
.card {
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  border: none;
  overflow: hidden;
  margin-bottom: 1.5rem;
}

.card-header {
  padding: 1.25rem 1.5rem;
  background-color: #f8f9fa;
  border-bottom: 1px solid #eee;
}

.card-title {
  font-size: 1.1rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
  color: #333;
}

.card-subtitle {
  font-size: 0.875rem;
  margin-bottom: 0;
}

.card-body {
  padding: 1.5rem;
}

.form-label {
  font-weight: 500;
  margin-bottom: 0.5rem;
  color: #495057;
}

.form-control, .form-select {
  padding: 0.65rem 1rem;
  border-radius: 8px;
  border: 1px solid #ced4da;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.form-control:focus, .form-select:focus {
  border-color: #4a6cf7;
  box-shadow: 0 0 0 0.2rem rgba(74, 108, 247, 0.25);
}

.form-control.is-invalid, .form-select.is-invalid {
  border-color: #dc3545;
}

.invalid-feedback {
  display: block;
  width: 100%;
  font-size: 0.8125rem;
  color: #dc3545;
  margin-top: 0.25rem;
}

/* Profile Picture Styles */
.profile-picture-section {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.current-avatar {
  position: relative;
}

.avatar-preview {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid #e9ecef;
}

.avatar-placeholder {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background-color: #4a6cf7;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 3px solid #e9ecef;
}

.avatar-initials {
  color: white;
  font-size: 1.5rem;
  font-weight: 600;
}

.avatar-actions {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.btn {
  padding: 0.65rem 1.25rem;
  border-radius: 8px;
  font-weight: 500;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
  gap: 0.5rem;
}

.btn-sm {
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
}

.btn-primary {
  background-color: #4a6cf7;
  border-color: #4a6cf7;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background-color: #3b5bd9;
  border-color: #3b5bd9;
}

.btn-primary:disabled {
  background-color: #4a6cf7;
  border-color: #4a6cf7;
  opacity: 0.65;
}

.btn-outline-primary {
  color: #4a6cf7;
  border-color: #4a6cf7;
  background-color: transparent;
}

.btn-outline-primary:hover {
  background-color: #4a6cf7;
  border-color: #4a6cf7;
  color: white;
}

.btn-outline-danger {
  color: #dc3545;
  border-color: #dc3545;
  background-color: transparent;
}

.btn-outline-danger:hover {
  background-color: #dc3545;
  border-color: #dc3545;
  color: white;
}

.btn-light {
  background-color: #f8f9fa;
  border-color: #e9ecef;
  color: #495057;
}

.btn-light:hover {
  background-color: #e9ecef;
  border-color: #dee2e6;
  color: #212529;
}

@media (max-width: 768px) {
  .profile-picture-section {
    flex-direction: column;
    text-align: center;
  }

  .avatar-actions {
    flex-direction: row;
    justify-content: center;
  }
}
</style>
