<template>
  <div class="mental-health-page page">
    <div class="container">
      <!-- Hero Section -->
      <div class="hero-section text-center mb-5">
        <h1>{{ $t('mentalHealth.title') }}</h1>
        <p class="text-secondary lead">{{ $t('mentalHealth.description') }}</p>
      </div>

      <div class="grid grid-2 gap-lg">
        <!-- Left Column: Mood & Quiz -->
        <div class="assessment-container">
          <!-- Well-being Index Gauge (NEW) -->
          <div v-if="results" class="card mb-4 gauge-section glass-card slide-up">
            <div class="card-header border-0 pb-0">
              <h3 class="mb-0">{{ $t('mentalHealth.amazing.gauge.title') }}</h3>
            </div>
            <div class="card-body text-center pt-0">
              <div class="gauge-wrapper">
                <div class="gauge-body">
                  <div class="gauge-fill" :style="{ width: quizScore + '%' }"></div>
                  <div class="gauge-marker" :style="{ left: quizScore + '%' }"></div>
                </div>
                <div class="gauge-labels d-flex justify-content-between mt-2">
                  <span class="small text-secondary">{{ $t('mentalHealth.amazing.gauge.low') }}</span>
                  <span class="font-weight-bold" :class="riskColor">{{ quizScore }}%</span>
                  <span class="small text-secondary">{{ $t('mentalHealth.amazing.gauge.high') }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Mood Tracker -->
          <div class="card mb-4 mood-section glass-card slide-up">
            <div class="card-header border-0">
              <h3>{{ $t('mentalHealth.mood.title') }}</h3>
            </div>
            <div class="card-body">
              <div class="mood-selector">
                <button 
                  v-for="(emoji, mood) in moodMap" 
                  :key="mood"
                  class="mood-btn"
                  :class="{ active: selectedMood === mood }"
                  @click="selectedMood = mood"
                >
                  <span class="mood-emoji">{{ emoji }}</span>
                  <span class="mood-label">{{ $t(`mentalHealth.mood.${mood}`) }}</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Daily Glow Affirmation (NEW) -->
          <div class="card mb-4 glow-card glass-card slide-up" style="animation-delay: 0.05s">
            <div class="card-body text-center p-4">
              <div class="glow-icon mb-2">✨</div>
              <h4>{{ $t('mentalHealth.amazing.glow.title') }}</h4>
              <p class="text-secondary small mb-3">{{ $t('mentalHealth.amazing.glow.desc') }}</p>
              
              <div class="affirmation-box" :class="{ revealed: isGlowRevealed }">
                <p v-if="isGlowRevealed" class="affirmation-text fade-in">{{ currentAffirmation }}</p>
                <button v-else class="btn btn-primary premium-btn" @click="revealGlow">
                  {{ $t('mentalHealth.amazing.glow.reveal') }}
                </button>
              </div>
            </div>
          </div>

          <!-- Mood History Visualizer (NEW) -->
          <div class="card mb-4 history-section glass-card slide-up" style="animation-delay: 0.1s">
            <div class="card-header border-0">
              <h3>{{ $t('mentalHealth.amazing.history.title') }}</h3>
            </div>
            <div class="card-body">
              <div class="history-chart">
                <div v-for="(val, i) in historyValues" :key="i" class="history-bar-wrapper">
                  <div class="history-bar" :style="{ height: val + '%' }"></div>
                  <div class="day-info mt-2">
                    <span class="day-mood">{{ historyMoods[i] }}</span>
                    <span class="day-label">{{ days[i] }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Assessment Quiz -->
          <div class="card quiz-section glass-card slide-up" style="animation-delay: 0.2s">
            <div class="card-header border-0">
              <h3>{{ $t('mentalHealth.quiz.title') }}</h3>
            </div>
            <div class="card-body">
              <div v-if="!results" class="quiz-form">
                <!-- Questions... -->
                <div v-for="q in ['q1', 'q2', 'q3']" :key="q" class="form-group mb-4">
                  <label class="form-label">{{ $t(`mentalHealth.quiz.${q}`) }}</label>
                  <div class="options-grid">
                    <button 
                      v-for="opt in ['a1', 'a2', 'a3']" 
                      :key="opt"
                      class="btn btn-secondary glass-btn"
                      :class="{ active: answers[q] === opt }"
                      @click="answers[q] = opt"
                    >
                      {{ $t(`mentalHealth.quiz.${q}${opt}`) }}
                    </button>
                  </div>
                </div>

                <div class="text-center mt-4">
                  <button 
                    class="btn btn-primary btn-lg w-100 premium-btn" 
                    :disabled="!isQuizComplete"
                    @click="analyzeWellbeing"
                  >
                    {{ $t('mentalHealth.quiz.submit') }}
                  </button>
                </div>
              </div>

              <!-- Results Section -->
              <div v-else class="results-display fade-in text-center">
                <div class="result-icon mb-3">
                  <span v-if="riskLevel === 'low'">🌟</span>
                  <span v-else-if="riskLevel === 'moderate'">👍</span>
                  <span v-else>🤝</span>
                </div>
                <h3>{{ $t('mentalHealth.results.title') }}</h3>
                <p class="lead mt-3">{{ $t(`mentalHealth.results.${riskLevel}`) }}</p>

                <!-- Recommendations -->
                <div class="recommendations-row mt-5 grid grid-3 gap-md">
                  <!-- Social -->
                  <div class="card recommendation-card glass-card">
                    <div class="card-body">
                      <div class="icon-circle mb-3">🤝</div>
                      <h5>{{ $t('mentalHealth.recommendations.social.title') }}</h5>
                      <p class="small text-secondary">{{ $t('mentalHealth.recommendations.social.desc') }}</p>
                      <div class="mt-2 text-primary small">{{ $t('mentalHealth.recommendations.social.apps') }}</div>
                    </div>
                  </div>
                  <!-- Activities -->
                  <div class="card recommendation-card glass-card">
                    <div class="card-body">
                      <div class="icon-circle mb-3">🏃</div>
                      <h5>{{ $t('mentalHealth.recommendations.activities.title') }}</h5>
                      <p class="small text-secondary">{{ $t('mentalHealth.recommendations.activities.desc') }}</p>
                      <ul class="activity-chips mt-2">
                        <li v-for="item in $tm('mentalHealth.recommendations.activities.items')" :key="item" class="badge">
                          {{ item }}
                        </li>
                      </ul>
                    </div>
                  </div>
                  <!-- Academic -->
                  <div class="card recommendation-card glass-card">
                    <div class="card-body">
                      <div class="icon-circle mb-3">📘</div>
                      <h5>{{ $t('mentalHealth.recommendations.academic.title') }}</h5>
                      <p class="small text-secondary">{{ $t('mentalHealth.recommendations.academic.desc') }}</p>
                      <p class="mt-2 text-primary small">{{ $t('mentalHealth.recommendations.academic.math') }}</p>
                    </div>
                  </div>
                  <!-- Sleep Hygiene (NEW) -->
                  <div class="card recommendation-card glass-card">
                    <div class="card-body">
                      <div class="icon-circle mb-3">💤</div>
                      <h5>{{ $t('mentalHealth.amazing.suggestions.sleep.title') }}</h5>
                      <p class="small text-secondary">{{ $t('mentalHealth.amazing.suggestions.sleep.desc') }}</p>
                    </div>
                  </div>
                  <!-- Brain Nutrition (NEW) -->
                  <div class="card recommendation-card glass-card">
                    <div class="card-body">
                      <div class="icon-circle mb-3">🍎</div>
                      <h5>{{ $t('mentalHealth.amazing.suggestions.nutrition.title') }}</h5>
                      <p class="small text-secondary">{{ $t('mentalHealth.amazing.suggestions.nutrition.desc') }}</p>
                    </div>
                  </div>
                  <!-- Wellness Workshops -->
                  <div class="card recommendation-card glass-card">
                    <div class="card-body">
                      <div class="icon-circle mb-3">🧘</div>
                      <h5>{{ $t('mentalHealth.amazing.suggestions.workshops.title') }}</h5>
                      <p class="small text-secondary">{{ $t('mentalHealth.amazing.suggestions.workshops.desc') }}</p>
                    </div>
                  </div>
                  <!-- Digital Detox (NEW) -->
                  <div class="card recommendation-card glass-card">
                    <div class="card-body">
                      <div class="icon-circle mb-3">📵</div>
                      <h5>{{ $t('mentalHealth.amazing.suggestions.detox.title') }}</h5>
                      <p class="small text-secondary">{{ $t('mentalHealth.amazing.suggestions.detox.desc') }}</p>
                    </div>
                  </div>
                  <!-- Creative Flow (NEW) -->
                  <div class="card recommendation-card glass-card">
                    <div class="card-body">
                      <div class="icon-circle mb-3">🎨</div>
                      <h5>{{ $t('mentalHealth.amazing.suggestions.creative.title') }}</h5>
                      <p class="small text-secondary">{{ $t('mentalHealth.amazing.suggestions.creative.desc') }}</p>
                    </div>
                  </div>
                  <!-- Physical Spark (NEW) -->
                  <div class="card recommendation-card glass-card">
                    <div class="card-body">
                      <div class="icon-circle mb-3">⚡</div>
                      <h5>{{ $t('mentalHealth.amazing.suggestions.physical.title') }}</h5>
                      <p class="small text-secondary">{{ $t('mentalHealth.amazing.suggestions.physical.desc') }}</p>
                    </div>
                  </div>
                </div>

                <!-- Daily Wellness Challenge (NEW) -->
                <div class="mt-5 p-4 glass-card border-primary-light">
                  <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="text-start">
                      <h4 class="mb-1">🏆 {{ $t('mentalHealth.amazing.suggestions.challenge.title') }}</h4>
                      <p class="mb-0 text-secondary small">{{ $t('mentalHealth.amazing.suggestions.challenge.desc') }}</p>
                    </div>
                    <button class="btn btn-primary premium-btn px-4" @click="generateChallenge">
                      {{ challengeText || 'Launch Challenge' }}
                    </button>
                  </div>
                </div>

                <button class="btn btn-secondary mt-5 glass-btn" @click="resetQuiz">
                  {{ $t('common.back') }}
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column: Features & Labs -->
        <div class="resources-container">
          <!-- SOS Button (NEW) -->
          <button class="sos-banner mb-4 bounce-in" @click="showSos = true">
            <span class="sos-icon">🆘</span>
            <div class="sos-text">
              <strong>{{ $t('mentalHealth.amazing.sos.btn') }}</strong>
              <span class="small d-block">Click for immediate support</span>
            </div>
          </button>


          <!-- Pulse Breathing Tool (NEW IDEA) -->
          <div class="card mb-4 pulse-card glass-card slide-up" style="animation-delay: 0.35s">
            <div class="card-body text-center p-4">
              <h4>{{ $t('mentalHealth.amazing.pulse.title') }}</h4>
              <p class="text-secondary small mb-4">{{ $t('mentalHealth.amazing.pulse.desc') }}</p>
              
              <div class="pulse-container mb-4">
                <div class="pulse-bubble" :class="breathingState">
                  <div class="pulse-inner"></div>
                </div>
                <div class="pulse-instruction fade-in" :key="breathingState">
                  {{ $t(`mentalHealth.amazing.pulse.${breathingState || 'stop'}`) }}
                </div>
              </div>

              <button 
                class="btn w-100" 
                :class="isBreathing ? 'btn-secondary glass-btn' : 'btn-primary premium-btn'"
                @click="toggleBreathing"
              >
                {{ isBreathing ? $t('mentalHealth.amazing.pulse.stop') : $t('mentalHealth.amazing.pulse.action') }}
              </button>
            </div>
          </div>

          <!-- Resources Hub -->
          <div class="card resources-card glass-card slide-up" style="animation-delay: 0.4s">
            <div class="card-header border-0">
              <h3>{{ $t('mentalHealth.resources.title') }}</h3>
            </div>
            <div class="card-body">
              <div class="resource-list">
                <div v-for="(res, key) in ['counseling', 'hotline', 'groups']" :key="key" class="resource-item">
                  <div class="resource-icon">{{ key === 0 ? '🏥' : key === 1 ? '📞' : '👥' }}</div>
                  <div class="resource-info">
                    <h5>{{ $t(`mentalHealth.resources.${res}`) }}</h5>
                    <p class="text-secondary small">Verified student support service.</p>
                  </div>
                </div>
              </div>
              <div class="disclaimer mt-4 p-3 glass-disclaimer rounded">
                <p class="text-secondary small mb-0">
                  ⚠️ {{ $t('mentalHealth.resources.disclaimer') }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- SOS Modal (Simulated) -->
      <div v-if="showSos" class="sos-modal-backdrop" @click.self="showSos = false">
        <div class="sos-modal glass-card p-5 text-center fade-in">
          <div class="sos-modal-icon mb-4">🆘</div>
          <h1>{{ $t('mentalHealth.amazing.sos.title') }}</h1>
          <p class="lead mb-4">{{ $t('mentalHealth.amazing.sos.desc') }}</p>
          <div class="emergency-numbers mb-4">
            <div class="number-card mb-2">Campus Police: 911</div>
            <div class="number-card">Hotline: 1234 567 890</div>
          </div>
          <button class="btn btn-secondary glass-btn" @click="showSos = false">Close</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, nextTick, onUnmounted } from 'vue'
import { useI18n } from 'vue-i18n'

const { t, tm } = useI18n()

const days = computed(() => {
  const localizedDays = tm('mentalHealth.amazing.history.days')
  // Ensure we return an array of strings even if tm returns something else
  return Array.isArray(localizedDays) ? localizedDays : []
})

const moodMap = {
  excellent: '🤩',
  good: '😊',
  neutral: '😐',
  stressed: '😫',
  sad: '😢'
}

// Daily Glow Affirmation Logic
const isGlowRevealed = ref(false)
const currentAffirmation = ref('')

const revealGlow = () => {
  const affirmations = tm('mentalHealth.amazing.glow.list')
  if (Array.isArray(affirmations) && affirmations.length > 0) {
    const randomIndex = Math.floor(Math.random() * affirmations.length)
    currentAffirmation.value = affirmations[randomIndex]
    isGlowRevealed.value = true
  }
}

const historyValues = [40, 60, 80, 50, 90, 70, 85]
const historyMoods = ['😐', '😊', '🤩', '😐', '🤩', '😊', '🤩']


const selectedMood = ref(null)
const results = ref(false)
const riskLevel = ref('low')
const quizScore = ref(0)
const showSos = ref(false)

const answers = ref({
  q1: null,
  q2: null,
  q3: null
})

const isQuizComplete = computed(() => {
  return answers.value.q1 && answers.value.q2 && answers.value.q3
})

const riskColor = computed(() => {
  if (quizScore.value >= 70) return 'text-danger'
  if (quizScore.value >= 40) return 'text-warning'
  return 'text-success'
})

const analyzeWellbeing = () => {
  let score = 0
  
  if (answers.value.q1 === 'a3') score += 40
  else if (answers.value.q1 === 'a2') score += 20
  
  if (answers.value.q2 === 'a3') score += 40
  else if (answers.value.q2 === 'a2') score += 20
  
  if (answers.value.q3 === 'a3') score += 20
  else if (answers.value.q3 === 'a2') score += 10

  quizScore.value = 100 - score // Inverse for "Well-being Index"
  
  if (score >= 70) riskLevel.value = 'high'
  else if (score >= 40) riskLevel.value = 'moderate'
  else riskLevel.value = 'low'

  results.value = true
}

const challengeText = ref('')
const isBreathing = ref(false)
const breathingState = ref('') // 'in', 'hold', 'out'
let breathingInterval = null

const toggleBreathing = () => {
  if (isBreathing.value) {
    stopBreathing()
  } else {
    startBreathing()
  }
}

const startBreathing = () => {
  isBreathing.value = true
  runBreathingCycle()
  breathingInterval = setInterval(runBreathingCycle, 12000) // 4+4+4 cycle
}

const stopBreathing = () => {
  isBreathing.value = false
  breathingState.value = ''
  if (breathingInterval) clearInterval(breathingInterval)
}

const runBreathingCycle = () => {
  breathingState.value = 'in'
  setTimeout(() => {
    if (isBreathing.value) breathingState.value = 'hold'
  }, 4000)
  setTimeout(() => {
    if (isBreathing.value) breathingState.value = 'out'
  }, 8000)
}

const challenges = [
  'Walk 5,000 steps today 🚶',
  'Drink 8 glasses of water 💧',
  'Meditate for 5 minutes 🧘',
  'No screen time 30 mins before bed 📵',
  'Write down 3 things you are grateful for ✍️',
  'Call a friend just to say hi ☎️',
  'Try a new healthy recipe 🥗'
]

const generateChallenge = () => {
  const randomIndex = Math.floor(Math.random() * challenges.length)
  challengeText.value = challenges[randomIndex]
}

const resetQuiz = () => {
  results.value = false
  answers.value = { q1: null, q2: null, q3: null }
  quizScore.value = 0
  challengeText.value = ''
}
</script>

<script>
export default {
  name: 'MentalHealth'
}
</script>

<style scoped>
.mental-health-page {
  background: var(--bg-secondary);
  min-height: 100vh;
  padding-bottom: var(--spacing-xl);
}

.hero-section h1 {
  background: linear-gradient(135deg, #4f46e5, #9333ea, #ec4899);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  font-size: 4rem;
  font-weight: 900;
  letter-spacing: -1px;
}

/* Glassmorphism Design System */
.glass-card {
  background: rgba(255, 255, 255, 0.7) !important;
  backdrop-filter: blur(20px) !important;
  border: 1px solid rgba(255, 255, 255, 0.5) !important;
  box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.1) !important;
  border-radius: var(--radius-xl) !important;
  overflow: hidden;
}

.glass-btn {
  background: rgba(255, 255, 255, 0.5) !important;
  backdrop-filter: blur(5px);
  border: 1px solid rgba(255, 255, 255, 0.3);
  color: var(--text-primary);
}

.premium-btn {
  background: linear-gradient(135deg, var(--primary), #9333ea) !important;
  border: none !important;
  box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
  color: white !important;
  font-weight: 700;
}

.premium-btn:hover {
  transform: scale(1.02);
  box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
}


.glass-input {
  background: rgba(0, 0, 0, 0.2) !important;
  border: 1px solid rgba(255, 255, 255, 0.1) !important;
  color: #ffffff !important;
  padding: 12px 20px;
  border-radius: 12px;
  font-size: 1rem;
}

.glass-input::placeholder {
  color: rgba(255, 255, 255, 0.4) !important;
}

.glass-input:focus {
  background: rgba(0, 0, 0, 0.3) !important;
  border-color: var(--primary) !important;
  box-shadow: 0 0 15px rgba(99, 102, 241, 0.3);
  outline: none;
}


/* Gauge Styles */
.gauge-body {
  height: 24px;
  background: #e5e7eb;
  border-radius: 12px;
  position: relative;
  overflow: hidden;
}

.gauge-fill {
  height: 100%;
  background: linear-gradient(90deg, #10b981, #f59e0b, #ef4444);
  transition: width 1s ease-in-out;
}

.gauge-marker {
  position: absolute;
  top: 0;
  width: 2px;
  height: 100%;
  background: white;
  box-shadow: 0 0 5px rgba(0,0,0,0.5);
  transition: left 1s ease-in-out;
}

/* History Chart */
.history-chart {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  height: 150px;
  padding-top: 20px;
}

.history-bar-wrapper {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}

.history-bar {
  width: 60%;
  background: linear-gradient(to top, var(--primary), #9333ea);
  border-radius: 4px;
  transition: all 1s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  box-shadow: 0 4px 10px rgba(99, 102, 241, 0.2);
}

.history-bar:hover {
  transform: scaleY(1.05);
  filter: brightness(1.2);
  box-shadow: 0 0 15px rgba(99, 102, 241, 0.5);
}

.day-info {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
}

.day-mood {
  font-size: 1.2rem;
}

.day-label {
  font-size: 0.65rem;
  font-weight: 700;
  color: var(--text-secondary);
  white-space: nowrap;
}

/* SOS Section */
.sos-banner {
  width: 100%;
  background: linear-gradient(135deg, #ef4444, #991b1b);
  border: none;
  border-radius: var(--radius-lg);
  padding: 20px;
  color: white;
  display: flex;
  align-items: center;
  gap: 15px;
  text-align: start;
  cursor: pointer;
  transition: transform 0.3s;
}

.sos-banner:hover {
  transform: translateX(10px);
}

.sos-icon {
  font-size: 2.5rem;
}


/* Modal */
.sos-modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.8);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.sos-modal {
  max-width: 500px;
  width: 90%;
}

.sos-modal-icon {
  font-size: 5rem;
}

.number-card {
  padding: 15px;
  background: rgba(239, 68, 68, 0.1);
  border: 1px solid #ef4444;
  border-radius: var(--radius-md);
  color: #ef4444;
  font-size: 1.25rem;
  font-weight: 900;
}

/* Daily Glow Styles */
.glow-card {
  border: 1px solid rgba(255, 215, 0, 0.3);
}

.glow-icon {
  font-size: 2.5rem;
  animation: float 3s ease-in-out infinite;
}

.affirmation-box {
  min-height: 80px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 10px;
}

.affirmation-text {
  font-size: 1.1rem;
  font-weight: 600;
  color: var(--primary);
  font-style: italic;
}

@keyframes float {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-10px); }
}

/* Pulse Breathing Tool Styles */
.pulse-container {
  height: 250px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  position: relative;
}

.pulse-bubble {
  width: 80px;
  height: 80px;
  background: radial-gradient(circle, var(--primary), #9333ea);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform 4s linear, box-shadow 4s linear;
  box-shadow: 0 0 20px rgba(99, 102, 241, 0.3);
}

.pulse-inner {
  width: 70%;
  height: 70%;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  backdrop-filter: blur(5px);
}

.pulse-bubble.in {
  transform: scale(2.5);
  box-shadow: 0 0 50px rgba(99, 102, 241, 0.6);
}

.pulse-bubble.hold {
  transform: scale(2.5);
  box-shadow: 0 0 80px rgba(99, 102, 241, 0.8);
  animation: pulseGlow 2s infinite alternate;
}

.pulse-bubble.out {
  transform: scale(1);
  box-shadow: 0 0 20px rgba(99, 102, 241, 0.3);
}

@keyframes pulseGlow {
  from { box-shadow: 0 0 50px rgba(99, 102, 241, 0.6); }
  to { box-shadow: 0 0 100px rgba(99, 102, 241, 0.9); }
}

.pulse-instruction {
  margin-top: 40px;
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--primary);
  height: 2rem;
}

/* Utility styles from MentalHealth.vue continued */
.mood-selector {
  display: flex;
  justify-content: space-between;
  gap: var(--spacing-md);
  padding: var(--spacing-sm) 0;
}

.mood-btn {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  background: var(--bg-primary);
  border: 1px solid var(--border-color);
  padding: var(--spacing-md);
  border-radius: var(--radius-lg);
  cursor: pointer;
  transition: all var(--transition-normal);
}

.mood-btn:hover {
  transform: translateY(-5px);
  border-color: var(--primary);
  background: white;
}

.mood-btn.active {
  background: var(--primary);
  border-color: var(--primary);
  color: white;
}

.mood-emoji {
  font-size: 2rem;
}

.mood-label {
  font-size: 0.85rem;
  font-weight: 600;
}

.options-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: var(--spacing-md);
}

.icon-circle {
  width: 40px;
  height: 40px;
  background: var(--bg-secondary);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
}

.btn-secondary {
  background: var(--bg-primary);
  border: 1px solid var(--border-color);
  color: var(--text-primary);
}

.btn-secondary:hover {
  border-color: var(--primary);
}

.btn-secondary.active {
  background: var(--primary);
  color: white;
  border-color: var(--primary);
}

.result-icon {
  font-size: 4rem;
}

.resource-item {
  display: flex;
  align-items: flex-start;
  gap: var(--spacing-md);
  padding: var(--spacing-md);
  border-bottom: 1px solid var(--border-color);
}

.resource-item:last-child {
  border-bottom: none;
}

.resource-icon {
  font-size: 2rem;
  background: var(--bg-secondary);
  width: 50px;
  height: 50px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: var(--radius-md);
}

.resource-info h5 {
  margin-bottom: 4px;
}

.glass-disclaimer {
  background: rgba(99, 102, 241, 0.05);
  border: 1px solid rgba(99, 102, 241, 0.2);
}

/* Animations */
.slide-up {
  animation: slideUp 0.6s ease-out forwards;
  opacity: 0;
}

.fade-in {
  animation: fadeIn 0.5s ease-in forwards;
}

.bounce-in {
  animation: bounceIn 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
}

@keyframes slideUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes bounceIn {
  from { opacity: 0; transform: scale(0.3); }
  to { opacity: 1; transform: scale(1); }
}

.recommendations-row {
  text-align: start;
}

.recommendation-card {
  border: 1px solid var(--border-color);
  background: white;
  transition: transform 0.3s ease;
}

.recommendation-card:hover {
  transform: translateY(-5px);
  border-color: var(--primary);
}

.activity-chips {
  list-style: none;
  padding: 0;
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.activity-chips .badge {
  background: var(--bg-secondary);
  color: var(--primary);
  padding: 4px 10px;
  border-radius: var(--radius-full);
  font-size: 0.75rem;
  border: 1px solid rgba(99, 102, 241, 0.2);
}

@media (max-width: 992px) {
  .grid-2 {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 600px) {
  .hero-section h1 {
    font-size: 1.8rem;
  }
  .mood-selector {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
  }
  .mood-btn {
    padding: var(--spacing-sm);
  }
  .mood-emoji {
    font-size: 1.5rem;
  }
  .options-grid {
    grid-template-columns: 1fr;
  }
  .pulse-bubble {
    width: 60px;
    height: 60px;
  }
  .pulse-bubble.in, .pulse-bubble.hold {
    transform: scale(2);
  }
  .pulse-instruction {
    font-size: 1.25rem;
  }
}
</style>
