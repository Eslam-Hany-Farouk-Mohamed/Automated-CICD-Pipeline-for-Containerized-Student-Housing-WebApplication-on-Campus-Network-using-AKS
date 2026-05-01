<template>
  <nav class="navbar" :class="{ 'navbar-scrolled': isScrolled || !isHomePage, 'navbar-transparent': !isScrolled && isHomePage }">
    <div class="container navbar-content">
      <!-- Logo (Left) -->
      <router-link to="/" class="navbar-brand">
        <div class="brand-wrapper">
          <div class="brand-box">
             🏘️
          </div>
          <span class="logo-text">Sakani<span class="text-primary-sub">.</span></span>
        </div>
      </router-link>

      <!-- Desktop Navigation (Center) -->
      <div class="navbar-menu" :class="{ active: isMenuOpen }">
        <router-link to="/" class="nav-link">{{ $t('nav.home') }}</router-link>
        <router-link to="/search" class="nav-link">{{ $t('nav.search') }}</router-link>
        
        <template v-if="authStore.isAuthenticated">
          <!-- Student Links -->
          <template v-if="authStore.isStudent">
            <router-link to="/ai-assistant" class="nav-link">{{ $t('nav.aiAssistant') }}</router-link>
            <router-link to="/problem-solver" class="nav-link">{{ $t('nav.problemSolver') }}</router-link>
            <router-link to="/mental-health" class="nav-link">{{ $t('nav.mentalHealth') }}</router-link>
          </template>

          <!-- Owner/Broker Links -->
          <template v-if="authStore.canManageListings">
            <router-link to="/dashboard" class="nav-link">{{ $t('nav.dashboard') }}</router-link>
            <router-link to="/my-listings" class="nav-link">{{ $t('nav.myListings') }}</router-link>
            <router-link to="/listings/add" class="nav-link nav-highlight">{{ $t('nav.addListing') }}</router-link>
          </template>
        </template>
      </div>

      <!-- Right Side (Actions) -->
      <div class="navbar-actions">
        <!-- Dark Mode Toggle -->
        <button class="action-icon-btn dark-mode-toggle" @click="toggleDarkMode" :title="$t('nav.toggleDarkMode')">
          <span class="icon">{{ isDarkMode ? '☀️' : '🌙' }}</span>
        </button>

        <!-- Message Icon -->
        <template v-if="authStore.isAuthenticated">
          <router-link to="/chats" class="nav-link action-icon-btn chats-icon" :title="$t('nav.chats')">
            <span class="icon">💬</span>
          </router-link>
        </template>

        <!-- Language Switcher (Icon) -->
        <button class="lang-switch icon-btn" @click="toggleLanguage" :title="currentLang === 'ar' ? 'English' : 'العربية'">
          <span class="icon">🌐</span>
          <span class="lang-text">{{ currentLang === 'ar' ? 'EN' : 'ع' }}</span>
        </button>

        <!-- Favorites Icon (behind Lang) -->
        <router-link v-if="authStore.isAuthenticated && authStore.isStudent" to="/favorites" class="nav-link action-icon-btn favorites-icon" :title="$t('nav.favorites')">
          <span class="icon">❤️</span>
        </router-link>

        <!-- User Profile / Auth Icon -->
        <template v-if="authStore.isAuthenticated">
          <div class="user-menu">
            <button class="user-btn" @click="toggleUserMenu" :title="authStore.user?.name">
              <span class="user-avatar">👤</span>
            </button>
            <div class="user-dropdown" v-if="isUserMenuOpen">
              <div class="dropdown-header px-3 py-2 border-bottom">
                <span class="small text-secondary">{{ authStore.user?.name }}</span>
              </div>
              <router-link to="/admin/metrics" class="dropdown-item" @click="isUserMenuOpen = false">
                📊 {{ $t('admin.viewMetrics') }}
              </router-link>
              <button @click="logout" class="dropdown-item danger">
                {{ $t('nav.logout') }}
              </button>
            </div>
          </div>
        </template>
        <template v-else>
          <router-link to="/login" class="nav-link profile-icon-link" :title="$t('nav.login')">
            <span class="profile-icon">👤</span>
          </router-link>
        </template>

        <!-- Mobile Menu Toggle -->
        <button class="menu-toggle" @click="toggleMenu">
          <span></span>
          <span></span>
          <span></span>
        </button>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '@/stores/auth'
