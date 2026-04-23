<template>
  <div class="maps-view">
    
    <div v-if="isLoading" class="map-loading">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading map...</span>
      </div>
      <p>Loading map...</p>
    </div>

    
    <div ref="mapContainer" class="google-map"></div>

    
    <div ref="infoWindowContent" style="display: none;">
      <div class="map-info-window">
        <div class="info-header">
          <div class="care-house-avatar">
            <i class="fa fa-home"></i>
          </div>
          <div class="info-details">
            <h6 class="care-house-name">{{ selectedCareHouse?.name }}</h6>
            <div class="status-badge" :class="selectedCareHouse?.active ? 'active' : 'inactive'">
              <i :class="selectedCareHouse?.active ? 'fa fa-check-circle' : 'fa fa-pause-circle'"></i>
              {{ selectedCareHouse?.active ? 'Active' : 'Inactive' }}
            </div>
          </div>
        </div>

        <div class="info-content" v-if="selectedCareHouse">
          <div v-if="selectedCareHouse.email" class="info-item">
            <i class="fa fa-envelope"></i>
            <a :href="`mailto:${selectedCareHouse.email}`">{{ selectedCareHouse.email }}</a>
          </div>

          <div v-if="selectedCareHouse.phone" class="info-item">
            <i class="fa fa-phone"></i>
            <a :href="`tel:${selectedCareHouse.phone}`">{{ selectedCareHouse.phone }}</a>
          </div>

          <div v-if="selectedCareHouse.address" class="info-item">
            <i class="fa fa-map-marker-alt"></i>
            <span>{{ selectedCareHouse.address }}</span>
          </div>

          <div class="info-actions">
            <button @click="viewDetails" class="btn-view">
              <i class="fa fa-eye"></i>
              View Details
            </button>
          </div>
        </div>
      </div>
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
  careHouses: {
    type: Array,
    default: () => []
  },
  showActiveOnly: {
    type: Boolean,
    default: false
  },
  centerLat: {
    type: Number,
    default: 48.8566 
  },
  centerLng: {
    type: Number,
    default: 2.3522 
  },
  zoom: {
    type: Number,
    default: 10
  },
  singleMarker: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['marker-clicked'])

const mapContainer = ref(null)
const infoWindowContent = ref(null)

const map = ref(null)
const markers = ref([])
const infoWindow = ref(null)
const isLoading = ref(true)
const selectedCareHouse = ref(null)

