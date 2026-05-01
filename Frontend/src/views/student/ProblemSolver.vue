<template>
  <div class="problem-solver-page page">
    <div class="container">
      <div class="hero-section text-center mb-4">
        <h1>{{ $t('problemSolver.title') }}</h1>
        <p class="text-secondary lead">{{ $t('problemSolver.description') }}</p>
      </div>

      <div class="quiz-container card">
        <div class="card-header">
          <h3>{{ $t('problemSolver.quiz.title') }}</h3>
        </div>
        <div class="card-body">
          <div v-if="!results" class="quiz-form">
            <div class="form-group slide-up">
              <label class="form-label">{{ $t('problemSolver.quiz.q1') }}</label>
              <div class="options-grid">
                <button 
                  v-for="opt in ['a1', 'a2', 'a3']" 
                  :key="opt"
                  class="btn btn-secondary"
                  :class="{ active: answers.q1 === opt }"
                  @click="answers.q1 = opt"
                >
                  {{ $t(`problemSolver.quiz.q1${opt}`) }}
                </button>
              </div>
            </div>

            <div class="form-group slide-up mt-3">
              <label class="form-label">{{ $t('problemSolver.quiz.q2') }}</label>
              <div class="options-grid">
                <button 
                  v-for="opt in ['a1', 'a2', 'a3']" 
                  :key="opt"
                  class="btn btn-secondary"
                  :class="{ active: answers.q2 === opt }"
                  @click="answers.q2 = opt"
                >
                  {{ $t(`problemSolver.quiz.q2${opt}`) }}
                </button>
              </div>
            </div>

            <div class="form-group slide-up mt-3">
              <label class="form-label">{{ $t('problemSolver.quiz.q3') }}</label>
              <div class="options-grid">
                <button 
                  v-for="opt in ['a1', 'a2', 'a3']" 
                  :key="opt"
                  class="btn btn-secondary"
                  :class="{ active: answers.q3 === opt }"
                  @click="answers.q3 = opt"
                >
                  {{ $t(`problemSolver.quiz.q3${opt}`) }}
                </button>
              </div>
            </div>

            <div class="text-center mt-4">
              <button 
                class="btn btn-primary btn-lg" 
                :disabled="!isQuizComplete"
                @click="calculateResult"
              >
                {{ $t('problemSolver.quiz.submit') }}
              </button>
            </div>
          </div>

          <div v-else class="results-section text-center fade-in">
            <div class="result-icon mb-3">
              <span v-if="score > 80">🌟</span>
              <span v-else-if="score > 50">👍</span>
              <span v-else>🧐</span>
            </div>
            <h2>{{ $t('problemSolver.results.title') }}: {{ score }}%</h2>
            <p class="mt-2 lead">
              {{ resultMessage }}
            </p>
            
            <!-- Recommendations Section -->
            <div class="recommendations-container mt-4">
              <h3 class="mb-3">{{ $t('problemSolver.results.recommendations') }}</h3>
              <div v-if="loadingListings" class="loader-container">
                <div class="loader"></div>
              </div>
              <div v-else-if="recommendedListings.length > 0" class="listings-grid grid-3">
                <ListingCard 
                  v-for="listing in recommendedListings" 
                  :key="listing.id" 
                  :listing="listing"
                  :is-recommended="true"
                />
              </div>
              <p v-else class="text-secondary">{{ $t('search.noResults') }}</p>
            </div>

            <button class="btn btn-secondary mt-4" @click="resetQuiz">
              {{ $t('common.back') }}
            </button>
          </div>
        </div>
      </div>

      <!-- New Section: AI Conflict Resolver -->
      <div class="conflict-resolver card glass-card mb-4 slide-up" style="animation-delay: 0.1s">
        <div class="card-header border-0 pb-0">
          <h3>{{ $t('problemSolver.resolver.title') }}</h3>
          <p class="text-secondary small">{{ $t('problemSolver.resolver.desc') }}</p>
        </div>
        <div class="card-body">
          <div class="form-group mb-3">
            <textarea 
              v-model="studentProblem" 
              class="form-control glass-input" 
              rows="3" 
              :placeholder="$t('problemSolver.resolver.placeholder')"
            ></textarea>
          </div>
          <button 
            class="btn btn-primary premium-btn w-100" 
            :disabled="!studentProblem.trim() || isSolving"
            @click="solveProblem"
          >
            <span v-if="!isSolving">{{ $t('problemSolver.resolver.action') }}</span>
            <span v-else class="spinner-border spinner-border-sm"></span>
          </button>

          <div v-if="solution" class="solution-display mt-4 p-4 glass-card fade-in">
            <h5 class="text-primary mb-2">💡 {{ $t('problemSolver.resolver.solution_title') }}</h5>
            <p class="mb-0">{{ solution }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { useListingsStore } from '@/stores/listings'
import ListingCard from '@/components/ListingCard.vue'
import api from '@/services/api'

const { t } = useI18n()
const listingsStore = useListingsStore()

const answers = ref({
  q1: null,
  q2: null,
  q3: null
})

const results = ref(false)
const score = ref(0)
const loadingListings = ref(false)

// Conflict Resolver Data
const studentProblem = ref('')
const solution = ref('')
const isSolving = ref(false)

const solveProblem = async () => {
  if (!studentProblem.value.trim() || isSolving.value) return
  
  isSolving.value = true
  solution.value = ''
  try {
    const response = await api.post('/ai/resolve', {
      problem: studentProblem.value
    })
    solution.value = response.data.solution
  } catch (err) {
    console.error('Solve problem error:', err)
  } finally {
    isSolving.value = false
  }
}

const isQuizComplete = computed(() => {
  return answers.value.q1 && answers.value.q2 && answers.value.q3
})

const recommendedListings = computed(() => {
  if (score.value >= 80) {
    // Recommend studios or apartments (premium)
    return listingsStore.listings.filter(l => l.type === 'studio' || l.type === 'apartment').slice(0, 3)
  } else if (score.value >= 50) {
    // Recommend shared or room (standard)
    return listingsStore.listings.filter(l => l.type === 'shared' || l.type === 'apartment').slice(0, 3)
  } else {
    // Recommend private rooms (more personal space)
    return listingsStore.listings.filter(l => l.type === 'room' || l.type === 'studio').slice(0, 3)
  }
})

const calculateResult = async () => {
  let s = 0
  if (answers.value.q1 === 'a1') s += 40
  else if (answers.value.q1 === 'a2') s += 20
  
  if (answers.value.q2 === 'a1') s += 30
  else if (answers.value.q2 === 'a2') s += 30
  else s += 10

  if (answers.value.q3 === 'a1') s += 30
  else if (answers.value.q3 === 'a2') s += 20
  else s += 10

  score.value = s
  results.value = true
  
  loadingListings.value = true
  try {
    if (listingsStore.listings.length === 0) {
      await listingsStore.fetchListings()
    }
  } finally {
    loadingListings.value = false
  }
}

const resultMessage = computed(() => {
  if (score.value > 80) return t('problemSolver.results.perfect')
  if (score.value > 50) return t('problemSolver.results.good')
  return t('problemSolver.results.challenging')
})

const resetQuiz = () => {
  answers.value = { q1: null, q2: null, q3: null }
  results.value = false
}
</script>

<style scoped>
.problem-solver-page {
  background: var(--bg-secondary);
  min-height: 100vh;
}

.hero-section h1 {
  background: linear-gradient(135deg, var(--primary), var(--primary-dark));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  font-size: 3rem;
  margin-bottom: var(--spacing-sm);
}

.quiz-container {
  max-width: 800px;
  margin: 0 auto;
  border: none;
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.3);
}

