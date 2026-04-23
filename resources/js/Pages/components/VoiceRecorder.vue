<template>
  <div class="voice-recorder">
    <div class="recorder-container" :class="{ 'is-recording': isRecording, 'has-audio': audioUrl }">
      <!-- Recording State -->
      <div v-if="!audioUrl" class="recording-controls">
        <button 
          type="button" 
          @click="toggleRecording" 
          class="record-btn"
          :class="{ 'recording': isRecording }"
        >
          <div class="icon-wrapper">
            <i v-if="!isRecording" class="fa fa-microphone"></i>
            <i v-else class="fa fa-stop"></i>
          </div>
          <div v-if="isRecording" class="pulse-ring"></div>
        </button>
        
        <div class="recording-status">
          <span v-if="isRecording" class="timer">{{ formattedTime }}</span>
          <span v-else class="instruction">Click to record voice note</span>
        </div>
        
        <div v-if="isRecording" class="visualizer">
          <div v-for="n in 8" :key="n" class="bar" :style="{ height: `${Math.random() * 100}%` }"></div>
        </div>
      </div>

      <!-- Preview State -->
      <div v-else class="audio-preview">
        <div class="audio-info">
          <div class="audio-icon">
            <i class="fa fa-volume-up"></i>
          </div>
          <div class="audio-details">
            <span class="audio-name">Voice Recording</span>
            <span class="audio-meta">{{ audioSize }} • {{ durationLabel }}</span>
          </div>
        </div>
        
        <audio ref="audioPlayer" :src="audioUrl" @loadedmetadata="onAudioLoaded"></audio>
        
        <div class="preview-actions">
          <button type="button" @click="playPreview" class="btn-play" v-if="!isPlaying">
            <i class="fa fa-play"></i>
          </button>
          <button type="button" @click="pausePreview" class="btn-pause" v-else>
            <i class="fa fa-pause"></i>
          </button>
          
          <button type="button" @click="clearRecording" class="btn-delete">
            <i class="fa fa-trash-alt"></i>
          </button>
        </div>
      </div>
    </div>
    
    <div v-if="error" class="error-message">
      <i class="fa fa-exclamation-triangle"></i>
      {{ error }}
    </div>
  </div>
</template>

<script setup>
import { ref, onUnmounted, computed } from 'vue';

const props = defineProps({
  modelValue: {
    type: [File, String, Object],
    default: null
  }
});

const emit = defineEmits(['update:modelValue', 'change']);

const isRecording = ref(false);
const audioUrl = ref(null);
const mediaRecorder = ref(null);
const audioChunks = ref([]);
const timer = ref(0);
const timerInterval = ref(null);
const error = ref(null);
const isPlaying = ref(false);
const audioPlayer = ref(null);
const duration = ref(0);
const audioBlob = ref(null);

const formattedTime = computed(() => {
  const mins = Math.floor(timer.value / 60);
  const secs = timer.value % 60;
  return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
});

const audioSize = computed(() => {
  if (!audioBlob.value) return '0 KB';
  const kb = audioBlob.value.size / 1024;
  return kb > 1024 ? `${(kb / 1024).toFixed(1)} MB` : `${Math.round(kb)} KB`;
});

const durationLabel = computed(() => {
  const mins = Math.floor(duration.value / 60);
  const secs = Math.round(duration.value % 60);
  return `${mins}:${secs.toString().padStart(2, '0')}`;
});

async function startRecording() {
  error.value = null;
  audioChunks.value = [];
  
  try {
    const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
    mediaRecorder.value = new MediaRecorder(stream);
    
    mediaRecorder.value.ondataavailable = (event) => {
      audioChunks.value.push(event.data);
    };
    
    mediaRecorder.value.onstop = () => {
      audioBlob.value = new Blob(audioChunks.value, { type: 'audio/webm' });
      audioUrl.value = URL.createObjectURL(audioBlob.value);
      
      const file = new File([audioBlob.value], 'recording.webm', { type: 'audio/webm' });
      emit('update:modelValue', file);
      emit('change', file);
      
      // Stop all tracks
      stream.getTracks().forEach(track => track.stop());
    };
    
    mediaRecorder.value.start();
    isRecording.value = true;
    startTimer();
  } catch (err) {
    error.value = "Microphone access denied or not available.";
    console.error(err);
  }
}

function stopRecording() {
  if (mediaRecorder.value && isRecording.value) {
    mediaRecorder.value.stop();
    isRecording.value = false;
    stopTimer();
  }
}

