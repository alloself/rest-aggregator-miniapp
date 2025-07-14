<template>
    <div class="relative" ref="userMenuRef">
        <!-- User Avatar Button -->
        <button
            @click="toggleMenu"
            class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer"
            :class="{ 'bg-gray-50': isOpen }"
        >
            <!-- Avatar -->
            <div class="relative">
                <div
                    class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-medium text-sm"
                >
                    {{ initials }}
                </div>
            </div>

            <!-- User Info (hidden on small screens) -->
            <div class="hidden sm:block text-left">
                <p class="text-sm font-medium text-gray-900">
                    {{ displayName }}
                </p>
            </div>

            <!-- Chevron -->
            <Icon
                name="mdi:chevron-down"
                :size="16"
                color="#9CA3AF"
                :class="[
                    'transition-transform duration-200',
                    { 'rotate-180': isOpen },
                ]"
            />
        </button>

        <!-- Dropdown Menu -->
        <Transition
            enter-active-class="transition ease-out duration-100"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-if="isOpen"
                class="absolute right-0 top-full mt-2 w-72 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
            >
                <!-- User Header -->
                <div class="p-4 border-b border-gray-100">
                    <div class="flex items-center space-x-3">
                        <div
                            class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-semibold"
                        >
                            {{ initials }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 truncate">
                                {{ fullName }}
                            </p>
                            <p class="text-sm text-gray-500 truncate">
                                {{ userEmail }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Menu Items -->
                <div class="py-2">
                    <!-- Logout -->
                    <div class="px-2">
                        <button
                            @click="handleLogout"
                            class="w-full flex items-center space-x-3 px-3 py-2 text-left rounded-lg hover:bg-red-50 transition-colors group cursor-pointer"
                        >
                            <div
                                class="w-8 h-8 rounded-lg bg-red-100 group-hover:bg-red-200 flex items-center justify-center transition-colors"
                            >
                                <Icon
                                    name="mdi:logout"
                                    :size="16"
                                    color="#DC2626"
                                />
                            </div>
                            <span class="font-medium text-red-700">Выйти</span>
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from "vue";
import { useRouter } from "vue-router";
import { onClickOutside } from "@vueuse/core";
import { useAuthStore } from "@/shared/stores/auth";
import Icon from "@shared/ui/Icon.vue";
import { storeToRefs } from "pinia";

const router = useRouter();
const userMenuRef = ref<HTMLElement>();
const isOpen = ref(false);

const authStore = useAuthStore();

const { user } = storeToRefs(authStore);

const fullName = computed(() => {
    if (!user.value) return "";
    const firstName = user.value.first_name || "";
    const lastName = user.value.last_name || "";
    return `${firstName} ${lastName}`.trim();
});

const userEmail = computed(() => {
    return user.value?.email || "";
});

const displayName = computed(() => {
    const names = fullName.value.split(" ");
    return names.length > 1 ? `${names[0]} ${names[1][0]}.` : names[0];
});

const initials = computed(() => {
    return fullName.value
        .split(" ")
        .slice(0, 2)
        .map((name) => name.charAt(0))
        .join("")
        .toUpperCase();
});

const toggleMenu = () => {
    isOpen.value = !isOpen.value;
};

const closeMenu = () => {
    isOpen.value = false;
};

const handleLogout = async () => {
    try {
        await authStore.logout();
        closeMenu();
        router.push({ name: "login" });
    } catch (error) {
        console.error("Logout failed:", error);
        router.push({ name: "login" });
    }
};

// Закрытие меню при клике вне его области
onClickOutside(userMenuRef, closeMenu);
</script>
