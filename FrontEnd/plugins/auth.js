import { onBeforeMount } from 'vue';
import { jwtDecode } from "jwt-decode";
import { useRouter } from 'vue-router';
export default defineNuxtPlugin((nuxtApp) => {
    const decodedToken = ref(null);
    const router = useRouter();
    if (import.meta.client) {
        const token = localStorage.getItem('token');
        if (!token) {
            decodedToken.value = 'No token available';
            return;
        }

        try {
            decodedToken.value = jwtDecode(token);

            const currentTime = Date.now() / 1000;
            if (decodedToken.value.exp < currentTime) {
                console.log('Token has expired');

                localStorage.removeItem('token');
                localStorage.removeItem('id');

                router.push('/');
            } else {
                console.log('Token is valid');
            }
        } catch (error) {
            decodedToken.value = 'Invalid token';
        }
    }

});
