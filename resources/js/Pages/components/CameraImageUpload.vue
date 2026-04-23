<!-- CameraImageUpload.vue - Composant réutilisable -->
<template>
  <div class="camera-image-upload">
    <div class="image-upload-area cursor-pointer"
         @drop="handleDrop"
         @dragover.prevent
         @dragenter.prevent
         @dragleave="handleDragLeave"
         :class="{ 'drag-over': isDragOver }">

      <!-- Input file normal -->
      <input
        ref="fileInput"
        type="file"
        multiple
        accept="image/*"
        @change="handleFileSelect"
        style="display: none"
      />

      <!-- Input caméra -->
      <input
        ref="cameraInput"
        type="file"
        accept="image/*"
        capture="environment"
        @change="handleCameraCapture"
        style="display: none"
      />

      <div v-if="modelValue.length === 0" class="upload-prompt">
        <i class="fa fa-cloud-upload-alt upload-icon"></i>
        <h4>{{ title || 'Upload Images' }}</h4>
        <p>{{ subtitle || 'Drag and drop images here, or choose an option below' }}</p>
        <div class="upload-options">
          <button type="button" @click="openFileSelector" class="upload-option-btn">
            <i class="fa fa-folder-open"></i>
            Browse Files
          </button>
          <button type="button" @click="openCamera" class="upload-option-btn camera-btn">
            <i class="fa fa-camera"></i>
            Take Photo
          </button>
        </div>
        <small>Supports: JPG, PNG, GIF, SVG (Max {{ maxFiles }} files, {{ formatFileSize(maxSize) }} each)</small>
      </div>

      <div v-else class="selected-images">
        <div v-for="(image, index) in modelValue" :key="index" class="image-preview">
          <img :src="image.preview" :alt="image.name" />
          <div class="image-info">
            <span class="image-name">{{ image.name }}</span>
            <span class="image-size">{{ formatFileSize(image.size) }}</span>
            <span v-if="image.source" class="image-source">
              <i :class="getSourceIcon(image.source)"></i>
              {{ getSourceLabel(image.source) }}
            </span>
          </div>
          <button type="button" @click="removeImage(index)" class="remove-image" :title="'Remove ' + image.name">
            <i class="fa fa-times"></i>
          </button>
        </div>

        <div class="add-more-actions" v-if="modelValue.length < maxFiles">
          <button type="button" @click="openFileSelector" class="add-more-btn">
            <i class="fa fa-folder-open"></i>
            <span>Add Files</span>
          </button>
          <button type="button" @click="openCamera" class="add-more-btn camera-btn">
            <i class="fa fa-camera"></i>
            <span>Take Photo</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Erreurs -->
    <transition name="fade">
      <div v-if="error" class="error-message">
        <i class="fa fa-exclamation-circle"></i>
        {{ error }}
      </div>
    </transition>

    <!-- Texte d'aide -->
    <div v-if="helpText" class="form-text">
      <i class="fa fa-info-circle"></i>
      {{ helpText }}
    </div>
  </div>
</template>

<script setup>
import { ref, defineProps, defineEmits } from 'vue';
import { useToast } from "vue-toastification";

const toast = useToast();

// Props
const props = defineProps({
  modelValue: {
    type: Array,
    default: () => []
  },
  maxFiles: {
    type: Number,
    default: 5
  },
  maxSize: {
    type: Number,
    default: 2 * 1024 * 1024 // 2MB
  },
  title: {
    type: String,
    default: 'Upload Images'
  },
  subtitle: {
    type: String,
    default: 'Drag and drop images here, or choose an option below'
  },
  helpText: {
    type: String,
    default: 'Upload receipts, invoices, or other supporting documents'
  },
  error: {
    type: String,
    default: ''
  },
  disabled: {
    type: Boolean,
    default: false
  }
});

// Emits
const emit = defineEmits(['update:modelValue', 'error', 'file-added', 'file-removed']);

// Refs
const fileInput = ref(null);
const cameraInput = ref(null);
const isDragOver = ref(false);

// Methods
const openFileSelector = () => {
  if (props.disabled) return;
  fileInput.value?.click();
};

const openCamera = () => {
  if (props.disabled) return;
  cameraInput.value?.click();
};