const initializeMap = async () => {
  try {
    
    if (!window.google) {
      await loadGoogleMapsAPI()
    }

    
    let centerLat = props.centerLat
    let centerLng = props.centerLng

    
    if (props.careHouses.length > 0) {
      const bounds = new google.maps.LatLngBounds()
      props.careHouses.forEach(house => {
        if (house.latitude && house.longitude) {
          bounds.extend(new google.maps.LatLng(parseFloat(house.latitude), parseFloat(house.longitude)))
        }
      })

      if (!bounds.isEmpty()) {
        const center = bounds.getCenter()
        centerLat = center.lat()
        centerLng = center.lng()
      }
    }

    
    if (props.singleMarker && props.singleMarker.latitude && props.singleMarker.longitude) {
      centerLat = parseFloat(props.singleMarker.latitude)
      centerLng = parseFloat(props.singleMarker.longitude)
    }

    
    map.value = new google.maps.Map(mapContainer.value, {
      center: { lat: centerLat, lng: centerLng },
      zoom: props.zoom,
      mapTypeControl: true,
      streetViewControl: true,
      fullscreenControl: true,
      zoomControl: true,
      styles: [
        {
          featureType: "poi",
          elementType: "labels",
          stylers: [{ visibility: "off" }]
        }
      ]
    })

    
    infoWindow.value = new google.maps.InfoWindow({
      content: infoWindowContent.value
    })

    
    addMarkers()

    
    if (props.careHouses.length > 1) {
      fitBoundsToMarkers()
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

const addMarkers = () => {
  
  clearMarkers()

  const housesToShow = props.singleMarker ? [props.singleMarker] : props.careHouses

  housesToShow.forEach(house => {
    if (!house.latitude || !house.longitude) return

    const lat = parseFloat(house.latitude)
    const lng = parseFloat(house.longitude)

    
    const marker = new google.maps.Marker({
      position: { lat, lng },
      map: map.value,
      title: house.name,
      icon: {
        url: getMarkerIcon(house),
        scaledSize: new google.maps.Size(40, 40),
        anchor: new google.maps.Point(20, 40)
      },
      animation: google.maps.Animation.DROP
    })

    
    marker.addListener('click', () => {
      selectedCareHouse.value = house
      infoWindow.value.open(map.value, marker)
      emit('marker-clicked', house)
    })

    markers.value.push(marker)
  })
}

const getMarkerIcon = (house) => {
  const color = house.active ? '4361ee' : 'f64e60' 
  return `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(`
    <svg width="40" height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
      <circle cx="20" cy="20" r="18" fill="#${color}" stroke="white" stroke-width="2"/>
      <text x="20" y="26" text-anchor="middle" fill="white" font-size="16" font-family="Arial, sans-serif">??</text>
    </svg>
  `)}`
}

const clearMarkers = () => {
  markers.value.forEach(marker => {
    marker.setMap(null)
  })
  markers.value = []
}

const fitBoundsToMarkers = () => {
  if (markers.value.length === 0) return

  const bounds = new google.maps.LatLngBounds()
  markers.value.forEach(marker => {
    bounds.extend(marker.getPosition())
  })

  map.value.fitBounds(bounds)

  
  const listener = google.maps.event.addListener(map.value, "idle", () => {
    if (map.value.getZoom() > 15) {
      map.value.setZoom(15)
    }
    google.maps.event.removeListener(listener)
  })
}

const viewDetails = () => {
  if (selectedCareHouse.value) {
    emit('marker-clicked', selectedCareHouse.value)
  }
}

watch(() => props.careHouses, () => {
  if (map.value) {
    addMarkers()
    if (props.careHouses.length > 1) {
      fitBoundsToMarkers()
    }
  }
}, { deep: true })

watch(() => props.showActiveOnly, () => {
  if (map.value) {
    addMarkers()
    if (props.careHouses.length > 1) {
      fitBoundsToMarkers()
    }
  }
})

onMounted(() => {
  nextTick(() => {
    initializeMap()
  })
})
</script>

<style lang="scss" scoped>
.maps-view {
  position: relative;
  width: 100%;
  height: 100%;

  .google-map {
    width: 100%;
    height: 100%;
    border-radius: 0.475rem;
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

:global(.map-info-window) {
  min-width: 280px;
  padding: 0;

  .info-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    border-bottom: 1px solid #e4e6ef;

    .care-house-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: linear-gradient(135deg, #4361ee, #6c5ce7);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 1.1rem;
    }

    .info-details {
      flex: 1;

      .care-house-name {
        margin: 0 0 0.25rem 0;
        font-weight: 600;
        color: #3f4254;
        font-size: 1rem;
      }

      .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.125rem 0.5rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;

        &.active {
          background-color: rgba(10, 187, 135, 0.1);
          color: #0abb87;
        }

        &.inactive {
          background-color: rgba(246, 78, 96, 0.1);
          color: #f64e60;
        }

        i {
          font-size: 0.7rem;
        }
      }
    }
  }

  .info-content {
    padding: 1rem;

    .info-item {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      margin-bottom: 0.75rem;
      font-size: 0.875rem;

      &:last-child {
        margin-bottom: 0;
      }

      i {
        width: 16px;
        color: #6c757d;
        font-size: 0.8rem;
      }

      a {
        color: #4361ee;
        text-decoration: none;

        &:hover {
          text-decoration: underline;
        }
      }

      span {
        color: #3f4254;
      }
    }

    .info-actions {
      margin-top: 1rem;
      padding-top: 0.75rem;
      border-top: 1px solid #e4e6ef;

      .btn-view {
        background: linear-gradient(to right, #4361ee, #6c5ce7);
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        width: 100%;
        justify-content: center;

        &:hover {
          background: linear-gradient(to right, #3d56d9, #5f4fd3);
          transform: translateY(-1px);
        }

        i {
          font-size: 0.8rem;
        }
      }
    }
  }
}

@media (max-width: 767.98px) {
  :global(.map-info-window) {
    min-width: 250px;

    .info-header {
      padding: 0.75rem;

      .care-house-avatar {
        width: 35px;
        height: 35px;
        font-size: 1rem;
      }

      .info-details .care-house-name {
        font-size: 0.9rem;
      }
    }

    .info-content {
      padding: 0.75rem;

      .info-item {
        font-size: 0.8rem;
        margin-bottom: 0.5rem;
      }
    }
  }
}
</style>
