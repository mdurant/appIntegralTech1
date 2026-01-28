import '../css/app.css';

// Inicializar Flatpickr y Select2 después de que el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    initializeDatePickers();
    initializeSelect2();
    initializeCookieConsent();
    
    // Reinicializar después de actualizaciones de Livewire
    if (typeof Livewire !== 'undefined') {
        Livewire.hook('morph.updated', ({ el }) => {
            initializeDatePickers(el);
            initializeSelect2(el);
        });
    }
});

function initializeDatePickers(container = document) {
    const dateInputs = container.querySelectorAll('.date-picker');
    dateInputs.forEach(function(input) {
        if (input._flatpickr) {
            return; // Ya está inicializado
        }
        
        if (typeof flatpickr !== 'undefined') {
            flatpickr(input, {
                locale: flatpickr.l10ns.es,
                dateFormat: 'd-m-Y',
                allowInput: true,
                clickOpens: true,
                animate: true,
                defaultDate: input.value || null,
            });
        }
    });
}

function initializeSelect2(container = document) {
    const selects = container.querySelectorAll('select.select2');
    selects.forEach(function(select) {
        if ($(select).hasClass('select2-hidden-accessible')) {
            return; // Ya está inicializado
        }
        
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $(select).select2({
                theme: 'default',
                width: '100%',
                language: {
                    noResults: function() {
                        return 'No se encontraron resultados';
                    },
                    searching: function() {
                        return 'Buscando...';
                    },
                },
            });
            
            // Manejar cambios de Livewire
            $(select).on('change', function() {
                const wireModel = select.getAttribute('wire:model');
                if (wireModel && typeof Livewire !== 'undefined') {
                    const component = Livewire.find(select.closest('[wire\\:id]')?.getAttribute('wire:id'));
                    if (component) {
                        component.set(wireModel, select.value);
                    }
                }
            });
        }
    });
}

/*
|--------------------------------------------------------------------------
| Cookie Consent Management
|--------------------------------------------------------------------------
*/

/**
 * Establece una cookie en el navegador
 */
function setCookie(name, value, days) {
    const expires = new Date();
    expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
    document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=/;SameSite=Lax;Secure`;
}

/**
 * Obtiene el valor de una cookie
 */
function getCookie(name) {
    const nameEQ = name + '=';
    const ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) === ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

/**
 * Elimina una cookie
 */
function deleteCookie(name) {
    document.cookie = `${name}=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/;`;
}

/**
 * Verifica si el usuario ya ha dado su consentimiento de cookies
 */
function hasCookieConsent() {
    return getCookie('cookie_consent_given') !== null;
}

/**
 * Guarda las preferencias de consentimiento de cookies
 */
function setCookieConsent(preferences) {
    // Marcar que el consentimiento fue dado
    setCookie('cookie_consent_given', 'true', 365);
    
    // Guardar las preferencias
    setCookie('cookie_preferences', JSON.stringify(preferences), 365);
    
    // Si marketing está habilitado, crear cookies de marketing
    if (preferences.marketing) {
        createMarketingCookies();
    } else {
        removeMarketingCookies();
    }
    
    // Si user_experience está habilitado, crear cookies de UX
    if (preferences.user_experience) {
        createUserExperienceCookies();
    } else {
        removeUserExperienceCookies();
    }
}

/**
 * Crea cookies de marketing
 */
function createMarketingCookies() {
    const visitorId = getCookie('marketing_visitor_id') || 'visitor_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    const sessionId = 'session_' + Date.now();
    const lastVisit = new Date().toISOString();
    
    setCookie('marketing_visitor_id', visitorId, 365);
    setCookie('marketing_session_id', sessionId, 1);
    setCookie('marketing_last_visit', lastVisit, 365);
    
    const marketingPreferences = {
        source: document.referrer || 'direct',
        user_agent: navigator.userAgent.substring(0, 100),
        accepted_at: new Date().toISOString(),
    };
    
    setCookie('marketing_preferences', JSON.stringify(marketingPreferences), 365);
}

/**
 * Crea cookies de experiencia de usuario
 */
function createUserExperienceCookies() {
    const language = document.documentElement.lang || 'es';
    const theme = 'light'; // Por defecto
    
    setCookie('ux_language', language, 365);
    setCookie('ux_theme', theme, 365);
    
    const uxPreferences = {
        language: language,
        theme: theme,
        last_activity: new Date().toISOString(),
    };
    
    setCookie('ux_preferences', JSON.stringify(uxPreferences), 365);
    setCookie('ux_last_activity', new Date().toISOString(), 30);
}

/**
 * Elimina todas las cookies de marketing
 */
function removeMarketingCookies() {
    deleteCookie('marketing_visitor_id');
    deleteCookie('marketing_session_id');
    deleteCookie('marketing_last_visit');
    deleteCookie('marketing_preferences');
}

/**
 * Elimina todas las cookies de experiencia de usuario
 */
function removeUserExperienceCookies() {
    deleteCookie('ux_language');
    deleteCookie('ux_theme');
    deleteCookie('ux_preferences');
    deleteCookie('ux_last_activity');
}

/**
 * Inicializa el sistema de consentimiento de cookies
 */
function initializeCookieConsent() {
    // Escuchar eventos de Livewire para guardar preferencias
    if (typeof Livewire !== 'undefined') {
        Livewire.on('cookie-consent-saved', (data) => {
            if (data && data.length > 0) {
                const preferences = data[0];
                setCookieConsent(preferences);
            }
        });
    }
    
    // También escuchar eventos personalizados del DOM
    document.addEventListener('cookie-consent-saved', (event) => {
        if (event.detail) {
            setCookieConsent(event.detail);
        }
    });
}
