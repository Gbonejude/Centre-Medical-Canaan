<template>
  <input
    type="text"
    ref="timepicker"
    class="form-control"
    :value="modelValue"
    placeholder="Ex: 02:30 PM"
    @input="$emit('update:modelValue', $event.target.value)"
  />
</template>

<script setup>
import { ref, onMounted, watch } from "vue"
import flatpickr from "flatpickr"
import "flatpickr/dist/flatpickr.min.css"
import "flatpickr/dist/themes/material_blue.css"

const props = defineProps({
  modelValue: String,
})
const emit = defineEmits(["update:modelValue"])
const timepicker = ref(null)
let fpInstance = null

onMounted(() => {
  fpInstance = flatpickr(timepicker.value, {
    enableTime: true,
    noCalendar: true,
    dateFormat: "h:i K",
    defaultDate: props.modelValue || null,
    onChange: ([date], dateStr) => {
      emit("update:modelValue", dateStr)
    },
  })
})

// Sync flatpickr internal state when modelValue changes from outside
watch(() => props.modelValue, (newVal) => {
  if (!fpInstance) return
  const current = fpInstance.input.value
  if (newVal !== current) {
    fpInstance.setDate(newVal || null, false) // false = don't trigger onChange
  }
})
</script>
