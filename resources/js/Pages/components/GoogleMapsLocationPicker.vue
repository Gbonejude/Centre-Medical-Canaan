<template>
  <div class="map-location-picker">
    <div class="map-header">
      <div class="map-title">
        <i class="fa fa-map-marker-alt"></i>
        <span>Location Picker</span>
      </div>
      <div class="search-container">
        <div class="search-wrapper">
          <i class="fa fa-search search-icon"></i>
          <input
            ref="searchInput"
            type="text"
            class="form-control search-input"
            placeholder="Search for a location..."
            @keydown.enter.prevent="searchLocation"
          />
          <button
            type="button"
            class="btn btn-outline-primary search-btn"
            @click="searchLocation"
          >
            Search
          </button>
        </div>
      </div>
    </div>

    <div class="map-container">
      <div ref="mapContainer" class="google-map"></div>

      
      <div v-if="isLoading" class="map-loading">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading map...</span>
        </div>
        <p>Loading map...</p>
      </div>
    </div>

    
    <div v-if="selectedLocation" class="location-info">
      <div class="info-header">
        <i class="fa fa-info-circle"></i>
        <span>Selected Location</span>
      </div>

      <div class="info-content">
        <div class="info-row">
          <label>Address:</label>
          <span>{{ selectedLocation.address || 'Unknown address' }}</span>
        </div>

        <div class="action-buttons">
          <button
            type="button"
            class="btn btn-success btn-sm"
            @click="confirmLocation"
          >
            <i class="fa fa-check"></i>
            Confirm Location
          </button>
          <button
            type="button"
            class="btn btn-outline-secondary btn-sm"
            @click="clearSelection"
          >
            <i class="fa fa-times"></i>
            Clear
          </button>
        </div>
      </div>
    </div>

    
    <div class="map-controls">
      <button
        type="button"
        class="btn btn-outline-info btn-sm current-location-btn"
        @click="getCurrentLocation"
        :disabled="isLoadingCurrentLocation"
      >
        <i class="fa fa-crosshairs"></i>
        <span v-if="isLoadingCurrentLocation">Getting location...</span>
        <span v-else>Use Current Location</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick, watch } from 'vue'

const props = defineProps({
  apiKey: {
    type: String,
    required: true
  },
  latitude: {
    type: [Number, String],
    default: null
  },
  longitude: {
    type: [Number, String],
    default: null
  },
  address: {
    type: String,
    default: ''
  },
  zoom: {
    type: Number,
    default: 12
  },
  height: {
    type: String,
    default: '400px'
  }
})

const emit = defineEmits(['location-selected', 'location-changed'])

const mapContainer = ref(null)
const searchInput = ref(null)

const map = ref(null)
const marker = ref(null)
const geocoder = ref(null)
const autocomplete = ref(null)
const isLoading = ref(true)
const isLoadingCurrentLocation = ref(false)
const selectedLocation = ref(null)

const initializeMap = async () => {
  try {
    
    if (!window.google) {
      await loadGoogleMapsAPI()
    }

    
    
    
    
    
    
    const defaultLat = props.latitude ? parseFloat(props.latitude) : 40.7128
    const defaultLng = props.longitude ? parseFloat(props.longitude) : -74.0060
    
    map.value = new google.maps.Map(mapContainer.value, {
      center: { lat: defaultLat, lng: defaultLng },
      zoom: props.zoom,
      mapTypeControl: true,
      streetViewControl: true,
      fullscreenControl: true,
      zoomControl: true,
    })

    
    geocoder.value = new google.maps.Geocoder()

    
   autocomplete.value = new google.maps.places.Autocomplete(searchInput.value, {
    types: ['establishment', 'geocode'],
    fields: ['geometry', 'name', 'formatted_address'] 
    })

    autocomplete.value.bindTo('bounds', map.value)

    
    autocomplete.value.addListener('place_changed', handlePlaceChanged)

    map.value.set('styles', [
    {
        featureType: 'poi',
        elementType: 'labels',
        stylers: [{ visibility: 'off' }]
    }
    ])
    
    map.value.addListener('click', handleMapClick)


    if (props.latitude && props.longitude) {
      addMarker(defaultLat, defaultLng)
      reverseGeocode(defaultLat, defaultLng)
    }

    // Initialize search input with existing address if provided
    if (props.address && searchInput.value) {
      searchInput.value.value = props.address
    }

    isLoading.value = false
  } catch (error) {
    console.error('Error initializing map:', error)
    isLoading.value = false
  }
}

