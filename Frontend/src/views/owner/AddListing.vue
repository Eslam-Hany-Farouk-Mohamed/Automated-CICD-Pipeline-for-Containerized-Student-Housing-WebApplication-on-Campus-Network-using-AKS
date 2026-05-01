<template>
  <div class="add-listing-page page">
    <div class="container">
      <h1>{{ $t('addListing.title') }}</h1>

      <form @submit.prevent="handleSubmit" class="listing-form">
        <!-- Basic Info -->
        <div class="form-section">
          <h3>{{ $t('addListing.basicInfo') }}</h3>
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label">العنوان (English)</label>
              <input type="text" v-model="form.title" class="form-input" required />
            </div>
            <div class="form-group">
              <label class="form-label">العنوان (عربي)</label>
              <input type="text" v-model="form.title_ar" class="form-input" />
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">الوصف (English)</label>
            <textarea v-model="form.description" class="form-textarea" required></textarea>
          </div>
          <div class="form-group">
            <label class="form-label">الوصف (عربي)</label>
            <textarea v-model="form.description_ar" class="form-textarea"></textarea>
          </div>
        </div>

        <!-- Location -->
        <div class="form-section">
          <h3>{{ $t('addListing.location') }}</h3>
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label">{{ $t('search.filters.city') }}</label>
              <select v-model="form.city_id" class="form-select" @change="onCityChange" required>
                <option :value="null">{{ $t('common.selectOption') }}</option>
                <option v-for="city in cities" :key="city.id" :value="city.id">
                  {{ city.name_ar }}
                </option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">{{ $t('search.filters.region') }}</label>
              <select v-model="form.region_id" class="form-select" :disabled="!form.city_id" required>
                <option :value="null">{{ $t('common.selectOption') }}</option>
                <option v-for="region in regions" :key="region.id" :value="region.id">
                  {{ region.name_ar }}
                </option>
              </select>
            </div>
          </div>
        </div>

        <!-- Details -->
        <div class="form-section">
          <h3>{{ $t('addListing.details') }}</h3>
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label">{{ $t('search.filters.type') }}</label>
              <select v-model="form.type" class="form-select" required>
                <option value="room">{{ $t('search.types.room') }}</option>
                <option value="apartment">{{ $t('search.types.apartment') }}</option>
                <option value="studio">{{ $t('search.types.studio') }}</option>
                <option value="shared">{{ $t('search.types.shared') }}</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">السعر (جنيه/شهر)</label>
              <input type="number" v-model="form.price" class="form-input" min="0" required />
            </div>
            <div class="form-group">
              <label class="form-label">{{ $t('listing.bedrooms') }}</label>
              <input type="number" v-model="form.bedrooms" class="form-input" min="0" />
            </div>
            <div class="form-group">
              <label class="form-label">{{ $t('listing.bathrooms') }}</label>
              <input type="number" v-model="form.bathrooms" class="form-input" min="0" />
            </div>
            <div class="form-group">
              <label class="form-label">المساحة (م²)</label>
              <input type="number" v-model="form.area_sqm" class="form-input" min="0" />
            </div>
          </div>
        </div>

        <!-- Images -->
        <div class="form-section">
          <h3>{{ $t('addListing.images') }}</h3>
          <div class="image-upload">
            <input 
              type="file" 
              @change="handleImages" 
              multiple 
              accept="image/*"
              ref="imageInput"
            />
            <div v-if="imagePreview.length" class="image-previews">
              <img v-for="(img, index) in imagePreview" :key="index" :src="img" />
            </div>
          </div>
        </div>

        <!-- Rules -->
        <div class="form-section">
          <h3>{{ $t('listing.rules') }}</h3>
          <div class="form-group">
            <textarea v-model="form.rules" class="form-textarea" placeholder="Rules in English"></textarea>
          </div>
          <div class="form-group">
            <textarea v-model="form.rules_ar" class="form-textarea" placeholder="القواعد بالعربي"></textarea>
          </div>
        </div>

        <div v-if="error" class="error-message">{{ error }}</div>

        <button type="submit" class="btn btn-primary btn-lg" :disabled="loading">
          <span v-if="loading" class="loader"></span>
          <span v-else>{{ $t('addListing.submit') }}</span>
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useListingsStore } from '@/stores/listings'

const router = useRouter()
const listingsStore = useListingsStore()

const loading = ref(false)
const error = ref(null)
const imageInput = ref(null)
const imagePreview = ref([])
const selectedImages = ref([])

const form = reactive({
  title: '',
  title_ar: '',
  description: '',
  description_ar: '',
  city_id: null,
  region_id: null,
  type: 'room',
  price: null,
  bedrooms: null,
  bathrooms: null,
  area_sqm: null,
  rules: '',
  rules_ar: '',
})

const cities = computed(() => listingsStore.cities)
const selectedCity = computed(() => cities.value.find(c => c.id === form.city_id))
const regions = computed(() => selectedCity.value?.regions || [])

const onCityChange = () => {
  form.region_id = null
}

const handleImages = (event) => {
  const files = event.target.files
  selectedImages.value = Array.from(files)
  imagePreview.value = []
  
  for (const file of files) {
    const reader = new FileReader()
    reader.onload = (e) => {
      imagePreview.value.push(e.target.result)
    }
    reader.readAsDataURL(file)
  }
}

const handleSubmit = async () => {
  loading.value = true
  error.value = null

  try {
    const formData = new FormData()
    Object.keys(form).forEach(key => {
      if (form[key] !== null && form[key] !== '') {
        formData.append(key, form[key])
      }
    })

    selectedImages.value.forEach((file, index) => {
      formData.append(`images[${index}]`, file)
    })

    await listingsStore.createListing(formData)
    router.push('/my-listings')
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to create listing'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  listingsStore.fetchCities()
})
</script>

<style scoped>
.add-listing-page h1 {
  margin-bottom: var(--spacing-lg);
}

.listing-form {
  background: var(--bg-primary);
  padding: var(--spacing-xl);
  border-radius: var(--radius-lg);
}

.form-section {
  margin-bottom: var(--spacing-xl);
  padding-bottom: var(--spacing-xl);
  border-bottom: 1px solid var(--border-color);
}

.form-section:last-of-type {
  border-bottom: none;
}

.form-section h3 {
  margin-bottom: var(--spacing-md);
  color: var(--primary);
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: var(--spacing-md);
}

.image-upload input {
  margin-bottom: var(--spacing-md);
}

.image-previews {
  display: flex;
  gap: var(--spacing-sm);
  flex-wrap: wrap;
}

.image-previews img {
  width: 100px;
  height: 100px;
  object-fit: cover;
  border-radius: var(--radius-md);
}

.btn-lg {
  padding: var(--spacing-md) var(--spacing-2xl);
  font-size: 1.1rem;
}

.error-message {
  padding: var(--spacing-sm) var(--spacing-md);
  background: rgba(239, 68, 68, 0.1);
  color: var(--danger);
  border-radius: var(--radius-md);
  margin-bottom: var(--spacing-md);
}

@media (max-width: 768px) {
  .form-grid {
    grid-template-columns: 1fr;
  }
}
</style>
