<template>
  <div class="home-page">
    <!-- Hero Section -->
    <section class="hero">
      <div class="hero-bg"></div>
      <div class="noise-overlay"></div>
      <div class="container hero-content">
        <h1 class="hero-title">{{ $t('home.hero.title') }}</h1>
        <p class="hero-subtitle">{{ $t('home.hero.subtitle') }}</p>
        
        <div class="hero-search-container">
          <div class="hero-search">
            <div class="search-input-group">
              <svg class="search-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
              </svg>
              <div class="search-divider"></div>
              <input 
                type="text" 
                v-model="searchQuery"
                :placeholder="$t('home.hero.searchPlaceholder')"
                @keyup.enter="goToSearch"
              />
            </div>
            <button class="btn btn-primary" @click="goToSearch">
              <svg class="btn-spark" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M12 3l1.912 5.813h6.112l-4.944 3.592 1.888 5.812-4.968-3.607-4.968 3.607 1.888-5.812-4.944-3.592h6.112z" fill="currentColor"/>
              </svg>
              {{ $t('nav.search') }}
            </button>
          </div>
        </div>

        <!-- Hero Stats -->
        <div class="hero-stats">
          <div class="stat-item" v-for="stat in stats" :key="stat.label">
            <span class="stat-value">{{ stat.value }}{{ stat.suffix }}</span>
            <span class="stat-label">{{ stat.label }}</span>
          </div>
        </div>
      </div>
      
      <div class="scroll-indicator">
        <div class="mouse"></div>
      </div>
    </section>

    <!-- How It Works -->
    <section class="how-it-works">
      <div class="container">
        <h2 class="section-title">{{ $t('home.howItWorks.title') }}</h2>
        <div class="steps">
          <div class="step" v-for="step in [1,2,3,4]" :key="step">
            <div class="step-icon">
              <!-- Discover -->
              <svg v-if="step === 1" class="step-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
              </svg>
              <!-- Match -->
              <svg v-if="step === 2" class="step-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
              </svg>
              <!-- Thrive -->
              <svg v-if="step === 3" class="step-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"></path>
              </svg>
              <!-- Resolve -->
              <svg v-if="step === 4" class="step-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                <line x1="12" y1="22.08" x2="12" y2="12"></line>
              </svg>
            </div>
            <h3>{{ $t(`home.howItWorks.step${step}`) }}</h3>
            <p>{{ $t(`home.howItWorks.step${step}Desc`) }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Listings -->
    <section class="featured">
      <div class="container">
        <h2 class="section-title">{{ $t('home.featured') }}</h2>
        <div v-if="listingsStore.loading" class="listings-grid">
          <SkeletonLoader v-for="n in 6" :key="n" />
        </div>
        <div v-else class="listings-grid">
          <ListingCard 
            v-for="listing in featuredListings" 
            :key="listing.id" 
            :listing="listing" 
          />
        </div>
        <div class="text-center mt-4">
          <router-link to="/search" class="btn btn-secondary">
            {{ $t('nav.search') }} →
          </router-link>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useListingsStore } from '@/stores/listings'
import ListingCard from '@/components/ListingCard.vue'
import SkeletonLoader from '@/components/SkeletonLoader.vue'

const router = useRouter()
const listingsStore = useListingsStore()
const searchQuery = ref('')

const featuredListings = computed(() => listingsStore.listings.slice(0, 6))

const stats = ref([
  { value: 500, target: 500, suffix: '+', label: 'Premium Rooms' },
  { value: 1200, target: 1200, suffix: '+', label: 'Happy Students' },
  { value: 95, target: 95, suffix: '%', label: 'Satisfaction Rate' },
  { value: 24, target: 24, suffix: '/7', label: 'AI Support' }
])

const isScrolled = ref(false)

const handleScroll = () => {
  isScrolled.value = window.scrollY > 50
}

const goToSearch = () => {
  router.push({ path: '/search', query: { search: searchQuery.value } })
}

onMounted(() => {
  listingsStore.fetchListings()
})
</script>

<style scoped>
.hero {
  position: relative;
  padding: 120px 0 100px;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  background: #1a1f1b; /* Soft Dark Green-Gray instead of pure Slate */
}

.hero-bg {
  position: absolute;
  inset: 0;
  background-image: url('https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80');
  background-size: cover;
  background-position: center;
  will-change: transform;
}

.hero-bg::after {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(
    135deg, 
    rgba(95, 122, 97, 0.7) 0%, 
    rgba(122, 146, 163, 0.6) 100%
  );
}

