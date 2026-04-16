import { createApp } from 'vue';
import App from './App.vue';
import router from './router/index';
import Toast from 'vue-toastification';
import 'vue-toastification/dist/index.css';
import '../css/app.css';

createApp(App).use(router).use(Toast).mount('#app');
