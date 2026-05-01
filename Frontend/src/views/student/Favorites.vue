<template>
  <div class="favorites-page page">
    <div class="container">
      <h1>{{ $t('nav.favorites') }}</h1>

      <div v-if="listingsStore.loading" class="loading">
        <div class="loader"></div>
      </div>

      <div v-else-if="favorites.length === 0" class="no-results">
        <p>{{ $t('search.noResults') }}</p>
        <router-link to="/search" class="btn btn-primary mt-2">
          {{ $t('nav.search') }}
        </router-link>
      </div>

      <div v-else class="listings-grid">
        <ListingCard 
          v-for="listing in favorites" 
          :key="listing.id" 
          :listing="listing" 
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { useListingsStore } from '@/stores/listings'
import ListingCard from '@/components/ListingCard.vue'

const listingsStore = useListingsStore()
const favorites = computed(() => listingsStore.favorites)

onMounted(() => {
  listingsStore.fetchFavorites()
})
</script>

<style scoped>
.favorites-page h1 {
  margin-bottom: var(--spacing-lg);
}

.listings-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: var(--spacing-lg);
}

.loading, .no-results {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: var(--spacing-2xl);
}

.no-results {
  color: var(--text-secondary);
}

@media (max-width: 1024px) {
  .listings-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 768px) {
  .listings-grid { grid-template-columns: 1fr; }
}
</style>