.noise-overlay {
  position: absolute;
  inset: 0;
  background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 400 400' xmlns='http://www.w3.org/2000/svg'%3%3Ffilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
  opacity: 0.05;
  pointer-events: none;
  z-index: 1;
}

.floating-shapes {
  position: absolute;
  inset: 0;
  overflow: hidden;
  pointer-events: none;
  z-index: 1;
}

.shape {
  position: absolute;
  border-radius: 50%;
  filter: blur(80px);
  opacity: 0.4;
  animation: float 20s infinite alternate ease-in-out;
}

.shape-1 {
  width: 400px;
  height: 400px;
  background: var(--primary);
  top: -100px;
  left: -100px;
}

.shape-2 {
  width: 300px;
  height: 300px;
  background: #ec4899;
  bottom: -50px;
  right: -50px;
  animation-delay: -5s;
}

.shape-3 {
  width: 250px;
  height: 250px;
  background: #a855f7;
  top: 40%;
  right: 15%;
  animation-delay: -10s;
}

@keyframes float {
  0% { transform: translate(0, 0) rotate(0deg); }
  100% { transform: translate(50px, 50px) rotate(15deg); }
}

.hero-content {
  position: relative;
  z-index: 10;
  text-align: center;
  color: white;
  max-width: 1000px !important;
}

.hero-title {
  font-size: clamp(2.2rem, 7vw, 4rem);
  font-weight: 800;
  line-height: 1.1;
  margin-bottom: 24px;
  letter-spacing: -0.04em;
  text-shadow: 0 10px 30px rgba(0,0,0,0.25);
  animation: revealUp 1.2s cubic-bezier(0.22, 1, 0.36, 1) both;
}

@media (min-width: 1024px) {
  .hero-title {
    white-space: nowrap;
  }
}

.hero-subtitle {
  font-size: clamp(1.1rem, 2.5vw, 1.4rem);
  opacity: 0.9;
  margin-bottom: 50px;
  font-weight: 500;
  max-width: 700px;
  margin-left: auto;
  margin-right: auto;
  line-height: 1.6;
  animation: revealUp 1.2s cubic-bezier(0.22, 1, 0.36, 1) 0.2s both;
}

.hero-search-container {
  max-width: 800px;
  margin: 0 auto 80px;
  animation: revealUp 1s cubic-bezier(0.23, 1, 0.32, 1) 0.4s both;
}

@keyframes revealUp {
  from { 
    opacity: 1; 
  }
}

.hero-search {
  display: flex;
  align-items: center;
  background: rgba(255, 255, 255, 0.08);
  backdrop-filter: blur(30px);
  padding: 10px;
  border-radius: 36px;
  border: 1px solid rgba(255,255,255,0.2);
  box-shadow: 
    0 25px 50px -12px rgba(0, 0, 0, 0.4),
    0 0 30px rgba(99, 102, 241, 0.1);
  transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
  width: 100%;
}

.search-input-group {
  flex: 1;
  display: flex;
  align-items: center;
  padding-left: 20px;
}

.search-svg {
  width: 20px;
  height: 20px;
  color: rgba(255, 255, 255, 0.8);
  transition: all 0.3s ease;
}

.search-divider {
  width: 1px;
  height: 24px;
  background: rgba(255, 255, 255, 0.2);
  margin: 0 20px;
}

.hero-search:hover {
  transform: translateY(-10px) scale(1.02);
  background: rgba(255, 255, 255, 0.12);
  border-color: rgba(255, 255, 255, 0.4);
  box-shadow: 
    0 40px 80px -20px rgba(0, 0, 0, 0.5),
    0 0 60px rgba(99, 102, 241, 0.3);
}

.hero-search:focus-within {
  background: rgba(255, 255, 255, 0.2);
  border-color: rgba(255, 255, 255, 0.6);
  box-shadow: 
    0 45px 90px -25px rgba(0, 0, 0, 0.6),
    0 0 80px rgba(99, 102, 241, 0.5);
}

.hero-search:focus-within .search-svg {
  color: white;
  transform: scale(1.1);
}

.hero-search input {
  flex: 1;
  padding: 18px 0;
  border: none;
  background: none;
  font-size: 1.2rem;
  color: white;
  font-weight: 600;
  letter-spacing: -0.2px;
}

.hero-search input::placeholder {
  color: rgba(255,255,255,0.5);
}

.hero-search input:focus {
  outline: none;
}

