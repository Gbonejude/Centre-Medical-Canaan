<template>
  <Head>
    <title>Profil Utilisateur | {{ user.lastname }}, {{ user.firstname }}</title>
    <meta name="description" content="Informations sur le profil utilisateur et ses permissions" />
  </Head>

  <div class="dashboard-wrapper">
    
    <div class="topbar">
      <div class="topbar-left">
        <Link :href="backToUsersUrl" class="nav-link">
          <i class="fa fa-chevron-left"></i>
          <span>Liste des utilisateurs</span>
        </Link>
        <div class="breadcrumbs">
          <span class="breadcrumb-item">Utilisateurs</span>
          <i class="fa fa-chevron-right breadcrumb-separator"></i>
          <span class="breadcrumb-item active">Profil</span>
        </div>
      </div>
      <div class="topbar-right">
        <div class="user-dropdown">
          <span class="user-name">{{ user.lastname }}, {{ user.firstname }}</span>
        </div>
      </div>
    </div>

    
    <div class="main-content">
      
      <div class="profile-header">
        <div class="user-card">
          <div class="user-avatar">
            <img 
              :src="userAvatar || '/assets/img/user.jpg'" 
              :alt="`${user.firstname} ${user.lastname}`" 
              @error="handleImageError"
            />
          </div>
          <div class="user-info">
            <h1 class="user-name">{{ user.lastname }}, {{ user.firstname }}</h1>
            <div class="user-meta">
              <div class="meta-item">
                <i class="fa fa-envelope"></i>
                <a :href="`mailto:${user.email}`">{{ user.email }}</a>
              </div>
              <div class="meta-item" v-if="user.phone">
                <i class="fa fa-phone-alt"></i>
                <a :href="`tel:${user.phone}`">{{ user.phone }}</a>
              </div>
              <div class="meta-item" v-if="user.birthday">
                <i class="fa fa-birthday-cake"></i>
                <span>{{ formatBirthday(user.birthday) }}</span>
              </div>
              <div class="meta-item">
                <i class="fa fa-user-check"></i>
                <span class="status-badge" :class="user.active ? 'active' : 'inactive'">
                  {{ user.active ? 'Actif' : 'Inactif' }}
                </span>
              </div>
            </div>
          </div>
        </div>
        
        <div class="action-panel">
          <Link 
            :href="editUserUrl" 
            class="action-button primary"
          >
            <i class="fa fa-edit"></i>
            <span>Modifier le profil</span>
          </Link>
          <button 
            type="button" 
            class="action-button secondary"
            @click="copyProfileLink"
          >
            <i class="fa fa-link"></i>
            <span>{{ linkCopied ? 'Copié !' : 'Copier le lien' }}</span>
          </button>
        </div>
      </div>

      
      <div class="profile-grid">
        
        <div class="grid-section user-details">
          <div class="card profile-card">
            <div class="card-header">
              <div class="card-title">Informations du profil</div>
              <div class="card-action">
                <span class="status-indicator" :class="user.active ? 'active' : 'inactive'"></span>
              </div>
            </div>
            <div class="card-content">
              <ul class="detail-list">
                <li class="detail-item">
                  <div class="detail-label">Nom complet</div>
                  <div class="detail-value">{{ user.firstname }} {{ user.lastname || '' }}</div>
                </li>
                <li class="detail-item">
                  <div class="detail-label">E-mail</div>
                  <div class="detail-value">
                    <a :href="`mailto:${user.email}`">{{ user.email }}</a>
                  </div>
                </li>
                <li class="detail-item" v-if="user.phone">
                  <div class="detail-label">Téléphone</div>
                  <div class="detail-value">
                    <a :href="`tel:${user.phone}`">{{ user.phone }}</a>
                  </div>
                </li>
                <li class="detail-item" v-if="user.birthday">
                  <div class="detail-label">Date de naissance</div>
                  <div class="detail-value">
                    <i class="fa fa-birthday-cake birthday-icon"></i>
                    {{ formatBirthday(user.birthday) }}
                  </div>
                </li>
                <li class="detail-item" v-if="user.gender">
                  <div class="detail-label">Genre</div>
                  <div class="detail-value">
                    <i :class="user.gender === 'male' ? 'fa fa-mars' : 'fa fa-venus'" class="gender-icon"></i>
                    {{ user.gender === 'male' ? 'Homme' : 'Femme' }}
                  </div>
                </li>
                <li class="detail-item">
                  <div class="detail-label">Membre depuis</div>
                  <div class="detail-value date-value">{{ formatDate(user.created_at) }}</div>
                </li>
                <li class="detail-item" v-if="user.updated_at">
                  <div class="detail-label">Dernière mise à jour</div>
                  <div class="detail-value date-value">{{ formatDate(user.updated_at) }}</div>
                </li>
              </ul>
            </div>
          </div>
        </div>
        
        
        <div class="grid-section user-permissions">
          <div class="card permissions-card">
            <div class="card-header">
              <div class="card-title">
                <span>Permissions de l'utilisateur</span>
                <span class="permission-count">{{ userPermissions ? userPermissions.length : 0 }}</span>
              </div>
              <div class="card-action">
                <button type="button" class="action-icon" title="Tout développer" @click="expandAll = !expandAll">
                  <i :class="expandAll ? 'fa fa-compress-alt' : 'fa fa-expand-alt'"></i>
                </button>
              </div>
            </div>
            <div class="card-content">
              <div v-if="userPermissions && userPermissions.length > 0" class="permissions-accordion">
                <div 
                  v-for="(group, category) in groupedPermissions" 
                  :key="category" 
                  class="accordion-item"
                  :class="{ 'expanded': expandAll || expandedCategories.includes(category) }"
                >
                  <div 
                    class="accordion-header" 
                    @click="toggleCategory(category)"
                  >
                    <div class="accordion-title">
                      <i class="fa fa-shield-alt icon-shield"></i>
                      <span>{{ capitalizeFirstLetter(category) }}</span>
                    </div>
                    <div class="accordion-actions">
                      <span class="item-count">{{ group.length }}</span>
                      <i :class="expandAll || expandedCategories.includes(category) ? 'fa fa-chevron-down' : 'fa fa-chevron-right'" class="toggle-icon"></i>
                    </div>
                  </div>
                  <div class="accordion-content">
                    <div class="permission-list">
                      <div 
                        v-for="permission in group" 
                        :key="permission.id" 
                        class="permission-item"
                      >
                        <i class="fa fa-check-circle permission-check"></i>
                        <span class="permission-name">{{ translateRole(permission.name) }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div v-else class="empty-permissions">
                <div class="empty-icon-container">
                  <i class="fa fa-lock empty-icon"></i>
                </div>
                <h3 class="empty-title">Aucune permission</h3>
                <p class="empty-message">
                  Cet utilisateur n'a aucune permission assignée pour le moment.
                </p>
                <Link 
                  :href="route('users.edit', user.uuid)" 
                  class="empty-action"
                >
                  <i class="fa fa-plus-circle"></i>
                  <span>Assigner des permissions</span>
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, Head, usePage } from '@inertiajs/vue3';
import { useToast } from "vue-toastification";

