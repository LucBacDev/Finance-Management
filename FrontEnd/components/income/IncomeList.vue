<template>
  <main class="content px-3 py-2">
    <div class="container-fluid">
      <!-- Income List -->
      <div class="card border-0">
        <div class="card-header d-flex justify-content-between align-items-center">
          <!-- Search Form -->
          <form @submit.prevent="searchIncomes" class="d-flex">
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

          <!-- Add Income Button -->
          <NuxtLink to="/income/create" class="btn btn-primary">
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
            No incomes available.
          </div>

          <!-- Income Table -->
          <table class="table table-bordered" v-if="data.length > 0">
            <thead>
              <tr>
                <th>#</th>
                <th>Title</th>
                <th>Category</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Description</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(income, index) in filteredData" :key="income.id">
                <td>{{ (pagination.current_page - 1) * pagination.per_page + index + 1 }}</td>
                <td>{{ income.source }}</td>
                <td>{{ income.category?.name || 'No Category' }}</td>
                <td>{{ formatCurrency(income.amount) }}</td>
                <td>{{ formatDate(income.date) }}</td>
                <td class="description">{{ income.description }}</td>
                <td>
                  <button class="btn btn-primary btn-sm m-1" @click="editIncome(income)">
                    <i class="fas fa-edit"></i>
                  </button>
                  <button class="btn btn-danger btn-sm m-1" @click="deleteIncome(income.id)">
                    <i class="fas fa-trash"></i>
                  </button>
                </td>
              </tr>
              <tr v-if="filteredData.length === 0">
                <td colspan="7" class="text-center">No matching data found</td>
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
let user_id_income = ref(null);

// Fetch income data based on search and pagination
const fetchData = async () => {
  isLoading.value = true;
  try {
    const response = await $axios.get('incomes', {
      params: {
        id: user_id_income.value,
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

// Search income entries
const searchIncomes = async () => {
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

// Delete an income
const deleteIncome = async (id) => {
  if (!confirm('Are you sure you want to delete this income entry?')) return;
  try {
    await $axios.delete(`incomes/${id}`);
    data.value = data.value.filter((income) => income.id !== id);
    if (pagination.value.current_page > pagination.value.total_pages) {
      pagination.value.current_page = pagination.value.total_pages;
    }
    Swal.fire('Success', 'Income deleted successfully', 'success');
    fetchData();
  } catch (error) {
    Swal.fire('Error', 'Failed to delete income', 'error');
  }
};

// Navigate to income edit page
const editIncome = (income) => {
  router.push(`/income/${income.id}`);
};

// Change pagination page
const changePage = (page) => {
  if (page < 1 || page > pagination.value.total_pages) return;
  pagination.value.current_page = page;
  fetchData();
};

// Format currency for display
const formatCurrency = (amount) => {
  return parseFloat(amount).toLocaleString('vi-VN') + ' VND';
};

// Format date for display
const formatDate = (date) => {
  return new Date(date).toLocaleDateString('vi-VN', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
};

// On mounted, load user ID and fetch data
onMounted(async () => {
  await store.dispatch('loadId');
  user_id_income.value = store.getters.getId;
  fetchData();
});
</script>

<style scoped>
/* Add styles for the table, buttons, and pagination */
.table {
  margin-top: 1rem;
  border-collapse: collapse;
  width: 100%;
  background-color: #ffffff;
  border: 1px solid #dee2e6;
  border-radius: 0.375rem;
  overflow: hidden;

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

  td.description {
    max-width: 120px;
  }

  .text-center {
    text-align: center;
  }
}
/* Add styles for the search form to make it smaller */
.form-control {
  width: 200px; /* Adjust the width of the search input */
  padding: 0.5rem 0.75rem; /* Reduce padding to make it smaller */
  font-size: 0.875rem; /* Adjust font size */
}

.btn {
  font-size: 0.75rem; /* Smaller font size for buttons */
  padding: 0.25rem 0.5rem; /* Adjust button padding */
}

.btn-primary {
  font-size: 0.75rem; /* Ensure the search button is smaller */
  padding: 0.25rem 0.5rem;
}

.btn-secondary {
  font-size: 0.75rem; /* For the reload button */
  padding: 0.25rem 0.5rem;
  margin-left: 0.5rem; /* Adjust spacing between the buttons */
}

.form {
  display: flex;
  align-items: center; /* Align buttons and input in one line */
  gap: 0.5rem; /* Space between elements */
}

.btn {
  margin: 0.25rem;
  padding: 0.3rem 0.75rem;
  font-size: 0.75rem;
  border-radius: 0.25rem;
}

.btn-primary {
  background-color: #007bff;
  border-color: #007bff;
  color: #ffffff;
}

.btn-danger {
  background-color: #dc3545;
  border-color: #dc3545;
  color: #ffffff;
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
