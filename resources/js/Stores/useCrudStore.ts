import axios from 'axios';
import {defineStore} from 'pinia';
export const useCrudStore = defineStore('crud', () => {

    let headers = {
        "Accept" : "application/json"
    };
    const post = async(route:string,payload:object) => {
        const response = await axios.post(route ,{
            ...payload
        },{
            headers:{
              ...headers
            }
        });
        return response.data;
    }
    const get = async(route:string,params:object) => {
        const response = await axios.get(route ,{
            headers:{
                ...headers
            },
            params:{
                ...params
            }

        },);
        return response.data;
    }

    const del = async(route:string) => {
        const response = await axios.delete(route ,{
            headers:{
                ...headers
            }
        });
        return response.data;
    }





    return {
        get,
        del,
        post

    };
});
