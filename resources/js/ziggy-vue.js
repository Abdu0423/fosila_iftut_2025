import { Ziggy } from './ziggy.js';

// Создаем функцию route для window
function createRoute(name, params, absolute, config = Ziggy) {
    if (typeof name === 'undefined') {
        return config.url;
    }
    
    const route = config.routes[name];
    if (!route) {
        throw new Error(`Route [${name}] not found.`);
    }
    
    let url = route.uri;
    
    // Заменяем параметры в URL
    if (params) {
        Object.keys(params).forEach(key => {
            url = url.replace(`{${key}}`, params[key]);
        });
    }
    
    // Удаляем оставшиеся неиспользованные параметры
    url = url.replace(/\{[^}]+\}/g, '');
    
    // Убираем двойные слеши
    url = url.replace(/\/+/g, '/');
    
    // Добавляем базовый URL если absolute
    if (absolute) {
        url = config.url + '/' + url;
    }
    
    return url;
}

// Добавляем в window для глобального доступа
if (typeof window !== 'undefined') {
    window.route = createRoute;
    window.Ziggy = Ziggy;
}

export default {
    install: (app) => {
        // Добавляем Ziggy как глобальное свойство
        app.config.globalProperties.$route = createRoute;
        
        // Добавляем Ziggy как глобальное свойство
        app.config.globalProperties.$ziggy = Ziggy;
        
        // Добавляем в provide/inject для Composition API
        app.provide('ziggy', Ziggy);
        app.provide('route', createRoute);
        
        // Добавляем глобальную функцию route для использования в template
        app.config.globalProperties.route = createRoute;
    }
};
