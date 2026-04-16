import { createRouter, createWebHistory } from 'vue-router';
import ImportsList from '@/components/ImportsList.vue';
import ImportUpload from '@/components/ImportUpload.vue';
import ImportDetail from '@/components/ImportDetail.vue';

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      component: ImportsList,
    },
    {
      path: '/upload',
      component: ImportUpload,
    },
    {
      path: '/imports/:id',
      component: ImportDetail,
    },
  ],
});

export default router;
