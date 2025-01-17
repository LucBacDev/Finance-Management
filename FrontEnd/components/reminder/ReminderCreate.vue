<template>
  <main class="content px-3 py-2">
    <div class="container-fluid">
      <div class="mb-3">
        <h4>Create Reminder</h4> <!-- Tiêu đề thay đổi thành "Create Reminder" -->
      </div>
      <div class="form-container">
        <form @submit.prevent="handleSubmit">
          <div class="form-group d-flex">
            <div class="col-12 col-lg-3 title">
              <label for="title">Title</label> <!-- Thay 'source' thành 'title' -->
              <input id="title" v-model="formData.title" placeholder="Reminder Title" class="form-control" />
              <span v-if="errors.title" class="text-danger">{{ errors.title }}</span>
            </div>
            <div class="col-12 col-lg-3">
              <label for="due_date">Due Date</label> <!-- Thay 'amount' thành 'due_date' -->
              <input id="due_date" v-model="formData.due_date" type="date" class="form-control" />
              <span v-if="errors.due_date" class="text-danger">{{ errors.due_date }}</span>
            </div>
          </div>
          <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" v-model="formData.description" placeholder="Description" rows="4"
              class="form-control"></textarea>
            <span v-if="errors.description" class="text-danger">{{ errors.description }}</span>
          </div>


          <br />
          <br />
          <button type="submit" class="btn btn-primary">Submit</button>
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
import { reminderValidation } from "../composables/reminderValidation"; // Import the validation logic

import { useStore } from 'vuex';

const store = useStore();

const router = useRouter();
const formData = reactive({
  title: "", // Thay 'source' thành 'title'
  due_date: "", // Thay 'amount' thành 'due_date'
  description: "",
});

const categories = reactive([]);

const { validationSchema } = reminderValidation(); // Use the validation schema

const errors = reactive({
  title: "", // Thay 'source' thành 'title'
  due_date: "", // Thay 'amount' thành 'due_date'
  description: "",
});

onMounted(async () => {
  await store.dispatch('loadId');
 
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
    console.log("Form data:", formData); // Debug log form data

    const response = await $axios.post("/reminders", formData); // Đổi endpoint từ '/incomes' thành '/reminders'
    console.log("Response:", response.data);

    Swal.fire({
      icon: "success",
      title: "Reminder created successfully", // Thay đổi thông báo thành "Reminder created successfully"
      text: "Your reminder has been created successfully!",
    });

    router.push("/reminder/list"); // Đổi đường dẫn từ '/income/list' thành '/reminder/list'
  } catch (error) {
    console.error('Error updating reminder:', error);
    let errorMessages = [];
    if (error.inner) {
      errorMessages = error.inner.map((err) => err.message);
    }

    Swal.fire({
      icon: 'error',
      title: 'Update Failed',
      text: errorMessages.join(' '),
    });
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

.title{
  margin-right: 120px;
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