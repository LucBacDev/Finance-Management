<template>
    <Snow />
    <div class="container-fluid d-flex align-items-center justify-content-center vh-100 bg-light">
        <div class="card shadow-lg" style="width: 22rem;">
            <!-- <div class="modal " tabindex="-1" v-if="submitted" style="display: block;" aria-hidden="true">
                <div class="modal-dialog ">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-success">Success</h5>
                            <button type="button" class="btn-close" @click="submitted = false"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Sign Up Success</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success btn-secondary"
                                @click="submitted = false">Close</button>
                        </div>
                    </div>
                </div>
            </div> -->
            <div class="login-header">
                <h3 class="card-title text-white text-center m-0">
                    Signup Form
                </h3>
            </div>
            <!-- Bind validation schema to Form component -->
            <Form :validation-schema="validationSchema" @submit="handleSubmit" class="p-4">

                <!-- Signup fields -->
                <div class="mb-3">
                    <label for="name" class="form-label">Your Name</label>
                    <Field type="text" id="name" class="form-control" placeholder="Enter your name" name="name"
                        v-model="formData.name" />
                    <ErrorMessage class="text-danger" name="name" />
                </div>

                <!-- Common fields -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <Field type="email" id="email" class="form-control" placeholder="Enter your email" name="email"
                        v-model="formData.email" />
                    <ErrorMessage class="text-danger" name="email" />
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <Field type="password" id="password" class="form-control" placeholder="Enter your password"
                        name="password" v-model="formData.password" />
                    <ErrorMessage class="text-danger" name="password" />
                </div>

                <div class="mb-3">
                    <label for="passwordConfirmed" class="form-label">Password Confirmation</label>
                    <Field autocomplete="new-password" type="password" id="passwordConfirmed" class="form-control"
                        placeholder="Verify your password" name="passwordConfirmed"
                        v-model="formData.passwordConfirmed" />
                    <ErrorMessage class="text-danger" name="passwordConfirmed" />
                </div>

                <div v-if="signUpError" class="text-danger mb-3">
                    {{ signUpError }}
                </div>

                <div v-if="check" class="mt-2 d-flex justify-content-center align-items-center gap-2">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <p class="mt-2">Registering...</p>
                </div>

                <!-- Submit button -->
                <button type="submit" class="btn btn-primary w-100">
                    Sign Up
                </button>

                <div class="text-center mt-3">
                    <small>
                        Already have an account?
                        <NuxtLink to="/" class="text-decoration-none">Login</NuxtLink>
                    </small>
                </div>
            </Form>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import * as yup from 'yup';
import Swal from "sweetalert2";
import { useRouter } from 'vue-router';
import { useNuxtApp } from "#app";

const router = useRouter();
const submitted = ref(false);
const signUpError = ref('');
const check = ref(false);
const validationSchema = yup.object({
    email: yup.string()
        .email('Invalid email address')
        .required('Email is required'),

    password: yup.string()
        .required('Password is required')
        .min(6, 'Password must be at least 6 characters'),

    name: yup.string()
        .max(25, 'Name must be under 25 characters')
        .required('Name is required'),

    passwordConfirmed: yup.string()
        .oneOf([yup.ref('password'), null], 'Passwords must match')
        .required('Password confirmation is required'),
});

// Form data
const formData = ref({
    name: '',
    email: '',
    password: '',
    passwordConfirmed: '',
    remember: false,
});

// Handle form submission
const handleSubmit = async (values) => {
    check.value = true;

    try {
        const { $axios } = useNuxtApp();

        const response = await $axios.post('/register', {
            name: formData.value.name,
            email: formData.value.email,
            password: formData.value.password,
            password_confirmation: formData.value.passwordConfirmed
        });

        check.value = true;

        setTimeout(() => {
            check.value = false;
            Swal.fire({
                icon: 'success',
                title: 'Registration Successful',
                didClose: () => {
                    router.push('/');
                }
            });
        }, 300);

    } catch (error) {
        if (error.response) {
            check.value = false;
            signUpError.value = error.response.data.errors.email[0];
        } else {
            check.value = false;
            signUpError.value = 'An error occurred. Please try again later.';
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
</style>