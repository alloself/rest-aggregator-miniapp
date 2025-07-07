<template>
    <Layout>
        <div class="space-y-4">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        Добро пожаловать
                    </h1>
                    <p class="mt-1 text-gray-600">
                        Обзор вашего аккаунта и последних активностей
                    </p>
                </div>
                <div class="text-sm text-gray-500">
                    Последнее обновление: {{ lastUpdated }}
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                <StatCard
                    title="Всего заказов"
                    :value="stats.totalOrders"
                    description="За все время"
                    icon="pi pi-shopping-cart"
                    iconColor="#3B82F6"
                    :trend="{ value: 12, unit: '%', period: 'за месяц' }"
                />
                
                <StatCard
                    title="Активные заказы"
                    :value="stats.activeOrders"
                    description="В обработке"
                    icon="pi pi-clock"
                    iconColor="#F59E0B"
                />
                
                <StatCard
                    title="Потрачено"
                    :value="formatCurrency(stats.totalSpent)"
                    description="За все время"
                    icon="pi pi-wallet"
                    iconColor="#10B981"
                    :trend="{ value: -5, unit: '%', period: 'за месяц' }"
                />
                
                <StatCard
                    title="Бонусные баллы"
                    :value="stats.bonusPoints"
                    description="Доступно к списанию"
                    icon="pi pi-star"
                    iconColor="#8B5CF6"
                />
            </div>

            <!-- Recent Orders Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-4 py-3 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">
                            Последние заказы
                        </h2>
                        <Button
                            label="Все заказы"
                            severity="secondary"
                            text
                            @click="navigateToOrders"
                        />
                    </div>
                </div>
                
                <div class="divide-y divide-gray-200">
                    <div 
                        v-for="order in recentOrders" 
                        :key="order.id"
                        class="px-4 py-3 hover:bg-gray-50 transition-colors"
                    >
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        Заказ #{{ order.id }}
                                    </div>
                                    <Badge 
                                        :value="getStatusLabel(order.status)"
                                        :severity="getStatusSeverity(order.status)"
                                    />
                                </div>
                                <div class="mt-1 text-sm text-gray-600">
                                    {{ order.restaurant }} • {{ formatCurrency(order.total) }}
                                </div>
                                <div class="mt-1 text-xs text-gray-500">
                                    {{ formatDate(order.createdAt) }}
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <Button
                                    icon="pi pi-eye"
                                    severity="secondary"
                                    text
                                    rounded
                                    @click="viewOrder(order.id)"
                                    aria-label="Просмотреть заказ"
                                />
                                <Button
                                    v-if="order.status === 'completed'"
                                    icon="pi pi-refresh"
                                    severity="secondary"
                                    text
                                    rounded
                                    @click="reorderItems(order.id)"
                                    aria-label="Повторить заказ"
                                />
                            </div>
                        </div>
                    </div>
                </div>
                
                <div v-if="recentOrders.length === 0" class="px-4 py-6 text-center">
                    <div class="text-gray-500">
                        <i class="pi pi-shopping-cart text-3xl mb-4 block" />
                        <p class="text-lg font-medium">Пока заказов нет</p>
                        <p class="text-sm mt-1">Оформите свой первый заказ</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    Быстрые действия
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <Button
                        label="Новый заказ"
                        icon="pi pi-plus"
                        @click="createNewOrder"
                        class="justify-start"
                    />
                    <Button
                        label="Настройки профиля"
                        icon="pi pi-user"
                        severity="secondary"
                        @click="navigateToSettings"
                        class="justify-start"
                    />
                    <Button
                        label="Связаться с поддержкой"
                        icon="pi pi-question-circle"
                        severity="secondary"
                        @click="contactSupport"
                        class="justify-start"
                    />
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { Layout } from '@account/ts/widgets/Layout';
import StatCard from '@account/ts/shared/ui/StatCard.vue';
import Button from 'primevue/button';
import Badge from 'primevue/badge';

const router = useRouter();

// Mock данные - заменить на реальные API вызовы
const stats = ref({
    totalOrders: 24,
    activeOrders: 2,
    totalSpent: 15420,
    bonusPoints: 340
});

const recentOrders = ref([
    {
        id: '12345',
        restaurant: 'Пицца Италия',
        total: 890,
        status: 'delivered',
        createdAt: '2024-01-15T12:30:00Z'
    },
    {
        id: '12344',
        restaurant: 'Суши Бар',
        total: 1250,
        status: 'in_progress',
        createdAt: '2024-01-14T19:45:00Z'
    },
    {
        id: '12343',
        restaurant: 'Бургер Хаус',
        total: 620,
        status: 'completed',
        createdAt: '2024-01-13T14:20:00Z'
    }
]);

const lastUpdated = computed(() => {
    return new Date().toLocaleString('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
});

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('ru-RU', {
        style: 'currency',
        currency: 'RUB',
        maximumFractionDigits: 0
    }).format(amount);
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleString('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const getStatusLabel = (status: string) => {
    const labels: Record<string, string> = {
        'pending': 'Ожидает',
        'in_progress': 'Готовится',
        'delivered': 'Доставлен',
        'completed': 'Завершен',
        'cancelled': 'Отменен'
    };
    return labels[status] || status;
};

const getStatusSeverity = (status: string) => {
    const severities: Record<string, string> = {
        'pending': 'warning',
        'in_progress': 'info',
        'delivered': 'success',
        'completed': 'success',
        'cancelled': 'danger'
    };
    return severities[status] || 'info';
};

// Actions
const navigateToOrders = () => {
    router.push('/account/orders');
};

const navigateToSettings = () => {
    router.push('/account/settings');
};

const viewOrder = (orderId: string) => {
    router.push(`/account/orders/${orderId}`);
};

const reorderItems = (orderId: string) => {
    // TODO: Implement reorder logic
    console.log('Reorder:', orderId);
};

const createNewOrder = () => {
    // TODO: Navigate to order creation or restaurant selection
    console.log('Create new order');
};

const contactSupport = () => {
    // TODO: Open support chat or contact form
    console.log('Contact support');
};

onMounted(() => {
    // TODO: Load real data from API
});
</script>

<style scoped>
/* Additional dashboard-specific styles if needed */
</style> 