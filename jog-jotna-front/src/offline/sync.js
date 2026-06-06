import axios from 'axios';
import { db } from './db';

const OFFLINE_PATHS = ['/api/observations'];

export function isOnline() {
  return typeof navigator !== 'undefined' ? navigator.onLine : true;
}

export function isOfflinePostable(config) {
  if (!config || config.method?.toLowerCase() !== 'post') return false;
  const url = config.url || '';
  return OFFLINE_PATHS.some((p) => url.includes(p));
}

export async function enqueue(config) {
  await db.syncQueue.add({
    method: config.method,
    url: config.url,
    data: typeof config.data === 'string' ? JSON.parse(config.data) : config.data,
    headers: config.headers,
    createdAt: Date.now(),
  });
}

export async function pendingCount() {
  return db.syncQueue.count();
}

export async function flushQueue() {
  if (!isOnline()) return 0;
  const items = await db.syncQueue.orderBy('createdAt').toArray();
  let synced = 0;
  for (const item of items) {
    try {
      await axios({
        method: item.method,
        url: item.url,
        data: item.data,
        headers: item.headers,
      });
      await db.syncQueue.delete(item.id);
      synced++;
    } catch {
      break;
    }
  }
  return synced;
}

export function initOfflineSync() {
  window.addEventListener('online', () => { flushQueue(); });
  if (isOnline()) flushQueue();
}