function toggleRecording() {
  if (isRecording.value) stopRecording();
  else startRecording();
}

function startTimer() {
  timer.value = 0;
  timerInterval.value = setInterval(() => {
    timer.value++;
    if (timer.value >= 300) stopRecording(); // Max 5 mins
  }, 1000);
}

function stopTimer() {
  clearInterval(timerInterval.value);
}

function clearRecording() {
  audioUrl.value = null;
  audioBlob.value = null;
  emit('update:modelValue', null);
  emit('change', null);
  isPlaying.value = false;
}

function playPreview() {
  if (audioPlayer.value) {
    audioPlayer.value.play();
    isPlaying.value = true;
    audioPlayer.value.onended = () => { isPlaying.value = false; };
  }
}

function pausePreview() {
  if (audioPlayer.value) {
    audioPlayer.value.pause();
    isPlaying.value = false;
  }
}

function onAudioLoaded() {
  if (audioPlayer.value) {
    duration.value = audioPlayer.value.duration;
  }
}

onUnmounted(() => {
  stopTimer();
  if (audioUrl.value) URL.revokeObjectURL(audioUrl.value);
});
</script>

<style lang="scss" scoped>
$primary: #4361ee;
$danger: #f64e60;
$success: #0abb87;
$border: #e4e6ef;

.voice-recorder {
  width: 100%;
}

.recorder-container {
  border: 2px dashed $border;
  border-radius: 12px;
  padding: 1.5rem;
  background-color: #f9fafb;
  transition: all 0.3s ease;
  min-height: 100px;
  display: flex;
  align-items: center;
  justify-content: center;

  &.is-recording {
    border-color: rgba($danger, 0.5);
    background-color: rgba($danger, 0.02);
  }

  &.has-audio {
    border-style: solid;
    border-color: rgba($primary, 0.3);
    background-color: white;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
  }
}

.recording-controls {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
}

.record-btn {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  border: none;
  background-color: white;
  color: $primary;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  transition: all 0.3s ease;
  position: relative;
  z-index: 2;

  &:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
  }

  &.recording {
    background-color: $danger;
    color: white;
    box-shadow: 0 0 0 0 rgba($danger, 0.4);
    animation: pulse-danger 2s infinite;
  }
}

.pulse-ring {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  border-radius: 50%;
  background-color: $danger;
  opacity: 0.3;
  animation: ring-grow 2s infinite ease-out;
  z-index: 1;
}

.recording-status {
  text-align: center;
  
  .timer {
    font-family: monospace;
    font-size: 1.25rem;
    font-weight: 600;
    color: $danger;
  }

  .instruction {
    font-size: 0.9rem;
    color: #7e8299;
  }
}

.visualizer {
  display: flex;
  align-items: flex-end;
  gap: 3px;
  height: 30px;
  
  .bar {
    width: 4px;
    background-color: $danger;
    border-radius: 2px;
    transition: height 0.1s ease;
  }
}

.audio-preview {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1.5rem;
}

.audio-info {
  display: flex;
  align-items: center;
  gap: 1rem;

  .audio-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    background-color: rgba($primary, 0.1);
    color: $primary;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
  }

  .audio-details {
    display: flex;
    flex-direction: column;

    .audio-name {
      font-weight: 600;
      color: #3f4254;
      font-size: 0.95rem;
    }

    .audio-meta {
      font-size: 0.85rem;
      color: #b5b5c3;
    }
  }
}

.preview-actions {
  display: flex;
  align-items: center;
  gap: 0.75rem;

  button {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 1px solid transparent;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    
    &.btn-play, &.btn-pause {
      background-color: $primary;
      color: white;
      &:hover { background-color: darken($primary, 5%); }
    }

    &.btn-delete {
      background-color: transparent;
      border-color: $border;
      color: #7e8299;
      &:hover { 
        background-color: rgba($danger, 0.05);
        color: $danger;
        border-color: rgba($danger, 0.1);
      }
    }
  }
}

.error-message {
  margin-top: 0.75rem;
  color: $danger;
  font-size: 0.85rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

@keyframes pulse-danger {
  0% { box-shadow: 0 0 0 0 rgba($danger, 0.4); }
  70% { box-shadow: 0 0 0 10px rgba($danger, 0); }
  100% { box-shadow: 0 0 0 0 rgba($danger, 0); }
}

@keyframes ring-grow {
  0% { transform: scale(1); opacity: 0.3; }
  100% { transform: scale(1.6); opacity: 0; }
}
</style>
