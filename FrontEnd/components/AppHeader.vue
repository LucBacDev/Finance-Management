<template>
    <nav class="navbar navbar-expand px-3 border-bottom">
        <button class="btn" id="sidebar-toggle" type="button">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div>
            <input type="checkbox" class="checkbox theme-toggle" id="chk" />
            <label class="label" for="chk">
                <i class="fas fa-moon"></i>
                <i class="fas fa-sun"></i>
                <div class="ball"></div>
            </label>
        </div>
        <div class="navbar-collapse navbar">
            <ul class="navbar-nav">
                <li class="nav-item dropdown d-flex align-items-center gap-2">
                    <a href="#" data-bs-toggle="dropdown" class="nav-icon pe-md-0">
                        <img src="data:image/jpeg;base64,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"
                            class="avatar img-fluid rounded" alt="">
                        <span class="ms-2" style="color: black;">{{ userName }}</span>
                    </a>

                    <div class="dropdown-menu dropdown-menu-end">
                        <nuxt-link class="dropdown-item" to="/setting">Setting</nuxt-link>
                        <button @click="logout" href="#" class="dropdown-item">Logout</button>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</template>

<script setup>
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { initializeSidebarAndTheme } from '~/assets/themeToggle';
import { useNuxtApp } from "#app";

const userName = ref('')
const route = useRouter();
const { $axios } = useNuxtApp();

const logout = async () => {
    try {
        const response = await $axios.post('/logout', {
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${localStorage.getItem('token')}`,
            },
        },
            // {
            //     skipInterceptor: true,
            // }
        );
        if (response.status === 200 || response.status === 401) {
            localStorage.removeItem('token');
            localStorage.removeItem('id');

            route.push('/');
        }
    } catch (error) {
        console.error('Error during logout:', error);
    }
};

const getUser = async () => {
    try {
        const response = await $axios.get('/users', {
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${localStorage.getItem('token')}`,
            },
        },
            // {
            //     skipInterceptor: true,
            // }
        );
        userName.value = response.data.data.name
    } catch (error) {
        console.error('Error during logout:', error);
    }
};
onMounted(() => {
    initializeSidebarAndTheme();
    getUser();
});

</script>

<style scoped>
.checkbox {
    opacity: 0;
    position: absolute;
}

.label {
    background-color: #111;
    border-radius: 50px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 5px;
    position: relative;
    height: 26px;
    width: 50px;
    transform: scale(0.8);
}

.label .ball {
    background-color: #fff;
    border-radius: 50%;
    position: absolute;
    top: 2px;
    left: 2px;
    height: 22px;
    width: 22px;
    transform: translateX(0px);
    transition: transform 0.2s linear;
}

.checkbox:checked+.label .ball {
    transform: translateX(24px);
}

.fa-moon {
    color: #f1c40f;
}

.fa-sun {
    color: #f39c12;
}
</style>