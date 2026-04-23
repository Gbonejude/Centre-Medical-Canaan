<script setup lang="ts">
import { defineProps, defineEmits, ref, watch, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps<{
  visible: boolean
  width?: string | number
  textClass?: string
  title?: string
  isAddMode: boolean
  isDetailMode: boolean
}>()

const emits = defineEmits(['update:visible', 'save'])

const modalRef = ref<HTMLElement | null>(null)
let modalInstance: bootstrap.Modal | null = null

const closeDialog = () => {
  emits('update:visible', false)
}

const saveData = () => {
  emits('save')
}

onMounted(() => {
  if (modalRef.value) {
    modalInstance = new bootstrap.Modal(modalRef.value)
  }
})

onBeforeUnmount(() => {
  if (modalInstance) {
    modalInstance.dispose()
  }
})

watch(() => props.visible, (newVal) => {
  if (modalInstance) {
    newVal ? modalInstance.show() : modalInstance.hide()
  }
})
</script>

<template>
  <div
    ref="modalRef"
    class="modal fade"
    tabindex="-1"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-dialog-centered" :style="{ maxWidth: props.width ?? 800 }">
      <div class="modal-content">
        
        <div class="modal-header">
          <h5 class="modal-title">{{ props.title ?? 'Modal Window' }}</h5>
          <button
            type="button"
            class="btn-close"
            @click="closeDialog"
          ></button>
        </div>

        
        <div class="modal-body" :class="props.textClass ?? 'mx-3'">
          <slot name="body" />
        </div>

        
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" @click="closeDialog">
            <slot name="cancel-button">Cancel</slot>
          </button>
          <button
            v-if="props.isAddMode && !props.isDetailMode"
            type="button"
            class="btn btn-warning"
            @click="saveData"
          >
            <slot name="save-button">Save</slot>
          </button>
          <button
            v-if="!props.isAddMode && !props.isDetailMode"
            type="button"
            class="btn btn-warning"
            @click="saveData"
          >
            <slot name="update-button">Update</slot>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.modal-dialog {
  margin-top: 10vh; 
}
.modal-backdrop {
  background-color: rgba(0, 0, 0, 0.5) !important; 
  backdrop-filter: none !important; 
}
</style>
