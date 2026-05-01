<template>
  <div class="my-listings-page page">
    <div class="container">
      <div class="page-header">
        <h1>{{ $t('nav.myListings') }}</h1>
        <router-link to="/listings/add" class="btn btn-primary">
          ➕ {{ $t('nav.addListing') }}
        </router-link>
      </div>

      <div v-if="listingsStore.loading" class="loading">
        <div class="loader"></div>
      </div>

      <div v-else-if="myListings.length === 0" class="no-listings">
        <p>لا توجد عقارات بعد</p>
        <router-link to="/listings/add" class="btn btn-primary">
          {{ $t('nav.addListing') }}
        </router-link>
      </div>

      <div v-else class="listings-table">
        <table>
          <thead>
            <tr>
              <th>العقار</th>
              <th>السعر</th>
              <th>النوع</th>
              <th>الحالة</th>
              <th>الإجراءات</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="listing in myListings" :key="listing.id">
              <td>
                <div class="listing-info">
                  <img 
                    :src="getImage(listing)" 
                    :alt="listing.title"
                    class="listing-thumb"
                  />
                  <div>
                    <p class="listing-title">{{ listing.title_ar || listing.title }}</p>
                    <p class="listing-location">
                      {{ listing.city?.name_ar }}, {{ listing.region?.name_ar }}
                    </p>
                  </div>
                </div>
              </td>
              <td>{{ formatPrice(listing.price) }}</td>
              <td>
                <span class="badge badge-primary">{{ listing.type }}</span>
              </td>
              <td>
                <span class="badge" :class="listing.is_available ? 'badge-success' : 'badge-warning'">
                  {{ listing.is_available ? 'متاح' : 'غير متاح' }}
                </span>
              </td>
              <td>
                <div class="actions">
                  <router-link :to="`/listings/${listing.id}`" class="btn btn-secondary btn-sm">
                    👁 {{ $t('common.view') }}
                  </router-link>
                  <button @click="deleteListing(listing.id)" class="btn btn-danger btn-sm">
                    🗑 {{ $t('common.delete') }}
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { useListingsStore } from '@/stores/listings'

const listingsStore = useListingsStore()

const myListings = computed(() => listingsStore.myListings)

const getImage = (listing) => {
  if (listing.images?.length > 0) {
    return `http://68.210.64.183/storage/${listing.images[0].path}`
  }
  return 'https://via.placeholder.com/80x60?text=No+Image'
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('ar-EG', {
    style: 'currency',
    currency: 'EGP',
    minimumFractionDigits: 0
  }).format(price)
}

const deleteListing = async (id) => {
  if (confirm('هل أنت متأكد من حذف هذا العقار؟')) {
    try {
      await listingsStore.deleteListing(id)
    } catch (error) {
      console.error('Failed to delete listing:', error)
    }
  }
}

onMounted(() => {
  listingsStore.fetchMyListings()
})
</script>

<style scoped>
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: var(--spacing-lg);
}

.listings-table {
  background: var(--bg-primary);
  border-radius: var(--radius-lg);
  overflow: hidden;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th, td {
  padding: var(--spacing-md);
  text-align: start;
  border-bottom: 1px solid var(--border-color);
}

th {
  background: var(--gray-100);
  font-weight: 600;
}

.listing-info {
  display: flex;
  align-items: center;
  gap: var(--spacing-md);
}

.listing-thumb {
  width: 80px;
  height: 60px;
  object-fit: cover;
  border-radius: var(--radius-sm);
}

.listing-title {
  font-weight: 600;
}

.listing-location {
  font-size: 0.85rem;
  color: var(--text-secondary);
}

.actions {
  display: flex;
  gap: var(--spacing-xs);
}

.btn-sm {
  padding: var(--spacing-xs) var(--spacing-sm);
  font-size: 0.85rem;
}

.btn-danger {
  background: var(--danger);
  color: white;
  border: none;
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
  .listings-table {
    overflow-x: auto;
  }

  table {
    min-width: 700px;
  }
}
</style>
