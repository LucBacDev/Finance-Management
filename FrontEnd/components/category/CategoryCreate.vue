<template>
  <main class="content px-3 py-2">
    <div class="container-fluid">
      <div class="mb-3">
        <h4>Create Category</h4>
      </div>
      <div class="form-container">
        <form @submit.prevent="handleSubmit">
          <div class="form-row">
            <!-- Name input field taking one-third of the width -->
            <div class="form-group col-md-4">
              <label for="name">Category Name</label>
              <input
                id="name"
                v-model="formData.name"
                placeholder="Enter category name"
                class="form-control"
              />
              <span v-if="errors.name" class="text-danger">{{ errors.name }}</span>
            </div>

            <!-- Description textarea taking full width -->
            <div class="form-group">
              <label for="description">Description</label>
              <textarea
                id="description"
                v-model="formData.description"
                placeholder="Enter description"
                rows="4"
                class="form-control"
              ></textarea>
              <span v-if="errors.description" class="text-danger">{{ errors.description }}</span>
            </div>
          </div>

          <!-- Type radio buttons -->
          <div class="form-group">
            <label>Type</label>
            <div class="d-flex">
              <label class="ml-3">
                <input
                  type="radio"
                  value="0"
                  v-model="formData.type_id" checked
                /> Income
              </label>
              <label class="ml-3">
                <input
                  type="radio"
                  value="1"
                  v-model="formData.type_id"
                /> Expense
              </label>
            </div>
            <span v-if="errors.type_id" class="text-danger">{{ errors.type_id }}</span>
          </div>

          <!-- Submit button -->
          <button type="submit" class="btn btn-primary">Submit</button>
        </form>
      </div>
    </div>
  </main>
</template>

<script setup>
import { reactive } from 'vue';
import { useNuxtApp } from '#app';
import { useRouter } from 'vue-router';
import Swal from 'sweetalert2';
import { categoryValidation } from '../composables/categoryValidation'; // Import validation logic

const router = useRouter();
const formData = reactive({
  name: '',
  description: '',
  type_id: '', // Field for income or expense
  use_id: '', // Unused field
});

const errors = reactive({
  name: '',
  description: '',
  type_id: '',
  use_id: '', // Unused field
});

const existingCategories = reactive([]);

// Fetch existing categories to use them for validation
const fetchCategories = async () => {
  try {
    const { $axios } = useNuxtApp();
    const response = await $axios.get('/categories');
    existingCategories.value = response.data; // Update list of existing categories
  } catch (error) {
    console.error('Error fetching categories:', error);
  }
};

fetchCategories();

// Run validation with the list of existing categories
const { validationSchema } = categoryValidation(existingCategories);

const handleSubmit = async () => {
  // Clear previous errors
  Object.keys(errors).forEach((key) => {
    errors[key] = '';
  });

  try {
    // Validate form data
    await validationSchema.validate(formData, { abortEarly: false });

    const { $axios } = useNuxtApp();
    const response = await $axios.post('/categories', formData);
    console.log('Response:', response.data);

    Swal.fire({
      icon: 'success',
      title: 'Category added successfully',
      text: 'The data has been successfully added!',
    });

    router.push('/category/list');
  } catch (validationError) {
    // Handle backend errors (if any)
    if (validationError.response && validationError.response.data.errors) {
      const backendErrors = validationError.response.data.errors;
      Object.keys(backendErrors).forEach((field) => {
        errors[field] = backendErrors[field][0]; // Show the first error for each field
      });
      console.log(errors); // Check if errors are set correctly
    } else {
      // Frontend validation errors (Yup validation)
      if (validationError.inner) {
        validationError.inner.forEach((err) => {
          errors[err.path] = err.message; // Show corresponding error for each field
        });
      } else {
        console.error('Error:', validationError.message);
      }
    }
  }
};
</script>

<style lang="scss" scoped>
.form-container {
  margin: 0 auto;
  padding: 20px;
  border: 1px solid #ccc;
  border-radius: 8px;
  background-color: #f9f9f9;
}

.form-group {
  margin-bottom: 1.5rem;

  label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: bold;
  }

  .form-control {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 1rem;
  }

  .form-control:focus {
    border-color: #007bff;
    outline: none;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
  }

  .ml-3 {
    margin-left: 1rem;
  }
}

.btn {
  display: inline-block;
  padding: 0.5rem 1rem;
  font-size: 1rem;
  font-weight: bold;
  text-align: center;
  color: #fff;
  background-color: #007bff;
  border: none;
  border-radius: 4px;
  cursor: pointer;

  &:hover {
    background-color: #0056b3;
  }
}
</style>