const handleFileSelect = (event) => {
  const files = Array.from(event.target.files);
  processFiles(files, 'file');
  event.target.value = '';
};

const handleCameraCapture = (event) => {
  const files = Array.from(event.target.files);
  processFiles(files, 'camera');
  event.target.value = '';
};

const handleDrop = (event) => {
  event.preventDefault();
  isDragOver.value = false;
  if (props.disabled) return;

  const files = Array.from(event.dataTransfer.files);
  processFiles(files, 'drop');
};

const handleDragLeave = (event) => {
  // Vérifier si on quitte vraiment la zone de drop
  if (!event.currentTarget.contains(event.relatedTarget)) {
    isDragOver.value = false;
  }
};

const processFiles = (files, source = 'file') => {
  if (props.disabled) return;

  const currentImages = [...props.modelValue];

  if (currentImages.length + files.length > props.maxFiles) {
    const message = `Maximum ${props.maxFiles} images allowed`;
    toast.error(message);
    emit('error', message);
    return;
  }

  const validFiles = [];

  files.forEach(file => {
    // Vérifier le type
    if (!file.type.startsWith('image/')) {
      const message = `${file.name} is not an image file`;
      toast.error(message);
      emit('error', message);
      return;
    }

    // Vérifier la taille
    if (file.size > props.maxSize) {
      const message = `${file.name} is too large (max ${formatFileSize(props.maxSize)})`;
      toast.error(message);
      emit('error', message);
      return;
    }

    validFiles.push(file);
  });

  // Traiter tous les fichiers valides en une seule fois
  if (validFiles.length === 0) return;

  let processedCount = 0;
  const newImages = [];

  validFiles.forEach((file, index) => {
    const reader = new FileReader();
    reader.onload = (e) => {
      const imageData = {
        file,
        name: file.name || `photo_${Date.now()}_${index}.jpg`,
        size: file.size,
        preview: e.target.result,
        source: source,
        id: Date.now() + Math.random() + index // ID unique pour le suivi
      };

      newImages.push(imageData);
      processedCount++;

      // Quand tous les fichiers sont traités, mettre à jour en une seule fois
      if (processedCount === validFiles.length) {
        const updatedImages = [...currentImages, ...newImages];
        emit('update:modelValue', updatedImages);

        // Émettre les événements pour chaque fichier ajouté
        newImages.forEach(img => {
          emit('file-added', img);
        });

        // Feedback positif
        if (newImages.length === 1) {
          toast.success(`${newImages[0].name} added successfully`);
        } else {
          toast.success(`${newImages.length} images added successfully`);
        }
      }
    };

    reader.onerror = (error) => {
      console.error('Error reading file:', error);
      toast.error(`Error reading ${file.name}`);
      processedCount++;

      // Même si il y a une erreur, vérifier si on peut mettre à jour
      if (processedCount === validFiles.length && newImages.length > 0) {
        const updatedImages = [...currentImages, ...newImages];
        emit('update:modelValue', updatedImages);

        newImages.forEach(img => {
          emit('file-added', img);
        });

        if (newImages.length === 1) {
          toast.success(`${newImages[0].name} added successfully`);
        } else {
          toast.success(`${newImages.length} images added successfully`);
        }
      }
    };

    reader.readAsDataURL(file);
  });
};

const removeImage = (index) => {
  if (props.disabled) return;

  const imageToRemove = props.modelValue[index];
  const updatedImages = [...props.modelValue];
  updatedImages.splice(index, 1);

  emit('update:modelValue', updatedImages);
  emit('file-removed', imageToRemove);

  toast.info(`${imageToRemove.name} removed`);
};

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes';
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const getSourceIcon = (source) => {
  switch (source) {
    case 'camera':
      return 'fa fa-camera';
    case 'drop':
      return 'fa fa-download';
    case 'file':
    default:
      return 'fa fa-folder';
  }
};

const getSourceLabel = (source) => {
  switch (source) {
    case 'camera':
      return 'Camera';
    case 'drop':
      return 'Dropped';
    case 'file':
    default:
      return 'File';
  }
};

// Gérer le drag over
const handleDragEnter = () => {
  if (!props.disabled) {
    isDragOver.value = true;
  }
};
</script>

