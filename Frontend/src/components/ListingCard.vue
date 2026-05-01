<template>
  <div class="listing-card" @click="goToListing">
    <div class="card-image">
      <img 
        :src="imageUrl" 
        :alt="listing.title"
        loading="lazy"
      />
      <span class="property-type">{{ $t(`search.types.${listing.type}`) }}</span>
      <span v-if="isRecommended" class="match-badge">✨ {{ $t('problemSolver.results.recommendations') }}</span>
      <button 
        v-if="showFavorite && authStore.isAuthenticated" 
        class="favorite-btn"
        :class="{ active: isFavorite }"
        @click.stop="toggleFavorite"
      >
        ❤
      </button>
    </div>
    <div class="card-content">
      <h3 class="listing-title">{{ localizedTitle }}</h3>
      <p class="listing-location">
        📍 {{ localizedCity }}, {{ localizedRegion }}
      </p>
      <div class="listing-details">
        <span v-if="listing.bedrooms">🛏 {{ listing.bedrooms }}</span>
        <span v-if="listing.bathrooms">🚿 {{ listing.bathrooms }}</span>
        <span v-if="listing.area_sqm">📐 {{ listing.area_sqm }} m²</span>
      </div>
      <div class="listing-footer">
        <span class="listing-price">
          {{ formatPrice(listing.price) }}
          <small>{{ $t('listing.perMonth') }}</small>
        </span>
        <span class="availability" :class="listing.is_available ? 'available' : 'unavailable'">
          {{ listing.is_available ? $t('listing.available') : $t('listing.notAvailable') }}
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '@/stores/auth'
import { useListingsStore } from '@/stores/listings'

const props = defineProps({
  listing: {
    type: Object,
    required: true
  },
  showFavorite: {
    type: Boolean,
    default: true
  },
  isRecommended: {
    type: Boolean,
    default: false
  }
})

const router = useRouter()
const { locale } = useI18n()
const authStore = useAuthStore()
const listingsStore = useListingsStore()

const isArabic = computed(() => locale.value === 'ar')

const localizedTitle = computed(() => 
  isArabic.value && props.listing.title_ar ? props.listing.title_ar : props.listing.title
)

const localizedCity = computed(() => 
  isArabic.value && props.listing.city?.name_ar ? props.listing.city.name_ar : props.listing.city?.name
)

const localizedRegion = computed(() => 
  isArabic.value && props.listing.region?.name_ar ? props.listing.region.name_ar : props.listing.region?.name
)

const imageUrl = computed(() => {
  if (props.listing.images?.length > 0) {
    const primaryImage = props.listing.images.find(img => img.is_primary) || props.listing.images[0]
    return `http://68.210.64.183/storage/${primaryImage.path}`
  }
  return 'https://via.placeholder.com/400x250?text=No+Image'
})

const isFavorite = computed(() => 
  listingsStore.favorites.some(f => f.id === props.listing.id)
)

const formatPrice = (price) => {
  return new Intl.NumberFormat(isArabic.value ? 'ar-EG' : 'en-EG', {
    style: 'currency',
    currency: 'EGP',
    minimumFractionDigits: 0
  }).format(price)
}

const goToListing = () => {
  router.push(`/listings/${props.listing.id}`)
}

const toggleFavorite = async () => {
  try {
    await listingsStore.toggleFavorite(props.listing.id)
  } catch (error) {
    console.error('Failed to toggle favorite:', error)
  }
}
</script>

<style scoped>
.listing-card {
  background: var(--bg-primary);
  border-radius: var(--radius-lg);
  overflow: hidden;
  box-shadow: var(--shadow-md);
  cursor: pointer;
  transition: all var(--transition-normal);
}

.listing-card:hover {
  transform: translateY(-6px);
  box-shadow: var(--shadow-xl);
}

.card-image {
  position: relative;
  height: 200px;
  overflow: hidden;
}

.card-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform var(--transition-slow);
}

.listing-card:hover .card-image img {
  transform: scale(1.05);
}

.property-type {
  position: absolute;
  top: var(--spacing-sm);
  left: var(--spacing-sm);
  padding: var(--spacing-xs) var(--spacing-sm);
  background: rgba(0, 0, 0, 0.7);
  color: white;
  font-size: 0.75rem;
  font-weight: 500;
  border-radius: var(--radius-sm);
}

[dir="rtl"] .property-type {
  left: auto;
  right: var(--spacing-sm);
}

.match-badge {
  position: absolute;
  top: var(--spacing-sm);
  right: var(--spacing-sm);
  padding: var(--spacing-xs) var(--spacing-sm);
  background: linear-gradient(135deg, var(--secondary), #10b981);
  color: white;
  font-size: 0.75rem;
  font-weight: 600;
  border-radius: var(--radius-sm);
  z-index: 1;
  box-shadow: var(--shadow-sm);
}

[dir="rtl"] .match-badge {
  right: auto;
  left: var(--spacing-sm);
}

.favorite-btn {
  position: absolute;
  top: var(--spacing-sm);
  right: var(--spacing-sm);
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: white;
  border: none;
  border-radius: var(--radius-full);
  cursor: pointer;
  font-size: 1rem;
  opacity: 0.9;
  transition: all var(--transition-fast);
}

[dir="rtl"] .favorite-btn {
  right: auto;
  left: var(--spacing-sm);
}

.favorite-btn:hover {
  transform: scale(1.1);
}

.favorite-btn.active {
  color: var(--danger);
}

.card-content {
  padding: var(--spacing-md);
}

.listing-title {
  font-size: 1.1rem;
  font-weight: 600;
  margin-bottom: var(--spacing-xs);
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.listing-location {
  color: var(--text-secondary);
  font-size: 0.9rem;
  margin-bottom: var(--spacing-sm);
}

.listing-details {
  display: flex;
  gap: var(--spacing-md);
  margin-bottom: var(--spacing-md);
  color: var(--text-secondary);
  font-size: 0.85rem;
}

.listing-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-top: var(--spacing-sm);
  border-top: 1px solid var(--border-color);
}

.listing-price {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--primary);
}

.listing-price small {
  font-size: 0.75rem;
  font-weight: 400;
  color: var(--text-secondary);
}

.availability {
  font-size: 0.75rem;
  padding: var(--spacing-xs) var(--spacing-sm);
  border-radius: var(--radius-full);
}

.availability.available {
  background: rgba(16, 185, 129, 0.1);
  color: var(--secondary);
}

.availability.unavailable {
  background: rgba(239, 68, 68, 0.1);
  color: var(--danger);
}

@media (max-width: 480px) {
  .card-image {
    height: 160px;
  }
  
  .listing-title {
    font-size: 1rem;
  }
  
  .listing-price {
    font-size: 1.1rem;
  }

  .listing-details {
    gap: var(--spacing-sm);
    font-size: 0.8rem;
  }
}
</style>
