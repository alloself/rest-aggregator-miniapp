import { createHttpClient } from '@/shared/api/http';

const { client, initClient } = createHttpClient({
  baseURL: import.meta.env.VITE_API_URL,
});

export { client, initClient };
