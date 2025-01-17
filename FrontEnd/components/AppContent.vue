<template>
  <main class="content px-3 py-2">
    <div class="container-fluid">
      <!-- Phần chào mừng và thời tiết -->
      <div class="row">
        <div class="col-12 col-md-6 d-flex">
          <div class="card flex-fill border-0 illustration">
            <div class="card-body p-0 d-flex flex-fill">
              <div class="g-0 w-100">
                <div class="p-3 m-1">
                  <h4>Welcome Back, {{ userName }}</h4>
                  <p class="mb-0">Dashboard, CodzSword</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-6 d-flex">
          <div class="card flex-fill border-0">
            <div class="card-body py-4">
              <div class="d-flex align-items-start">
                <div class="flex-grow-1">
                  <h4 class="mb-2">{{ weather.temperature }} °C</h4>
                  <p class="mb-2">{{ weather.condition }}</p>
                  <p class="mb-2">{{ location }}</p> <!-- Hiển thị tên tỉnh/thành phố -->
                  <div class="mb-0">
                    <span class="text-muted me-2">Gió: {{ weather.windSpeed }} m/s</span>
                    <span class="text-muted">Mây: {{ weather.cloudiness }} % </span>
                    <span class="text-muted">Mưa: {{ weather.rain }}</span>
                  </div>
                </div>
                <div>
                  <img v-if="weather.temperature > 20" src="https://banner2.cleanpng.com/20180316/qbq/av0un0x7e.webp"
                    alt="Mặt trời" class="weather-icon" />
                  <img v-else src="https://banner2.cleanpng.com/20180316/qbq/av0un0x7e.webp" alt="Trời mưa"
                    class="weather-icon" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Phần thu nhập và chi tiêu -->
      <div class="row mt-3">
        <div class="col-12 col-md-6 d-flex">
          <div class="card flex-fill border-0">
            <div class="card-body py-4">
              <div class="d-flex align-items-start">
                <div class="flex-grow-1">
                  <h4 class="mb-2">Today you have earned</h4>
                  <h5 class="mb-2">{{ formatNumber(incomeTotalDay) }}</h5>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-6 d-flex">
          <div class="card flex-fill border-0">
            <div class="card-body py-4">
              <div class="d-flex align-items-start">
                <div class="flex-grow-1">
                  <h4 class="mb-2">Today you have spent</h4>
                  <h5 class="mb-2">{{ formatNumber(expenseTotalDay) }}</h5>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-6 d-flex">
          <div class="card flex-fill border-0">
            <div class="card-body table-responsive">
              <p class="fs-5">5 most recent income today</p>
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Amount</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(income, index) in filteredDataIncome" :key="income.id">
                    <td>{{ index + 1 }}</td>
                    <td>{{ income.source }}</td>
                    <td>{{ income.category?.name || 'No Category' }}</td>
                    <td>{{ formatNumber(income.amount) }}</td>
                  </tr>
                  <tr v-if="filteredDataIncome.length === 0">
                    <td colspan="5" class="text-center">No matching data found</td>
                  </tr>
                </tbody>
              </table>

            </div>
          </div>
        </div>
        <div class="col-12 col-md-6 d-flex">
          <div class="card flex-fill border-0">
            <div class="card-body table-responsive">
              <p class="fs-5">5 most recent expense today</p>
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Amount</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(expense, index) in filteredDataExpense" :key="expense.id">
                    <td>{{ index + 1 }}</td>
                    <td>{{ expense.title }}</td>
                    <td>{{ expense.category?.name || 'No Category' }}</td>
                    <td>{{ formatNumber(expense.amount) }}</td>
                  </tr>
                  <tr v-if="filteredDataExpense.length === 0">
                    <td colspan="5" class="text-center">No matching data found</td>
                  </tr>
                </tbody>
              </table>

            </div>
          </div>
        </div>
      </div>

    </div>


  </main>
  <div class="notifications-container">
    <div class="notification-item" v-for="reminder in activeReminders" :key="reminder.id">
      <div class="notification alert alert-success alert-dismissible fade show" role="alert">
        <p class="fs-6 over-flow-hidden fw-bold">{{ reminder.title }}</p>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"
          @click="removeNotification(reminder.id)"></button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useStore } from "vuex";
import { useNuxtApp } from "#app";

// Biến lưu trữ người dùng và thời tiết
const userName = ref("");
const user_id = ref(null);
const incomeTotalDay = ref(0);
const expenseTotalDay = ref(0);
const weather = ref({
  temperature: "--",
  condition: "Đang tải thông tin thời tiết...",
  windSpeed: "--",
  cloudiness: "--",
  rain: "--"
});
const location = ref(""); // Tên thành phố/tỉnh

const activeReminders = ref([]);
let notifiedReminders = ref(new Set());

const dataReminder = ref([]);

