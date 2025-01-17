<template>
  <main class="content px-3 py-2">
    <div class="container-fluid">
      <div class="mb-3">
        <h4>Create Income</h4>
      </div>
      <div class="form-container">
  <form @submit.prevent="handleSubmit">
    <!-- Row 1 -->
    <div class="form-row">
      <!-- Source input field (Column 1) -->
      <div class="form-group col-md-6">
        <label for="source">Source</label>
        <input
          id="source"
          v-model="formData.source"
          placeholder="Source"
          class="form-control"
        />
        <span v-if="errors.source" class="text-danger">{{ errors.source }}</span>
      </div>

      <!-- Amount input field (Column 2) -->
      <div class="form-group col-md-6">
        <label for="amount">Amount</label>
        <input
          id="amount"
          v-model="formData.amount"
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
          v-model="formData.date"
          type="date"
          class="form-control"
        />
        <span v-if="errors.date" class="text-danger">{{ errors.date }}</span>
      </div>

       <!-- Category select (Column 1) -->
       <div class="form-group col-md-6">
        <label for="category_id">Category</label>
        <select
          id="category_id"
          v-model="formData.category_id"
          class="form-control"
        >
          <option value="" disabled selected>Select Category</option>
          <option v-for="category in categories.filter(category => category.type_id === 0)" :key="category.id" :value="category.id">
            {{ category.name }}
          </option>
        </select>
        <span v-if="errors.category_id" class="text-danger">{{ errors.category_id }}</span>
      </div>
    </div>

    <!-- Row 3 -->
    <div class="">
           <!-- Description textarea (Column 2) -->
           <div class="form-group col-md-12">
        <label for="description">Description</label>
        <textarea
          id="description"
          v-model="formData.description"
          placeholder="Description"
          rows="4"
          class="form-control"
        ></textarea>
        <span v-if="errors.description" class="text-danger">{{ errors.description }}</span>
      </div>


      <!-- Submit Button (Column 2) -->
      <div class="form-group col-md-1">
        <button type="submit" class="btn btn-primary" style="width: 100%;">Submit</button>
      </div>
    </div>
  </form>
</div>

    </div>
  </main>
</template>
<script setup>
import { reactive, onMounted } from "vue";
import { useNuxtApp } from "#app";
import { useRouter } from "vue-router";
import Swal from "sweetalert2";
import { useValidation } from "../composables/useValidation"; // Import the validation logic

import { useStore } from 'vuex';

const store = useStore();

const router = useRouter();
const formData = reactive({
  source: "",
  amount: "",
  date: "",
  description: "",
  category_id: "",
  user_id: "",
});

const categories = reactive([]);

const { validationSchema } = useValidation(); // Use the validation schema

const errors = reactive({
  source: "",
  amount: "",
  date: "",
  description: "",
  category_id: "",
});

const fetchCategories = async () => {
  const { $axios } = useNuxtApp();
  try {
    const response = await $axios.get("/categories");
    categories.splice(0, categories.length, ...response.data.data);
  } catch (error) {
    console.error("Error fetching categories:", error.message);
  }
};

onMounted(async () => {
  fetchCategories();
  await store.dispatch('loadId');
  const id = computed(() => store.getters.getId);
  formData.user_id = id.value;
});

const handleSubmit = async () => {
  // Clear previous errors
  Object.keys(errors).forEach((key) => {
    errors[key] = "";
  });

  try {
    // Validate the form data
    await validationSchema.validate(formData, { abortEarly: false });

    const { $axios } = useNuxtApp();
    const response = await $axios.post("/incomes", formData);
    console.log("Response:", response.data);

    Swal.fire({
      icon: "success",
      title: "Thêm thu nhập thành công",
      text: "Dữ liệu đã được thêm vào thành công!",
    });

    router.push("/income/list");
  } catch (validationError) {
    // Handle validation errors
    if (validationError.inner) {
      validationError.inner.forEach((err) => {
        errors[err.path] = err.message;
      });
    } else {
      console.error("Error:", validationError.message);
    }
  }
};
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
  flex-wrap: wrap; /* Ensures the fields wrap to the next line if necessary */
  gap: 20px; /* Adds space between columns */
}

.form-group {
  flex: 1 1 48%; /* Makes each form group take up about 48% of the row's width */
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