import { setLanguage } from '@/i18n'

const router = useRouter()
const route = useRoute()
const { locale } = useI18n()
const authStore = useAuthStore()

const isMenuOpen = ref(false)
const isUserMenuOpen = ref(false)
const isScrolled = ref(false)
const isDarkMode = ref(false)

const currentLang = computed(() => locale.value)
const userInitial = computed(() => authStore.user?.name?.charAt(0)?.toUpperCase() || 'U')
const isHomePage = computed(() => route.path === '/')

const handleScroll = () => {
  isScrolled.value = window.scrollY > 50
}

const toggleDarkMode = () => {
  isDarkMode.value = !isDarkMode.value
  document.documentElement.setAttribute('data-theme', isDarkMode.value ? 'dark' : 'light')
  localStorage.setItem('theme', isDarkMode.value ? 'dark' : 'light')
}

const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value
}

const toggleUserMenu = () => {
  isUserMenuOpen.value = !isUserMenuOpen.value
}

const toggleLanguage = () => {
  const newLang = currentLang.value === 'ar' ? 'en' : 'ar'
  setLanguage(newLang)
  locale.value = newLang
}

const logout = async () => {
  await authStore.logout()
  isUserMenuOpen.value = false
  router.push('/')
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll)
  const savedTheme = localStorage.getItem('theme')
  if (savedTheme === 'dark') {
    isDarkMode.value = true
    document.documentElement.setAttribute('data-theme', 'dark')
  }
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<style scoped>
.navbar {
  background: var(--bg-primary);
  border-bottom: 1px solid var(--border-color);
  position: fixed;
  left: 0;
  right: 0;
  top: 0;
  z-index: 1000;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.navbar-transparent {
  background: transparent;
  border-bottom-color: transparent;
  backdrop-filter: none;
}

.navbar-transparent .nav-link {
  color: rgba(255, 255, 255, 0.9);
}

.navbar-transparent .action-icon-btn,
.navbar-transparent .lang-switch.icon-btn,
.navbar-transparent .user-avatar,
.navbar-transparent .menu-toggle span {
  color: white;
  border-color: rgba(255, 255, 255, 0.2);
  background: rgba(255, 255, 255, 0.1);
}

.navbar-transparent .logo-text {
  background: linear-gradient(135deg, #fff 0%, #e2e8f0 100%);
  -webkit-background-clip: text;
}

.navbar-scrolled {
  background: var(--glass-bg);
  backdrop-filter: blur(12px);
  border-bottom: 1px solid var(--glass-border);
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
}

.navbar-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 80px;
  transition: height 0.4s ease;
}

.navbar-scrolled .navbar-content {
  height: 70px;
}

.navbar-brand {
  text-decoration: none;
  flex: 1.2;
}

.brand-wrapper {
  display: flex;
  align-items: center;
  gap: 14px;
}

.brand-box {
  width: 50px;
  height: 50px;
  background: linear-gradient(135deg, var(--primary), var(--primary-dark));
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 14px; /* Professional Squircle */
  font-size: 1.6rem;
  box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.brand-box::after {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, transparent 100%);
}

.brand-wrapper:hover .brand-box {
  transform: scale(1.05) rotate(-5deg);
  box-shadow: 0 6px 16px rgba(99, 102, 241, 0.4);
}

.logo-text {
  font-size: 2rem;
  font-weight: 900;
  letter-spacing: -0.8px;
  background: linear-gradient(135deg, var(--gray-900) 0%, var(--gray-700) 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  position: relative;
}

[data-theme="dark"] .logo-text {
  background: linear-gradient(135deg, #fff 0%, #cbd5e1 100%);
  -webkit-background-clip: text;
}

.text-primary-sub {
  -webkit-text-fill-color: var(--primary);
}

.navbar-menu {
  display: flex;
  align-items: center;
  gap: 8px; /* Refined spacing */
  flex: 2;
  justify-content: center;
}

.nav-link {
  padding: 8px 16px;
  color: var(--text-secondary);
  font-weight: 600;
  font-size: 0.95rem;
  border-radius: 12px;
  transition: all var(--transition-fast);
}

.nav-link:hover,
.nav-link.router-link-active {
  color: var(--primary);
  background: rgba(99, 102, 241, 0.06);
}

.navbar-actions {
  display: flex;
  align-items: center;
  gap: 12px; /* Perfect spacing between action items */
  flex: 1;
  justify-content: flex-end;
}

/* Unified Professional Squircle Shape for ALL Action Buttons */
.action-icon-btn, 
.lang-switch.icon-btn, 
.user-avatar {
  width: 42px;
  height: 42px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(255, 255, 255, 0.05);
  backdrop-filter: blur(8px);
  border: 1px solid var(--border-color);
  border-radius: 14px; /* Pro Squircle */
  font-size: 1.2rem;
  color: var(--text-primary);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 2px 4px rgba(0,0,0,0.02);
  cursor: pointer;
  padding: 0;
}

.lang-switch.icon-btn {
  padding: 0 12px;
  width: auto; /* Lang needs width for text */
  min-width: 42px;
  gap: 6px;
}

.action-icon-btn:hover,
.lang-switch.icon-btn:hover,
.user-btn:hover .user-avatar {
  background: white;
  border-color: var(--primary);
  color: var(--primary);
  transform: translateY(-3px);
  box-shadow: 0 8px 15px rgba(99, 102, 241, 0.1);
}

.favorites-icon .icon {
  color: #f43f5e;
}

.lang-text {
  font-size: 0.8rem;
  font-weight: 700;
}

.user-menu {
  position: relative;
}

.user-btn {
  padding: 0;
  background: none;
  border: none;
  cursor: pointer;
}

.action-icon-btn {
  font-size: 1.1rem;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(255, 255, 255, 0.03);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 12px;
  width: 38px;
  height: 38px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.action-icon-btn:hover {
  background: var(--primary);
  border-color: var(--primary-light);
  color: white !important;
  transform: translateY(-3px) scale(1.05);
}

.favorites-icon .icon {
  color: #f43f5e;
}

.user-dropdown {
  position: absolute;
  top: 100%;
  right: 0;
  min-width: 180px;
  background: var(--bg-primary);
  border: 1px solid var(--border-color);
  border-radius: var(--radius-md);
  box-shadow: var(--shadow-lg);
  margin-top: var(--spacing-sm);
  overflow: hidden;
}

[dir="rtl"] .user-dropdown {
  right: auto;
  left: 0;
}

.dropdown-item {
  display: block;
  width: 100%;
  padding: var(--spacing-sm) var(--spacing-md);
  text-align: start;
  background: none;
  border: none;
  cursor: pointer;
  transition: background var(--transition-fast);
}

.dropdown-item:hover {
  background: var(--gray-100);
}

.dropdown-item.danger {
  color: var(--danger);
}

.menu-toggle {
  display: none;
  flex-direction: column;
  gap: 4px;
  padding: var(--spacing-sm);
  background: none;
  border: none;
  cursor: pointer;
}

.menu-toggle span {
  width: 24px;
  height: 2px;
  background: var(--text-primary);
  border-radius: 2px;
}

@media (max-width: 768px) {
  .navbar-content {
    height: 60px;
  }

  .navbar-brand {
    font-size: 1.25rem;
  }

  .navbar-menu {
    display: none;
    position: absolute;
    top: 60px;
    left: 0;
    right: 0;
    background: var(--bg-primary);
    flex-direction: column;
    padding: var(--spacing-md);
    border-bottom: 1px solid var(--border-color);
    box-shadow: var(--shadow-lg);
    z-index: 99;
  }

  .navbar-menu.active {
    display: flex;
  }

  .nav-link {
    width: 100%;
    padding: var(--spacing-md);
    text-align: center;
  }

  .navbar-actions {
    gap: var(--spacing-xs);
  }

  .action-icon-btn, .lang-switch.icon-btn, .user-avatar {
    width: 34px;
    height: 34px;
    font-size: 1rem;
  }

  .lang-text {
    display: none;
  }

  .menu-toggle {
    display: flex;
    order: -1; /* Toggle on the left for LTR, right for RTL if handled by flex-direction */
  }
}

[dir="rtl"] .menu-toggle {
  order: 10;
}
</style>