const toast = useToast();
const page = usePage();

const props = defineProps({
  user: {
    type: Object,
    required: true
  },
  userPermissions: {
    type: Array,
    default: () => []
  },
  userAvatar: {
    type: String,
    default: null
  }
});

const expandAll = ref(false);
const expandedCategories = ref([]);
const linkCopied = ref(false);

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

const editUserUrl = computed(() => {
  const queryString = getNavigationQueryString();
  const baseUrl = route('users.edit', props.user.uuid);
  return queryString ? `${baseUrl}?${queryString}` : baseUrl;
});

const groupedPermissions = computed(() => {
  const grouped = {};
  
  if (props.userPermissions && props.userPermissions.length > 0) {
    props.userPermissions.forEach(permission => {
      
      const category = permission.name.split('.')[0];
      
      if (!grouped[category]) {
        grouped[category] = [];
      }
      
      grouped[category].push(permission);
    });
  }
  
  return grouped;
});

function handleImageError(event) {
  event.target.src = '/assets/img/user.jpg';
  console.warn('User avatar failed to load, falling back to default image');
}

function toggleCategory(category) {
  const index = expandedCategories.value.indexOf(category);
  if (index === -1) {
    expandedCategories.value.push(category);
  } else {
    expandedCategories.value.splice(index, 1);
  }
}

