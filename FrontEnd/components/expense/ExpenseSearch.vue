<template>
  <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">Search Expense</h5>
      </div>
      <div class="card-body">
          <!-- Tìm kiếm -->
          <form @submit.prevent="searchExpense">
              <div class="input-group">
                  <input 
                      type="text" 
                      v-model="searchQuery" 
                      class="form-control" 
                      placeholder="Enter search query" 
                  />
                  <button @click="searchExpense" class="btn btn-primary">Search</button>
              </div>
          </form>

          <!-- Hiển thị kết quả dưới dạng bảng -->
          <div v-if="searchPerformed">
              <!-- Nếu có dữ liệu -->
              <div v-if="expenses.length > 0" class="table-responsive">
                  <table class="table table-bordered">
                      <thead>
                          <tr>
                              <th>#</th>
                              <th>TITLE</th>
                              <th>Category</th>
                              <th>Amount</th>
                              <th>Date</th>
                              <th>Description</th>
                          </tr>
                      </thead>
                      <tbody>
                          <tr v-for="(expense, index) in expenses" :key="expense.id">
                              <td>{{ index + 1 }}</td>
                              <td>{{ expense.title }}</td>
                              <td>{{ expense.category?.name || 'No Category' }}</td>
                              <td>{{ formatCurrency(expense.amount) }}</td>
                              <td>{{ formatDate(expense.date) }}</td>
                              <td class="description">{{ expense.description }}</td>
                          </tr>
                      </tbody>
                  </table>
              </div>

              <!-- Nếu không có dữ liệu -->
              <div v-else>
                  <span class="m-1">Không tìm thấy dữ liệu cho từ khóa "{{ searchQuery }}"</span>
              </div>
          </div>
      </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      searchQuery: '',
      expenses: [],
      searchPerformed: false,
    };
  },
  methods: {
    
    async searchExpense() {
         // Check if searchQuery is empty
         if (!this.searchQuery) {
        alert('Vui lòng nhập từ khóa tìm kiếm');
        return;
      }
      const token = localStorage.getItem('token');
      if (!token) {
        console.log('No token available');
        return;
      }

      try {
        const response = await axios.get('http://localhost:8000/api/expenses', {
          headers: {
            Authorization: `Bearer ${token}`,
          },
          params: { search: this.searchQuery },
        });
        this.expenses = response.data.data || [];
      } catch (error) {
        console.error('Error fetching data:', error);
        if (error.response && error.response.status === 401) {
          alert('You are not authenticated. Please log in.');
          this.$router.push('/');
        } else {
          this.expenses = [];
        }
      } finally {
        this.searchPerformed = true;
      }
    },
    formatCurrency(amount) {
      return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND',
      }).format(amount);
    },
    formatDate(date) {
      return new Date(date).toLocaleDateString();
    },
  },
};
</script>


<style scoped>
.table {
  width: 100%;
  margin-top: 1rem;
  border-collapse: collapse;
}

.table th, .table td {
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

.table .btn {
  padding: 0.25rem 0.5rem;
  font-size: 0.75rem;
}

.table .btn-primary {
  background-color: #007bff;
  color: #fff;
  border-color: #007bff;
}

.table .btn-danger {
  background-color: #dc3545;
  color: #fff;
  border-color: #dc3545;
}
</style>
