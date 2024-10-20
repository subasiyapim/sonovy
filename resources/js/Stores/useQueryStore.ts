import axios from 'axios';
import {defineStore} from 'pinia';
export const useQueryStore = defineStore('query', () => {

    const search = async (query: any, url: string) => {
        const response = await axios.get(url, {
            params: {
                search: query,
            }
        })
        return response.data;
    };

    const last = async ( slug: string) => {
        //params where için kullanılacak
        const response = await axios.get(slug)
        return response.data;
    };

    const find = async (slug: string,payload:object) => {
        //params where için kullanılacak
        const response = await axios.post(slug ,{
            ...payload
        })
        return response.data;
    };

    const post = async(route,payload) => {
        const response = await axios.post(route ,{
            ...payload
        });
        return response.data;
    }






    return {
        last,
        search,
        find,
        post

    };
});
