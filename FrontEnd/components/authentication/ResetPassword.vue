<template>
    <Snow />
    <div class="container-fluid d-flex align-items-center justify-content-center vh-100 bg-light">
        <div class="card shadow-lg" style="width: 22rem;">
            <div class="login-header">
                <p class="card-title text-white m-0">
                    Enter your user account's verified email address and we will send you a password reset link.
                </p>
            </div>
            <!-- Bind validation schema to Form component -->
            <Form :validation-schema="validationSchema" @submit="handleSubmit" class="p-4">
                <!-- Common fields -->
                <div class="mb-3">
                    <Field type="email" id="email" class="form-control" placeholder="Enter your email" name="email"
                        v-model="email" />
                    <ErrorMessage class="text-danger" name="email" />
                </div>
                <div v-if="emailError" class="text-danger mb-3">
                    {{ emailError }}
                </div>
                <!-- Submit button -->
                <button type="submit" class="btn btn-primary w-100">
                    Send Password Reset Email
                </button>
            </Form>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import * as yup from 'yup';
import { useRouter } from 'vue-router';
// import axios from 'axios';
import Swal from "sweetalert2";

const email = ref('');
const emailError = ref('');
const router = useRouter();

const validationSchema = yup.object({
    email: yup.string().email('Invalid email address').required('Email is required'),
});

// Handle form submission
const handleSubmit = async (values) => {
    try {
        const url = 'http://127.0.0.1:8000/api/password/email';
        const method = 'POST';
        const response = await $fetch(url, {
            method: method,
            body: {
                email: email.value,
            },
        });
        Swal.fire({
            icon: 'success',
            title: 'Password Reset Link Send', 
            didClose: () => {
                router.push('/');
            }
        });
    } catch (error) {
        if (error.response && error.response.status === 400) {
            emailError.value = error.response._data.error;
        } else {
            alert('An error occurred. Please try again later.');
        }
        console.error('Submission error:', error);
    }
};

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

.card {
    width: 30em !important;
}
</style>