import { defineStore } from "pinia";
import { ref } from "vue";

import type { AuthUser, LoginRequest, LoginResponse } from "../types/auth";
import { client } from "@account/ts/shared/api/axios";

export const useAuthStore = defineStore("auth", () => {
    const user = ref<AuthUser | null>(null);

    async function login(credentials: LoginRequest) {
        await client.post<LoginResponse>("/auth/login", credentials);
        await fetchUser();
    }

    async function logout(): Promise<void> {
        try {
            await client.post("/auth/logout");
        } finally {
            user.value = null;
        }
    }

    async function fetchUser(): Promise<void> {
        try {
            const response = await client.get<AuthUser>("/api/me");
            user.value = response.data;
        } catch (error) {
            user.value = null;
        }
    }

    return {
        user,
        login,
        logout,
        fetchUser,
    };
});
