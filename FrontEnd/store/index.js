import { createStore } from "vuex";

const store = createStore({
  state() {
    return {
      id: null,
    };
  },

  mutations: {
    setId(state, id) {
      state.id = id; // Cập nhật id trong store
    },
  },

  actions: {
    saveId({ commit }, id) {
        localStorage.setItem("id", id); // Lưu vào localStorage
        commit("setId", id); // Lưu vào Vuex state
      },
    loadId({ commit }) {
      const id = localStorage.getItem("id");
      console.log();
      if (id) {
        commit("setId", id); // Cập nhật vào store
      }
    },
  },

  getters: {
    getId: (state) => state.id, // Truy xuất id từ store
  },
});

export default store;
