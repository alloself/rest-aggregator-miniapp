<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Sticky Header -->
        <header
            class="sticky top-0 z-50 bg-white shadow-sm border-b border-gray-200 h-16"
        >
            <div class="flex items-center justify-between px-4 py-3 h-full">
                <!-- Logo / Brand -->
                <div class="flex items-center space-x-4">
                    <h1 class="text-xl font-semibold text-gray-900">
                        Личный кабинет
                    </h1>
                </div>

                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    <UserMenu />
                </div>
            </div>
        </header>

        <div class="flex">
            <!-- Sticky Sidebar -->
            <aside
                class="sticky top-16 bg-white shadow-sm border-r border-gray-200 transition-all duration-300 ease-in-out flex flex-col z-40 self-start"
                :class="sidebarCollapsed ? 'w-16' : 'w-64'"
                style="height: calc(100vh - 4rem)"
            >
                <!-- Navigation - Scrollable -->
                <nav class="flex-1 p-3 overflow-hidden">
                    <ul class="space-y-2">
                        <li v-for="item in navigationItems" :key="item.name">
                            <RouterLink
                                :to="item.path"
                                class="flex items-center px-2 py-2 rounded-lg text-sm font-medium transition-colors"
                                :class="[
                                    getNavItemClass(item.path),
                                    sidebarCollapsed
                                        ? 'justify-center'
                                        : 'space-x-2',
                                ]"
                                v-tooltip.right="
                                    sidebarCollapsed ? item.label : ''
                                "
                            >
                                <Icon
                                    :name="item.icon"
                                    size="16px"
                                    class="text-base flex-shrink-0"
                                />
                                <span
                                    v-if="!sidebarCollapsed"
                                    class="whitespace-nowrap"
                                >
                                    {{ item.label }}
                                </span>
                            </RouterLink>
                        </li>
                    </ul>
                </nav>

                <!-- Collapse Toggle Button - Fixed Bottom -->
                <div
                    class="p-3 border-t border-gray-200 bg-white flex-shrink-0 flex"
                    :class="sidebarCollapsed ? 'justify-center' : 'justify-end'"
                >
                    <Button
                        :icon="
                            sidebarCollapsed
                                ? 'pi pi-chevron-right'
                                : 'pi pi-chevron-left'
                        "
                        text
                        rounded
                        @click="toggleSidebar"
                        :aria-label="
                            sidebarCollapsed
                                ? 'Развернуть меню'
                                : 'Свернуть меню'
                        "
                        v-tooltip.right="
                            sidebarCollapsed
                                ? 'Развернуть меню'
                                : 'Свернуть меню'
                        "
                    />
                </div>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 transition-all duration-300 ease-in-out">
                <div class="p-4">
                    <router-view v-slot="{ Component, route }">
                        <component :is="Component" :key="route.name" />
                    </router-view>
                </div>
            </main>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import Button from "primevue/button";
import { UserMenu } from "@account/ts/widgets/UserMenu";
import Icon from "@shared/ui/Icon.vue";

const route = useRoute();

const sidebarCollapsed = ref(true);

const navigationItems = [
    {
        name: "restaurant",
        path: "/restaurant",
        label: "Рестораны",
        icon: "mdi:restaurant",
    },
];

const toggleSidebar = () => {
    sidebarCollapsed.value = !sidebarCollapsed.value;
    localStorage.setItem(
        "account-sidebar-collapsed",
        sidebarCollapsed.value.toString(),
    );
};

const getNavItemClass = (path: string) => {
    const isActive = route.path === path;
    return isActive
        ? "bg-blue-100 text-blue-700 border border-blue-200"
        : "text-gray-700 hover:bg-gray-100 hover:text-gray-900";
};

onMounted(() => {
    const saved = localStorage.getItem("account-sidebar-collapsed");
    if (saved !== null) {
        sidebarCollapsed.value = saved === "true";
    }
});
</script>
