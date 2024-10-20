import axios from 'axios';
import {defineStore} from 'pinia';

export const useQueryStore = defineStore('query', () => {

    const search = async (query: any, url: string) => {
        const response = await axios.get(url, {
            params: {
                search: query,
            }
        });
        return response.data;
    };

    const last = async (slug: string) => {
        // Use params for where condition if needed
        const response = await axios.get(slug);
        return response.data;
    };

    const find = async (slug: string, payload: object) => {
        // Use params for where condition if needed
        const response = await axios.post(slug, {
            ...payload
        });
        return response.data;
    };

    // Added explicit types for 'route' and 'payload'
    const post = async (route: string, payload: object) => {
        const response = await axios.post(route, {
            ...payload
        });
        return response.data;
    };

    return {
        last,
        search,
        find,
        post
    };
});