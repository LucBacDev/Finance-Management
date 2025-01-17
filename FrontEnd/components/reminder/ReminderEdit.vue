<template>
  <main class="content px-3 py-2">
    <div class="container-fluid">
      <div class="mb-3">
        <h4>Reminder Dashboard</h4>
      </div>
      <div class="form-container">
        <form @submit.prevent="updateReminder">
          <div class="form-group d-flex">
            <div class="col-12 col-lg-3 title">
              <label for="title">Title</label>
              <input type="text" id="title" v-model="reminder.title" class="form-control" />
            </div>
            <div class="col-12 col-lg-3">
              <label for="date">Reminder Date</label>
              <input type="date" id="date" v-model="reminder.due_date" class="form-control" />
            </div>
          </div>
          <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" v-model="reminder.description" class="form-control" rows="4"></textarea>
          </div>
          <div class="form-group">
            <label>Status</label>
            <div class="form-check">
              <input type="radio" id="status-completed" value="true" v-model="reminder.status"
                class="form-check-input" />
              <label class="form-check-label" for="status-completed">Completed</label>
            </div>
            <div class="form-check">
              <input type="radio" id="status-not-completed" value="false" v-model="reminder.status"
                class="form-check-input" />
              <label class="form-check-label" for="status-not-completed">Not Completed</label>
            </div>
          </div>
          <button type="submit" class="btn btn-primary">Update Reminder</button>
        </form>
      </div>
    </div>
  </main>
</template>


<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import Swal from 'sweetalert2';
import { useNuxtApp } from '#app';
import { reminderValidation } from '../composables/reminderValidation';
import { useStore } from 'vuex';

const store = useStore();

const route = useRoute();
const router = useRouter();
const { $axios } = useNuxtApp();
const { validationSchema } = reminderValidation();

const reminderId = route.params.id;
const reminder = ref({
  title: '',
  date: '',
  description: '',
  category_id: '',
  status: 'false', // Giá trị mặc định là Not Completed
});

const fetchReminder = async () => {
  try {
    const response = await $axios.get(`/reminders/${reminderId}`);
    reminder.value = response.data.data;
    reminder.value.status = response.data.data.status ? 'true' : 'false'; // Chuyển thành string cho radio
  } catch (error) {
    console.error('Error fetching reminder:', error.message);
  }
};

const updateReminder = async () => {
  try {
    reminder.value.status = reminder.value.status === 'true'; // Chuyển thành boolean trước khi gửi
    await validationSchema.validate(reminder.value, { abortEarly: false });
    await $axios.put(`/reminders/${reminderId}`, reminder.value);

    Swal.fire({
      icon: 'success',
      title: 'Success',
      text: 'Reminder updated successfully!',
    });

    router.push('/reminder/list');
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

onMounted(async () => {
  await fetchReminder();
});
</script>

<style lang="scss" scoped>
.form-container {
  margin: 0 auto;
  padding: 20px;
  border: 1px solid #ccc;
  border-radius: 8px;
  background-color: #f9f9f9;
}

.title {
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
