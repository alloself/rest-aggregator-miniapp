<template>
    <div class="working-hours-editor">
        <Panel header="Часы работы" class="mb-4">
            <div class="space-y-4">
                <div
                    v-for="day in days"
                    :key="day.key"
                    class="flex items-center gap-4 p-3 border border-gray-200 rounded-lg"
                >
                    <div class="w-32">
                        <span class="font-medium">{{ day.label }}</span>
                    </div>

                    <div 
                        v-if="isWorkingDay(day.key)"
                        class="flex items-center gap-2 flex-1"
                    >
                    <div class="flex flex-1 items-center gap-2">
                        <div class="flex flex-col">
                            <label class="text-sm text-gray-600 mb-1">Открытие</label>
                            <DatePicker
                                :model-value="parseTime(modelValue?.[day.key]?.startTime)"
                                @update:model-value="updateStartTime(day.key, $event)"
                                time-only
                                hour-format="24"
                                :show-icon="false"
                                class="w-24"
                                placeholder="00:00"
                            />
                        </div>
                        
                        <span class="text-gray-400 mt-6">—</span>
                        
                        <div class="flex flex-col">
                            <label class="text-sm text-gray-600 mb-1">Закрытие</label>
                            <DatePicker
                                :model-value="parseTime(modelValue?.[day.key]?.endTime)"
                                @update:model-value="updateEndTime(day.key, $event)"
                                time-only
                                hour-format="24"
                                :show-icon="false"
                                class="w-24"
                                placeholder="00:00"
                            />
                        </div>
                        </div>
                        
                        <Button
                            @click="setDayOff(day.key)"
                            size="small"
                            severity="secondary"
                            outlined
                            class="ml-2"
                        >
                            Выходной
                        </Button>
                    </div>

                    <div 
                        v-else
                        class="flex-1 flex items-center justify-between"
                    >
                        <span class="text-gray-500 italic">Выходной день</span>
                        <Button
                            @click="setWorkingDay(day.key)"
                            size="small"
                            severity="success"
                            outlined
                        >
                            Добавить часы
                        </Button>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-gray-200">
                <div class="flex gap-2">
                    <Button
                        @click="copyFromPrevious"
                        size="small"
                        severity="secondary"
                        outlined
                    >
                        Скопировать с предыдущего дня
                    </Button>
                    <Button
                        @click="setStandardHours"
                        size="small"
                        severity="secondary"
                        outlined
                    >
                        Стандартные часы (9:00-22:00)
                    </Button>
                    <Button
                        @click="clearAll"
                        size="small"
                        severity="danger"
                        outlined
                    >
                        Очистить все
                    </Button>
                </div>
            </div>
        </Panel>
    </div>
</template>

<script setup lang="ts">
import Panel from 'primevue/panel';
import DatePicker from 'primevue/datepicker';
import Button from 'primevue/button';

// Типы для часов работы
interface DaySchedule {
    startTime: string;
    endTime: string;
}

interface WorkingHours {
    monday?: DaySchedule;
    tuesday?: DaySchedule;
    wednesday?: DaySchedule;
    thursday?: DaySchedule;
    friday?: DaySchedule;
    saturday?: DaySchedule;
    sunday?: DaySchedule;
}

// Дни недели
const days = [
    { key: 'monday', label: 'Понедельник' },
    { key: 'tuesday', label: 'Вторник' },
    { key: 'wednesday', label: 'Среда' },
    { key: 'thursday', label: 'Четверг' },
    { key: 'friday', label: 'Пятница' },
    { key: 'saturday', label: 'Суббота' },
    { key: 'sunday', label: 'Воскресенье' },
] as const;

type DayKey = typeof days[number]['key'];

// Модель данных
const modelValue = defineModel<WorkingHours>('modelValue', {
    default: () => ({})
});

/**
 * Проверяет, является ли день рабочим (есть время работы)
 */
const isWorkingDay = (dayKey: DayKey): boolean => {
    const day = modelValue.value?.[dayKey];
    return !!(day?.startTime && day?.endTime);
};

/**
 * Устанавливает день как рабочий
 */
const setWorkingDay = (dayKey: DayKey) => {
    const currentValue = modelValue.value || {};
    
    currentValue[dayKey] = {
        startTime: '09:00',
        endTime: '22:00'
    };
    
    modelValue.value = currentValue;
};

/**
 * Устанавливает день как выходной
 */
const setDayOff = (dayKey: DayKey) => {
    const currentValue = modelValue.value || {};
    
    if (currentValue[dayKey]) {
        delete currentValue[dayKey];
    }
    
    modelValue.value = currentValue;
};

/**
 * Парсит строку времени в объект Date
 */
const parseTime = (timeString?: string): Date | null => {
    if (!timeString) return null;
    
    const [hours, minutes] = timeString.split(':').map(Number);
    const date = new Date();
    date.setHours(hours, minutes, 0, 0);
    return date;
};

/**
 * Форматирует объект Date в строку времени
 */
const formatTime = (date: Date | null): string => {
    if (!date) return '';
    
    const hours = date.getHours().toString().padStart(2, '0');
    const minutes = date.getMinutes().toString().padStart(2, '0');
    return `${hours}:${minutes}`;
};

/**
 * Обновляет время начала работы
 */
const updateStartTime = (dayKey: DayKey, date: Date | Date[] | (Date | null)[] | null | undefined) => {
    const currentValue = modelValue.value || {};
    if (!currentValue[dayKey]) return;
    
    // Извлекаем Date из возможных типов DatePicker
    const dateValue = Array.isArray(date) ? date[0] : date;
    // Приводим undefined к null для совместимости с formatTime
    const normalizedDate = dateValue === undefined ? null : dateValue;
    currentValue[dayKey]!.startTime = formatTime(normalizedDate);
    modelValue.value = currentValue;
};

/**
 * Обновляет время окончания работы
 */
const updateEndTime = (dayKey: DayKey, date: Date | Date[] | (Date | null)[] | null | undefined) => {
    const currentValue = modelValue.value || {};
    if (!currentValue[dayKey]) return;
    
    // Извлекаем Date из возможных типов DatePicker
    const dateValue = Array.isArray(date) ? date[0] : date;
    // Приводим undefined к null для совместимости с formatTime
    const normalizedDate = dateValue === undefined ? null : dateValue;
    currentValue[dayKey]!.endTime = formatTime(normalizedDate);
    modelValue.value = currentValue;
};

/**
 * Копирует часы работы с предыдущего дня
 */
const copyFromPrevious = () => {
    const currentValue = modelValue.value || {};
    
    for (let i = 1; i < days.length; i++) {
        const currentDay = days[i].key;
        const previousDay = days[i - 1].key;
        
        if (currentValue[previousDay]) {
            currentValue[currentDay] = {
                ...currentValue[previousDay]
            };
        }
    }
    
    modelValue.value = currentValue;
};

/**
 * Устанавливает стандартные часы работы (9:00-22:00) для всех дней
 */
const setStandardHours = () => {
    const currentValue: WorkingHours = {};
    
    days.forEach(day => {
        currentValue[day.key] = {
            startTime: '09:00',
            endTime: '22:00'
        };
    });
    
    modelValue.value = currentValue;
};

/**
 * Очищает все часы работы (все дни становятся выходными)
 */
const clearAll = () => {
    modelValue.value = {};
};
</script>

<style scoped>
.working-hours-editor {
    width: 100%;
}

.space-y-4 > * + * {
    margin-top: 1rem;
}
</style> 