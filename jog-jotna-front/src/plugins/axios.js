import axios from 'axios';
import { enqueue, flushQueue, isOfflinePostable, isOnline } from '@/offline/sync';

axios.interceptors.response.use(
  (response) => response,
  async (error) => {
    const config = error.config;
    if (!error.response && config && !config.__offlineRetried && isOfflinePostable(config)) {
      if (!isOnline()) {
        await enqueue(config);
        return Promise.resolve({
          data: {
            message: 'Observation enregistrée hors ligne. Synchronisation à la reconnexion.',
            offline: true,
          },
          status: 202,
          statusText: 'Accepted',
          headers: {},
          config,
        });
      }
    }
    return Promise.reject(error);
  }
);

export async function syncNow() {
  return flushQueue();
}

export default axios;
