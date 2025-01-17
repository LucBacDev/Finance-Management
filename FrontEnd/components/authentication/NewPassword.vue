<template>
    <Snow />
    <div class="container-fluid d-flex align-items-center justify-content-center vh-100 bg-light">
        <div class="card shadow-lg" style="width: 22rem;">
            <div class="login-header">
                <h3 class="card-title text-white text-center m-0">
                    Reset Password
                </h3>
            </div>
            <!-- Bind validation schema to Form component -->
            <Form :validation-schema="validationSchema" @submit="handleSubmit" class="p-4">

                <!-- New Password field -->
                <div class="mb-3">
                    <label for="password" class="form-label">New Password</label>
                    <Field type="password" autocomplete="new-password" id="password" class="form-control" placeholder="Enter new password"
                        name="password" v-model="formData.password" />
                    <ErrorMessage class="text-danger" name="password" />
                </div>

                <!-- Confirm Password field -->
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <Field type="password" autocomplete="new-password" id="confirm_password" class="form-control" placeholder="Confirm new password"
                        name="confirm_password" v-model="formData.confirm_password" />
                    <ErrorMessage class="text-danger" name="confirm_password" />
                </div>

                <div v-if="resetError" class="text-danger mb-3">
                    {{ resetError }}
                </div>

                <!-- Submit button -->
                <button type="submit" class="btn btn-primary w-100">
                    Reset Password
                </button>
            </Form>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import * as yup from 'yup';
import Swal from "sweetalert2";
// import axios from 'axios';
import { useNuxtApp } from "#app";

const resetError = ref('');
const router = useRouter();
const token = ref('');

// Validation Schema using Yup
const validationSchema = yup.object({
    password: yup.string()
        .min(6, 'Password must be at least 6 characters')
        .required('Password is required'),

    confirm_password: yup.string()
        .oneOf([yup.ref('password'), null], 'Passwords must match')
        .required('Confirm Password is required'),
});

const formData = ref({
    password: '',
    confirm_password: '',
});

const handleSubmit = async () => {
    const { $axios } = useNuxtApp(); 
    resetError.value = '';
    try {
        const response = await $axios.post('reset-password', {
            password: formData.value.password,
            password_confirmation: formData.value.confirm_password,
            token: token.value
        });
        Swal.fire({
            icon: 'success',
            title: 'Password Reset Successful',
            didClose: () => {
                router.push('/');
            }
        });
        // router.push('/');
    } catch (error) {
        resetError.value = 'An error occurred while resetting the password. Please try again later.';
        console.error('Submission error:', error);
    }
};

onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search);
    token.value = urlParams.get('token');
});
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
