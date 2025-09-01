import { Ziggy } from './ziggy.js';

export default {
    install: (app) => {
        // Добавляем Ziggy как глобальное свойство
        app.config.globalProperties.$route = (name, params, absolute, config = Ziggy) => {
            return window.route(name, params, absolute, config);
        };
        
        // Добавляем Ziggy как глобальное свойство
        app.config.globalProperties.$ziggy = Ziggy;
        
        // Добавляем в provide/inject для Composition API
        app.provide('ziggy', Ziggy);
        app.provide('route', (name, params, absolute, config = Ziggy) => {
            return window.route(name, params, absolute, config);
        });
    }
};
