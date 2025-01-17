// nuxt.config.ts
export default defineNuxtConfig({
  compatibilityDate: '2024-04-03',
  devtools: { enabled: true },
  runtimeConfig: {
    public: {
      baseUrl: process.env.API_BASE_URL || 'http://127.0.0.1:8000/api/', // Default URL
    },
  },
  app: {
    head: {
      link: [
        {
          rel: 'stylesheet',
          href: 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
        },
        {
          rel: 'stylesheet',
          href: 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css',
        },
      ],
      script: [
        {
          src: 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
          type: 'text/javascript',
        },
        {
          src: 'https://kit.fontawesome.com/ae360af17e.js',
          crossorigin: 'anonymous',
          type: 'text/javascript',
        },
        {
          src: 'https://apis.google.com/js/platform.js',
          async: true,
          defer: true,
        },

      ],
    },

  },
  modules: [
    '@vee-validate/nuxt', // VeeValidate for form validation
  ],
  plugins: [
    '~/plugins/auth.js',
    { src: '~/plugins/store.js', mode: 'client' },
  ],
});