function copyProfileLink() {
  const url = window.location.href;
  
  if (navigator.clipboard && navigator.clipboard.writeText) {
    navigator.clipboard.writeText(url).then(() => {
      linkCopied.value = true;
      toast.success('Lien du profil copié dans le presse-papier !', {
        position: "top-right",
        timeout: 2000,
      });
      setTimeout(() => {
        linkCopied.value = false;
      }, 2000);
    }).catch(err => {
      console.error('Failed to copy to clipboard:', err);
      fallbackCopyTextToClipboard(url);
    });
  } else {
    
    fallbackCopyTextToClipboard(url);
  }
}

function fallbackCopyTextToClipboard(text) {
  const textArea = document.createElement("textarea");
  textArea.value = text;
  
  
  textArea.style.top = "0";
  textArea.style.left = "0";
  textArea.style.position = "fixed";
  
  document.body.appendChild(textArea);
  textArea.focus();
  textArea.select();
  
  try {
    const successful = document.execCommand('copy');
    if (successful) {
      linkCopied.value = true;
      toast.success('Lien du profil copié dans le presse-papier !', {
        position: "top-right",
        timeout: 2000,
      });
      setTimeout(() => {
        linkCopied.value = false;
      }, 2000);
    } else {
      throw new Error('Copy command was unsuccessful');
    }
  } catch (err) {
    console.error('Fallback: Oops, unable to copy', err);
    toast.error('Échec de la copie du lien dans le presse-papier', {
      position: "top-right",
      timeout: 3000,
    });
  }
  
  document.body.removeChild(textArea);
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
  
  // Gérer les permissions préfixées (ex: user.create)
  if (roleName.includes('.')) {
    const [category, action] = roleName.split('.');
    return `${translateRole(category)} : ${action}`;
  }
  
  return roles[roleName.toUpperCase()] || roleName;
}

function capitalizeFirstLetter(string) {
  if (!string) return '';
  return string.charAt(0).toUpperCase() + string.slice(1);
}

function formatDate(dateString) {
  if (!dateString) return 'N/A';
  
  const date = new Date(dateString);
  const options = { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  };
  
  return new Intl.DateTimeFormat('fr-FR', options).format(date);
}

function formatBirthday(dateString) {
  if (!dateString) return 'Non spécifié';
  
  const date = new Date(dateString);
  const options = { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric'
  };
  
  return new Intl.DateTimeFormat('fr-FR', options).format(date);
}
</script>

<style lang="scss" scoped>

$bg-color: #f7f9fc;
$card-bg: #ffffff;
$border-color: #e6eaf0;
$text-color: #334155;
$text-muted: #64748b;
$primary-color: #3b82f6;
$primary-dark: #2563eb;
$secondary-color: #6366f1;
$secondary-dark: #4f46e5;
$accent-color: #8b5cf6;
$success-color: #10b981;
$warning-color: #f59e0b;
$danger-color: #ef4444;

$border-radius: 6px;
$card-radius: 12px;
$transition: all 0.2s cubic-bezier(0.25, 0.8, 0.25, 1);
$shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
$shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
$shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);

.dashboard-wrapper {
  background-color: $bg-color;
  min-height: 100vh;
  color: $text-color;
  font-family: 'Inter', 'Segoe UI', Roboto, -apple-system, sans-serif;
  display: flex;
  flex-direction: column;
}

.topbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 2rem;
  background-color: rgba($card-bg, 0.95);
  backdrop-filter: blur(10px);
  border-bottom: 1px solid $border-color;
  position: sticky;
  top: 0;
  z-index: 100;
  box-shadow: $shadow-sm;
  
  .topbar-left {
    display: flex;
    align-items: center;
    gap: 2rem;
    
    .nav-link {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      color: $text-color;
      text-decoration: none;
      font-weight: 500;
      transition: $transition;
      
      &:hover {
        color: $primary-color;
      }
    }
    
    .breadcrumbs {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      color: $text-muted;
      font-size: 0.875rem;
      
      .breadcrumb-item {
        &.active {
          color: $primary-color;
          font-weight: 500;
        }
      }
      
      .breadcrumb-separator {
        font-size: 0.75rem;
        color: #cbd5e1;
      }
    }
  }
  
  .topbar-right {
    .user-dropdown {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      
      .user-name {
        font-weight: 500;
      }
      
      .badge {
        font-size: 0.75rem;
        font-weight: 500;
        padding: 0.25rem 0.5rem;
        border-radius: $border-radius;
        
        &.active {
          background-color: rgba($success-color, 0.1);
          color: $success-color;
        }
        
        &.inactive {
          background-color: rgba($danger-color, 0.1);
          color: $danger-color;
        }
      }
    }
  }
  
  @media (max-width: 768px) {
    padding: 1rem;
    
    .topbar-left {
      gap: 1rem;
      
      .breadcrumbs {
        display: none;
      }
    }
  }
}

