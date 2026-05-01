import { createRouter, createWebHistory } from 'vue-router'

// Public pages
import Home from '@/views/Home.vue'
import Login from '@/views/auth/Login.vue'
import Register from '@/views/auth/Register.vue'
import Search from '@/views/Search.vue'
import ListingDetails from '@/views/ListingDetails.vue'

// Student pages
import Favorites from '@/views/student/Favorites.vue'
import AiAssistant from '@/views/student/AiAssistant.vue'
import ProblemSolver from '@/views/student/ProblemSolver.vue'
import MentalHealth from '@/views/student/MentalHealth.vue'

// Owner pages
import OwnerDashboard from '@/views/owner/Dashboard.vue'
import AddListing from '@/views/owner/AddListing.vue'
import MyListings from '@/views/owner/MyListings.vue'

// Shared pages
import Chats from '@/views/Chats.vue'
import AdminDashboard from '@/views/AdminDashboard.vue'

const routes = [
    // Public
    { path: '/', name: 'home', component: Home },
    { path: '/login', name: 'login', component: Login, meta: { guest: true } },
    { path: '/register', name: 'register', component: Register, meta: { guest: true } },
    { path: '/search', name: 'search', component: Search },
    { path: '/listings/:id', name: 'listing-details', component: ListingDetails },

    // Student
    {
        path: '/favorites',
        name: 'favorites',
        component: Favorites,
        meta: { requiresAuth: true, roles: ['student'] }
    },
    {
        path: '/ai-assistant',
        name: 'ai-assistant',
        component: AiAssistant,
        meta: { requiresAuth: true }
    },
    {
        path: '/problem-solver',
        name: 'problem-solver',
        component: ProblemSolver,
        meta: { requiresAuth: true, roles: ['student'] }
    },
    {
        path: '/mental-health',
        name: 'mental-health',
        component: MentalHealth,
        meta: { requiresAuth: true, roles: ['student'] }
    },

    // Owner/Broker
    {
        path: '/dashboard',
        name: 'dashboard',
        component: OwnerDashboard,
        meta: { requiresAuth: true, roles: ['owner', 'broker'] }
    },
    {
        path: '/listings/add',
        name: 'add-listing',
        component: AddListing,
        meta: { requiresAuth: true, roles: ['owner', 'broker'] }
    },
    {
        path: '/my-listings',
        name: 'my-listings',
        component: MyListings,
        meta: { requiresAuth: true, roles: ['owner', 'broker'] }
    },

    // Shared
    {
        path: '/chats',
        name: 'chats',
        component: Chats,
        meta: { requiresAuth: true }
    },
    {
        path: '/admin/metrics',
        name: 'admin-metrics',
        component: AdminDashboard,
        meta: { requiresAuth: true }
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

// Navigation guards
router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token')
    const user = JSON.parse(localStorage.getItem('user') || 'null')

    // Check if route requires auth
    if (to.meta.requiresAuth && !token) {
        return next({ name: 'login', query: { redirect: to.fullPath } })
    }

    // Check if route is for guests only
    if (to.meta.guest && token) {
        return next({ name: 'home' })
    }

    // Check role-based access
    if (to.meta.roles && user) {
        const userRole = user.role?.name
        if (!to.meta.roles.includes(userRole)) {
            return next({ name: 'home' })
        }
    }

    next()
})

export default router
