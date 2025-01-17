<template>
  <main class="content px-3 py-2">
    <div class="container-fluid">
      <!-- Search Reminders -->
      <div class="card border-0">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Search Reminders</h5>
          </div>
          <div class="card-body">
            <!-- Search Form -->
            <form @submit.prevent="searchReminders">
              <div class="input-group">
                <input type="text" v-model="searchQuery" class="form-control" placeholder="Enter search query" />
                <button type="submit" class="btn btn-primary">Search</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Reminder List -->
      <div class="card border-0 mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">Reminder List</h5>
          <NuxtLink to="/reminder/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add
          </NuxtLink>
        </div>
        <div class="card-body table-responsive">
          <!-- Table -->
          <div v-if="searchPerformed && reminders.length > 0" class="table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Title</th>
                  <th>Description</th>
                  <th>Due Date</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(reminder, index) in reminders" :key="reminder.id">
                  <td>{{ index + 1 }}</td>
                  <td>{{ reminder.title }}</td>
                  <td class="description">{{ reminder.description || "No Description" }}</td>
                  <td>{{ formatDate(reminder.due_date) }}</td>
                  <td>{{ reminder.status ? "Hoàn thành" : "Chưa hoàn thành" }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-else-if="searchPerformed && reminders.length === 0">
            <span>No results found for "{{ searchQuery }}"</span>
          </div>
          <div v-else class="table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Title</th>
                  <th>Reminder Date</th>
                  <th>Description</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(reminder, index) in filteredData" :key="reminder.id">
                  <td>{{ (pagination.current_page - 1) * pagination.per_page + index + 1 }}</td>
                  <td>{{ reminder.title }}</td>
                  <td>{{ formatDate(reminder.due_date) }}</td>
                  <td class="description">{{ reminder.description }}</td>
                  <td>{{ reminder.status ? "Complete" : "Not Complete" }}</td>
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
          </div>
        </div>
      </div>

      <div class="pagination mt-3 d-flex justify-content-center">
        <ul class="pagination">
          <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
            <button class="page-link" @click="changePage(pagination.current_page - 1)">
              Previous
            </button>
          </li>
          <li class="page-item" v-for="page in pagination.total_pages" :key="page"
            :class="{ active: page === pagination.current_page }">
            <button class="page-link" @click="changePage(page)">
              {{ page }}
            </button>
          </li>
          <li class="page-item" :class="{ disabled: pagination.current_page === pagination.total_pages }">
            <button class="page-link" @click="changePage(pagination.current_page + 1)">
              Next
            </button>
          </li>
        </ul>
      </div>
    </div>
  </main>
</template>


<script setup>
import { ref, computed, onMounted } from "vue";
import { useNuxtApp } from "#app";
import Swal from "sweetalert2";
import { useRouter } from "vue-router";
import { useStore } from "vuex";

const store = useStore();
const { $axios } = useNuxtApp();
const router = useRouter();

const data = ref([]); // Dữ liệu mặc định
const reminders = ref([]); // Dữ liệu tìm kiếm
const searchQuery = ref("");
const pagination = ref({
  current_page: 1,
  per_page: 5,
  total_items: 0,
  total_pages: 0,
});
let user_id_reminder = ref(null);
let searchPerformed = ref(false); // Kiểm tra đã thực hiện tìm kiếm chưa

const fetchData = async () => {
  try {
    const response = await $axios.get("reminders", {
      params: {
        id: user_id_reminder.value,
        page: pagination.value.current_page,
        per_page: pagination.value.per_page,
      },
    });
    if (response.data?.data && response.data?.pagination) {
      data.value = response.data.data; // Dữ liệu mặc định
      pagination.value = response.data.pagination;
    } else {
      throw new Error("API returned unexpected data format.");
    }
  } catch (error) {
    console.error("Error fetching data:", error);
    data.value = [];
  }
};

const searchReminders = async () => {
  if (!searchQuery.value.trim()) {
    alert("Please enter a search query");
    return;
  }

  try {
    const response = await $axios.get("reminders", { params: { search: searchQuery.value } });
    reminders.value = response.data.data || [];
    searchPerformed.value = true; // Đánh dấu đã tìm kiếm
  } catch (error) {
    console.error("Error searching reminders:", error);
    reminders.value = [];
  }
};

const filteredData = computed(() => {
  return data.value; // Trả về dữ liệu mặc định khi chưa tìm kiếm
});

const deleteReminder = async (id) => {
  if (!confirm("Do you want to delete this reminder?")) return;
  try {
    await $axios.delete(`reminders/${id}`);
    fetchData();
    Swal.fire({ icon: "success", title: "Deleted", text: "Reminder deleted." });
  } catch (error) {
    console.error("Error deleting reminder:", error);
    Swal.fire({ icon: "error", title: "Error", text: "Failed to delete reminder." });
  }
};

const editReminder = (reminder) => {
  router.push(`/reminder/${reminder.id}`);
};

const changePage = (page) => {
  if (page < 1 || page > pagination.value.total_pages) return;
  pagination.value.current_page = page;
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString("vi-VN", { year: "numeric", month: "long", day: "numeric" });
};

onMounted(() => {
  store.dispatch("loadId").then(() => {
    user_id_reminder.value = store.state.user_id;
    fetchData(); // Gọi API để lấy dữ liệu mặc định
  });
});

// Watch for changes in the search query and fetch data accordingly
watch(searchQuery, (newSearchQuery) => {
  if (!newSearchQuery.trim()) {
    searchPerformed.value = false; // Clear search results if the search query is empty
    fetchData(); // Fetch all data if no search query is provided
  } else {
    searchReminders(); // Perform search when the query changes
  }
});

// Watch for changes in pagination and fetch data accordingly
watch(
  () => pagination.value.current_page,
  () => {
    fetchData(); // Fetch data when the current page changes
  }
);
</script>


<style scoped>
.table {
  width: 100%;
  margin-top: 1rem;
  border-collapse: collapse;
}

.table th,
.table td {
  text-align: center;
  padding: 0.75rem;
  vertical-align: middle;
  border: 1px solid #dee2e6;
}

.table th {
  background-color: #f5f5f5;
  font-weight: bold;
  text-transform: uppercase;
  font-size: 0.9rem;
}

.table td.description {
  max-width: 120px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.table tbody tr:hover {
  background-color: #f8f9fa;
}
</style>