const checkRemindersForToday = () => {
  const today = new Date().toISOString().slice(0, 10);
  dataReminder.value.forEach((reminder) => {
    if (reminder.due_date.startsWith(today) && !notifiedReminders.value.has(reminder.id)) {
      activeReminders.value.push(reminder);
      notifiedReminders.value.add(reminder.id);
    }
  });
};

const removeNotification = (id) => {
  activeReminders.value = activeReminders.value.filter(
    (reminder) => reminder.id !== id
  );
  notifiedReminders.value.delete(id);
};

// Lấy ngày hiện tại
const today = new Date();
const day = today.getDate();
const month = today.getMonth() + 1;
const year = today.getFullYear();

const { $axios } = useNuxtApp();
const store = useStore();

// Hàm format số
const formatNumber = (amount) => {
  return parseFloat(amount).toLocaleString("vi-VN") + " VND";
};

// Hàm lấy thông tin người dùng
const getUser = async () => {
  try {
    const response = await $axios.get("/users", {
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${localStorage.getItem("token")}`
      }
    });
    userName.value = response.data.data.name;
  } catch (error) {
    console.error("Error fetching user:", error);
  }
};

const filteredDataIncome = ref([]); // Dữ liệu đã lọc để hiển thị
const filteredDataExpense = ref([]); // Dữ liệu đã lọc để hiển thị

// Hàm lấy thông tin tài chính trong ngày
const fetchData = async () => {
  try {
    const resIncome = await $axios.get("/incomes", {
      params: {
        user_id: user_id.value,
      },
    });
    filteredDataIncome.value = resIncome.data.data.filter((income) => {
      const incomeDate = new Date(income.date); // Chuyển đổi date thành đối tượng Date
      return (
        incomeDate.getDate() === day &&
        incomeDate.getMonth() + 1 === month &&
        incomeDate.getFullYear() === year
      );
    });

    const resExpense = await $axios.get("/expenses", {
      params: {
        user_id: user_id.value,
      },
    });
    filteredDataExpense.value = resExpense.data.data.filter((expense) => {
      const expenseDate = new Date(expense.date); // Chuyển đổi date thành đối tượng Date
      return (
        expenseDate.getDate() === day &&
        expenseDate.getMonth() + 1 === month &&
        expenseDate.getFullYear() === year
      );
    });

    const resTotalDay = await $axios.get("total-day", {
      params: { id: user_id.value, day, month, year }
    });
    incomeTotalDay.value = resTotalDay.data.total_income || 0;
    expenseTotalDay.value = resTotalDay.data.total_expense || 0;

    const responseReminder = await $axios.get("reminder-list");
    dataReminder.value = responseReminder.data.data;
    checkRemindersForToday();
  } catch (error) {
    console.error("Error fetching data:", error.response?.data || error.message);
  }
};

// // Hàm xử lý phân trang
// const changePage = (page) => {
//   if (page < 1 || page > pagination.total_pages) return;
//   pagination.current_page = page;
//   fetchData(); // Lấy lại dữ liệu cho trang mới
// };

// // Pagination
// const pagination = {
//   current_page: 1,
//   per_page: 10,
//   total_pages: 1,
// };



// Hàm lấy thông tin thời tiết
const fetchWeather = async (lat, lon) => {
  const apiKey = "e11b89a26072699dcdae06356c07e7e2";
  const apiUrl = `https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&units=metric&lang=vi&appid=${apiKey}`;
  try {
    const response = await fetch(apiUrl);
    const data = await response.json();
    weather.value = {
      temperature: data.main.temp,
      condition: data.weather[0].description,
      windSpeed: data.wind.speed,
      cloudiness: data.clouds.all,
      rain: data.rain ? `${data.rain["1h"]} mm` : "Không có"
    };
    location.value = data.name;
  } catch (error) {
    console.error("Error fetching weather:", error);
  }
};

// Hàm lấy vị trí người dùng
const getUserLocation = () => {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      (position) => {
        const lat = position.coords.latitude;
        const lon = position.coords.longitude;
        fetchWeather(lat, lon);
      },
      (error) => {
        console.error("Error fetching location:", error);
      }
    );
  } else {
    alert("Trình duyệt không hỗ trợ geolocation");
  }
};

// onMounted
onMounted(async () => {
  await getUser();
  getUserLocation();
  await store.dispatch("loadId");
  user_id.value = store.getters.getId;
  await fetchData();
});
</script>

<style scoped>
.weather-icon {
  width: 50px;
  height: 50px;
  margin-left: 10px;
}

.notifications-container {
  position: fixed;
  top: 0;
  right: 0;
  padding: 10px;
  z-index: 9999;
}

.notification-item {
  background-color: #f8f9fa;
  border: 1px solid #ccc;
  border-radius: 5px;
  margin-bottom: 10px;
  padding: 10px;
}

.notification {
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 1050;
  width: 300px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
</style>