const loadGoogleMapsAPI = () => {
  return new Promise((resolve, reject) => {
    if (window.google && window.google.maps) {
      resolve()
      return
    }

    const script = document.createElement('script')
    script.src = `https://maps.googleapis.com/maps/api/js?key=${props.apiKey}&libraries=places`
    script.async = true
    script.defer = true

    script.onload = () => resolve()
    script.onerror = () => reject(new Error('Failed to load Google Maps API'))

    document.head.appendChild(script)
  })
}

const handlePlaceChanged = () => {
  const place = autocomplete.value.getPlace()

  if (!place.geometry) {
    console.warn('No geometry found for place')
    return
  }
   clearMarker()
  const location = place.geometry.location
  const lat = location.lat()
  const lng = location.lng()

  map.value.setCenter({ lat, lng })
  map.value.setZoom(15)

  addMarker(lat, lng)

  // Use formatted_address from the place object (this is what user selected from autocomplete)
  const addressToUse = place.formatted_address || place.name || 'Unknown address'

  selectedLocation.value = {
    lat,
    lng,
    address: addressToUse
  }

  emit('location-changed', selectedLocation.value)
}

const handleMapClick = (event) => {
  const lat = event.latLng.lat()
  const lng = event.latLng.lng()
    clearMarker()
  addMarker(lat, lng)
  reverseGeocode(lat, lng)
}

const addMarker = (lat, lng) => {
if (marker.value) {
    console.log('Suppression ancien marqueur')
    marker.value.setMap(null)
    marker.value = null
  }

setTimeout(() => {
    marker.value = new google.maps.Marker({
      position: { lat, lng },
      map: map.value,
      draggable: true,
      animation: google.maps.Animation.DROP
    })

    marker.value.addListener('dragend', (event) => {
      const newLat = event.latLng.lat()
      const newLng = event.latLng.lng()
      reverseGeocode(newLat, newLng)
    })
  }, 50)
}

const reverseGeocode = (lat, lng) => {
  geocoder.value.geocode({ location: { lat, lng } }, (results, status) => {
    if (status === 'OK' && results[0]) {
      selectedLocation.value = {
        lat,
        lng,
        address: results[0].formatted_address
      }
    } else {
      selectedLocation.value = {
        lat,
        lng,
        address: 'Unknown address'
      }
    }

    emit('location-changed', selectedLocation.value)
  })
}

const searchLocation = () => {
  const query = searchInput.value.value.trim()
  if (!query) return

  geocoder.value.geocode({ address: query }, (results, status) => {
    if (status === 'OK' && results[0]) {
      const location = results[0].geometry.location
      const lat = location.lat()
      const lng = location.lng()

      map.value.setCenter({ lat, lng })
      map.value.setZoom(15)
        clearMarker()

      addMarker(lat, lng)

      selectedLocation.value = {
        lat,
        lng,
        address: results[0].formatted_address
      }

      emit('location-changed', selectedLocation.value)
    } else {
      alert('Location not found. Please try a different search term.')
    }
  })
}

const getCurrentLocation = () => {
  if (!navigator.geolocation) {
    alert('Geolocation is not supported by this browser.')
    return
  }

  isLoadingCurrentLocation.value = true

  navigator.geolocation.getCurrentPosition(
    (position) => {
      const lat = position.coords.latitude
      const lng = position.coords.longitude

      map.value.setCenter({ lat, lng })
      map.value.setZoom(15)
    clearMarker()

      addMarker(lat, lng)
      reverseGeocode(lat, lng)

      isLoadingCurrentLocation.value = false
    },
    (error) => {
      console.error('Error getting current location:', error)
      alert('Unable to get current location. Please allow location access.')
      isLoadingCurrentLocation.value = false
    }
  )
}

const confirmLocation = () => {
  if (selectedLocation.value) {
    emit('location-selected', selectedLocation.value)
  }
}

const clearMarker = () => {
     console.log('clearMarker appelée')
     console.log('clemarker.valuea',marker.value)
    if (marker.value) {
        marker.value.setVisible(false)
        marker.value.setMap(null)
        marker.value = null
    }

  
  if (map.value) {
    
    google.maps.event.trigger(map.value, 'resize')
  }
}

const clearSelection = () => {
  selectedLocation.value = null
  if (marker.value) {
    marker.value.setMap(null)
    marker.value = null
  }
  searchInput.value.value = ''
  emit('location-changed', null)
}

