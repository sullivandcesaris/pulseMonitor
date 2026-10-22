import { computed, ref } from 'vue';

export type Language = 'en' | 'pt';

const language = ref<Language>('en');
let initialized = false;

export function useLanguage() {
    if (!initialized && typeof window !== 'undefined') {
        const stored = window.localStorage.getItem('pulsemonitor-language');
        if (stored === 'en' || stored === 'pt') language.value = stored;
        initialized = true;
    }

    const isPortuguese = computed(() => language.value === 'pt');
    const setLanguage = (value: Language) => {
        language.value = value;
        if (typeof window !== 'undefined') window.localStorage.setItem('pulsemonitor-language', value);
    };

    return { language, isPortuguese, setLanguage };
}
