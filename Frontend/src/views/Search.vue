<template>
  <div class="search-page">
    <div class="container">
      <h1>{{ $t('search.title') }}</h1>

      <!-- Filters -->
      <div class="filters-card">
        <div class="filters-grid">
          <div class="form-group">
            <label class="form-label">{{ $t('search.filters.keyword') }}</label>
            <input 
              type="text" 
              v-model="filters.search" 
              class="form-input"
              :placeholder="$t('home.hero.searchPlaceholder')"
              @keyup.enter="search"
            />
          </div>

          <div class="form-group">
            <label class="form-label">{{ $t('search.filters.city') }}</label>
            <select v-model="filters.city_id" class="form-select" @change="onCityChange">
              <option :value="null">{{ $t('common.selectOption') }}</option>
              <option v-for="city in cities" :key="city.id" :value="city.id">
                {{ isArabic ? city.name_ar : city.name }}
              </option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">{{ $t('search.filters.region') }}</label>
            <select v-model="filters.region_id" class="form-select" :disabled="!selectedCity">
              <option :value="null">{{ $t('common.selectOption') }}</option>
              <option v-for="region in regions" :key="region.id" :value="region.id">
                {{ isArabic ? region.name_ar : region.name }}
              </option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">{{ $t('search.filters.type') }}</label>
            <select v-model="filters.type" class="form-select">
              <option :value="null">{{ $t('common.selectOption') }}</option>
              <option value="room">{{ $t('search.types.room') }}</option>
              <option value="apartment">{{ $t('search.types.apartment') }}</option>
              <option value="studio">{{ $t('search.types.studio') }}</option>
              <option value="shared">{{ $t('search.types.shared') }}</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">{{ $t('search.filters.priceRange') }}</label>
            <div class="price-range">
              <input 
                type="number" 
                v-model="filters.price_min" 
                class="form-input"
                :placeholder="$t('search.filters.minPrice')"
              />
              <span>-</span>
              <input 
                type="number" 
                v-model="filters.price_max" 
                class="form-input"
                :placeholder="$t('search.filters.maxPrice')"
              />
            </div>
          </div>
        </div>

        <div class="filters-actions">
          <button class="btn btn-primary" @click="search">{{ $t('nav.search') }}</button>
          <button class="btn btn-secondary" @click="clearFilters">{{ $t('common.cancel') }}</button>
        </div>
      </div>

      <!-- Results -->
      <div class="results-header">
        <p v-if="!listingsStore.loading">
          {{ $t('search.results', { count: listingsStore.pagination.total }) }}
        </p>
      </div>

      <div v-if="listingsStore.loading" class="loading">
        <div class="loader"></div>
      </div>

      <div v-else-if="listings.length === 0" class="no-results">
        <p>{{ $t('search.noResults') }}</p>
      </div>

      <div v-else class="listings-grid">
        <ListingCard 
          v-for="listing in listings" 
          :key="listing.id" 
          :listing="listing" 
        />
      </div>

      <!-- Pagination -->
      <div v-if="listingsStore.pagination.lastPage > 1" class="pagination">
        <button 
          class="btn btn-secondary"
          :disabled="listingsStore.pagination.currentPage === 1"
          @click="changePage(listingsStore.pagination.currentPage - 1)"
        >
          {{ $t('common.previous') }}
        </button>
        <span class="page-info">
          {{ listingsStore.pagination.currentPage }} / {{ listingsStore.pagination.lastPage }}
        </span>
        <button 
          class="btn btn-secondary"
          :disabled="listingsStore.pagination.currentPage === listingsStore.pagination.lastPage"
          @click="changePage(listingsStore.pagination.currentPage + 1)"
        >
          {{ $t('common.next') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useListingsStore } from '@/stores/listings'
import ListingCard from '@/components/ListingCard.vue'

const route = useRoute()
const { locale } = useI18n()
const listingsStore = useListingsStore()

const isArabic = computed(() => locale.value === 'ar')

const filters = reactive({
  city_id: null,
  region_id: null,
  type: null,
  price_min: null,
  price_max: null,
  search: '',
})

const cities = computed(() => listingsStore.cities)
const selectedCity = computed(() => cities.value.find(c => c.id === filters.city_id))
const regions = computed(() => selectedCity.value?.regions || [])
const listings = computed(() => listingsStore.listings)

const onCityChange = () => {
  filters.region_id = null
}

const search = () => {
  listingsStore.setFilters(filters)
  listingsStore.fetchListings(1)
}

const clearFilters = () => {
  Object.assign(filters, {
    city_id: null,
    region_id: null,
    type: null,
    price_min: null,
    price_max: null,
    search: '',
  })
  listingsStore.clearFilters()
  listingsStore.fetchListings(1)
}

const changePage = (page) => {
  listingsStore.fetchListings(page)
}

onMounted(() => {
  listingsStore.fetchCities()
  if (route.query.search) {
    filters.search = route.query.search
    listingsStore.setFilters({ search: route.query.search })
  }
  listingsStore.fetchListings(1)
})
</script>

<style scoped>
.search-page {
  padding: var(--spacing-xl) 0;
}

.search-page h1 {
  margin-bottom: var(--spacing-lg);
}

.filters-card {
  background: var(--bg-primary);
  padding: var(--spacing-lg);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-md);
  margin-bottom: var(--spacing-xl);
}

.filters-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: var(--spacing-md);
}

.price-range {
  display: flex;
  align-items: center;
  gap: var(--spacing-sm);
}

.price-range input {
  flex: 1;
}

.filters-actions {
  display: flex;
  gap: var(--spacing-sm);
  margin-top: var(--spacing-md);
  padding-top: var(--spacing-md);
  border-top: 1px solid var(--border-color);
}

.results-header {
  margin-bottom: var(--spacing-md);
  color: var(--text-secondary);
}

.listings-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: var(--spacing-lg);
}

.loading, .no-results {
  display: flex;
  justify-content: center;
  padding: var(--spacing-2xl);
}

.no-results {
  color: var(--text-secondary);
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: var(--spacing-md);
  margin-top: var(--spacing-xl);
}

.page-info {
  color: var(--text-secondary);
}

@media (max-width: 1024px) {
  .filters-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  .listings-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .filters-grid, .listings-grid {
    grid-template-columns: 1fr;
  }
}
</style>
