<template>
  <div class="listing-details-page">
    <div class="container">
      <div v-if="loading" class="loading">
        <div class="loader"></div>
      </div>

      <template v-else-if="listing">
        <!-- Back Button -->
        <button class="btn btn-secondary mb-2" @click="goBack">
          ← {{ $t('common.back') }}
        </button>

        <div class="listing-layout">
          <!-- Main Content -->
          <div class="listing-main">
            <!-- Image Gallery -->
            <div class="image-gallery">
              <img :src="mainImage" :alt="listing.title" class="main-image" />
              <div v-if="listing.images?.length > 1" class="thumbnails">
                <img 
                  v-for="(img, index) in listing.images" 
                  :key="index"
                  :src="getImageUrl(img.path)"
                  @click="setMainImage(img.path)"
                  :class="{ active: currentImage === img.path }"
                />
              </div>
            </div>

            <!-- Details -->
            <div class="listing-info">
              <div class="listing-header">
                <div>
                  <span class="property-type badge badge-primary">
                    {{ $t(`search.types.${listing.type}`) }}
                  </span>
                  <h1>{{ localizedTitle }}</h1>
                  <p class="location">📍 {{ localizedCity }}, {{ localizedRegion }}</p>
                </div>
                <div class="price-box">
                  <span class="price">{{ formatPrice(listing.price) }}</span>
                  <span class="period">{{ $t('listing.perMonth') }}</span>
                </div>
              </div>

              <div class="features">
                <div v-if="listing.bedrooms" class="feature">
                  <span class="feature-icon">🛏</span>
                  <span>{{ listing.bedrooms }} {{ $t('listing.bedrooms') }}</span>
                </div>
                <div v-if="listing.bathrooms" class="feature">
                  <span class="feature-icon">🚿</span>
                  <span>{{ listing.bathrooms }} {{ $t('listing.bathrooms') }}</span>
                </div>
                <div v-if="listing.area_sqm" class="feature">
                  <span class="feature-icon">📐</span>
                  <span>{{ listing.area_sqm }} {{ $t('listing.sqm') }}</span>
                </div>
              </div>

              <div class="section">
                <h3>الوصف / Description</h3>
                <p>{{ localizedDescription }}</p>
              </div>

              <div v-if="listing.rules || listing.rules_ar" class="section">
                <h3>{{ $t('listing.rules') }}</h3>
                <p>{{ isArabic && listing.rules_ar ? listing.rules_ar : listing.rules }}</p>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="listing-sidebar">
            <div class="owner-card">
              <h3>{{ $t('listing.contact') }}</h3>
              <div class="owner-info">
                <div class="owner-avatar">
                  {{ listing.user?.name?.charAt(0) }}
                </div>
                <div>
                  <p class="owner-name">{{ listing.user?.name }}</p>
                  <p class="owner-role">{{ listing.user?.role?.name_ar || listing.user?.role?.name }}</p>
                </div>
              </div>
              <button 
                v-if="authStore.isAuthenticated && !isOwner"
                class="btn btn-primary btn-full"
                @click="startChat"
              >
                💬 {{ $t('listing.contact') }}
              </button>
              <button 
                v-if="authStore.isAuthenticated && authStore.isStudent"
                class="btn btn-secondary btn-full mt-1"
                @click="toggleFavorite"
              >
                {{ isFavorite ? '❤ ' + $t('listing.removeFavorite') : '🤍 ' + $t('listing.addFavorite') }}
              </button>
              <router-link 
                v-if="!authStore.isAuthenticated" 
                to="/login" 
                class="btn btn-primary btn-full"
              >
                {{ $t('nav.login') }}
              </router-link>
            </div>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useListingsStore } from '@/stores/listings'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'

const route = useRoute()
const router = useRouter()
const { locale } = useI18n()
const listingsStore = useListingsStore()
const authStore = useAuthStore()

const loading = ref(true)
const currentImage = ref(null)

const isArabic = computed(() => locale.value === 'ar')
const listing = computed(() => listingsStore.currentListing)
const isOwner = computed(() => authStore.user?.id === listing.value?.user_id)

const localizedTitle = computed(() => 
  isArabic.value && listing.value?.title_ar ? listing.value.title_ar : listing.value?.title
)
const localizedDescription = computed(() => 
  isArabic.value && listing.value?.description_ar ? listing.value.description_ar : listing.value?.description
)
const localizedCity = computed(() => 
  isArabic.value && listing.value?.city?.name_ar ? listing.value.city.name_ar : listing.value?.city?.name
)
const localizedRegion = computed(() => 
  isArabic.value && listing.value?.region?.name_ar ? listing.value.region.name_ar : listing.value?.region?.name
)

