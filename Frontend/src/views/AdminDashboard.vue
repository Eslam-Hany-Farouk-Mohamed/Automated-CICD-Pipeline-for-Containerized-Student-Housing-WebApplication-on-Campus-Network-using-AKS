<template>
  <div class="admin-dashboard page">
    <div class="container">
      <div class="dashboard-header mb-4">
        <div>
          <h1>{{ $t('admin.metricsTitle') || 'إحصائيات النظام' }}</h1>
          <p class="text-secondary">{{ $t('admin.metricsSubtitle') || 'نظرة عامة على أداء المنصة والبيانات الحالية' }}</p>
        </div>
        <button class="btn btn-secondary" @click="fetchMetrics" :disabled="loading">
          <span v-if="loading" class="loader mr-2"></span>
          {{ loading ? $t('common.loading') : $t('common.refresh') || 'تحديث' }}
        </button>
      </div>

      <div v-if="loading && !metrics" class="loading-state">
        <div class="loader-lg"></div>
        <p>جاري تحميل الإحصائيات...</p>
      </div>

      <div v-else-if="metrics" class="metrics-content">
        <!-- Section: Users -->
        <h2 class="mb-3">👤 {{ $t('admin.users') || 'المستخدمون' }}</h2>
        <div class="grid grid-4 mb-4">
          <div class="card stat-card glass-morphism">
            <div class="stat-icon users-icon">👥</div>
            <div class="stat-details">
              <span class="stat-value">{{ metrics.users.total }}</span>
              <span class="stat-label">إجمالي المستخدمين</span>
            </div>
          </div>
          <div class="card stat-card">
            <div class="stat-icon students-icon">🎓</div>
            <div class="stat-details">
              <span class="stat-value">{{ metrics.users.students }}</span>
              <span class="stat-label">الطلاب</span>
            </div>
          </div>
          <div class="card stat-card">
            <div class="stat-icon owners-icon">🏠</div>
            <div class="stat-details">
              <span class="stat-value">{{ metrics.users.owners }}</span>
              <span class="stat-label">الملاك</span>
            </div>
          </div>
          <div class="card stat-card">
            <div class="stat-icon brokers-icon">🏢</div>
            <div class="stat-details">
              <span class="stat-value">{{ metrics.users.brokers }}</span>
              <span class="stat-label">الوسطاء</span>
            </div>
          </div>
        </div>

        <!-- Section: Listings & Interactions -->
        <div class="grid grid-2 mb-4">
          <div class="section-card">
            <h2 class="mb-3">🏘️ {{ $t('admin.listings') || 'العقارات' }}</h2>
            <div class="grid grid-2">
              <div class="card stat-card accent-border">
                <div class="stat-details">
                  <span class="stat-value">{{ metrics.listings.total }}</span>
                  <span class="stat-label">إجمالي العقارات</span>
                </div>
              </div>
              <div class="card stat-card success-border">
                <div class="stat-details">
                  <span class="stat-value">{{ metrics.listings.available }}</span>
                  <span class="stat-label">متاحة حالياً</span>
                </div>
              </div>
            </div>
          </div>
          <div class="section-card">
            <h2 class="mb-3">💬 {{ $t('admin.interactions') || 'التفاعلات' }}</h2>
            <div class="grid grid-2">
              <div class="card stat-card">
                <div class="stat-details">
                  <span class="stat-value">{{ metrics.interactions.chats }}</span>
                  <span class="stat-label">إجمالي المحادثات</span>
                </div>
              </div>
              <div class="card stat-card">
                <div class="stat-details">
                  <span class="stat-value">{{ metrics.interactions.messages }}</span>
                  <span class="stat-label">إجمالي الرسائل</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Section: Locations -->
        <h2 class="mb-3">📍 {{ $t('admin.locations') || 'المواقع' }}</h2>
        <div class="grid grid-2">
          <div class="card location-card">
            <div class="card-body flex-between">
              <div>
                <h3>{{ metrics.locations.cities }}</h3>
                <p class="text-secondary">المدن المسجلة</p>
              </div>
              <div class="location-icon">🏙️</div>
            </div>
          </div>
          <div class="card location-card">
            <div class="card-body flex-between">
              <div>
                <h3>{{ metrics.locations.regions }}</h3>
                <p class="text-secondary">المناطق والأحياء</p>
              </div>
              <div class="location-icon">🗺️</div>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="error-state">
        <p>{{ error || 'فشل تحميل البيانات' }}</p>
        <button class="btn btn-primary mt-2" @click="fetchMetrics">إعادة المحاولة</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const metrics = ref(null)
const loading = ref(true)
const error = ref(null)

const fetchMetrics = async () => {
  loading.value = true
  error.value = null
  try {
    const response = await axios.get('/api/metrics')
    if (response.data.success) {
      metrics.value = response.data.data
    } else {
      error.value = 'فشل في استرداد الإحصائيات'
    }
  } catch (err) {
    console.error('Error fetching metrics:', err)
    error.value = 'حدث خطأ أثناء الاتصال بالخادم'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchMetrics()
})
</script>

<style scoped>
.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  flex-wrap: wrap;
  gap: var(--spacing-md);
}

.stat-card {
  display: flex;
  align-items: center;
  gap: var(--spacing-md);
  padding: var(--spacing-lg);
  border-bottom: 3px solid transparent;
}

.stat-card:hover {
  transform: translateY(-5px);
  border-bottom-color: var(--primary);
}

.stat-icon {
  font-size: 2.5rem;
  width: 60px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--gray-100);
  border-radius: var(--radius-md);
}

.stat-details {
  display: flex;
  flex-direction: column;
}

.stat-value {
  font-size: 2.25rem;
  font-weight: 800;
  color: var(--text-primary);
  line-height: 1;
}

.stat-label {
  font-size: 0.875rem;
  color: var(--text-secondary);
  margin-top: var(--spacing-xs);
}

.accent-border {
  border-left: 4px solid var(--accent);
}

.success-border {
  border-left: 4px solid var(--secondary);
}

.location-icon {
  font-size: 2rem;
  opacity: 0.5;
}

.loading-state, .error-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 400px;
  background: var(--bg-primary);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-md);
}

.loader-lg {
  width: 48px;
  height: 48px;
  border: 5px solid var(--gray-200);
  border-top-color: var(--primary);
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: var(--spacing-md);
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.mr-2 {
  margin-right: var(--spacing-sm);
}
</style>
