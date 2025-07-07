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
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-medium text-sm">
                    {{ initials }}
                </div>
                <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-400 border-2 border-white rounded-full"></div>
            </div>
            
            <!-- User Info (hidden on small screens) -->
            <div class="hidden sm:block text-left">
                <p class="text-sm font-medium text-gray-900">{{ displayName }}</p>
                <p class="text-xs text-gray-500">{{ userRole }}</p>
            </div>
            
            <!-- Chevron -->
            <svg 
                class="w-4 h-4 text-gray-400 transition-transform duration-200"
                :class="{ 'rotate-180': isOpen }"
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
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
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-semibold">
                            {{ initials }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 truncate">{{ fullName }}</p>
                            <p class="text-sm text-gray-500 truncate">{{ userEmail }}</p>
                        </div>
                    </div>
                </div>

                <!-- Menu Items -->
                <div class="py-2">
                    <!-- Account Section -->
                    <div class="px-2 mb-2">
                        <p class="px-3 py-1 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Аккаунт
                        </p>
                        <MenuItem 
                            v-for="item in accountItems" 
                            :key="item.label"
                            :icon="item.icon"
                            :label="item.label"
                            @click="handleItemClick(item.route)"
                        />
                    </div>

                    <!-- Quick Actions -->
                    <div class="px-2 mb-2">
                        <p class="px-3 py-1 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Действия
                        </p>
                        <MenuItem 
                            v-for="item in actionItems" 
                            :key="item.label"
                            :icon="item.icon"
                            :label="item.label"
                            @click="handleItemClick(item.route)"
                        />
                    </div>

                    <!-- Divider -->
                    <hr class="border-gray-100 my-2">

                    <!-- Logout -->
                    <div class="px-2">
                        <button
                            @click="handleLogout"
                            class="w-full flex items-center space-x-3 px-3 py-2 text-left rounded-lg hover:bg-red-50 transition-colors group cursor-pointer"
                        >
                            <div class="w-8 h-8 rounded-lg bg-red-100 group-hover:bg-red-200 flex items-center justify-center transition-colors">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
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
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { apiClient } from '@shared/api/client'
import MenuItem from './UserMenuItem.vue'

const router = useRouter()
const userMenuRef = ref<HTMLElement>()
const isOpen = ref(false)

// User data (replace with actual user store/composable)
const fullName = ref('Иван Петров')
const userEmail = ref('ivan.petrov@example.com')
const userRole = ref('Владелец ресторана')

const displayName = computed(() => {
    const names = fullName.value.split(' ')
    return names.length > 1 ? `${names[0]} ${names[1][0]}.` : names[0]
})

const initials = computed(() => {
    return fullName.value
        .split(' ')
        .slice(0, 2)
        .map(name => name.charAt(0))
        .join('')
        .toUpperCase()
})

const accountItems = [
    { icon: '👤', label: 'Профиль', route: '/account/profile' },
    { icon: '⚙️', label: 'Настройки', route: '/account/settings' },
    { icon: '💳', label: 'Платежи', route: '/account/billing' }
]

const actionItems = [
    { icon: '📦', label: 'Мои заказы', route: '/account/orders' },
    { icon: '🔔', label: 'Уведомления', route: '/account/notifications' },
    { icon: '❓', label: 'Помощь', route: '/account/help' }
]

const toggleMenu = () => {
    isOpen.value = !isOpen.value
}

const closeMenu = () => {
    isOpen.value = false
}

const handleItemClick = (route: string) => {
    router.push(route)
    closeMenu()
}

const handleLogout = async () => {
    try {
        await apiClient.logout()
        closeMenu()
        router.push('/account/login')
    } catch (error) {
        console.error('Logout failed:', error)
        router.push('/account/login')
    }
}

// Close menu when clicking outside
const handleClickOutside = (event: Event) => {
    if (userMenuRef.value && !userMenuRef.value.contains(event.target as Node)) {
        closeMenu()
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
})
</script>
