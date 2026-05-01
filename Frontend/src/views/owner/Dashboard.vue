<template>
  <div class="dashboard-page page">
    <div class="container">
      <h1>{{ $t('dashboard.title') }}</h1>

      <!-- Stats Cards -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon">🏠</div>
          <div class="stat-info">
            <span class="stat-value">{{ myListings.length }}</span>
            <span class="stat-label">{{ $t('dashboard.totalListings') }}</span>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">✅</div>
          <div class="stat-info">
            <span class="stat-value">{{ activeListings }}</span>
            <span class="stat-label">{{ $t('dashboard.activeListings') }}</span>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">💬</div>
          <div class="stat-info">
            <span class="stat-value">0</span>
            <span class="stat-label">{{ $t('dashboard.recentMessages') }}</span>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="quick-actions">
        <router-link to="/listings/add" class="btn btn-primary">
          ➕ {{ $t('nav.addListing') }}
        </router-link>
        <router-link to="/my-listings" class="btn btn-secondary">
          📋 {{ $t('nav.myListings') }}
        </router-link>
        <router-link to="/chats" class="btn btn-secondary">
          💬 {{ $t('nav.chats') }}
        </router-link>
      </div>

      <!-- Recent Listings -->
      <div class="section">
        <h2>عقاراتك الأخيرة</h2>
        <div v-if="loading" class="loading">
          <div class="loader"></div>
        </div>
        <div v-else-if="myListings.length === 0" class="no-listings">
          <p>لا توجد عقارات بعد</p>
          <router-link to="/listings/add" class="btn btn-primary">
            {{ $t('nav.addListing') }}
          </router-link>
        </div>
        <div v-else class="listings-grid">
          <ListingCard 
            v-for="listing in recentListings" 
            :key="listing.id" 
            :listing="listing"
            :show-favorite="false"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useListingsStore } from '@/stores/listings'
import ListingCard from '@/components/ListingCard.vue'

const listingsStore = useListingsStore()
const loading = ref(true)

const myListings = computed(() => listingsStore.myListings)
const activeListings = computed(() => myListings.value.filter(l => l.is_available).length)
const recentListings = computed(() => myListings.value.slice(0, 3))

onMounted(async () => {
  try {
    await listingsStore.fetchMyListings()
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.dashboard-page h1 {
  margin-bottom: var(--spacing-lg);
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: var(--spacing-lg);
  margin-bottom: var(--spacing-xl);
}

.stat-card {
  display: flex;
  align-items: center;
  gap: var(--spacing-md);
  padding: var(--spacing-lg);
  background: var(--bg-primary);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-md);
}

.stat-icon {
  font-size: 2.5rem;
}

.stat-info {
  display: flex;
  flex-direction: column;
}

.stat-value {
  font-size: 2rem;
  font-weight: 700;
  color: var(--primary);
}

.stat-label {
  color: var(--text-secondary);
}

.quick-actions {
  display: flex;
  gap: var(--spacing-md);
  margin-bottom: var(--spacing-xl);
}

.section {
  margin-bottom: var(--spacing-xl);
}

.section h2 {
  margin-bottom: var(--spacing-lg);
}

.listings-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: var(--spacing-lg);
}

.loading, .no-listings {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: var(--spacing-md);
  padding: var(--spacing-2xl);
  background: var(--bg-primary);
  border-radius: var(--radius-lg);
  color: var(--text-secondary);
}

@media (max-width: 1024px) {
  .stats-grid, .listings-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .stats-grid, .listings-grid {
    grid-template-columns: 1fr;
  }

  .quick-actions {
    flex-wrap: wrap;
  }
}
</style>
