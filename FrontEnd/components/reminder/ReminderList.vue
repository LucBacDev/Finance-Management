<template>
  <main class="content px-3 py-2">
    <div class="container-fluid">
      <div class="card border-0">
        <div class="card-header d-flex justify-content-between align-items-center">
          <!-- Search Form -->
          <form @submit.prevent="searchReminders" class="d-flex">
            <input
              type="text"
              v-model="searchQuery"
              class="form-control mr-2"
              placeholder="Enter search query"
              :disabled="isLoading"
            />
            <button type="submit" class="btn btn-primary" :disabled="isLoading">
              Search
            </button>
            <!-- Reload Button -->
            <button
              type="button"
              class="btn btn-secondary ml-2"
              @click="reloadData"
              :disabled="isLoading"
            >
              <i class="fas fa-sync-alt"></i> Reload
            </button>
          </form>

          <!-- Add Reminder Button -->
          <NuxtLink to="/reminder/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add
          </NuxtLink>
        </div>
        <div class="card-body table-responsive">
          <!-- Loading Indicator -->
          <div v-if="isLoading" class="text-center">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>

          <!-- No Data Warning -->
          <div v-else-if="data.length === 0" class="alert alert-warning">
            No reminders available.
          </div>

          <!-- Reminders Table -->
          <table class="table table-bordered" v-if="data.length > 0">
            <thead>
              <tr>
                <th>#</th>
                <th>Title</th>
                <th>Description</th>
                <th>Due Date</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(reminder, index) in filteredData" :key="reminder.id">
                <td>{{ (pagination.current_page - 1) * pagination.per_page + index + 1 }}</td>
                <td>{{ reminder.title }}</td>
                <td class="description">{{ reminder.description }}</td>
                <td>{{ formatDate(reminder.due_date) }}</td>
                <td>{{ reminder.status ? 'Completed' : 'Pending' }}</td>
                <td>
                  <button class="btn btn-primary btn-sm m-1" @click="editReminder(reminder)">
                    <i class="fas fa-edit"></i>
                  </button>
                  <button class="btn btn-danger btn-sm m-1" @click="deleteReminder(reminder.id)">
                    <i class="fas fa-trash"></i>
                  </button>
                </td>
              </tr>
              <tr v-if="filteredData.length === 0">
                <td colspan="6" class="text-center">No matching data found</td>
              </tr>
            </tbody>
          </table>

          <!-- Pagination Controls -->
          <nav v-if="pagination.total_pages > 1">
            <ul class="pagination justify-content-end">
              <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
                <button class="page-link" @click="changePage(pagination.current_page - 1)">Previous</button>
              </li>
              <li
                class="page-item"
                v-for="page in pagination.total_pages"
                :key="page"
                :class="{ active: page === pagination.current_page }"
              >
                <button class="page-link" @click="changePage(page)">{{ page }}</button>
              </li>
              <li class="page-item" :class="{ disabled: pagination.current_page === pagination.total_pages }">
                <button class="page-link" @click="changePage(pagination.current_page + 1)">Next</button>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useNuxtApp } from '#app';
import { useRouter } from 'vue-router';
import { useStore } from 'vuex';
import Swal from 'sweetalert2';

// Initialize variables
const store = useStore();
const data = ref([]);
const searchQuery = ref('');
const pagination = ref({
  current_page: 1,
  per_page: 5,
  total_items: 0,
  total_pages: 0,
});
const isLoading = ref(false);
const { $axios } = useNuxtApp();
const router = useRouter();
let user_id_reminder = ref(null);

// Fetch reminder data based on search and pagination
const fetchData = async () => {
  isLoading.value = true;
  try {
    const response = await $axios.get('reminders', {
      params: {
        page: pagination.value.current_page,
        per_page: pagination.value.per_page,
        search: searchQuery.value.trim(),
      },
    });
    if (response.data?.data && response.data?.pagination) {
      data.value = response.data.data;
      pagination.value = response.data.pagination;
    } else {
      data.value = [];
      pagination.value.total_pages = 0;
    }
  } catch (error) {
    console.error('Error fetching data:', error);
    data.value = [];
    pagination.value.total_pages = 0;
  } finally {
    isLoading.value = false;
  }
};

// Search reminder entries
const searchReminders = async () => {
  pagination.value.current_page = 1; // Reset to page 1 when searching
  fetchData();
};

// Reload the data (reset search query and pagination)
const reloadData = () => {
  searchQuery.value = ''; // Clear search query
  pagination.value.current_page = 1; // Reset to first page
  fetchData();
};

// Filter data based on search query
const filteredData = computed(() => {
  return data.value;
});

// Delete a reminder
const deleteReminder = async (id) => {
  if (!confirm('Are you sure you want to delete this reminder?')) return;
  try {
    await $axios.delete(`reminders/${id}`);
    data.value = data.value.filter((reminder) => reminder.id !== id);
    if (pagination.value.current_page > pagination.value.total_pages) {
      pagination.value.current_page = pagination.value.total_pages;
    }
    Swal.fire('Success', 'Reminder deleted successfully', 'success');
    fetchData();
  } catch (error) {
    Swal.fire('Error', 'Failed to delete reminder', 'error');
  }
};

// Navigate to reminder edit page
const editReminder = (reminder) => {
  router.push(`/reminder/${reminder.id}`);
};

// Change pagination page
const changePage = (page) => {
  if (page < 1 || page > pagination.value.total_pages) return;
  pagination.value.current_page = page;
  fetchData();
};

// Format date for display
const formatDate = (date) => {
  return new Date(date).toLocaleDateString('vi-VN', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
};

// On mounted, fetch reminder data
onMounted(() => {
  fetchData();
});
</script>

<style scoped>
/* Add styles specific to the reminder table, buttons, and pagination (similar to the existing styles) */
.table {
  margin-top: 1rem;
  border-collapse: collapse;
  width: 100%;
  background-color: #ffffff;
  border: 1px solid #dee2e6;
  border-radius: 0.375rem;
  overflow: hidden;
}

th,
td {
  text-align: center;
  padding: 0.75rem;
  vertical-align: middle;
  border: 1px solid #dee2e6;
  font-size: 0.875rem;
}

th {
  background-color: #f5f5f5;
  font-weight: 600;
  color: #343a40;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

tbody tr:hover {
  background-color: #f8f9fa;
}

td {
  white-space: nowrap;
  text-overflow: ellipsis;
  overflow: hidden;
}

.description {
  max-width: 120px;
}

.text-center {
  text-align: center;
}

.form-control {
  width: 200px;
  padding: 0.5rem 0.75rem;
  font-size: 0.875rem;
}

.btn {
  font-size: 0.75rem;
  padding: 0.25rem 0.5rem;
}

.pagination {
  margin-top: 1rem;
  justify-content: flex-end;
}

.page-item.active .page-link {
  background-color: #007bff;
  border-color: #007bff;
}

.page-item.disabled .page-link {
  pointer-events: none;
  background-color: #f8f9fa;
  border-color: #e9ecef;
}
</style>
