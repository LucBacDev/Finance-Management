<template>
  <main class="content px-3 py-2">
    <div class="container-fluid">
      <div class="mb-3">
        <h4>Edit Expense</h4>
      </div>
      <div class="form-container">
        <form @submit.prevent="updateExpense">
          <!-- Row 1 -->
          <div class="form-row">
            <!-- Title input field (Column 1) -->
            <div class="form-group col-md-6">
              <label for="title">Title</label>
              <input
                id="title"
                v-model="expense.title"
                placeholder="Title"
                class="form-control"
              />
              <span v-if="errors.title" class="text-danger">{{ errors.title }}</span>
            </div>

            <!-- Amount input field (Column 2) -->
            <div class="form-group col-md-6">
              <label for="amount">Amount</label>
              <input
                id="amount"
                v-model="expense.amount"
                type="number"
                placeholder="Amount"
                class="form-control"
              />
              <span v-if="errors.amount" class="text-danger">{{ errors.amount }}</span>
            </div>
          </div>

          <!-- Row 2 -->
          <div class="form-row">
            <!-- Date input field (Column 1) -->
            <div class="form-group col-md-6">
              <label for="date">Date</label>
              <input
                id="date"
                v-model="expense.date"
                type="date"
                class="form-control"
              />
              <span v-if="errors.date" class="text-danger">{{ errors.date }}</span>
            </div>

            <!-- Category select (Column 2) -->
            <div class="form-group col-md-6">
              <label for="category_id">Category</label>
              <select
                id="category_id"
                v-model="expense.category_id"
                class="form-control"
              >
                <option value="" disabled selected>Select Category</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                  {{ category.name }}
                </option>
              </select>
              <span v-if="errors.category_id" class="text-danger">{{ errors.category_id }}</span>
            </div>
          </div>

          <!-- Row 3 -->
          <div class="form-row">
            <!-- Description textarea (Column 1) -->
            <div class="form-group col-md-12">
              <label for="description">Description</label>
              <textarea
                id="description"
                v-model="expense.description"
                placeholder="Description"
                rows="4"
                class="form-control"
              ></textarea>
              <span v-if="errors.description" class="text-danger">{{ errors.description }}</span>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="form-group">
            <button type="submit" class="btn btn-primary">Update</button>
          </div>
        </form>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import Swal from 'sweetalert2';  // Import SweetAlert2
import { useNuxtApp } from '#app';
import { useStore } from 'vuex';
import { expenseValidation } from '../composables/expenseValidation'; // Import validation logic

const store = useStore();
const route = useRoute();
const router = useRouter();
const { $axios } = useNuxtApp();
const { validationSchema } = expenseValidation();  // Get validation schema from useValidation

const expenseId = route.params.id;
const expense = ref({
  title: '',
  amount: 0,
  date: '',
  description: '',
  category_id: '',
  user_id: "", // Make sure user_id is correctly set
});

const categories = reactive([]);

// Errors object for validation
const errors = reactive({
  title: "",
  amount: "",
  date: "",
  description: "",
  category_id: "",
});

// Fetch categories
const fetchCategories = async () => {
  try {
    const response = await $axios.get('/categories');
    categories.splice(0, categories.length, ...response.data.data);
  } catch (error) {
    console.error('Error fetching categories:', error.message);
  }
};

// Fetch expense data by ID
const fetchExpense = async () => {
  try {
    const response = await $axios.get(`/expenses/${expenseId}`);
    expense.value = response.data.data;
  } catch (error) {
    console.error('Error fetching expense:', error.message);
  }
};

// Update expense with validation
const updateExpense = async () => {
  // Clear previous errors
  Object.keys(errors).forEach((key) => {
    errors[key] = "";
  });

  try {
    // Validate the expense data
    await validationSchema.validate(expense.value, { abortEarly: false });

    // Update the expense data
    await $axios.put(`/expenses/${expenseId}`, expense.value);

    Swal.fire({
      icon: 'success',
      title: 'Update Successful',
      text: 'Expense data has been updated successfully!',
    });

    // Redirect to the expense list
    router.push('/expense/list');
  } catch (validationError) {
    // Handle validation errors
    if (validationError.inner) {
      validationError.inner.forEach((err) => {
        errors[err.path] = err.message;
      });
    } else {
      console.error("Error:", validationError.message);
    }

    Swal.fire({
      icon: 'error',
      title: 'Update Failed',
      text: 'Please check the form and try again.',
    });
  }
};

// Initialize data on mounted
onMounted(async () => {
  fetchCategories();
  fetchExpense();
  await store.dispatch('loadId');
  const id = computed(() => store.getters.getId);
  expense.user_id = id.value; // Make sure user_id is correctly set
});
</script>
<style lang="scss" scoped>
.form-container {
  margin: 0 auto;
  padding: 20px;
  background-color: #f9f9f9;
  border-radius: 8px;
}

.form-row {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
}

.form-group {
  flex: 1 1 48%;
  margin-bottom: 1.5rem;
}

.form-group label {
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
}

.btn:hover {
  background-color: #0056b3;
}
</style>
