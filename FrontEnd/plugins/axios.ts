import axios from 'axios';
import { useRouter } from 'vue-router';

export default defineNuxtPlugin((nuxtApp) => {
  const config = useRuntimeConfig();
  const instance = axios.create({
    baseURL: config.public.baseUrl,
    headers: {
      'Content-Type': 'application/json',
    },
  });
  
  instance.interceptors.request.use((config) => {
    const token = localStorage.getItem('token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  });

  // Optional: Add interceptors if needed
  instance.interceptors.request.use((request) => {
    // console.log('Starting Request', request);
    return request;
  });

  instance.interceptors.response.use(
    (response) => response,
    (error) => {
      if (error.config && error.config.skipInterceptor) {
        return Promise.reject(error);
      }
      if (error.status === 401) {
        localStorage.removeItem('token');
        localStorage.removeItem('id');
        return navigateTo('/');
      }

      return Promise.reject(error);
    }
  );


  // Make Axios instance available globally
  return {
    provide: {
      axios: instance,
    },
  };
});