<style scoped>
.camera-image-upload {
  position: relative;
}

.image-upload-area {
  border: 2px dashed #e0e0e0;
  border-radius: 12px;
  padding: 2rem;
  text-align: center;
  background: #fafafa;
  transition: all 0.3s ease;
  min-height: 200px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.image-upload-area:hover:not(.disabled) {
  border-color: #007bff;
  background: #f8f9ff;
}

.image-upload-area.drag-over {
  border-color: #28a745;
  background: #f0fff4;
  transform: scale(1.02);
}

.image-upload-area.disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.upload-prompt {
  width: 100%;
}

.upload-icon {
  font-size: 3rem;
  color: #6c757d;
  margin-bottom: 1rem;
}

.upload-prompt h4 {
  color: #333;
  margin-bottom: 0.5rem;
}

.upload-prompt p {
  color: #666;
  margin-bottom: 1rem;
}

.upload-options {
  display: flex;
  gap: 1rem;
  justify-content: center;
  margin: 1rem 0;
  flex-wrap: wrap;
}

.upload-option-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border: 2px solid #007bff;
  background: white;
  color: #007bff;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: 500;
  min-width: 120px;
}

.upload-option-btn:hover:not(:disabled) {
  background: #007bff;
  color: white;
  transform: translateY(-2px);
}

.upload-option-btn.camera-btn {
  border-color: #28a745;
  color: #28a745;
}

.upload-option-btn.camera-btn:hover:not(:disabled) {
  background: #28a745;
  color: white;
}

.upload-option-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.selected-images {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 1rem;
  width: 100%;
}

.image-preview {
  position: relative;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  overflow: hidden;
  background: white;
  transition: transform 0.3s ease;
}

.image-preview:hover {
  transform: translateY(-4px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.image-preview img {
  width: 100%;
  height: 150px;
  object-fit: cover;
}

.image-info {
  padding: 0.75rem;
}

.image-name {
  display: block;
  font-weight: 500;
  color: #333;
  margin-bottom: 0.25rem;
  font-size: 0.9rem;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.image-size {
  display: block;
  color: #666;
  font-size: 0.8rem;
  margin-bottom: 0.25rem;
}

.image-source {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  color: #007bff;
  font-size: 0.8rem;
}

.image-source i.fa-camera {
  color: #28a745;
}

.image-source i.fa-download {
  color: #17a2b8;
}

.remove-image {
  position: absolute;
  top: 0.5rem;
  right: 0.5rem;
  background: rgba(220, 53, 69, 0.9);
  color: white;
  border: none;
  border-radius: 50%;
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  opacity: 0;
}

.image-preview:hover .remove-image {
  opacity: 1;
}

.remove-image:hover {
  background: #dc3545;
  transform: scale(1.1);
}

.add-more-actions {
  grid-column: 1 / -1;
  display: flex;
  gap: 1rem;
  justify-content: center;
  margin-top: 1rem;
}

.add-more-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border: 2px dashed #007bff;
  background: transparent;
  color: #007bff;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.add-more-btn:hover {
  background: #f8f9ff;
  transform: translateY(-2px);
}

.add-more-btn.camera-btn {
  border-color: #28a745;
  color: #28a745;
}

.add-more-btn.camera-btn:hover {
  background: #f0fff4;
}

.error-message {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #dc3545;
  font-size: 0.875rem;
  margin-top: 0.5rem;
}

.form-text {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #6c757d;
  font-size: 0.875rem;
  margin-top: 0.5rem;
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

/* Responsive */
@media (max-width: 768px) {
  .upload-options {
    flex-direction: column;
    align-items: center;
  }

  .upload-option-btn {
    width: 100%;
    max-width: 200px;
    justify-content: center;
  }

  .selected-images {
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  }

  .add-more-actions {
    flex-direction: column;
    align-items: center;
  }

  .add-more-btn {
    width: 100%;
    max-width: 200px;
    justify-content: center;
  }

  .image-upload-area {
    padding: 1rem;
  }
}

@media (max-width: 480px) {
  .selected-images {
    grid-template-columns: 1fr;
  }

  .upload-prompt h4 {
    font-size: 1.2rem;
  }

  .upload-icon {
    font-size: 2rem;
  }
}
</style>
