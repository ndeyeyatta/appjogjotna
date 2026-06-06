import Dexie from 'dexie';

export const db = new Dexie('jogJotna');

db.version(1).stores({
  syncQueue: '++id, url, method, createdAt',
});
