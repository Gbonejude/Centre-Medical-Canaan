<template>
    <input type="text" ref="datepicker" class="form-control" :value="modelValue" placeholder="Ex: 2025-07-15"
        @input="$emit('update:modelValue', $event.target.value)" />
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import flatpickr from 'flatpickr'
import 'flatpickr/dist/flatpickr.min.css'
import 'flatpickr/dist/themes/material_blue.css'
const props = defineProps({
    modelValue: String,
    maxDate: {
        type: String,
        default: 'today'
    }
})
const emit = defineEmits(['update:modelValue'])
const datepicker = ref(null)

const resolvedMaxDate = computed(() => {
    if (props.maxDate === 'today') {
        return 'today'
    }
    if (props.maxDate === 'tomorrow') {
        const tomorrow = new Date()
        tomorrow.setDate(tomorrow.getDate() + 1)
        return tomorrow
    }
    return props.maxDate 
})
onMounted(() => {
    flatpickr(datepicker.value, {
        dateFormat: 'Y-m-d',
        enableTime: false,
        time_24hr: false,
        defaultDate: props.modelValue,
        maxDate: resolvedMaxDate.value,
        onChange: ([date], dateStr) => {
            emit('update:modelValue', dateStr)
        }
    })
})
</script>
