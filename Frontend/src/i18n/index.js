import { createI18n } from 'vue-i18n'
import en from './en.json'
import ar from './ar.json'

const savedLanguage = localStorage.getItem('language') || 'ar'

const i18n = createI18n({
    legacy: false,
    locale: savedLanguage,
    fallbackLocale: 'en',
    messages: { en, ar },
})

export function setLanguage(lang) {
    i18n.global.locale.value = lang
    localStorage.setItem('language', lang)
    document.documentElement.setAttribute('dir', lang === 'ar' ? 'rtl' : 'ltr')
    document.documentElement.setAttribute('lang', lang)
}

// Initialize direction
document.documentElement.setAttribute('dir', savedLanguage === 'ar' ? 'rtl' : 'ltr')
document.documentElement.setAttribute('lang', savedLanguage)

export default i18n
