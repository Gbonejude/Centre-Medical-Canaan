<template>
  <div class="searchable-select" v-click-outside="close">
    <div class="select-trigger" @click="toggle">
      <div class="selected-text" v-if="selectedOption">
        {{ labelPrefix }} {{ selectedOption.label }}
      </div>
      <div class="placeholder" v-else>
        {{ placeholder }}
      </div>
      <i class="fa fa-chevron-down ms-auto ms-2 opacity-50"></i>
    </div>

    <div class="select-dropdown" v-if="isOpen">
      <div class="search-box p-2">
        <div class="input-group input-group-sm">
          <span class="input-group-text bg-white border-end-0">
            <i class="fa fa-search text-muted"></i>
          </span>
          <input
            type="text"
            class="form-control border-start-0 ps-0"
            v-model="searchQuery"
            placeholder="Rechercher..."
            ref="searchInput"
            @click.stop
          />
        </div>
      </div>
      <div class="options-list">
        <div
          class="option-item"
          :class="{ active: modelValue === '' }"
          @click="select('')"
        >
          {{ placeholder }}
        </div>
        <div
          v-for="option in filteredOptions"
          :key="option.value"
          class="option-item"
          :class="{ active: modelValue === option.value }"
          @click="select(option.value)"
        >
          {{ option.label }}
        </div>
        <div v-if="filteredOptions.length === 0" class="p-3 text-center text-muted small">
          Aucun résultat trouvé
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue';

const props = defineProps({
  modelValue: [String, Number],
  options: Array, // Array of { value, label }
  placeholder: { type: String, default: 'Sélectionner...' },
  labelPrefix: { type: String, default: '' }
});

const emit = defineEmits(['update:modelValue']);

const isOpen = ref(false);
const searchQuery = ref('');
const searchInput = ref(null);

const filteredOptions = computed(() => {
  if (!searchQuery.value) return props.options;
  const q = searchQuery.value.toLowerCase();
  return props.options.filter(opt => opt.label.toLowerCase().includes(q));
});

const selectedOption = computed(() => {
  return props.options.find(opt => opt.value === props.modelValue);
});

function toggle() {
  isOpen.value = !isOpen.value;
  if (isOpen.value) {
    nextTick(() => {
      searchInput.value?.focus();
    });
  }
}

function close() {
  isOpen.value = false;
  searchQuery.value = '';
}

function select(value) {
  emit('update:modelValue', value);
  close();
}

// Custom directive for clicking outside
const vClickOutside = {
  mounted(el, binding) {
    el.clickOutsideEvent = (event) => {
      if (!(el === event.target || el.contains(event.target))) {
        binding.value();
      }
    };
    document.addEventListener('click', el.clickOutsideEvent);
  },
  unmounted(el) {
    document.removeEventListener('click', el.clickOutsideEvent);
  },
};
</script>

<style lang="scss" scoped>
.searchable-select {
  position: relative;
  width: 200px;
  user-select: none;
}

.select-trigger {
  height: 38px;
  padding: 0.5rem 0.85rem;
  background: white;
  border: 1px solid #e4e6ef;
  border-radius: 8px;
  display: flex;
  align-items: center;
  cursor: pointer;
  font-size: 0.875rem;
  transition: all 0.2s;

  &:hover {
    border-color: #4361ee;
  }

  .selected-text {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-weight: 500;
  }

  .placeholder {
    color: #a1a5b7;
  }
}

.select-dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  margin-top: 4px;
  background: white;
  border: 1px solid #e4e6ef;
  border-radius: 8px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.1);
  z-index: 1000;
  max-height: 300px;
  display: flex;
  flex-direction: column;
}

.options-list {
  overflow-y: auto;
  flex: 1;

  .option-item {
    padding: 0.6rem 1rem;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.2s;

    &:hover {
      background: #f8f9ff;
      color: #4361ee;
    }

    &.active {
      background: #4361ee;
      color: white;
    }
  }
}
</style>
