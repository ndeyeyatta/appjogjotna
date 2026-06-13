import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';

const routes = [
  { path: '/login',                    name: 'login',               component: () => import('@/pages/Login.vue'),               meta: { guest: true } },
  { path: '/dashboard/parent',         name: 'dashboard-parent',    component: () => import('@/pages/DashboardParent.vue'),      meta: { auth: true, roles: ['parent'] } },
  { path: '/dashboard/encadreur',      name: 'dashboard-encadreur', component: () => import('@/pages/DashboardEncadreur.vue'),   meta: { auth: true, roles: ['encadreur'] } },
  { path: '/dashboard/responsable',    name: 'dashboard-responsable',component: () => import('@/pages/DashboardResponsable.vue'),meta: { auth: true, roles: ['responsable','admin'] } },
  { path: '/enfants',                  name: 'enfants',             component: () => import('@/pages/EnfantList.vue'),           meta: { auth: true } },
  { path: '/enfants/creer',            name: 'enfant-creer',        component: () => import('@/pages/EnfantForm.vue'),           meta: { auth: true, roles: ['encadreur','responsable','admin'] } },
  { path: '/enfants/:id/modifier',     name: 'enfant-modifier',     component: () => import('@/pages/EnfantForm.vue'),           props: r => ({ id: parseInt(r.params.id) }), meta: { auth: true, roles: ['encadreur','responsable','admin'] } },
  { path: '/enfants/:id/mesure',       name: 'enfant-mesure',       component: () => import('@/pages/MesureForm.vue'),           props: r => ({ id: parseInt(r.params.id) }), meta: { auth: true, roles: ['encadreur','responsable','admin'] } },
  { path: '/enfants/:id',              name: 'enfant-fiche',        component: () => import('@/pages/EnfantFiche.vue'),          props: r => ({ id: parseInt(r.params.id) }), meta: { auth: true } },
  { path: '/evaluations/creer',        name: 'evaluation-creer',    component: () => import('@/pages/EvaluationForm.vue'),       props: r => ({ enfantId: r.query.enfant ? parseInt(r.query.enfant) : null }), meta: { auth: true, roles: ['encadreur','responsable','admin'] } },
  { path: '/evaluations/:id/resultat', name: 'evaluation-resultat', component: () => import('@/pages/EvaluationResultat.vue'),   props: r => ({ id: parseInt(r.params.id) }), meta: { auth: true } },
  { path: '/observations/creer',       name: 'observation-creer',   component: () => import('@/pages/ObservationForm.vue'),      props: r => ({ enfantId: r.query.enfant ? parseInt(r.query.enfant) : null }), meta: { auth: true, roles: ['parent'] } },
  { path: '/observations/validation',  name: 'observation-validation', component: () => import('@/pages/ObservationValidation.vue'), meta: { auth: true, roles: ['encadreur','responsable','admin'] } },
  { path: '/admin/utilisateurs',      name: 'admin-utilisateurs',  component: () => import('@/pages/AdminUsers.vue'),           meta: { auth: true, roles: ['admin'] } },
  { path: '/admin/config',            name: 'admin-config',        component: () => import('@/pages/AdminConfig.vue'),          meta: { auth: true, roles: ['admin'] } },
  { path: '/admin/sauvegarde',        name: 'admin-sauvegarde',    component: () => import('@/pages/AdminBackup.vue'),          meta: { auth: true, roles: ['admin'] } },
  { path: '/alertes',                  name: 'alertes',             component: () => import('@/pages/AlerteList.vue'),           meta: { auth: true } },
  { path: '/seances/creer',            name: 'seance-creer',        component: () => import('@/pages/SeanceForm.vue'),           meta: { auth: true, roles: ['encadreur','responsable','admin'] } },
  { path: '/rapports',                 name: 'rapports',            component: () => import('@/pages/RapportPage.vue'),          meta: { auth: true, roles: ['responsable','admin'] } },
  { path: '/notifications',            name: 'notifications',       component: () => import('@/pages/Notifications.vue'),        meta: { auth: true } },
  { path: '/profil',                   name: 'profil',              component: () => import('@/pages/Profil.vue'),               meta: { auth: true } },
  { path: '/', redirect: '/login' },
  { path: '/:pathMatch(.*)*', redirect: '/login' },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior: () => ({ top: 0 }),
});

router.beforeEach(async (to) => {
  const authStore = useAuthStore();

  if (localStorage.getItem('jog_token') && !authStore.user) {
    try { await authStore.chargerProfil(); } catch {}
  }

  if (to.meta.auth && !authStore.estConnecte) return '/login';

  if (to.meta.guest && authStore.estConnecte) {
    const r = authStore.user?.role;
    if (r === 'parent')      return '/dashboard/parent';
    if (r === 'encadreur')   return '/dashboard/encadreur';
    return '/dashboard/responsable';
  }

  if (to.meta.roles && authStore.user && !to.meta.roles.includes(authStore.user.role)) {
    const r = authStore.user.role;
    if (r === 'parent')      return '/dashboard/parent';
    if (r === 'encadreur')   return '/dashboard/encadreur';
    return '/dashboard/responsable';
  }
});

export default router;
