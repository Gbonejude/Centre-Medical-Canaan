<template>
    <input type="text" ref="datepicker" class="form-control" :value="modelValue" :placeholder="placeholder"
        @input="$emit('update:modelValue', $event.target.value)" :disabled="disabled" />
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import flatpickr from 'flatpickr'
import 'flatpickr/dist/flatpickr.min.css'
import 'flatpickr/dist/themes/material_blue.css'
const props = defineProps({
    modelValue: String,
    disabled: {
        type: Boolean,
        default: false
    },
    maxDate: {
        type: [String, Date],
        default: null
    },
    minDate: {
        type: [String, Date],
        default: null
    },
    placeholder: {
        type: String,
        default: 'Ex: 2025-07-15'
    }
})
const emit = defineEmits(['update:modelValue'])
const datepicker = ref(null)
let fp = null

const resolveDate = (dateVal) => {
    if (dateVal === 'today') return 'today'
    if (dateVal === 'tomorrow') {
        const tomorrow = new Date()
        tomorrow.setDate(tomorrow.getDate() + 1)
        return tomorrow
    }
    return dateVal
}

const resolvedMaxDate = computed(() => resolveDate(props.maxDate))
const resolvedMinDate = computed(() => resolveDate(props.minDate))

onMounted(() => {
    fp = flatpickr(datepicker.value, {
        dateFormat: 'Y-m-d',
        enableTime: false,
        time_24hr: false,
        defaultDate: props.modelValue,
        clickOpens: !props.disabled,
        maxDate: resolvedMaxDate.value,
        minDate: resolvedMinDate.value,
        disableMobile: true,
        onChange: ([date], dateStr) => {
            emit('update:modelValue', dateStr)
        }
    })
})

watch(() => props.modelValue, (newValue) => {
    if (fp && newValue !== fp.input.value) {
        fp.setDate(newValue, false)
    }
})

watch(() => props.disabled, (newValue) => {
    if (fp) {
        fp.set('clickOpens', !newValue)
    }
})

watch(resolvedMinDate, (newValue) => {
    if (fp) {
        fp.set('minDate', newValue)
    }
})

watch(resolvedMaxDate, (newValue) => {
    if (fp) {
        fp.set('maxDate', newValue)
    }
})
</script>