.main-content {
  flex: 1;
  padding: 2rem;
  
  @media (max-width: 768px) {
    padding: 1rem;
  }
}

.profile-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  background-color: $card-bg;
  border-radius: $card-radius;
  padding: 1.5rem;
  box-shadow: $shadow;
  
  .user-card {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    
    .user-avatar {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      overflow: hidden;
      border: 3px solid $card-bg;
      box-shadow: $shadow;
      position: relative;
      
      &::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border-radius: 50%;
        box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.1);
      }
      
      img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: $transition;
        
        &:hover {
          transform: scale(1.05);
        }
      }
    }
    
    .user-info {
      .user-name {
        margin: 0 0 0.5rem;
        font-size: 1.5rem;
        font-weight: 600;
        color: $text-color;
      }
      
      .user-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        
        .meta-item {
          display: flex;
          align-items: center;
          gap: 0.5rem;
          color: $text-muted;
          font-size: 0.875rem;
          
          i {
            color: $primary-color;
          }
          
          a {
            color: $text-muted;
            text-decoration: none;
            transition: $transition;
            
            &:hover {
              color: $primary-color;
              text-decoration: underline;
            }
          }
          
          .status-badge {
            font-size: 0.75rem;
            font-weight: 500;
            padding: 0.25rem 0.6rem;
            border-radius: 1rem;
            
            &.active {
              background-color: rgba($success-color, 0.1);
              color: $success-color;
            }
            
            &.inactive {
              background-color: rgba($danger-color, 0.1);
              color: $danger-color;
            }
          }
        }
      }
    }
  }
  
  .action-panel {
    display: flex;
    gap: 0.75rem;
    
    .action-button {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.6rem 1.25rem;
      border-radius: $border-radius;
      font-weight: 500;
      border: none;
      cursor: pointer;
      transition: $transition;
      font-size: 0.875rem;
      
      &.primary {
        background-color: $primary-color;
        color: white;
        text-decoration: none;
        box-shadow: $shadow-sm;
        
        &:hover {
          background-color: $primary-dark;
          transform: translateY(-2px);
          box-shadow: $shadow;
        }
      }
      
      &.secondary {
        background-color: #f1f5f9;
        color: $text-color;
        
        &:hover {
          background-color: #e2e8f0;
          transform: translateY(-2px);
        }
      }
    }
  }
  
  @media (max-width: 992px) {
    flex-direction: column;
    align-items: flex-start;
    gap: 1.5rem;
    
    .action-panel {
      width: 100%;
      
      .action-button {
        flex: 1;
        justify-content: center;
      }
    }
  }
  
  @media (max-width: 576px) {
    .user-card {
      flex-direction: column;
      align-items: center;
      text-align: center;
      width: 100%;
      
      .user-meta {
        justify-content: center;
      }
    }
  }
}

.profile-grid {
  display: grid;
  grid-template-columns: 1fr 2fr;
  gap: 1.5rem;
  
  @media (max-width: 992px) {
    grid-template-columns: 1fr;
  }
}

.card {
  background-color: $card-bg;
  border-radius: $card-radius;
  border: 1px solid $border-color;
  overflow: hidden;
  box-shadow: $shadow;
  
  .card-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid $border-color;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #fcfcfd;
    
    .card-title {
      font-size: 1rem;
      font-weight: 600;
      color: $text-color;
      display: flex;
      align-items: center;
      gap: 0.75rem;
      
      .permission-count {
        font-size: 0.75rem;
        background-color: $primary-color;
        color: white;
        padding: 0.125rem 0.5rem;
        border-radius: 1rem;
      }
    }
    
    .card-action {
      .status-indicator {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        
        &.active {
          background-color: $success-color;
          box-shadow: 0 0 0 2px rgba($success-color, 0.2);
        }
        
        &.inactive {
          background-color: $danger-color;
          box-shadow: 0 0 0 2px rgba($danger-color, 0.2);
        }
      }
      
      .action-icon {
        background: none;
        border: none;
        color: $text-muted;
        cursor: pointer;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: $border-radius;
        transition: $transition;
        
        &:hover {
          background-color: #f1f5f9;
          color: $primary-color;
        }
      }
    }
  }
  
  .card-content {
    padding: 1.5rem;
  }
}

