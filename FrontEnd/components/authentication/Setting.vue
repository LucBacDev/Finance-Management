<template>
    <main class="content px-3 py-2">
    <div class="container-fluid">
      <!-- Table Element -->
      <div class="card border-0">
        <div class="container">
        <Form :validation-schema="validationSchema" @submit="update" class="p-4">

            <!-- Common fields -->
            <div class="mb-3">
                <label for="name" class="form-label">User Name:</label>
                <Field type="text" autocomplete="name" id="name" class="form-control mb-2" placeholder="Enter your name"
                    name="name" v-model="name" />
                <ErrorMessage class="text-danger" name="name" />
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Enter Your New Password</label>
                <Field type="password" autocomplete="new-password" id="password" class="mb-2 form-control"
                    placeholder="Enter your password" name="password" v-model="password" />
                <ErrorMessage class="text-danger" name="password" />
            </div>
            <!-- Submit button -->
            <div class="d-flex justify-content-center">
                <button type="submit" class="btn btn-primary text-center">
                    Update
                </button>
            </div>
        </Form>
    </div>
      </div>
    </div>
  </main>
   
</template>

<script setup>
import { useNuxtApp } from "#app";
import Swal from "sweetalert2";
import * as yup from 'yup';
import { useRouter } from "vue-router";

const router = useRouter();
const name = ref('');
const password = ref('');
const validationSchema = yup.object({

    password: yup.string()
        .required('Password is required')
        .min(6, 'Password must be at least 6 characters'),

    name: yup.string()
        .max(25, 'Name must be under 25 characters')
});

const update = async () => {
    const { $axios } = useNuxtApp();
    try {
        const response = await $axios.post('/update', {
            name: name.value,
            password: password.value,
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${localStorage.getItem('token')}`,
            },
        },
            {
                skipInterceptor: true,
            });
        Swal.fire({
            icon: 'success',
            title: 'Update Successful',
            didClose: () => {
                router.push('/');
            }
        });
    } catch (error) {
        console.error('Submission error:', error);
    }
}
</script>