.hero-search .btn {
  padding: 18px 44px;
  border-radius: 28px;
  font-weight: 800;
  font-size: 1.1rem;
  background: linear-gradient(135deg, var(--primary), var(--secondary));
  box-shadow: 0 10px 25px rgba(95, 122, 97, 0.4);
  position: relative;
  overflow: hidden;
  display: flex;
  align-items: center;
  gap: 10px;
}

.btn-spark {
  width: 18px;
  height: 18px;
}

.hero-search .btn::after {
  content: '';
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: linear-gradient(
    45deg, 
    transparent 0%, 
    rgba(255,255,255,0.2) 50%, 
    transparent 100%
  );
  transform: rotate(45deg);
  animation: shimmer 3s infinite;
}

@keyframes shimmer {
  0% { transform: translateX(-100%) rotate(45deg); }
  100% { transform: translateX(100%) rotate(45deg); }
}

/* Hero Stats */
.hero-stats {
  display: flex;
  justify-content: center;
  gap: 80px;
  margin-top: 40px;
}

.stat-item {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.stat-value {
  font-size: clamp(2.5rem, 5vw, 3.5rem);
  font-weight: 900;
  color: #fff;
  letter-spacing: -1px;
}

.stat-label {
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 3px;
  opacity: 0.7;
  font-weight: 700;
  margin-top: 8px;
}

/* Scroll Indicator */
.scroll-indicator {
  position: absolute;
  bottom: 40px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 2;
  opacity: 0.7;
}

.mouse {
  width: 24px;
  height: 40px;
  border: 2px solid white;
  border-radius: 12px;
  position: relative;
}

.mouse::after {
  content: '';
  width: 4px;
  height: 4px;
  background: white;
  border-radius: 50%;
  position: absolute;
  top: 8px;
  left: 50%;
  transform: translateX(-50%);
  animation: mouseScroll 1.5s infinite;
}

@keyframes mouseScroll {
  0% { transform: translate(-50%, 0); opacity: 1; }
  100% { transform: translate(-50%, 15px); opacity: 0; }
}

@keyframes slideUp {
  from { opacity: 0; transform: translateY(40px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.how-it-works {
  padding: 100px 0;
  background: var(--bg-primary);
}

.section-title {
  text-align: center;
  margin-bottom: 64px;
  font-size: 2.5rem;
  font-weight: 800;
  position: relative;
}

.section-title::after {
  content: '';
  position: absolute;
  bottom: -16px;
  left: 50%;
  transform: translateX(-50%);
  width: 60px;
  height: 4px;
  background: var(--primary);
  border-radius: 2px;
}

.steps {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 24px;
}

.step {
  text-align: center;
  padding: 40px 24px;
  background: var(--bg-primary);
  border-radius: 24px;
  border: 1px solid var(--border-color);
  transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}

.step:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.1);
  border-color: var(--primary-light);
}

.step-icon {
  width: 72px;
  height: 72px;
  background: rgba(99, 102, 241, 0.08);
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 24px;
  color: var(--primary);
  transition: all 0.4s ease;
}

.step:hover .step-icon {
  background: var(--primary);
  color: white;
  transform: scale(1.1) rotate(4deg);
}

.step-svg {
  width: 32px;
  height: 32px;
}

.step h3 {
  margin-bottom: 12px;
  font-size: 1.25rem;
  font-weight: 800;
  color: var(--text-primary);
  letter-spacing: -0.5px;
}

.step p {
  color: var(--text-secondary);
  font-size: 0.95rem;
  line-height: 1.6;
  font-weight: 500;
}

.featured {
  padding: 100px 0;
  background: var(--bg-secondary);
}

.listings-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 32px;
}

/* Reveal Animations */
.reveal {
  opacity: 0;
  transform: translateY(30px);
  transition: all 1s cubic-bezier(0.4, 0, 0.2, 1);
}

.reveal-active {
  opacity: 1;
  transform: translateY(0);
}

@media (max-width: 1024px) {
  .steps {
    grid-template-columns: repeat(2, 1fr);
  }
  .listings-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .hero-content h1 {
    font-size: 2.5rem;
  }

  .hero-search {
    flex-direction: column;
    gap: 12px;
    padding: 12px;
    border-radius: 32px;
  }

  .hero-search input {
    width: 100%;
    padding: 12px;
    text-align: center;
  }

  .hero-search .btn {
    width: 100%;
  }

  .hero-stats {
    flex-wrap: wrap;
    gap: 24px;
  }

  .steps, .listings-grid {
    grid-template-columns: 1fr;
  }
}
</style>
