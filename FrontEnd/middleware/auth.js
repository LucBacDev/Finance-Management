import { useRouter } from "vue-router";

export default defineNuxtRouteMiddleware((to, from) => {
    const router = useRouter();
        
    if (import.meta.client) {
        const token = localStorage.getItem('token');
        const publicRoutes = ['/register', '/newpassword', '/reset'];

        if (!token && !publicRoutes.includes(to.path)) {

            return navigateTo('/');
        }
    }
});