const mainImage = computed(() => {
  if (currentImage.value) {
    return getImageUrl(currentImage.value)
  }
  if (listing.value?.images?.length > 0) {
    return getImageUrl(listing.value.images[0].path)
  }
  return 'https://via.placeholder.com/800x500?text=No+Image'
})

const isFavorite = computed(() => 
  listingsStore.favorites.some(f => f.id === listing.value?.id)
)

const getImageUrl = (path) => `http://68.210.64.183/storage/${path}`
const setMainImage = (path) => { currentImage.value = path }

const formatPrice = (price) => {
  return new Intl.NumberFormat(isArabic.value ? 'ar-EG' : 'en-EG', {
    style: 'currency',
    currency: 'EGP',
    minimumFractionDigits: 0
  }).format(price)
}

const goBack = () => router.back()

const toggleFavorite = async () => {
  try {
    await listingsStore.toggleFavorite(listing.value.id)
  } catch (error) {
    console.error('Failed to toggle favorite:', error)
  }
}

const startChat = async () => {
  try {
    const response = await api.post('/chats', {
      owner_id: listing.value.user_id,
      listing_id: listing.value.id
    })
    router.push('/chats')
  } catch (error) {
    console.error('Failed to start chat:', error)
  }
}

onMounted(async () => {
  try {
    await listingsStore.fetchListing(route.params.id)
    if (authStore.isAuthenticated) {
      await listingsStore.fetchFavorites()
    }
  } catch (error) {
    console.error('Failed to load listing:', error)
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.listing-details-page {
  padding: var(--spacing-xl) 0;
}

.listing-layout {
  display: grid;
  grid-template-columns: 1fr 350px;
  gap: var(--spacing-xl);
}

.image-gallery {
  margin-bottom: var(--spacing-lg);
}

.main-image {
  width: 100%;
  height: 450px;
  object-fit: cover;
  border-radius: var(--radius-lg);
}

.thumbnails {
  display: flex;
  gap: var(--spacing-sm);
  margin-top: var(--spacing-sm);
}

.thumbnails img {
  width: 80px;
  height: 60px;
  object-fit: cover;
  border-radius: var(--radius-sm);
  cursor: pointer;
  opacity: 0.6;
  transition: opacity var(--transition-fast);
}

.thumbnails img:hover,
.thumbnails img.active {
  opacity: 1;
}

.listing-info {
  background: var(--bg-primary);
  padding: var(--spacing-lg);
  border-radius: var(--radius-lg);
}

.listing-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: var(--spacing-lg);
}

.listing-header h1 {
  font-size: 1.75rem;
  margin-top: var(--spacing-sm);
}

.location {
  color: var(--text-secondary);
  margin-top: var(--spacing-xs);
}

.price-box {
  text-align: end;
}

.price {
  font-size: 2rem;
  font-weight: 700;
  color: var(--primary);
}

.period {
  display: block;
  color: var(--text-secondary);
}

.features {
  display: flex;
  gap: var(--spacing-lg);
  padding: var(--spacing-md) 0;
  border-top: 1px solid var(--border-color);
  border-bottom: 1px solid var(--border-color);
  margin-bottom: var(--spacing-lg);
}

.feature {
  display: flex;
  align-items: center;
  gap: var(--spacing-sm);
}

.feature-icon {
  font-size: 1.25rem;
}

.section {
  margin-bottom: var(--spacing-lg);
}

.section h3 {
  margin-bottom: var(--spacing-sm);
  color: var(--text-primary);
}

.section p {
  color: var(--text-secondary);
  line-height: 1.8;
}

.owner-card {
  background: var(--bg-primary);
  padding: var(--spacing-lg);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-md);
  position: sticky;
  top: 90px;
}

.owner-card h3 {
  margin-bottom: var(--spacing-md);
}

.owner-info {
  display: flex;
  align-items: center;
  gap: var(--spacing-md);
  margin-bottom: var(--spacing-lg);
}

.owner-avatar {
  width: 50px;
  height: 50px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, var(--primary), var(--primary-dark));
  color: white;
  font-size: 1.25rem;
  font-weight: 600;
  border-radius: var(--radius-full);
}

.owner-name {
  font-weight: 600;
}

.owner-role {
  color: var(--text-secondary);
  font-size: 0.9rem;
}

.btn-full {
  width: 100%;
}

@media (max-width: 1024px) {
  .listing-layout {
    grid-template-columns: 1fr;
  }

  .owner-card {
    position: static;
  }
}

@media (max-width: 768px) {
  .listing-header {
    flex-direction: column;
    gap: var(--spacing-md);
  }

  .features {
    flex-wrap: wrap;
  }
}
</style>
