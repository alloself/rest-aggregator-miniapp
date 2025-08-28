import { createHttpClient } from "@/shared/api/http";

const { client, initClient } = createHttpClient({
    baseURL: import.meta.env.VITE_API_URL,
    onAuthError: () => {
        const loginPath = "/account/login";
        if (window.location.pathname !== loginPath) {
            window.location.href = loginPath;
        }
    },
});

export { client, initClient };
