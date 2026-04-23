<template>
    <input
      type="text"
      ref="datepicker"
      class="form-control"
      :value="modelValue"
      :placeholder="enableTime ? 'Ex: 2025-07-15 14:30' : 'Ex: 2025-07-15'"
      @input="$emit('update:modelValue', $event.target.value)"
    />
</template>

  <script setup>
  import { ref, onMounted } from 'vue'
  import flatpickr from 'flatpickr'
  import 'flatpickr/dist/flatpickr.min.css'
import 'flatpickr/dist/themes/material_blue.css'
  const props = defineProps({
    modelValue: String,
    enableTime: {
      type: Boolean,
      default: true
    }
  })
  const emit = defineEmits(['update:modelValue'])
  const datepicker = ref(null)

  onMounted(() => {
    flatpickr(datepicker.value, {
        dateFormat: props.enableTime ? 'Y-m-d H:i' : 'Y-m-d',
        enableTime: props.enableTime,
        time_24hr: true,
      defaultDate: props.modelValue,

      onChange: ([date], dateStr) => {
        emit('update:modelValue', dateStr)
      }
    })
  })
  </script>
