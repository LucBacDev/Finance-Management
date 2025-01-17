<template>
  <div>
    <div class="calendar-navigation d-flex">
      <button id="prev-month"> &lt </button>
          <span id="month-year-display" hidden></span>
          <p class="fs-2 mb-0">{{ currentMonth + 1 }}/{{ currentYear }}</p>
          <button id="next-month">></button>
    </div>
    <div class="d-flex justify-content-around row mt-5">
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card">
          <div class="card-body">
            <div class="card-title">
              Expense
            </div>
            <div class="card-text">
              <div>
                <div v-if="isLoading" class="loading-overlay">
                  <div class="spinner"></div>
                </div>

                <div v-else>
                  {{ formatNumber(total_expense) }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card">
          <div class="card-body">
            <div class="card-title">
              Income
            </div>
            <div class="card-text">
              <div>
                <div v-if="isLoading" class="loading-overlay">
                  <div class="spinner"></div>
                </div>

                <div v-else>
                  {{ formatNumber(total_income) }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card">
          <div class="card-body">
            <div class="card-title">
              Balance
            </div>
            <div class="card-text">
              <div>
                <div v-if="isLoading" class="loading-overlay">
                  <div class="spinner"></div>
                </div>

                <div v-else>
                  {{ formatNumber(balance) }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div>
      <div class="button-container">
        <button type="button" @click="showExpenseList"
          :class="['btn', selectedTab === 'expense' ? 'btn-primary active' : 'btn-secondary']">Chi Tiêu</button>
        <button type="button" @click="showIncomeList"
          :class="['btn', selectedTab === 'income' ? 'btn-success active' : 'btn-secondary']">Thu Nhập</button>
      </div>
      <div class="mt-5 container" style="position: relative;">
        <div v-if="isLoading" class="loading-overlay">
          <div class="spinner"></div>
        </div>
        <div v-else class="row d-flex">
          <div class="col-12 col-lg-6">
            <canvas ref="chartCanvasDoc" style="padding-left:20px"></canvas>
          </div>
          <div class="col-12 col-lg-6">
            <canvas id="chartjs-doughnut" ref="chartCanvas"></canvas>
          </div>
        </div>
      </div>
    </div>
    <div class="table-container">
      <h2 class="table-title">Danh sách</h2>
      <table class="custom-table">
        <thead>
          <tr>
            <th>Id</th>
            <th>Tên Danh mục</th>
            <th>Tổng tiền</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in displayedList" :key="item.id">
            <td>{{ item.category_id }}</td>
            <td>{{ item.category_name }}</td>
            <td>{{ formatNumber(item.total_amount) }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref, watch } from "vue";
import Chart from "chart.js/auto";
import { useNuxtApp } from "#app";
import { useStore } from 'vuex';

const { $axios } = useNuxtApp();

// Declare variables
const displayedList = ref([]);
const expenses = ref([]);
const incomes = ref([]);
const currentMonth = ref(new Date().getMonth());
const currentYear = ref(new Date().getFullYear());
const selectedTab = ref('expense');
const total_income = ref(0);
const total_expense = ref(0);
const balance = ref(0);
const statistic = ref([]);
const user_id = ref(null);
let chartInstance = null;
let chartDoc = null;
const chartCanvas = ref(null);
const chartCanvasDoc = ref(null);

// Store
const store = useStore();

// Methods
const formatNumber = (amount) => {
  return parseFloat(amount).toLocaleString("vi-VN") + " VND";
};
const updateMonthYearDisplay = () => {
  const monthYearDisplay = document.getElementById("month-year-display");
  monthYearDisplay.textContent = `${getMonthName(currentMonth.value)} ${currentYear.value}`;
};

const prevMonth = () => {
  if (currentMonth.value === 0) {
    currentMonth.value = 11;
    currentYear.value--;
  } else {
    currentMonth.value--;
  }
  updateMonthYearDisplay();
  fetchData();
};

const nextMonth = () => {
  if (currentMonth.value === 11) {
    currentMonth.value = 0;
    currentYear.value++;
  } else {
    currentMonth.value++;
  }
  updateMonthYearDisplay();
  fetchData();
};

const getMonthName = (month) => {
  const months = [
    "January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December",
  ];
  return months[month];
};
const isLoading = ref(false);

const fetchData = async () => {
  try {

    const month = currentMonth.value + 1;
    const year = currentYear.value;
    isLoading.value = true; // Bắt đầu loading
    const resIncomeSummary = await $axios.get("income-summary", {
      params: { id: user_id.value, year: year, month: month }
    });
    incomes.value = resIncomeSummary.data.data;

    const resExpenseSummary = await $axios.get("expense-summary", {
      params: { id: user_id.value, year: year, month: month }
    });
    expenses.value = resExpenseSummary.data.data;

    const resIncome = await $axios.get("income-total", {
      params: { id: user_id.value, year: year, month: month }
    });
    total_income.value = resIncome.data.total_income;

    const resExpense = await $axios.get("expense-total", {
      params: { id: user_id.value, year: year, month: month }
    });
    total_expense.value = resExpense.data.total_expense;

    balance.value = total_income.value - total_expense.value;

    const resStatistic = await $axios.get("statistics", {
      params: { id: user_id.value, year: year, month: month }
    });
    statistic.value = resStatistic.data.data;

    // Default display the expense list
    showExpenseList();
  } catch (error) {
    console.error("Error fetching data:", error.response?.data || error.message);
  } finally {
    isLoading.value = false; // Kết thúc loading

  };
}

const showExpenseList = () => {
  displayedList.value = expenses.value;
  selectedTab.value = 'expense';
  updateChart(expenses.value);
  renderChart(statistic)
};

const showIncomeList = () => {
  displayedList.value = incomes.value;
  selectedTab.value = 'income';
  updateChart(incomes.value);
  renderChart(statistic)
};

// Chart methods
const updateChart = (data) => {

  if (chartInstance) {
    chartInstance.destroy();
  }

  const labels = data.map(item => item.category_name);
  const dataValues = data.map(item => item.total_amount);

  const predefinedColors = ["#007bff", "#28a745", "#ffc107", "#dee2e6"];
  const backgroundColors = [];

  labels.forEach((_, index) => {
    if (index < predefinedColors.length) {
      backgroundColors.push(predefinedColors[index]);
    } else {
      backgroundColors.push(getRandomColor());
    }
  });

  if (chartCanvas.value) {
    chartInstance = new Chart(chartCanvas.value, {
      type: "doughnut",
      data: {
        labels: labels,
        datasets: [
          {
            data: dataValues || 0,
            backgroundColor: backgroundColors,
            borderColor: "transparent",
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: true,
            position: "top",
          },
        },
      },
    });
  }
  chartInstance.resize();

};

const getRandomColor = () => {
  const letters = '0123456789ABCDEF';
  let color = '#';
  for (let i = 0; i < 6; i++) {
    color += letters[Math.floor(Math.random() * 16)];
  }
  return color;
};

const renderChart = (statistic) => {
  console.log(chartDoc);

  if (chartDoc) {
    console.log("123");
    chartDoc.destroy();
    console.log("123");

  }

  const ctx = chartCanvasDoc.value.getContext('2d');
  const labels = statistic.value.map(item => item.month_year);
  const chiTieu = statistic.value.map(item => item.total_expense);
  const soDu = statistic.value.map(item => item.balance);

  chartDoc = new Chart(ctx, {
    type: 'bar',
    data: {
      labels,
      datasets: [
        {
          label: 'Expense',
          data: chiTieu,
          backgroundColor: 'rgba(255, 99, 132, 0.8)',
        },
        {
          label: 'Balance',
          data: soDu,
          backgroundColor: 'rgba(54, 162, 235, 0.8)',
        },
      ],
    },
    options: {
      responsive: true,
      plugins: {
        title: {
          display: true,
          text: 'Stacked Bar Chart: Spending and Balance by Month',
        },
      },
      scales: {
        x: {
          stacked: true,
        },
        y: {
          stacked: true,
          title: {
            display: true,
            text: 'Money (đồng)',
          },
        },
      },
    },
  });
  chartInstance.resize();

};

// Lifecycle hooks
onMounted(async () => {
  document.getElementById("prev-month").addEventListener("click", prevMonth);
  document.getElementById("next-month").addEventListener("click", nextMonth);
  updateMonthYearDisplay();
  await store.dispatch('loadId');
  user_id.value = store.getters.getId;
  await fetchData();
  // await renderChart(statistic);

});

watch([currentMonth, currentYear], fetchData);
</script>


<style>
#chartjs-doughnut {
  width: 100%;
  height: 300px;
}

.active {
  background-color: #007bff;
  color: white;
}

.calendar-navigation {
  margin-top: 20px;
  justify-content: center;

}

.calendar-navigation h1 {
  margin: 0 20px;
}

.table-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  margin-top: 20px;
  padding: 20px;
}

.table-title {
  font-size: 1.5rem;
  margin-bottom: 10px;
  color: #333;
}

.custom-table {
  width: 80%;
  border-collapse: collapse;
  margin-top: 20px;
}

.custom-table th,
.custom-table td {
  text-align: center;
  padding: 10px;
  border: 1px solid #ddd;
  background-color: #fff;
}

.custom-table th {
  background-color: #007bff;
  color: white;
  font-weight: bold;
}

.custom-table tr:hover {
  background-color: #f1f1f1;
}

.button-container {
  display: flex;
  justify-content: center;
  gap: 20px;
  /* Khoảng cách giữa các nút */
  margin-top: 20px;
}

button {
  /* padding: 10px 20px; */
  margin: 10px;
  font-size: 1rem;
  cursor: pointer;
  border: 2px solid #007bff;
  border-radius: 5px;
  background-color: white;
  color: #007bff;
  transition: all 0.3s ease;
}

button:hover {
  background-color: #007bff;
  color: white;
}

button.active {
  background-color: #007bff;
  color: white;
}

button:focus {
  outline: none;
}

.card {
  border: none;
  border-radius: 10px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  margin-bottom: 20px;
}

.card .card-body {
  padding: 20px;
}

.card .card-body .card-title {
  font-size: 20px;
  color: #007bff;
}

.card .card-body .card-text {
  font-size: 20px;
  font-weight: bold;
}

.card .card-body .progress {
  height: 5px;
  margin-top: 10px;
}

.canvas {
  height: 300px;
  min-height: 300px;
  min-width: 300px;

}

.loading-overlay {
  position: absolute;
  /* Đảm bảo chỉ áp dụng cho vùng chứa */
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(255, 255, 255, 0.8);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
  border-radius: 10px;
  /* Để phù hợp với thiết kế thẻ card */
}

.spinner {
  border: 5px solid #f3f3f3;
  border-top: 5px solid #007bff;
  border-radius: 50%;
  width: 50px;
  height: 50px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }

  100% {
    transform: rotate(360deg);
  }
}
</style>