watch([() => props.latitude, () => props.longitude], ([newLat, newLng]) => {
  if (newLat && newLng && map.value) {
    const lat = parseFloat(newLat)
    const lng = parseFloat(newLng)
      map.value.setCenter({ lat, lng })
    clearMarker()

    addMarker(lat, lng)
    reverseGeocode(lat, lng)
  }
})

watch(() => props.address, (newAddress) => {
  if (newAddress && searchInput.value) {
    searchInput.value.value = newAddress
  }
})

onMounted(() => {
  nextTick(() => {
    initializeMap()
  })
})
</script>

<style lang="scss" scoped>
.map-location-picker {
  background: white;
  border-radius: 0.475rem;
  box-shadow: 0 0.5rem 1.5rem 0.5rem rgba(0, 0, 0, 0.075);
  overflow: hidden;
  border: 1px solid #e4e6ef;

  .map-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 1rem;
    border-bottom: 1px solid #e4e6ef;

    .map-title {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-weight: 600;
      color: #3f4254;
      margin-bottom: 1rem;

      i {
        color: #4361ee;
      }
    }

    .search-container {
      .search-wrapper {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        position: relative;

        .search-icon {
          position: absolute;
          left: 0.875rem;
          color: #a1a5b7;
          z-index: 2;
        }

        .search-input {
          flex: 1;
          padding-left: 2.5rem;
          border: 1px solid #e4e6ef;
          border-radius: 0.475rem;
          font-size: 0.95rem;

          &:focus {
            border-color: #4361ee;
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
          }
        }

        .search-btn {
          white-space: nowrap;
        }
      }
    }
  }

  .map-container {
    position: relative;
    height: v-bind(height);

    .google-map {
      width: 100%;
      height: 100%;
    }

    .map-loading {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(255, 255, 255, 0.9);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      z-index: 10;

      p {
        margin-top: 1rem;
        color: #6c757d;
      }
    }
  }

  .location-info {
    background: #f8f9fa;
    border-top: 1px solid #e4e6ef;
    padding: 1rem;

    .info-header {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-weight: 600;
      color: #3f4254;
      margin-bottom: 0.75rem;

      i {
        color: #0abb87;
      }
    }

    .info-content {
      .info-row {
        display: flex;
        margin-bottom: 0.5rem;

        label {
          font-weight: 500;
          color: #6c757d;
          min-width: 80px;
          margin-right: 0.5rem;
        }

        span {
          color: #3f4254;
        }
      }

      .coordinates-row {
        display: flex;
        gap: 2rem;
        margin: 0.75rem 0;

        .coordinate-item {
          display: flex;
          align-items: center;
          gap: 0.5rem;

          label {
            font-weight: 500;
            color: #6c757d;
            font-size: 0.875rem;
          }

          .coordinate-value {
            font-family: monospace;
            background: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            border: 1px solid #e4e6ef;
            font-size: 0.825rem;
            color: #3f4254;
          }
        }
      }

      .action-buttons {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.75rem;

        .btn {
          display: flex;
          align-items: center;
          gap: 0.25rem;
          font-size: 0.875rem;

          i {
            font-size: 0.8rem;
          }
        }
      }
    }
  }

  .map-controls {
    position: absolute;
    top: 80px;
    right: 10px;
    z-index: 5;

    .current-location-btn {
      background: white;
      border: 1px solid #e4e6ef;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);

      &:hover:not(:disabled) {
        background: #f8f9fa;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
      }

      &:disabled {
        opacity: 0.6;
        cursor: not-allowed;
      }
    }
  }
}

@media (max-width: 767.98px) {
  .map-location-picker {
    .map-header {
      padding: 0.75rem;

      .search-container .search-wrapper {
        flex-direction: column;
        gap: 0.5rem;

        .search-btn {
          width: 100%;
        }
      }
    }

    .location-info {
      .info-content {
        .coordinates-row {
          flex-direction: column;
          gap: 0.5rem;
        }

        .action-buttons {
          flex-direction: column;

          .btn {
            width: 100%;
            justify-content: center;
          }
        }
      }
    }

    .map-controls {
      position: static;
      padding: 0.75rem;
      background: #f8f9fa;
      border-top: 1px solid #e4e6ef;

      .current-location-btn {
        width: 100%;
        justify-content: center;
      }
    }
  }
}</style>