.options-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: var(--spacing-md);
  margin-top: var(--spacing-sm);
}

.btn-secondary.active {
  background: var(--primary);
  color: white;
  border-color: var(--primary);
}

.result-icon {
  font-size: 4rem;
}

.loader-container {
  display: flex;
  justify-content: center;
  padding: var(--spacing-lg) 0;
}

.tip-card {
  text-align: center;
  transition: transform var(--transition-normal);
}

.tip-card:hover {
  transform: translateY(-10px);
}

.tip-icon {
  font-size: 2.5rem;
  margin-bottom: var(--spacing-sm);
}

.slide-up {
  animation: slideUp 0.5s ease-out;
}

.fade-in {
  animation: fadeIn 0.5s ease-in;
}

@keyframes slideUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.glass-card {
  background: rgba(255, 255, 255, 0.7) !important;
  backdrop-filter: blur(20px) !important;
  border: 1px solid rgba(255, 255, 255, 0.5) !important;
  box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.1) !important;
}

.premium-btn {
  background: linear-gradient(135deg, var(--primary), #9333ea) !important;
  border: none !important;
  box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
  color: white !important;
  font-weight: 700;
  transition: all 0.3s ease;
}

.premium-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
}

.glass-input {
  background: rgba(255, 255, 255, 0.5) !important;
  border: 1px solid rgba(255, 255, 255, 0.3) !important;
  border-radius: 12px;
}

.glass-input:focus {
  background: white !important;
  border-color: var(--primary) !important;
  box-shadow: 0 0 15px rgba(99, 102, 241, 0.2);
}

.solution-display {
  border-left: 4px solid var(--primary);
  text-align: left;
}

@media (max-width: 768px) {
  .hero-section h1 {
    font-size: 2.2rem;
  }

  .options-grid {
    grid-template-columns: 1fr;
    gap: 8px;
  }

  .quiz-container {
    border-radius: 0;
    margin: 0 -1rem;
  }

  .premium-btn {
    padding: 12px;
  }

  .solution-display {
    padding: 1.5rem !important;
  }
}

@media (max-width: 480px) {
  .hero-section h1 {
    font-size: 1.8rem;
  }
}
</style>
