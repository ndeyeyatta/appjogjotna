import { defineStore } from 'pinia';
import axios from 'axios';

const TOKEN_KEY = 'jog_token';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem(TOKEN_KEY) || null,
  }),

  getters: {
    estConnecte: (state) => !!state.token,
    nomComplet: (state) => {
      if (!state.user) return '';
      if (state.user.nom_complet) return state.user.nom_complet;
      return `${state.user.prenom || ''} ${state.user.nom || ''}`.trim();
    },
    notifNonLues: (state) => state.user?.notifications_non_lues ?? 0,
  },

  actions: {
    init() {
      if (this.token) {
        axios.defaults.headers.common.Authorization = `Bearer ${this.token}`;
      }
    },

    _appliquerToken(token) {
      this.token = token;
      if (token) {
        localStorage.setItem(TOKEN_KEY, token);
        axios.defaults.headers.common.Authorization = `Bearer ${token}`;
      } else {
        localStorage.removeItem(TOKEN_KEY);
        delete axios.defaults.headers.common.Authorization;
      }
    },

    async login(email, mot_de_passe) {
      const { data } = await axios.post('/api/login', { email, mot_de_passe });
      this._appliquerToken(data.token);
      await this.chargerProfil();
      return data;
    },

    async chargerProfil() {
      const { data } = await axios.get('/api/me');
      this.user = data;
      return data;
    },

    async logout() {
      try {
        if (this.token) await axios.post('/api/logout');
      } catch {}
      this.user = null;
      this._appliquerToken(null);
    },
  },
});
