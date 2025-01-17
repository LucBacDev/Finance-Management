<template>
  <main class="content px-3 py-2">
    <div class="container-fluid">
      <!-- Table Element -->
      <div class="card border-0">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">Category List</h5>
          <!-- Search Input -->
          <NuxtLink to="/category/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add
          </NuxtLink>
        </div>
        <div class="card-body table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Title</th>
                <th scope="col" class="description">Description</th>
                <th scope="col">Type</th> <!-- Thêm cột Type ID -->
                <th scope="col">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(category, index) in data" :key="index">
                <th scope="row">{{ index + 1 }}</th>
                <td>{{ category.name }}</td>
                <td class="description">{{ category.description }}</td>
                <td>{{ category.type_id ? "expense" : "income" }}</td> <!-- Hiển thị Type ID -->
                <td>
                  <button class="btn btn-primary btn-sm" @click="editCategory(category)">
                    <i class="fas fa-edit"></i>
                  </button>
                  <button class="btn btn-danger btn-sm" @click="deleteCategory(category.id)">
                    <i class="fas fa-trash"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, onMounted } from "vue";
import Swal from 'sweetalert2';
import { useNuxtApp, useRouter } from "#app";

const data = ref([]); // Reactive state lưu danh sách categories
const { $axios } = useNuxtApp(); // Axios instance trong Nuxt 3
const router = useRouter(); // Sử dụng router để điều hướng

// Lấy dữ liệu từ API
const fetchData = async () => {
  try {
    const response = await $axios.get("categories"); // Đường dẫn API
    data.value = response.data.data; // Lưu danh sách vào state
  } catch (error) {
    console.error("Error fetching data:", error.response?.data || error.message);
  }
};

// Xóa một mục category
const deleteCategory = async (id) => {
  if (!confirm("Are you sure you want to delete this category?")) return;
  try {
    await $axios.delete(`categories/${id}`); // Gửi yêu cầu DELETE
    data.value = data.value.filter((category) => category.id !== id); // Cập nhật danh sách
    Swal.fire({
      icon: 'success',
      title: 'Xóa danh mục thành công',
      text: 'Dữ liệu đã được xóa vào thành công!',
    });
  } catch (error) {
    console.error("Error deleting category:", error.response?.data || error.message);
    Swal.fire({
      icon: 'error',
      title: 'xóa danh mục lỗi rồi',
      text: 'Dữ liệu xóa lỗi rồi!',
    });
  }
};

// Sửa một mục category
const editCategory = (category) => {
  router.push(`/category/${category.id}`); // Sử dụng router.push để điều hướng
};

onMounted(fetchData); // Lấy dữ liệu khi component được render
</script>

<style lang="scss" scoped>
.table {
margin-top: 1rem;
border-collapse: collapse; /* Đảm bảo các đường viền không bị tách biệt */
width: 100%;
background-color: #ffffff;
border: 1px solid #dee2e6;
border-radius: 0.375rem;
overflow: hidden;

th, td {
  text-align: center; /* Canh giữa nội dung */
  padding: 0.75rem;
  vertical-align: middle; /* Giữ nội dung ở giữa theo chiều dọc */
  border: 1px solid #dee2e6; /* Đường viền giữa các ô */
  font-size: 0.875rem;
}

th {
  background-color: #f5f5f5; /* Màu nền cho tiêu đề */
  font-weight: 600;
  color: #343a40;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.description {
  max-width: 120px; /* Đặt chiều rộng tối đa cho cột Description */
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis; /* Thêm dấu "..." nếu quá dài */
}

.text-end {
  text-align: right; /* Căn cột nút sang phải */
}

/* Hàng hover */

}

/* Định dạng nút */
.btn {
margin: 0.25rem;
padding: 0.3rem 0.75rem;
font-size: 0.75rem;
border-radius: 0.25rem;
}

.btn-primary {
background-color: #007bff;
border-color: #007bff;
&:hover {
  background-color: #0056b3;
  border-color: #0056b3;
}
}

.btn-danger {
background-color: #dc3545;
border-color: #dc3545;
&:hover {
  background-color: #a71d2a;
  border-color: #a71d2a;
}
}
</style>
