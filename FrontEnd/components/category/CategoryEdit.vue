<template>
  <main class="content px-3 py-2">
    <div class="container-fluid">
      <div class="mb-3">
        <h4>Edit Category</h4>
      </div>
      <div class="form-container">
        <form @submit.prevent="updateCategory">
          <div class="form-group">
            <label for="name">Name</label>
            <input
              type="text"
              id="name"
              v-model="category.name"
              placeholder="Name"
              class="form-control"
            />
            <span v-if="errors.name" class="text-danger">{{ errors.name }}</span>
          </div>

          <div class="form-group">
            <label for="description">Description</label>
            <textarea
              id="description"
              v-model="category.description"
              placeholder="Description"
              rows="4"
              class="form-control"
            ></textarea>
            <span v-if="errors.description" class="text-danger">{{ errors.description }}</span>
          </div>

          <div class="form-group">
            <label>Type</label>
            <div class="d-flex">
              <label class="ml-3">
                <input
                  type="radio"
                  value="0"
                  v-model="category.type_id"
                /> Income
              </label>
              <label class="ml-3">
                <input
                  type="radio"
                  value="1"
                  v-model="category.type_id"
                /> Expense
              </label>
            </div>
            <span v-if="errors.type_id" class="text-danger">{{ errors.type_id }}</span>
          </div>

          <button type="submit" class="btn btn-primary" :disabled="isLoading">Update</button>
        </form>
      </div>
    </div>
  </main>
</template>
<script setup>
import { reactive, ref, onMounted, toRaw } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import Swal from 'sweetalert2';
import { useNuxtApp } from '#app';
import { categoryValidation } from '../composables/categoryValidation';

const route = useRoute();
const router = useRouter();
const { $axios } = useNuxtApp();
const categoryId = route.params.id;

const category = reactive({
  name: '',
  description: '',
  type_id: '', // Income or Expense
});

const errors = ref({});
const isLoading = ref(false);

// Fetch category data
const fetchCategory = async () => {
  try {
    isLoading.value = true;
    const response = await $axios.get(`/categories/${categoryId}`);
    Object.assign(category, response.data.data);
  } catch (error) {
    console.error('Error fetching category data:', error);
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Failed to load category data!',
    });
  } finally {
    isLoading.value = false;
  }
};

// Update category
const updateCategory = async () => {
  // Reset errors
  Object.keys(errors).forEach((key) => (errors[key] = ''));

  try {
    // Gửi request cập nhật danh mục
    await $axios.put(`/categories/${categoryId}`, category);

    // Hiển thị thông báo thành công
    Swal.fire({
      icon: 'success',
      title: 'Updated',
      text: 'Category updated successfully!',
    });

    router.push('/category/list');
  } catch (error) {
    // Xử lý lỗi xác thực từ API
    if (error.response?.status === 422) {
      const validationErrors = error.response.data.errors;
      Object.keys(validationErrors).forEach((field) => {
        errors[field] = validationErrors[field][0];
      });
    } else {
      console.error('Unexpected error:', error);
    }
  }
};


// Fetch category on mounted
onMounted(fetchCategory);
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

  &:disabled {
    background-color: #ccc;
    cursor: not-allowed;
  }
}
</style>
