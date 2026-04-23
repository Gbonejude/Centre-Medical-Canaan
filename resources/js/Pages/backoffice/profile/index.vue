<template>
  <Head>
    <title>My Profile</title>
  </Head>
  <div class="content container-fluid">
    
    <div class="page-header">
      <div class="row align-items-center">
        <div class="col">
          <h3 class="page-title">My Profile</h3>
          <ul class="breadcrumb">
            <li class="breadcrumb-item">
              <Link :href="route('dashboard.index')">Dashboard</Link>
            </li>
            <li class="breadcrumb-item active">Profile</li>
          </ul>
        </div>
        <div class="col-auto float-end ms-auto">
          <Link :href="route('profile.edit')" class="btn btn-primary">
            <i class="fa fa-edit me-1"></i> Edit Profile
          </Link>
        </div>
      </div>
    </div>

    
    <div class="row">
      <div class="col-md-4">
        <div class="card profile-card">
          <div class="card-body">
            <div class="profile-image-container">
              <div class="profile-image">
                
                <img
                    v-if="userAvatar"
                    :src="userAvatar"
                    :alt="`${user.firstname} ${user.lastname}`"
                    class="profile-avatar-image"
                    @error="handleAvatarError"
                />
                <span
                    v-else
                    class="avatar-text">
                    {{ getInitials(user.firstname + ' ' + user.lastname) }}
                </span>
              </div>
            </div>
            <div class="profile-info text-center">
              <h4 class="user-name">{{ user.firstname }} {{ user.lastname }}</h4>
              <p class="user-role">{{ getRoleLabel() }}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-8">
        <div class="card">
          <div class="card-header">
            <h5 class="card-title">Personal Information</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="info-item">
                  <div class="info-label">
                    <i class="fa fa-user text-primary"></i>
                    <span>Username</span>
                  </div>
                  <div class="info-value">{{ user.username }}</div>
                </div>

                <div class="info-item">
                  <div class="info-label">
                    <i class="fa fa-envelope text-primary"></i>
                    <span>Email</span>
                  </div>
                  <div class="info-value">{{ user.email }}</div>
                </div>

                <div class="info-item">
                  <div class="info-label">
                    <i class="fa fa-phone text-primary"></i>
                    <span>Phone</span>
                  </div>
                  <div class="info-value">{{ user.phone || 'Not provided' }}</div>
                </div>
                  <div class="info-item">
                  <div class="info-label">
                    <i class="fa fa-key text-primary"></i>
                    <span>Password</span>
                  </div>
                  <div class="info-value">
                    <Link :href="route('profile.edit')" class="text-primary small">
                      Change Password
                    </Link>
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="info-item">
                  <div class="info-label">
                    <i class="fa fa-venus-mars text-primary"></i>
                    <span>Gender</span>
                  </div>
                  <div class="info-value">{{ formatGender(user.gender) }}</div>
                </div>

                <div class="info-item">
                  <div class="info-label">
                    <i class="fa fa-birthday-cake text-primary"></i>
                    <span>Birthday</span>
                  </div>
                  <div class="info-value">{{ formatDate(user.birthday) || 'Not provided' }}</div>
                </div>

                <div class="info-item">
                  <div class="info-label">
                    <i class="fa fa-calendar text-primary"></i>
                    <span>Joined Date</span>
                  </div>
                  <div class="info-value">{{ formatDate(user.created_at) }}</div>
                </div>

              </div>
            </div>
          </div>
        </div>

        
        <div class="card mt-4">
          <div class="card-header">
            <h5 class="card-title">Permissions & Access</h5>
          </div>
          <div class="card-body">
            <div class="permissions-container">
              <div v-for="permission in user.permissions" :key="permission.id" class="permission-badge">
                {{ permission.name }}
              </div>

              <div v-if="!user.permissions || user.permissions.length === 0" class="text-muted">
                No specific permissions assigned.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
import { ref, computed } from 'vue';

const props = defineProps({
  user: Object,
});

const avatarError = ref(false);

const userAvatar = computed(() => {
  if (avatarError.value) return null;

  
  if (props.user?.avatar_url) {
    return props.user.avatar_url;
  }

  return null;
});

const handleAvatarError = () => {
  avatarError.value = true;
};

function getInitials(name) {
  if (!name) return "";
  return name
    .split(" ")
    .map((part) => part.charAt(0))
    .join("")
    .toUpperCase()
    .substring(0, 2);
}

function formatGender(gender) {
  if (!gender) return 'Not specified';

  return gender.charAt(0).toUpperCase() + gender.slice(1);
}

function formatDate(dateString) {
  if (!dateString) return 'N/A';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
}

function getRoleLabel() {
  if (props.user.permissions.some(p => p.name === 'ADMI')) {
    return 'Administrator';
  } else if (props.user.permissions.some(p => p.name === 'OFFICE')) {
    return 'Office Staff';
  } else if (props.user.permissions.some(p => p.name.includes('DSP'))) {
    return 'Direct Support Professional';
  } else if (props.user.permissions.some(p => p.name === 'CAREGIVER')) {
    return 'Caregiver';
  } else {
    return 'User';
  }
}
</script>

<style scoped>
.profile-card {
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  border: none;
  overflow: hidden;
}

.profile-image-container {
  display: flex;
  justify-content: center;
  margin-bottom: 1.5rem;
}

.profile-image {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  background-color: #4a6cf7;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 3rem;
  font-weight: 600;
  overflow: hidden;
  position: relative;
}

.profile-avatar-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 50%;
}

.avatar-text {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

.profile-info {
  text-align: center;
}

.user-name {
  font-size: 1.5rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
  color: #333;
}

.user-role {
  font-size: 0.95rem;
  color: #6c757d;
  margin-bottom: 0;
}

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
  margin-bottom: 0;
  color: #333;
}

.card-body {
  padding: 1.5rem;
}

.info-item {
  margin-bottom: 1.25rem;
}

.info-label {
  display: flex;
  align-items: center;
  margin-bottom: 0.5rem;
}

.info-label i {
  margin-right: 0.75rem;
  width: 16px;
  text-align: center;
}

.info-label span {
  font-weight: 500;
  color: #495057;
}

.info-value {
  color: #333;
  padding-left: 1.75rem;
}

.permissions-container {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.permission-badge {
  padding: 0.5rem 1rem;
  border-radius: 50px;
  background-color: #e3f2fd;
  color: #0d6efd;
  font-size: 0.875rem;
  font-weight: 500;
}
</style>
