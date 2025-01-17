<template>
    <Snow />
    <div class="container-fluid d-flex align-items-center justify-content-center vh-100 bg-light">
        <div class="card shadow-lg" style="width: 22rem;">
            <div class="login-header">
                <h3 class="card-title text-white text-center m-0">
                    Login Form
                </h3>
            </div>
            <!-- Bind validation schema to Form component -->
            <Form :validation-schema="validationSchema" @submit="handleSubmit" class="p-4">

                <!-- Common fields -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <Field type="email" autocomplete="email" id="email" class="form-control mb-2"
                        placeholder="Enter your email" name="email" v-model="formData.email" />
                    <ErrorMessage class="text-danger" name="email" />
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <Field type="password" autocomplete="current-password" id="password" class="mb-2 form-control"
                        placeholder="Enter your password" name="password" v-model="formData.password" />
                    <ErrorMessage class="text-danger" name="password" />
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <div class="form-check">
                        <Field type="checkbox" id="remember-me" class="form-check-input" name="remember"
                            v-model="formData.remember" />
                        <label for="remember-me" class="form-check-label">Remember me</label>
                    </div>
                    <NuxtLink to="/reset" class="text-decoration-none">Forgot password?</NuxtLink>
                </div>

                <div v-if="loginError" class="text-danger mb-3">
                    {{ loginError }}
                </div>

                <div v-if="check" class="mt-2 d-flex justify-content-center align-items-center gap-2">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <p class="mt-2">Authenticating...</p>
                </div>

                <!-- Submit button -->
                <button type="submit" class="btn btn-primary w-100">
                    Login
                </button>

                <div class="text-center mt-3">
                    <small>
                        Don't have an account?
                        <NuxtLink to="/register" class="text-decoration-none">Sign Up</NuxtLink>
                    </small>
                </div>
            </Form>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import * as yup from 'yup';
import { useNuxtApp } from "#app";
import axios from 'axios';

const submitted = ref(false);
const loginError = ref('');
const router = useRouter();
const check = ref(false)

const validationSchema = yup.object({
    email: yup.string()
        .email('Invalid email address')
        .required('Email is required'),

    password: yup.string()
        .required('Password is required')
        .min(6, 'Password must be at least 6 characters')
});

// Form data
const formData = ref({
    name: '',
    email: '',
    password: '',
    remember: false,
});

// Handle form submission
const handleSubmit = async () => {
    check.value = true;
    loginError.value = '';
    try {
        const { $axios } = useNuxtApp();
        const response = await $axios.post('/login', {
            email: formData.value.email,
            password: formData.value.password,
        },
            {
                skipInterceptor: true,
            });
        setTimeout(() => {
            localStorage.setItem('token', response.data.token);
            localStorage.setItem('id', response.data.id);

            router.push('/home');
        }, 300);

    } catch (error) {
        if (error.response.status === 401) {
            loginError.value = 'Invalid email or password. Please try again.';
            check.value = false;

        } else {
            check.value = false;
            loginError.value = 'An error occurred. Please try again later.';
        }
        // console.error('Submission error:', error);
    }

};

onMounted(() => {
    const token = localStorage.getItem('token');

    if (token) {
        router.push('/home');
    }

})
</script>

<style scoped>
* {
    overflow: hidden;
}

.container-fluid {
    background: linear-gradient(135deg, #4158d0, #c850c0);
}

.card {
    border-radius: 15px;
}

.login-header {
    border-top-left-radius: inherit;
    border-top-right-radius: inherit;
    padding: 16px;
    background: linear-gradient(135deg, #4158d0, #c850c0);
}

.login-header h2 {
    margin: 0;
    font-weight: 700;
}

.card-title {
    font-weight: 600;
}

.btn-primary {
    padding: 8px 16px;
    background: linear-gradient(135deg, #4158d0, #c850c0);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #c850c0, #4158d0);
}

.text-primary {
    font-weight: 500;
}

a:hover {
    text-decoration: underline;
}

/* Bootstrap */
.form-check-input:focus {
    box-shadow: unset;
}
</style>