.detail-list {
  list-style: none;
  padding: 0;
  margin: 0;
  
  .detail-item {
    padding: 0.75rem 0;
    border-bottom: 1px solid $border-color;
    
    &:first-child {
      padding-top: 0;
    }
    
    &:last-child {
      border-bottom: none;
      padding-bottom: 0;
    }
    
    .detail-label {
      color: $text-muted;
      font-size: 0.75rem;
      margin-bottom: 0.25rem;
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }
    
    .detail-value {
      color: $text-color;
      font-size: 0.95rem;
      
      a {
        color: $primary-color;
        text-decoration: none;
        transition: $transition;
        
        &:hover {
          text-decoration: underline;
        }
      }
      
      .gender-icon, .birthday-icon {
        margin-right: 0.5rem;
        color: $primary-color;
      }
      
      .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.75rem;
        font-weight: 500;
        padding: 0.375rem 0.75rem;
        border-radius: 1rem;
        
        &.active {
          background-color: rgba($success-color, 0.1);
          color: $success-color;
        }
        
        &.inactive {
          background-color: rgba($danger-color, 0.1);
          color: $danger-color;
        }
      }
      
      &.date-value {
        font-size: 0.85rem;
        color: $text-muted;
      }
    }
  }
}

.permissions-accordion {
  .accordion-item {
    border-bottom: 1px solid $border-color;
    
    &:last-child {
      border-bottom: none;
    }
    
    .accordion-header {
      padding: 1rem 0;
      display: flex;
      justify-content: space-between;
      align-items: center;
      cursor: pointer;
      transition: $transition;
      
      &:hover {
        color: $primary-color;
      }
      
      .accordion-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-weight: 500;
        
        .icon-shield {
          color: $primary-color;
        }
      }
      
      .accordion-actions {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        
        .item-count {
          font-size: 0.75rem;
          background-color: #f1f5f9;
          color: $text-color;
          padding: 0.125rem 0.5rem;
          border-radius: 1rem;
        }
        
        .toggle-icon {
          transition: $transition;
          color: $text-muted;
        }
      }
    }
    
    .accordion-content {
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.3s ease-out;
    }
    
    &.expanded {
      .accordion-content {
        max-height: 1000px;
        transition: max-height 0.5s ease-in;
      }
      
      .toggle-icon {
        color: $primary-color;
      }
    }
  }
}

.permission-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 0.75rem;
  padding: 0.5rem 0 1.5rem 2rem;
  
  .permission-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem;
    border-radius: $border-radius;
    transition: $transition;
    
    &:hover {
      background-color: #f8fafc;
    }
    
    .permission-check {
      color: $success-color;
      font-size: 0.875rem;
    }
    
    .permission-name {
      font-size: 0.875rem;
      color: $text-color;
    }
  }
  
  @media (max-width: 576px) {
    grid-template-columns: 1fr;
  }
}

.empty-permissions {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 2rem 0;
  text-align: center;
  
  .empty-icon-container {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f1f5f9;
    margin-bottom: 1.5rem;
    
    .empty-icon {
      font-size: 1.75rem;
      color: $text-muted;
    }
  }
  
  .empty-title {
    margin: 0 0 0.5rem;
    font-size: 1.1rem;
    font-weight: 600;
    color: $text-color;
  }
  
  .empty-message {
    color: $text-muted;
    margin: 0 0 1.5rem;
    max-width: 300px;
    font-size: 0.95rem;
  }
  
  .empty-action {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background-color: $primary-color;
    color: white;
    border-radius: $border-radius;
    text-decoration: none;
    font-weight: 500;
    transition: $transition;
    font-size: 0.875rem;
    
    &:hover {
      background-color: $primary-dark;
      transform: translateY(-2px);
      box-shadow: $shadow;
    }
  }
}
</style>
