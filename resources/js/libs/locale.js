import {usePage} from "@inertiajs/vue3";

export default {
    install(app, options) {

        app.config.globalProperties.__ = (key, attributes = {}) => {
            let message = key.split('.').reduce((o, i) => {
                if (o && o[i] !== undefined && o[i] !== null) {
                    return o[i];
                }
                return key; // fallback to the key if translation is not found
            }, usePage().props.translations)

            if (typeof attributes === 'object' && attributes !== null) {
                // Replace placeholders with attributes
                Object.keys(attributes).forEach((attribute) => {
                    const placeholder = `:${attribute}`;
                    message = message.replace(new RegExp(placeholder, 'g'), attributes[attribute]);
                });
            }

            return message;
        }

        app.provide('locale', app.config.globalProperties.__)
    }
}